<?php

namespace App\Http\Requests\Engagement;

use App\Enums\ParticipationStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SetEventParticipationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => [
                'required',
                Rule::enum(ParticipationStatus::class),
            ],
        ];
    }
}
