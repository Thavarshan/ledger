<?php

namespace App\Http\Requests\Settings;

use App\Concerns\ProfileValidationRules;
use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Validates profile changes for the currently authenticated web user.
 */
class ProfileUpdateRequest extends FormRequest
{
    use ProfileValidationRules;

    /**
     * Return profile rules scoped to the current user's unique email record.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $user = $this->user();

        if (! $user instanceof User) {
            abort(401);
        }

        return $this->profileRules($user->id);
    }
}
