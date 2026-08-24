<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseOrderResource extends JsonResource
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
            'nomor_po' => $this->nomor_po,
            'supplier_id' => $this->supplier_id,
            'tanggal' => optional($this->tanggal)->toDateString(),
            'status' => $this->status,
            'total' => $this->total,
            'catatan' => $this->catatan,
            'user_id' => $this->user_id,
            'created_at' => optional($this->created_at)->toDateTimeString(),
            'updated_at' => optional($this->updated_at)->toDateTimeString(),
        ];
    }
}
