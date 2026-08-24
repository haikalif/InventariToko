<?php

namespace App\Models;

use App\Models\Concerns\HasCuid;
use Illuminate\Database\Eloquent\Model;

class ModelSales extends Model
{
    use HasCuid;

    protected $table = 'sales';

    protected $fillable = [
        'nomor_transaksi',
        'tanggal',
        'user_id',
        'status',
        'metode_pembayaran',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'total' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function salesItems()
    {
        return $this->hasMany(ModelSalesItems::class, 'sale_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
