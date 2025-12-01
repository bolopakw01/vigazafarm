<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        if ($this->has('password') && $this->input('password') === '') {
            $this->merge(['password' => null]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        $passwordRules = ['nullable', 'string'];
        if ($this->user()?->peran !== 'owner') {
            $passwordRules[] = 'min:8';
        }

        $rules = [
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('vf_pengguna', 'surel')->ignore($this->user()->id)],
            'nomor_telepon' => ['nullable', 'string', 'max:30'],
            'password' => $passwordRules,
            'alamat' => ['nullable', 'string', 'max:500'],
            'profile_picture' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ];

        // Username hanya required untuk owner
        if ($this->user()->peran === 'owner') {
            $rules['username'] = ['required', 'string', 'max:255', Rule::unique('vf_pengguna', 'nama_pengguna')->ignore($this->user()->id)];
            $rules['profile_picture'] = ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'];
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nama.required' => 'Nama lengkap wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah digunakan.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan.',
            'nomor_telepon.max' => 'Nomor telepon maksimal 30 karakter.',
            'alamat.max' => 'Alamat maksimal 500 karakter.',
            'password.min' => 'Password minimal 8 karakter.',
            'profile_picture.image' => 'File harus berupa gambar.',
            'profile_picture.mimes' => 'Format gambar harus JPG, PNG, atau GIF.',
            'profile_picture.max' => 'Ukuran gambar maksimal 2MB.',
        ];
    }
}