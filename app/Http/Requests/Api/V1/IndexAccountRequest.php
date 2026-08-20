<?php

namespace App\Http\Requests\Api\V1;

use App\Http\Requests\IndexAccountRequest as BaseIndexAccountRequest;

/** Validates API account listing filters and pagination limits. */
class IndexAccountRequest extends BaseIndexAccountRequest
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
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            ...parent::rules(),
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
