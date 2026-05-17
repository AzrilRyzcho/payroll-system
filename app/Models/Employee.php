<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use SoftDeletes;

    protected $fillable = ['role_id', 'name', 'email', 'phone', 'address'];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function payrolls()
    {
        return $this->hasMany(Payroll::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}
