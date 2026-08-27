<?php

namespace App\Models;

use App\Models\Concerns\HasCuid;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    use HasCuid;

    protected $table = 'purchase_orders';

    protected $fillable = [
        'nomor_po',
        'supplier_id',
        'tanggal',
        'status',
        'total',
        'catatan',
        'user_id',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'total' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function supplier()
    {
        return $this->belongsTo(ModelSupliers::class, 'supplier_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(ModelPurchaseOrdersItems::class);
    }
}
