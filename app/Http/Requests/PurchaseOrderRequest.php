<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class PurchaseOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::user()?->role === 'admin'
            || Auth::user()?->role === 'superadmin'
            || Auth::user()?->role === 'staff';
    }

    public function rules(): array
    {
        $poId = $this->route('purchase_order') ?? $this->route('id');
        $required = $this->isMethod('POST') ? 'required' : 'sometimes|required';

        return [
            'nomor_po'    => $required . '|string|max:25|unique:purchase_orders,nomor_po,' . $poId,
            'supplier_id' => $required . '|string|exists:suppliers,id',
            'tanggal'     => $required . '|date',
            'status'      => 'sometimes|string|in:draft,dipesan,diterima_sebagian,diterima,dibatalkan',
            'catatan'     => 'sometimes|nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'nomor_po.required' => 'Nomor PO harus diisi.',
            'nomor_po.max'      => 'Nomor PO maksimal 25 karakter.',
            'nomor_po.unique'   => 'Nomor PO sudah digunakan.',
            'supplier_id.required' => 'Supplier harus dipilih.',
            'supplier_id.exists'   => 'Supplier tidak ditemukan.',
            'tanggal.required'  => 'Tanggal harus diisi.',
            'tanggal.date'      => 'Tanggal harus berupa tanggal yang valid.',
            'status.in'         => 'Status harus salah satu dari: draft, dipesan, diterima_sebagian, diterima, dibatalkan.',
            'catatan.string'    => 'Catatan harus berupa teks.',
        ];
    }

    public function prepareForValidation(): void
    {
        $merged = [];
        foreach (['nomor_po', 'catatan'] as $field) {
            if ($this->has($field) && is_string($this->$field)) {
                $merged[$field] = trim($this->$field);
            }
        }
        if (!empty($merged)) {
            $this->merge($merged);
        }
    }
}
