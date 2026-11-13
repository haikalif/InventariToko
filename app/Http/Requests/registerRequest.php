<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class registerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Siapapun boleh mendaftar (role otomatis di-set ke role terendah di Controller)
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'     => 'required|string|max:255|unique:users,name',
            'email'    => 'required|string|email|max:255|unique:users,email',
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'different:name',
                'different:email',
                'regex:/^(?=.*[a-zA-Z])(?=.*[0-9]).+$/',
            ],
            'avatar'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'      => 'Nama lengkap wajib diisi.',
            'name.max'           => 'Nama maksimal 255 karakter.',
            'name.unique'        => 'Nama ini sudah terdaftar di database, silakan gunakan nama lain.',
            'email.required'     => 'Alamat email wajib diisi.',
            'email.email'        => 'Format alamat email tidak valid.',
            'email.unique'       => 'Email sudah terpakai oleh akun lain.',
            'password.required'  => 'Password wajib diisi.',
            'password.min'       => 'Password wajib minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.different' => 'Password harus unik dan tidak boleh sama dengan nama atau email.',
            'password.regex'     => 'Password harus unik (wajib kombinasi huruf dan angka).',
            'avatar.image'       => 'File foto profil harus berupa gambar.',
            'avatar.mimes'       => 'Format foto profil harus JPG, JPEG, PNG, atau WEBP.',
            'avatar.max'         => 'Ukuran foto profil maksimal 2 MB.',
        ];
    }

    public function prepareForValidation(): void
    {
        if ($this->has('name') && is_string($this->name)) {
            $this->merge([
                'name' => preg_replace('/\s+/', ' ', trim($this->name)),
            ]);
        }

        if ($this->has('email') && is_string($this->email)) {
            $this->merge([
                'email' => strtolower(trim($this->email)),
            ]);
        }
    }
}
