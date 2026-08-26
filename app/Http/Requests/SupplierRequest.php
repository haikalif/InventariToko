<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class SupplierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::user()?->role === 'admin'
            || Auth::user()?->role === 'superadmin';
    }

    public function rules(): array
    {
        return [
            'nama_supplier' => 'required|string|max:255',
            'kontak'        => 'sometimes|string|max:50',
            'email'         => 'sometimes|email|max:255|unique:suppliers,email,' . $this->route('id'),
            'alamat'        => 'sometimes|string',
        ];
    }

    public function messages(): array
    {
        return [
            'nama_supplier.required' => 'Nama supplier harus diisi.',
            'nama_supplier.max'      => 'Nama supplier maksimal 255 karakter.',
            'kontak.max'             => 'Kontak maksimal 50 karakter.',
            'email.email'            => 'Format email tidak valid.',
            'email.unique'           => 'Email sudah digunakan.',
            'alamat.string'          => 'Alamat harus berupa teks.',
        ];
    }

    public function prepareForValidation(): void
    {
        $this->merge([
            'nama_supplier' => trim($this->nama_supplier),
            'email'         => trim($this->email),
            'kontak'        => trim($this->kontak),
        ]);
    }
}
