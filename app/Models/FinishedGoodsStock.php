<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinishedGoodsStock extends Model
{
    protected $table = 'finished_good_stocks';

    protected $fillable = [
        'production_id','warehouse_id','available_carton','entry_date','added_by'
    ];

    protected $casts = [
        'entry_date' => 'date',
    ];

    public function production()
    {
        return $this->belongsTo(Production::class, 'production_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by');
    }
}
