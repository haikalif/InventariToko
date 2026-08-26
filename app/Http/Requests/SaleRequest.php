<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class SaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nomor_transaksi'   => 'required|string|max:25|unique:sales,nomor_transaksi,' . $this->route('id'),
            'tanggal'           => 'required|date',
            'status'            => 'sometimes|string|in:pending,selesai,dibatalkan',
            'metode_pembayaran' => 'sometimes|nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'nomor_transaksi.required' => 'Nomor transaksi harus diisi.',
            'nomor_transaksi.max'      => 'Nomor transaksi maksimal 25 karakter.',
            'nomor_transaksi.unique'   => 'Nomor transaksi sudah digunakan.',
            'tanggal.required'         => 'Tanggal harus diisi.',
            'tanggal.date'             => 'Tanggal harus berupa tanggal yang valid.',
            'status.in'                => 'Status harus salah satu dari: pending, selesai, dibatalkan.',
            'metode_pembayaran.max'    => 'Metode pembayaran maksimal 255 karakter.',
        ];
    }

    public function prepareForValidation(): void
    {
        $this->merge([
            'nomor_transaksi'   => trim($this->nomor_transaksi ?? ''),
            'metode_pembayaran' => trim($this->metode_pembayaran ?? ''),
        ]);
    }
}
