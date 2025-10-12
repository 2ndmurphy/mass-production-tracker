<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = ['name', 'contact', 'address'];

    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }

    public function rawMaterialBatches()
    {
        return $this->hasMany(RawMaterialBatches::class);
    }
}
