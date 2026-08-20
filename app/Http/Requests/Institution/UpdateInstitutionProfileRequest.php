<?php

namespace App\Http\Requests\Institution;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInstitutionProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'organization_name' => [
                'sometimes',
                'string',
                'max:255',
            ],

            'description' => [
                'sometimes',
                'string',
            ],

            'website' => [
                'sometimes',
                'nullable',
                'url',
                'max:255',
            ],

            'contact_email' => [
                'sometimes',
                'nullable',
                'email',
                'max:255',
            ],

            'contact_phone' => [
                'sometimes',
                'nullable',
                'string',
                'max:30',
            ],

            'location' => [
                'sometimes',
                'nullable',
                'string',
                'max:255',
            ],
        ];
    }
}
