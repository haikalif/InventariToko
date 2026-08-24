<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class PurchaseOrderItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::user()?->role === 'admin'
            || Auth::user()?->role === 'superadmin';
    }

    public function rules(): array
    {
        return [
            'purchase_order_id' => 'required|string|exists:purchase_orders,id',
            'product_id'        => 'required|string|exists:products,id',
            'jumlah'            => 'required|integer|min:1',
            'jumlah_diterima'   => 'sometimes|integer|min:0',
            'harga_satuan'      => 'required|numeric|min:0',
            'subtotal'          => 'sometimes|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'purchase_order_id.required' => 'Purchase order harus dipilih.',
            'purchase_order_id.exists'   => 'Purchase order tidak ditemukan.',
            'product_id.required'        => 'Produk harus dipilih.',
            'product_id.exists'          => 'Produk tidak ditemukan.',
            'jumlah.required'            => 'Jumlah harus diisi.',
            'jumlah.integer'             => 'Jumlah harus berupa bilangan bulat.',
            'jumlah.min'                 => 'Jumlah minimal 1.',
            'jumlah_diterima.integer'    => 'Jumlah diterima harus berupa bilangan bulat.',
            'jumlah_diterima.min'        => 'Jumlah diterima minimal 0.',
            'harga_satuan.required'      => 'Harga satuan harus diisi.',
            'harga_satuan.numeric'       => 'Harga satuan harus berupa angka.',
            'harga_satuan.min'           => 'Harga satuan minimal 0.',
            'subtotal.numeric'           => 'Subtotal harus berupa angka.',
            'subtotal.min'               => 'Subtotal minimal 0.',
        ];
    }

    public function prepareForValidation(): void
    {
        $this->merge([
            'purchase_order_id' => trim($this->purchase_order_id ?? ''),
            'product_id'        => trim($this->product_id ?? ''),
        ]);
    }
}
