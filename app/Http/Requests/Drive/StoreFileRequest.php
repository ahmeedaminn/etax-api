<?php

namespace App\Http\Requests\Drive;

use App\Models\Category;
use App\Models\InstitutionProfile;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file' => [
                'required',
                'file',
                'max:10240',
            ],

            'fileable_id' => [
                'required',
                'integer',
                'min:1',
            ],

            'fileable_type' => [
                'required',
                'string',
                Rule::in([
                    User::class,
                    InstitutionProfile::class,
                    Category::class,
                    Post::class,
                ]),
            ],
        ];
    }
}
