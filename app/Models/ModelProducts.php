<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModelProducts extends Model
{
    protected $table = 'products';



    protected $fillable = [
        'id',
        'nama_produk',
        'harga',
        'stok',
        'kategori_id',
        'supplier_id',
    ];

    protected $casts = [
        'harga' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    protected $hidden = [
        'deleted_at',
    ];


}
