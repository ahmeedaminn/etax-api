<?php

namespace App\Http\Requests\Post;

use App\Enums\PostType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => [
                'required',
                Rule::enum(PostType::class),
            ],

            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'required',
                'string',
            ],

            'start_at' => [
                // Announcement payloads do not keep Event-only fields.
                'exclude_unless:type,EVENT',
                'required',
                'date',
            ],

            'end_at' => [
                'exclude_unless:type,EVENT',
                'required',
                'date',
                'after:start_at',
            ],

            'location' => [
                'exclude_unless:type,EVENT',
                'required',
                'string',
                'max:255',
            ],

            'capacity' => [
                'exclude_unless:type,EVENT',
                'nullable',
                'integer',
                'min:1',
            ],
        ];
    }
}
