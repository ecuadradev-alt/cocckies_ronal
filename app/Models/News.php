<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    use HasFactory;

    protected $fillable = [
        'titulo',
        'descripcion',
        'url',
        'fecha_publicacion',
    ];

    protected $casts = [
        'fecha_publicacion' => 'datetime',
    ];
}
