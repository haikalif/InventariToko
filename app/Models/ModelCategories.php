<?php

namespace App\Models;

use App\Models\Concerns\HasCuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasCuid, SoftDeletes;

    protected $table = 'categories';

    protected $fillable = [
        'nama_kategori',
        'deskripsi',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $hidden = [
        'deleted_at',
    ];
}
