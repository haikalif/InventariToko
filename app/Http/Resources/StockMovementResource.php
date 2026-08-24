<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StockMovementResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'product_id'    => $this->product_id,
            'tipe'          => $this->tipe,
            'jumlah'        => $this->jumlah,
            'stok_sebelum'  => $this->stok_sebelum,
            'stok_sesudah'  => $this->stok_sesudah,
            'referensi'     => $this->referensi,
            'user_id'       => $this->user_id,
            'jenis'         => $this->jenis,
            'keterangan'    => $this->keterangan,
            'created_at'    => optional($this->created_at)->toDateTimeString(),
            'updated_at'    => optional($this->updated_at)->toDateTimeString(),
        ];
    }
}
