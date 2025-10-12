<?php

namespace App\Http\Controllers\Staff\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\{StockMovement, Material};
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    public function index()
    {
        $inventory = Material::query()->select('materials.id', 'materials.name', 'materials.unit',
            DB::raw("
                SUM(CASE WHEN stock_movements.type IN ('in','transfer_in') THEN stock_movements.quantity
                         WHEN stock_movements.type IN ('out','transfer_out') THEN -stock_movements.quantity
                         ELSE 0 END) as total_quantity
            ")
        )
        ->leftJoin('stock_movements', 'stock_movements.material_id', '=', 'materials.id')
        ->groupBy('materials.id', 'materials.name', 'materials.unit')
        ->get();

        return view('pages.staff.warehouse.inventory.index', compact('inventory'));
    }
}
