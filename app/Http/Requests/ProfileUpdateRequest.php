<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [

            // Nome do utilizador
            'name' => ['required', 'string', 'max:255'],

            // Email do utilizador
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($this->user()->id),
            ],

            // O avatar é validado apenas quando existe um ficheiro,
            // no Controller, por isso não é necessário colocá-lo aqui.

        ];
    }
}
