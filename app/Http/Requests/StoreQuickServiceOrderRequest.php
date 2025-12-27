<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuickServiceOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', 'in:DINE IN,TAKE AWAY'],
            'pax' => ['nullable', 'integer', 'min:1'],
            'table_number' => ['nullable', 'string', 'max:50'],
            'customer_name' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'type.required' => 'Order type is required',
            'type.in' => 'Order type must be either DINE IN or TAKE AWAY',
            'pax.integer' => 'Number of people must be a valid number',
            'pax.min' => 'Number of people must be at least 1',
        ];
    }
}
