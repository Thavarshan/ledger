<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validates scoped integration-token creation requests.
 *
 * The allowed abilities intentionally exclude tokens:manage so delegated
 * integrations cannot mint or revoke additional credentials.
 */
class CreateTokenRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:100'],
            'abilities' => ['required', 'array', 'min:1'],
            'abilities.*' => [
                'string',
                Rule::in([
                    'accounts:read', 'accounts:write',
                    'transactions:read', 'transactions:write',
                    'profile:read', 'profile:write',
                ]),
            ],
            'expires_in_days' => ['sometimes', 'integer', Rule::in([30, 90, 365])],
            'current_password' => ['required', 'current_password:sanctum'],
        ];
    }
}
