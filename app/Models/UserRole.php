<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserRole extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'name',
    ];

    public function getRoles()
    {
        return UserRole::where('name', '!=', 'администратор')->orderBy('id', 'ASC')->get()->toArray();
    }

    public function users()
    {
        return $this->hasMany(User::class, 'role_id', 'id');
    }
}
