<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QCInspection extends Model
{
    protected $table = 'q_c_inspections';

    protected $fillable = [
        'production_batch_id',
        'inspector_id',
        'result',
        'sample_count',
        'defect_type',
        'notes',
        'checked_at',
    ];

    protected $casts = [
        'checked_at' => 'datetime',
    ];

    // Relations
    public function productionBatch()
    {
        return $this->belongsTo(Production::class, 'production_batch_id');
    }

    public function inspector()
    {
        return $this->belongsTo(User::class, 'inspector_id');
    }
}
