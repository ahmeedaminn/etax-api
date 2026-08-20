<?php

namespace App\Http\Requests\Post;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // PATCH validates only fields that the client actually sends.
            'title' => [
                'sometimes',
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'sometimes',
                'required',
                'string',
            ],

            'start_at' => [
                'sometimes',
                'required',
                'date',
            ],

            'end_at' => [
                'sometimes',
                'required',
                'date',
            ],

            'location' => [
                'sometimes',
                'required',
                'string',
                'max:255',
            ],

            'capacity' => [
                'sometimes',
                'nullable',
                'integer',
                'min:1',
            ],
        ];
    }
}
