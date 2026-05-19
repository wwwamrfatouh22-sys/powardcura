<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Staff extends Authenticatable
{
    use Notifiable, SoftDeletes;

    public const ROLES = [
        'receptionist' => 'Receptionist',
        'nurse' => 'Nurse',
        'accountant' => 'Accountant',
        'support' => 'Support',
        'admin_assistant' => 'Admin Assistant',
        'lab' => 'Laboratory',
        'radiology' => 'Radiology',
        'laboratory' => 'Laboratory',
        'radiology_lab' => 'Radiology & Laboratory',
    ];

    public const STATUSES = [
        'active' => 'Active',
        'inactive' => 'Inactive',
    ];

    protected $table = 'staff';

    protected $fillable = [
        'name',
        'full_name',
        'email',
        'phone',
        'role',
        'department_id',
        'password',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'api_token',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function displayName(): string
    {
        return $this->full_name ?: $this->name ?: 'Staff Member';
    }
}
