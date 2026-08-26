<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class SaleItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::user()?->role === 'admin'
            || Auth::user()?->role === 'superadmin';
    }

    public function rules(): array
    {
        return [
            'sale_id'      => 'required|string|exists:sales,id',
            'product_id'   => 'required|string|exists:products,id',
            'jumlah'       => 'required|integer|min:1',
            'harga_satuan' => 'required|numeric|min:0',
            'subtotal'     => 'sometimes|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'sale_id.required'      => 'Transaksi penjualan harus dipilih.',
            'sale_id.exists'        => 'Transaksi penjualan tidak ditemukan.',
            'product_id.required'   => 'Produk harus dipilih.',
            'product_id.exists'     => 'Produk tidak ditemukan.',
            'jumlah.required'       => 'Jumlah harus diisi.',
            'jumlah.integer'        => 'Jumlah harus berupa bilangan bulat.',
            'jumlah.min'            => 'Jumlah minimal 1.',
            'harga_satuan.required' => 'Harga satuan harus diisi.',
            'harga_satuan.numeric'  => 'Harga satuan harus berupa angka.',
            'harga_satuan.min'      => 'Harga satuan minimal 0.',
            'subtotal.numeric'      => 'Subtotal harus berupa angka.',
            'subtotal.min'          => 'Subtotal minimal 0.',
        ];
    }

    public function prepareForValidation(): void
    {
        $this->merge([
            'sale_id'    => trim($this->sale_id ?? ''),
            'product_id' => trim($this->product_id ?? ''),
        ]);
    }
}
