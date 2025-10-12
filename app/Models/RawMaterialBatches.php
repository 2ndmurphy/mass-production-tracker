<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RawMaterialBatches extends Model
{
    protected $table = 'raw_material_batches';

    protected $fillable = [
        'batch_code',
        'supplier_id',
        'material_id',
        'received_date',
        'quantity',
        'unit',
        'status',
        'received_by',
        'notes'
    ];

    protected $casts = [
        'received_date' => 'date',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function material()
    {
        return $this->belongsTo(Material::class, 'material_id');
    }

    public function productionMaterials()
    {
        return $this->hasMany(ProductionMaterial::class, 'raw_batch_id');
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class, 'raw_batch_id');
    }

    public function receivedBy()
    {
        return $this->belongsTo(User::class, 'received_by');
    }
}
