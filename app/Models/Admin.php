<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Admin extends Authenticatable
{
    use HasApiTokens, HasFactory;

    protected $table = 'admin';
    protected $primaryKey = 'maAdmin';
    public $timestamps = true;

    protected $fillable = ['hoTen', 'email', 'matKhau', 'soDienThoai'];

    protected $hidden = ['matKhau'];

    public function phongDaoTao()
    {
        return $this->hasMany(PhongDaoTao::class, 'maAdmin', 'maAdmin');
    }
}
