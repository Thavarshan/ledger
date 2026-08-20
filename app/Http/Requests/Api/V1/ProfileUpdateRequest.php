<?php

namespace App\Http\Requests\Api\V1;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Validates editable authenticated-user profile fields.
 *
 * Email uniqueness excludes the current user so unchanged profile updates are
 * accepted without weakening the database-backed uniqueness rule.
 */
class ProfileUpdateRequest extends FormRequest
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
        $user = $this->user();
        $userId = $user instanceof User ? $user->getKey() : 0;

        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => [
                'sometimes',
                'required',
                'email',
                'max:255',
                'unique:users,email,'.(is_int($userId) || is_string($userId) ? $userId : 0),
            ],
        ];
    }
}
