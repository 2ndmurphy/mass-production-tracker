<?php

namespace App\Http\Controllers\Staff\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\StockMovement;

class MovementController extends Controller
{
    public function index()
    {
        $movements = StockMovement::with(['material', 'warehouse', 'createdBy'])
            ->latest()
            ->paginate(50);

        // return view('warehouse.movements.index', compact('movements'));
    }
}
