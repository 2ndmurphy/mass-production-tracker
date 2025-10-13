<?php

namespace App\Http\Controllers\Staff\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\{StockMovement, RawMaterialBatches, Warehouse, Material, Supplier};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StockController extends Controller
{
    // GET: show form or data for Stock In page
    public function index()
    {
        $materials = Material::all();
        $warehouses = Warehouse::all();
        $suppliers = Supplier::all();

        return view('pages.staff.warehouse.stock.index', compact('materials', 'warehouses', 'suppliers'));
    }

    // POST: Stock In (Receive Raw Material)
    public function store(Request $request)
    {
        $data = $request->validate([
            'material_id' => 'required|exists:materials,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'batch_code' => 'nullable|string|max:50|unique:raw_material_batches,batch_code',
            'quantity' => 'required|numeric|min:0.01',
            'unit' => 'required|string|max:20',
            'notes' => 'nullable|string|max:500',
        ]);
        
        $data['batch_code'] = RawMaterialBatches::generateCode();

        // Create new batch
        $batch = RawMaterialBatches::create([
            'batch_code' => $data['batch_code'],
            'supplier_id' => $data['supplier_id'],
            'material_id' => $data['material_id'],
            'received_date' => now(),
            'quantity' => $data['quantity'],
            'unit' => $data['unit'],
            'status' => 'received',
            'received_by' => Auth::id(),
            'notes' => $data['notes'] ?? null,
        ]);

        // Log stock movement
        StockMovement::create([
            'material_id' => $data['material_id'],
            'raw_batch_id' => $batch->id,
            'warehouse_id' => $data['warehouse_id'],
            'type' => 'in',
            'quantity' => $data['quantity'],
            'unit' => $data['unit'],
            'created_by' => Auth::id(),
            'note' => 'Initial stock-in for batch ' . $batch->batch_code,
        ]);

        return redirect()->back()->with('success', 'Stock received successfully.');
    }

    // POST: Stock Out (Send to Production)
    public function out(Request $request)
    {
        $data = $request->validate([
            'raw_batch_id' => 'required|exists:raw_material_batches,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'quantity' => 'required|numeric|min:0.01',
            'unit' => 'required|string|max:20',
            'note' => 'nullable|string|max:500',
        ]);

        $batch = RawMaterialBatches::findOrFail($data['raw_batch_id']);

        // Log stock out
        StockMovement::create([
            'material_id' => $batch->material_id,
            'raw_batch_id' => $batch->id,
            'warehouse_id' => $data['warehouse_id'],
            'type' => 'out',
            'quantity' => $data['quantity'],
            'unit' => $data['unit'],
            'created_by' => Auth::id(),
            'note' => $data['note'] ?? 'Sent to production',
        ]);

        return redirect()->back()->with('success', 'Stock out recorded successfully.');
    }
}
