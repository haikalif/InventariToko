<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'sku' => $this->sku,
            'nama_produk' => $this->nama_produk,
            'category_id' => $this->category_id,
            'supplier_id' => $this->supplier_id,
            'deskripsi' => $this->deskripsi,
            'satuan' => $this->satuan,
            'harga_beli' => $this->harga_beli,
            'harga_jual' => $this->harga_jual,
            'stok' => $this->stok,
            'stok_minimum' => $this->stok_minimum,
            'barcode' => $this->barcode,
            'gambar' => $this->gambar,
            'aktif' => $this->aktif,
            'created_at' => optional($this->created_at)->toDateTimeString(),
            'updated_at' => optional($this->updated_at)->toDateTimeString(),
        ];
    }
}
