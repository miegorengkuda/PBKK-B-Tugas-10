<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProjectRequest extends FormRequest
{
    /**
     * Tentukan apakah user memiliki izin untuk memperbarui proyek ini.
     * Menggunakan ProjectPolicy via $this->user()->can('update', $project).
     */
    public function authorize(): bool
    {
        $project = $this->route('project');

        return $project && $this->user() && $this->user()->can('update', $project);
    }

    /**
     * Aturan validasi untuk pembaruan proyek.
     */
    public function rules(): array
    {
        return [
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'category'    => ['nullable', 'string', 'max:100'],
            'status'      => ['required', 'in:draft,active,archived'],
            'tech_stack'  => ['nullable', 'string', 'max:500'],
            'started_at'  => ['nullable', 'date'],
        ];
    }

    /**
     * Pesan error validasi kustom.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Judul proyek wajib diisi.',
            'title.max'      => 'Judul proyek maksimal 255 karakter.',
            'status.required'=> 'Status proyek wajib dipilih.',
            'status.in'      => 'Status proyek harus berupa draft, active, atau archived.',
            'started_at.date'=> 'Tanggal mulai proyek harus berupa format tanggal yang valid.',
        ];
    }
}
