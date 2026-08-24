<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class PurchaseOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::user()?->role === 'admin'
            || Auth::user()?->role === 'superadmin';
    }

    public function rules(): array
    {
        return [
            'nomor_po'    => 'required|string|max:25|unique:purchase_orders,nomor_po,' . $this->route('id'),
            'supplier_id' => 'required|string|exists:suppliers,id',
            'tanggal'     => 'required|date',
            'status'      => 'sometimes|string|in:draft,dipesan,diterima_sebagian,diterima,dibatalkan',
            'total'       => 'sometimes|numeric|min:0',
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
            'total.numeric'     => 'Total harus berupa angka.',
            'total.min'         => 'Total minimal 0.',
            'catatan.string'    => 'Catatan harus berupa teks.',
        ];
    }

    public function prepareForValidation(): void
    {
        $this->merge([
            'nomor_po' => trim($this->nomor_po ?? ''),
            'catatan'  => trim($this->catatan ?? ''),
        ]);
    }
}
