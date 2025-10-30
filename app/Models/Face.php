<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Face extends Model
{
    use HasFactory;
    protected $table = 'faces';
    protected $primaryKey = 'face_id';
    protected $fillable = ['user_id', 'embedding', 'image_url', 'captured_at'];

    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
