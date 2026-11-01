<?php

namespace App\Http\Requests\Logbook;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreLogbookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'projek_id' => ['nullable', 'exists:projeks,id'],
            'date' => ['required', 'date'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'progress_percentage' => ['required', 'integer', 'between:0,100'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Judul aktivitas logbook wajib diisi.',
            'description.required' => 'Deskripsi pekerjaan wajib diisi.',
            'progress_percentage.required' => 'Persentase progres wajib ditentukan.',
        ];
    }
}
