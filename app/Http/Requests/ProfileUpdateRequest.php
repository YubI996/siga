<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
     /**
     * Tentukan apakah user boleh pakai request ini.
     */
    public function authorize(): bool
    {
        // Kalau ini dipakai untuk update profil user yang sedang login:
        return auth()->check();
    }

    /**
     * Aturan validasi untuk request ini.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],

            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                // ignore id user saat ini (UUID aman-aman saja di sini)
                Rule::unique(User::class, 'email')->ignore($this->user()->getKey()),
            ],

            // level_wilayah: rt / lurah / camat / super-admin / operator / viewer
            'level_wilayah' => [
                'required',
                'string',
                Rule::in(['super-admin', 'camat', 'lurah', 'rt', 'operator', 'viewer']),
            ],

            // Relasi wilayah (boleh null, nanti dipaksa lewat withValidator)
            'kecamatan_id' => ['nullable', 'uuid', 'exists:kecamatans,id'],
            'kelurahan_id' => ['nullable', 'uuid', 'exists:kelurahans,id'],
            'rt_id'        => ['nullable', 'uuid', 'exists:rts,id'],
        ];
    }
    /**
     * Pesan error kustom untuk setiap aturan.
     */
    public function messages(): array
    {
        return [
            // name
            'name.required' => 'Nama wajib diisi.',
            'name.string'   => 'Nama harus berupa teks.',
            'name.max'      => 'Nama tidak boleh lebih dari :max karakter.',

            // email
            'email.required'  => 'Email wajib diisi.',
            'email.string'    => 'Email harus berupa teks.',
            'email.lowercase' => 'Email akan disimpan dalam huruf kecil.',
            'email.email'     => 'Format email tidak valid.',
            'email.max'       => 'Email tidak boleh lebih dari :max karakter.',
            'email.unique'    => 'Email ini sudah digunakan.',

            // level_wilayah
            'level_wilayah.required' => 'Level wilayah wajib dipilih.',
            'level_wilayah.string'   => 'Level wilayah harus berupa teks.',
            'level_wilayah.in'       => 'Level wilayah yang dipilih tidak valid.',

            // kecamatan_id
            'kecamatan_id.uuid'   => 'Kecamatan tidak valid.',
            'kecamatan_id.exists' => 'Kecamatan yang dipilih tidak ditemukan di sistem.',

            // kelurahan_id
            'kelurahan_id.uuid'   => 'Kelurahan tidak valid.',
            'kelurahan_id.exists' => 'Kelurahan yang dipilih tidak ditemukan di sistem.',

            // rt_id
            'rt_id.uuid'   => 'RT tidak valid.',
            'rt_id.exists' => 'RT yang dipilih tidak ditemukan di sistem.',
        ];
    }

    /**
     * Nama atribut yang lebih ramah untuk ditampilkan di pesan error.
     */
    public function attributes(): array
    {
        return [
            'name'          => 'nama',
            'email'         => 'email',
            'level_wilayah' => 'level wilayah',
            'kecamatan_id'  => 'kecamatan',
            'kelurahan_id'  => 'kelurahan',
            'rt_id'         => 'RT',
        ];
    }


    /**
     * Validasi lanjutan tergantung level_wilayah.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $level = $this->input('level_wilayah');

            // Camat/Lurah/RT wajib punya kecamatan
            if (in_array($level, ['camat', 'lurah', 'rt'], true) && ! $this->filled('kecamatan_id')) {
                $validator->errors()->add('kecamatan_id', 'Kecamatan wajib diisi untuk level wilayah ini.');
            }

            // Lurah/RT wajib punya kelurahan
            if (in_array($level, ['lurah', 'rt'], true) && ! $this->filled('kelurahan_id')) {
                $validator->errors()->add('kelurahan_id', 'Kelurahan wajib diisi untuk level wilayah ini.');
            }

            // RT wajib punya rt_id
            if ($level === 'rt' && ! $this->filled('rt_id')) {
                $validator->errors()->add('rt_id', 'RT wajib diisi untuk level RT.');
            }
        });
    }
}
