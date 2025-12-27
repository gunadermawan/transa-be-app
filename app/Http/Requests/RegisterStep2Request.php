<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterStep2Request extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'business_name' => ['required', 'string', 'max:255'],
            'business_phone' => ['nullable', 'string', 'max:20'],
            'business_email' => ['nullable', 'email', 'max:255'],
            'address' => ['required', 'string', 'max:500'],
            'outlet_name' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'business_name.required' => 'Nama bisnis wajib diisi.',
            'business_name.max' => 'Nama bisnis tidak boleh lebih dari 255 karakter.',
            'business_phone.max' => 'Nomor telepon bisnis tidak boleh lebih dari 20 karakter.',
            'business_email.email' => 'Format email bisnis tidak valid.',
            'business_email.max' => 'Email bisnis tidak boleh lebih dari 255 karakter.',
            'address.required' => 'Alamat bisnis wajib diisi.',
            'address.max' => 'Alamat tidak boleh lebih dari 500 karakter.',
            'outlet_name.max' => 'Nama outlet tidak boleh lebih dari 255 karakter.',
        ];
    }
}
