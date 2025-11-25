<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
        'current_password' => ['required', 'current_password'],
        'password' => [
            'required',
            'confirmed',
            'string',
            'min:8',
            'regex:/[A-Z]/',      
            'regex:/[0-9]/',
            'regex:/[@$!%*?&#^()_+\-=]/',
        ],
    ];
    }
}
