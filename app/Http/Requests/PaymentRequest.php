<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
           'stripeToken'=>'required|string',
            'amount'=>'required|numeric|regex:/^\d+(\.\d{1,2})?$/',
        ];
    }
    public function messages(): array{
        return [
            'amount.regex'=>'The amount must be an integer or a decimal with up to two decimal places',
        ];
    }
}
