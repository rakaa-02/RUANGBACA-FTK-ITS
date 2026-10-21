<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    protected $table = 'admin';
    protected $primaryKey = 'id_admin';

    const UPDATED_AT = null; // tabel lama hanya punya created_at

    protected $fillable = ['username', 'password', 'nama_lengkap', 'email'];
    protected $hidden = ['password'];
}
