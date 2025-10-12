<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'department_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relasi
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class, 'created_by');
    }

    public function rawMaterialBatches()
    {
        return $this->hasMany(RawMaterialBatches::class, 'received_by');
    }

    public function qcInspections()
    {
        return $this->hasMany(QCInspection::class, 'inspector_id');
    }

    public function startedProductions()
    {
        return $this->hasMany(Production::class, 'started_by');
    }

    public function completedProductions()
    {
        return $this->hasMany(Production::class, 'completed_by');
    }

    public function finishedGoodsAdded()
    {
        return $this->hasMany(FinishedGoodsStock::class, 'added_by');
    }

    // Helper
    public function isRole($roleName)
    {
        return strtolower($this->role->name) === strtolower($roleName);
    }

    public function isDepartment($deptName)
    {
        return strtolower($this->department->name) === strtolower($deptName);
    }
}
