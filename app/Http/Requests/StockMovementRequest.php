<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StockMovementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::user()?->role === 'admin'
            || Auth::user()?->role === 'superadmin';
    }

    public function rules(): array
    {
        return [
            'product_id' => 'required|string|exists:products,id',
            'tipe'       => 'required|string|in:masuk,keluar,retur,penyesuaian',
            'jumlah'     => 'required|integer|min:1',
            'referensi'  => 'sometimes|nullable|string|max:255',
            'jenis'      => 'sometimes|nullable|string|max:50',
            'keterangan' => 'sometimes|nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.required' => 'Produk harus dipilih.',
            'product_id.exists'   => 'Produk tidak ditemukan.',
            'tipe.required'       => 'Tipe pergerakan stok harus diisi.',
            'tipe.in'             => 'Tipe harus salah satu dari: masuk, keluar, retur, penyesuaian.',
            'jumlah.required'     => 'Jumlah harus diisi.',
            'jumlah.integer'      => 'Jumlah harus berupa bilangan bulat.',
            'jumlah.min'          => 'Jumlah minimal 1.',
            'referensi.max'       => 'Referensi maksimal 255 karakter.',
            'jenis.max'           => 'Jenis maksimal 50 karakter.',
            'keterangan.string'   => 'Keterangan harus berupa teks.',
        ];
    }

    public function prepareForValidation(): void
    {
        $this->merge([
            'tipe'      => trim($this->tipe ?? ''),
            'referensi' => trim($this->referensi ?? ''),
        ]);
    }
}
