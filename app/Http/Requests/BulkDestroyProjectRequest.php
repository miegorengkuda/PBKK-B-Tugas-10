<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BulkDestroyProjectRequest extends FormRequest
{
    /**
     * Tentukan apakah user memiliki izin untuk bulk destroy.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Aturan validasi untuk penghapusan massal proyek.
     */
    public function rules(): array
    {
        return [
            'ids'   => ['required', 'array', 'min:1'],
            'ids.*' => ['required', 'integer', 'exists:projects,id'],
        ];
    }

    /**
     * Pesan error validasi kustom.
     */
    public function messages(): array
    {
        return [
            'ids.required' => 'Pilih minimal satu proyek melalui checkbox untuk dihapus.',
            'ids.min'      => 'Pilih minimal satu proyek melalui checkbox untuk dihapus.',
            'ids.*.exists' => 'Salah satu ID proyek yang dipilih tidak valid atau tidak ditemukan.',
        ];
    }
}
