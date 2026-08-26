<?php

namespace App\Models;

use App\Models\Concerns\HasCuid;
use Illuminate\Database\Eloquent\Model;

class ModelStockMovements extends Model
{
    use HasCuid;

    protected $table = 'stock_movements';

    protected $fillable = [
        'product_id',
        'tipe',
        'jumlah',
        'stok_sebelum',
        'stok_sesudah',
        'referensi',
        'user_id',
        'jenis',
        'keterangan',
    ];

    protected $casts = [
        'jumlah' => 'integer',
        'stok_sebelum' => 'integer',
        'stok_sesudah' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
