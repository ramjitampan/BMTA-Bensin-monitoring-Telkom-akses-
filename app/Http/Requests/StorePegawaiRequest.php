<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePegawaiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama'    => 'required|string|max:255',
            'jabatan' => 'nullable|string|max:255',
            'divisi'  => 'nullable|string|max:255',
            'no_hp'   => 'nullable|string|max:20',
        ];
    }
}
