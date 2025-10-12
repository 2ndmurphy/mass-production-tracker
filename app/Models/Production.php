<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Production extends Model
{
    protected $table = 'productions';

    protected $fillable = [
        'production_code',
        'production_date',
        'shift',
        'quantity_carton',
        'status',
        'started_by',
        'completed_by',
        'completed_at',
        'notes'
    ];

    protected $casts = [
        'production_date' => 'date',
        'completed_at' => 'datetime',
    ];

    public static function generateCode()
    {
        $prefix = 'PRD-' . now()->format('Ymd') . '-';
        // Query the latest production_code for today prefix. Removing an accidental ->query() call
        // which breaks the builder chain and can cause a void/ non-object return.
        $lastCode = self::where('production_code', 'like', $prefix . '%')
            ->orderBy('production_code', 'desc')
            ->value('production_code');

        if ($lastCode) {
            $lastNumber = (int) substr($lastCode, -3);
            $nextNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $nextNumber = '001';
        }

        return $prefix . $nextNumber;
    }

    public function startedBy()
    {
        return $this->belongsTo(User::class, 'started_by');
    }

    public function completedBy()
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    public function productionMaterials()
    {
        return $this->hasMany(ProductionMaterial::class, 'production_id');
    }

    public function qcResults()
    {
        return $this->hasMany(QualityControlResult::class, 'production_id');
    }

    public function qcInspections()
    {
        return $this->hasMany(QCInspection::class, 'production_batch_id');
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class, 'related_production_id');
    }

    public function finishedGoods()
    {
        return $this->hasOne(FinishedGoodsStock::class, 'production_id');
    }
}
