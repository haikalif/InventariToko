<?php

namespace App\Models;

use App\Models\Concerns\HasCuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ModelProducts extends Model
{
    protected $table = 'products';

    use SoftDeletes, HasCuid;

    protected $fillable = [
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
