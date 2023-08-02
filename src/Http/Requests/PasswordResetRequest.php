<?php

namespace Mastani\NovaPasswordReset\Http\Requests;

use Laravel\Nova\Nova;
use Illuminate\Validation\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Foundation\Http\FormRequest;

class PasswordResetRequest extends FormRequest
{

    protected $stopOnFirstFailure = true;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        $minPasswordSize = config('password-reset.min_password_size');

        return [
            'current_password' => ['required'],
            'new_password' => "required|string|min:$minPasswordSize",
            'confirm_new_password' => "required|string|min:$minPasswordSize|same:new_password"
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if (!Hash::check($this->current_password, $this->user()->password)) {
                $validator->errors()->add(
                    'current_password',
                    __('password-reset::password-reset.oldsPasswordIsNotCorrect')
                );
            }
        });
    }

    public function messages(): array
    {
        return [
            'current_password.required' => __('password-reset::password-reset.oldPasswordRequired'),
            'new_password.required' => __('password-reset::password-reset.newPasswordRequired'),
            'confirm_new_password.required' => __('password-reset::password-reset.confirmPasswordRequired'),
            'confirm_new_password.same' => __('password-reset::password-reset.confirmNewPasswordSame'),
        ];
    }
}
