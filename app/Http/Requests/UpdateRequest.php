<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $currentUser = auth()->user();
        $targetUser = \App\Models\User::findOrFail($this->route('user'));


        if ($currentUser->role === 'superadmin') return true;

        if ($targetUser->role === 'superadmin') return false;

        return $currentUser->role === 'admin';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $this->route('user'),
            'password' => 'sometimes|string|min:8|confirmed',
            'role' => 'sometimes|string|in:admin,kasir,staff',
        ];
    }
}
