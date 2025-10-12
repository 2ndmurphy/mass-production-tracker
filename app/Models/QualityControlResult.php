<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QualityControlResult extends Model
{
    protected $fillable = [
        'production_id',
        'qc_by',
        'sample_count',
        'status',
        'defect_type',
        'action_taken',
        'checked_at'
    ];

    public function production()
    {
        return $this->belongsTo(Production::class, 'production_id');
    }

    public function qcBy()
    {
        return $this->belongsTo(User::class, 'qc_by');
    }
}
