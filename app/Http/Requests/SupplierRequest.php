<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class SupplierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return in_array(Auth::user()?->role, ['superadmin', 'admin', 'staff']);
    }

    public function rules(): array
    {
        $supplierId = $this->route('suplier') ?? $this->route('id');
        $required = $this->isMethod('POST') ? 'required' : 'sometimes|required';

        return [
            'nama_supplier' => $required . '|string|max:255',
            'kontak'        => 'sometimes|nullable|string|max:50',
            'email'         => 'sometimes|nullable|email|max:255|unique:suppliers,email,' . $supplierId,
            'alamat'        => 'sometimes|nullable|string',
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
        $merged = [];
        foreach (['nama_supplier', 'email', 'kontak'] as $field) {
            if ($this->has($field) && is_string($this->$field)) {
                $merged[$field] = trim($this->$field);
            }
        }
        if (!empty($merged)) {
            $this->merge($merged);
        }
    }
}
