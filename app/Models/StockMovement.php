<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    protected $table = 'stock_movements';

    protected $fillable = [
        'material_id',
        'raw_batch_id',
        'warehouse_id',
        'type',
        'quantity',
        'unit',
        'related_production_id',
        'created_by',
        'note'
    ];

    public function material()
    {
        return $this->belongsTo(Material::class, 'material_id');
    }

    public function rawBatch()
    {
        return $this->belongsTo(RawMaterialBatches::class, 'raw_batch_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    public function production()
    {
        return $this->belongsTo(Production::class, 'related_production_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
