<?php

namespace App\Http\Requests\Drive;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreFileRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'file'          => 'required|file|max:10240', // Max 10MB limit
            'fileable_id'   => 'required|integer',
            'fileable_type' => 'required|string', // e.g., 'App\Models\Post'
        ];
    }
}
