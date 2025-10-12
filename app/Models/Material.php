<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $fillable = ['name', 'unit'];

    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }

    public function rawMaterialBatches()
    {
        return $this->hasMany(RawMaterialBatches::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function productionMaterials()
    {
        return $this->hasMany(ProductionMaterial::class);
    }
}
