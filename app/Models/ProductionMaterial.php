<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductionMaterial extends Model
{
    protected $table = 'production_materials';

    protected $fillable = [
        'production_id','raw_batch_id','material_id','quantity_used','unit'
    ];

    public function production()
    {
        return $this->belongsTo(Production::class, 'production_id');
    }

    public function rawBatch()
    {
        return $this->belongsTo(RawMaterialBatches::class, 'raw_batch_id');
    }

    public function material()
    {
        return $this->belongsTo(Material::class, 'material_id');
    }
}
