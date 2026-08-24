<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::user()?->role === 'admin'
            || Auth::user()?->role === 'superadmin';
    }

    public function rules(): array
    {
        return [
            'sku'          => 'required|string|max:255|unique:products,sku,' . $this->route('id'),
            'nama_produk'  => 'required|string|max:255',
            'category_id'  => 'sometimes|nullable|exists:categories,id',
            'supplier_id'  => 'sometimes|nullable|exists:suppliers,id',
            'deskripsi'    => 'sometimes|nullable|string',
            'satuan'       => 'sometimes|string|max:50',
            'harga_beli'   => 'sometimes|numeric|min:0',
            'harga_jual'   => 'sometimes|numeric|min:0',
            'stok'         => 'sometimes|integer|min:0',
            'stok_minimum' => 'sometimes|integer|min:0',
            'barcode'      => 'sometimes|nullable|string|max:255',
            'gambar'       => 'sometimes|nullable|string|max:255',
            'aktif'        => 'sometimes|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'sku.required'          => 'SKU harus diisi.',
            'sku.max'               => 'SKU maksimal 255 karakter.',
            'sku.unique'            => 'SKU sudah digunakan.',
            'nama_produk.required'  => 'Nama produk harus diisi.',
            'nama_produk.max'       => 'Nama produk maksimal 255 karakter.',
            'category_id.exists'    => 'Kategori tidak ditemukan.',
            'supplier_id.exists'    => 'Supplier tidak ditemukan.',
            'deskripsi.string'      => 'Deskripsi harus berupa teks.',
            'satuan.max'            => 'Satuan maksimal 50 karakter.',
            'harga_beli.numeric'    => 'Harga beli harus berupa angka.',
            'harga_beli.min'        => 'Harga beli minimal 0.',
            'harga_jual.numeric'    => 'Harga jual harus berupa angka.',
            'harga_jual.min'        => 'Harga jual minimal 0.',
            'stok.integer'          => 'Stok harus berupa bilangan bulat.',
            'stok.min'              => 'Stok minimal 0.',
            'stok_minimum.integer'  => 'Stok minimum harus berupa bilangan bulat.',
            'stok_minimum.min'      => 'Stok minimum minimal 0.',
            'barcode.max'           => 'Barcode maksimal 255 karakter.',
            'gambar.max'            => 'Nama gambar maksimal 255 karakter.',
            'aktif.boolean'         => 'Status aktif harus true atau false.',
        ];
    }

    public function prepareForValidation(): void
    {
        $this->merge([
            'sku'         => trim($this->sku),
            'nama_produk' => trim($this->nama_produk),
            'satuan'      => trim($this->satuan),
            'barcode'     => trim($this->barcode),
        ]);
    }
}
