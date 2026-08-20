<?php

namespace App\Models;

use App\Models\Concerns\HasCuid;
use Illuminate\Database\Eloquent\Model;

class ModelSalesItems extends Model

{
    use HasCuid;

    protected $table = 'sales_items';

    protected $fillable = [
        'sales_id',
        'product_id',
        'jumlah',
    ];

    protected $casts = [
        'harga_satuan' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function sales()
    {
        return $this->belongsTo(ModelSales::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
