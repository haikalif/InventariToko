<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class PurchaseOrderItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return in_array(Auth::user()?->role, ['superadmin', 'admin', 'staff']);
    }

    public function rules(): array
    {
        $required = $this->isMethod('POST') ? 'required' : 'sometimes|required';

        return [
            'product_id'        => $required . '|string|exists:products,id',
            'jumlah'            => $required . '|integer|min:1',
            'jumlah_diterima'   => 'sometimes|integer|min:0',
            'harga_satuan'      => $required . '|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
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
        ];
    }

    public function prepareForValidation(): void
    {
        $merged = [];
        foreach (['purchase_order_id', 'product_id'] as $field) {
            if ($this->has($field) && is_string($this->$field)) {
                $merged[$field] = trim($this->$field);
            }
        }
        if (!empty($merged)) {
            $this->merge($merged);
        }
    }
}
