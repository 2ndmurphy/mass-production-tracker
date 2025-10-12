<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    protected $fillable = ['name', 'type', 'location'];

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function finishedGoodsStocks()
    {
        return $this->hasMany(FinishedGoodsStock::class);
    }
}
