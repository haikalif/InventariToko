<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class registerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $currentUser = auth()->user();

        if ($currentUser?->role === 'superadmin') return true;

        if ($currentUser?->role === 'admin') {
            return $this->input('role') !== 'superadmin';
        }

        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string|in:admin,kasir,staff',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama harus diisi.',
            'email.required' => 'Email harus diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan.',
            'password.required' => 'Password harus diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak sesuai.',
            'role.required' => 'Role harus diisi.',
            'role.in' => 'Role harus salah satu dari: admin, kasir, staff.',
        ];
    }

    public function prepareForValidation(): void
    {
        if ($this->has('name')) {
            $this->merge([
                'name' => preg_replace('/[^a-zA-Z0-9\s]/', '', trim($this->name)),
            ]);
        }

        $this->merge([

            'email' => trim($this->email),
            'role' => trim($this->role),
        ]);
    }
}
