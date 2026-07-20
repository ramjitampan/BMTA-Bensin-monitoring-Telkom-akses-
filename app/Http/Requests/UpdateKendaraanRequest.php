<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateKendaraanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('kendaraan');

        return [
            'plat_nomor' => 'required|string|unique:kendaraans,plat_nomor,' . $id,
            'merk'       => 'required|string|max:255',
            'jenis'      => 'required|in:R4',
            'tahun'      => 'required|digits:4|integer|min:1900|max:' . date('Y'),
        ];
    }
}
