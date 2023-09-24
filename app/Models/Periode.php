<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Periode extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'department_id',
        'cabinet_id',
        'role_id',
        'is_active',        
    ];

    public function cabinet()
    {
        return $this->belongsTo(Cabinet::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withDefault([
            'name' => 'Deleted User',
        ]);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
