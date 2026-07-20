<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PerjalananRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pegawai_id'      => 'required|exists:pegawais,id',
            'kendaraan_id'    => 'required|exists:kendaraans,id',
            'tanggal'         => 'required|date',
            'tujuan'          => 'required|string|max:255',
            'uraian'          => 'nullable|string|max:255',
            'km_lama'         => 'required|numeric|min:0',
            'km_baru'         => 'required|numeric|gt:km_lama',
            'jumlah_biaya'    => 'required|numeric|min:1000',
            'harga_per_liter' => 'required|numeric|min:1',
            'no_bon'          => 'nullable|string|max:100',
            'foto_bon'        => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ];
    }
}
