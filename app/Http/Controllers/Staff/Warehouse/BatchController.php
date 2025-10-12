<?php

namespace App\Http\Controllers\Staff\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\RawMaterialBatches;

class BatchController extends Controller
{
    public function index()
    {
        $batches = RawMaterialBatches::with(['material', 'supplier', 'receivedBy'])
            ->latest()
            ->paginate(20);

        return view('pages.staff.warehouse.batches.index', compact('batches'));
    }

    public function show($id)
    {
        $batch = RawMaterialBatches::with(['material', 'supplier', 'stockMovements'])->findOrFail($id);

        return view('pages.staff.warehouse.batches.show', compact('batch'));
    }
}
