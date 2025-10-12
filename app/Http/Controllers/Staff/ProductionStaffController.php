<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\Production;
use App\Models\ProductionMaterial;
use App\Models\RawMaterialBatches;
use App\Models\StockMovement;
use App\Models\Warehouse;
use App\Models\Material;

class ProductionStaffController extends Controller
{
    public function index()
    {
        $count = Production::count();
        $inProgress = Production::where('status','in_progress')->count();
        $qcPending = Production::where('status','qc_pending')->count();
        $qcPassed = Production::where('status','qc_passed')->count();
        $batches = Production::orderBy('created_at','desc')->limit(10)->get();
        return view('pages.staff.production.index', compact([
            'count',
            'inProgress',
            'qcPending',
            'qcPassed',
            'batches',
        ]));
    }

    public function create()
    {
        $materials = Material::all();
        $warehouses = Warehouse::all();
        $batches = Production::orderBy('created_at','desc')->paginate(10);
        return view('pages.staff.production.create', compact('materials', 'warehouses', 'batches'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'production_code' => 'nullable|string|unique:productions,production_code',
            'production_date' => 'nullable|date',
            'shift' => 'nullable|string',
            'quantity_carton' => 'nullable|integer',
            'notes' => 'nullable|string',
        ]);

        $data["production_code"] = Production::generateCode();
        $data['status'] = 'planned';

        $batch = Production::create($data);

        return redirect()->route('production.index', $batch->id)
            ->with('success', 'Production batch created');
    }

    public function show($id)
    {
        $batch = Production::with(['productionMaterials.rawBatch', 'productionMaterials.material'])->findOrFail($id);
        $rawBatches = RawMaterialBatches::where('status', 'in_use')->get();
        return view('pages.staff.production.show', compact('batch', 'rawBatches'));
    }

    public function update(Request $request, $id)
    {
        $batch = Production::findOrFail($id);

        $data = $request->validate([
            'production_date' => 'nullable|date',
            'shift' => 'nullable|string',
            'quantity_carton' => 'nullable|integer',
            'notes' => 'nullable|string',
            'status' => 'nullable|string',
        ]);

        $batch->update($data);

        return back()->with('success', 'Batch updated');
    }

    public function destroy($id)
    {
        $batch = Production::findOrFail($id);
        $batch->delete();
        return redirect()->route('production.index')->with('success', 'Production batch removed');
    }

    // add material usage to a production batch -> create production_material + stock movement (raw out)
    public function addMaterial(Request $request, $id)
    {
        $batch = Production::findOrFail($id);

        $data = $request->validate([
            'raw_batch_id' => 'required|exists:raw_material_batches,id',
            'material_id' => 'required|exists:materials,id',
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'quantity_used' => 'required|numeric|min:0.0001',
            'unit' => 'nullable|string',
        ]);

        $warehouseId = $data['warehouse_id'] ?? Warehouse::where('type', 'RawMaterial')->value('id') ?? Warehouse::first()->id ?? null;
        if (!$warehouseId) {
            return back()->withErrors(['warehouse' => 'No warehouse found. Please create a warehouse first.']);
        }

        DB::beginTransaction();
        try {
            $raw = RawMaterialBatches::findOrFail($data['raw_batch_id']);

            $outQty = StockMovement::where('raw_batch_id', $raw->id)
                ->whereIn('type', ['out', 'transfer_out'])
                ->sum('quantity');

            $inQty = StockMovement::where('raw_batch_id', $raw->id)
                ->whereIn('type', ['in', 'transfer_in'])
                ->sum('quantity');

            // treat original received qty as base inbound
            $available = ($raw->quantity + $inQty) - $outQty;

            if ($available < $data['quantity_used']) {
                DB::rollBack();
                return back()->withErrors(['quantity_used' => "Insufficient quantity in raw batch (available: $available)"]);
            }

            ProductionMaterial::create([
                'production_id' => $batch->id,
                'raw_batch_id'  => $raw->id,
                'material_id'   => $data['material_id'],
                'quantity_used' => $data['quantity_used'],
                'unit'          => $data['unit'] ?? $raw->unit,
            ]);

            StockMovement::create([
                'material_id' => $data['material_id'],
                'raw_batch_id' => $raw->id,
                'warehouse_id' => $warehouseId,
                'type' => 'out',
                'quantity' => $data['quantity_used'],
                'unit' => $data['unit'] ?? $raw->unit,
                'related_production_id' => $batch->id,
                'created_by' => Auth::id(),
                'note' => "Consumption for production {$batch->production_code}",
            ]);

            DB::commit();
            return back()->with('success', 'Material added to production and stock adjusted');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    // start production
    public function start($id)
    {
        $batch = Production::findOrFail($id);
        if ($batch->status !== 'planned') {
            return back()->withErrors(['status' => 'Batch cannot be started from current status']);
        }
        $batch->update([
            'status' => 'in_progress',
            'started_by' => Auth::id(),
        ]);
        return back()->with('success', 'Production started');
    }

    // complete production -> set status qc_pending
    public function complete($id)
    {
        $batch = Production::findOrFail($id);
        if (!in_array($batch->status, ['in_progress', 'planned'])) {
            return back()->withErrors(['status' => 'Batch cannot be completed from current status']);
        }

        $batch->update([
            'status' => 'qc_pending',
            'completed_by' => Auth::id(),
            'completed_at' => now(),
        ]);

        return redirect()->route('production.show', $batch->id)->with('success', 'Production completed — awaiting QC');
    }
}
