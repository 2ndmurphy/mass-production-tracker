<?php

namespace App\Http\Controllers\Staff\Production;

use App\Http\Controllers\Controller;
use App\Models\Production;
use App\Models\ProductionMaterial;
use App\Models\Material;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductionController extends Controller
{
    /**
     * Display all production records (planned, in progress, completed).
     */
    public function index()
    {
        $productions = Production::with(['startedBy'])
            ->latest()
            ->get();

        return view('pages.staff.production.index', compact('productions'));
    }

    /**
     * Show the form to create a new production batch.
     */
    public function create()
    {
        $materials = Material::all();

        return view('pages.staff.production.create', compact('materials'));
    }

    /**
     * Store a new production batch record.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'production_code' => 'nullable|string|max:50|unique:productions,production_code',
            'production_date' => 'required|date',
            'shift' => 'required|string|max:20',
            'quantity_carton' => 'required|numeric|min:1',
            'notes' => 'nullable|string|max:1000',
        ]);

        $data['production_code'] = Production::generateCode();
        $data['status'] = 'planned';
        $data['started_by'] = $user->id;

        Production::create($data);

        return redirect()->route('production.index')->with('success', 'Production batch created successfully.');
    }

    /**
     * Start a production batch (move from planned → in_progress).
     */
    public function start($id)
    {
        $production = Production::findOrFail($id);
        $production->update(['status' => 'in_progress']);

        return redirect()->back()->with('success', 'Production started successfully.');
    }

    /**
     * Show details of an active or completed production batch.
     */
    public function show($id)
    {
        $production = Production::with(['productionMaterials.material', 'startedBy'])->findOrFail($id);
        $materials = Material::all();
        return view('pages.staff.production.show', compact('production', 'materials'));
    }

    /**
     * Update progress or complete production.
     */
    public function updateStatus(Request $request, $id)
    {
        $production = Production::findOrFail($id);

        $data = $request->validate([
            'status' => 'required|in:in_progress,completed,qc_pending',
            'notes' => 'nullable|string|max:1000',
        ]);

        $production->update($data);

        return redirect()->route('production.index')->with('success', 'Production status updated successfully.');
    }

    /**
     * Complete a production batch (mark as ready for QC).
     */
    public function complete($id)
    {
        $production = Production::findOrFail($id);
        $production->update(['status' => 'qc_pending']);

        return redirect()->route('production.index')->with('success', 'Production marked as ready for QC.');
    }

    /**
     * Record materials used for a given production batch.
     */
    public function recordMaterials(Request $request, $id)
    {
        $production = Production::findOrFail($id);

        $validated = $request->validate([
            'materials' => 'required|array',
            'materials.*.material_id' => 'required|exists:materials,id',
            'materials.*.quantity_used' => 'required|numeric|min:0.1',
            'materials.*.unit' => 'required|string|max:10',
        ]);

        foreach ($validated['materials'] as $item) {
            ProductionMaterial::create([
                'production_id' => $production->id,
                'material_id' => $item['material_id'],
                'quantity_used' => $item['quantity_used'],
                'unit' => $item['unit'],
            ]);
        }



        return redirect()->route('production.show', $production->id)->with('success', 'Materials recorded successfully.');
    }
}
