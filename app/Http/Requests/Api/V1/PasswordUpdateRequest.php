<?php

namespace App\Http\Requests\Api\V1;

use App\Concerns\PasswordValidationRules;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Validates password changes made by authenticated API clients.
 *
 * The shared password rules keep web and API password requirements identical.
 */
class PasswordUpdateRequest extends FormRequest
{
    use PasswordValidationRules;

    /**
     * Determine if the user is authorized to make this request.
     *
     * Authorization is enforced by the route's Sanctum ability middleware.
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
            'current_password' => ['required', 'string', 'current_password:sanctum'],
            'password' => $this->passwordRules(),
        ];
    }
}
