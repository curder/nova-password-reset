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
        $minPasswordSize = config('nova-password-reset.min_password_size');

        return [
            'current_password' => ['required', Password::default()],
            'new_password' => "required|string|min:$minPasswordSize",
            'confirm_new_password' => "required|string|min:$minPasswordSize|same:new_password"
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (!Hash::check($this->current_password, $this->user()->password)) {
                $validator->errors()->add(
                    'current_password',
                    Nova::allTranslations()['novaPasswordReset.oldsPasswordIsNotCorrect']
                );
            }
        });
    }

    public function messages(): array
    {
        return [
            'current_password.required' => __('novaPasswordReset.oldPasswordRequired'),
            'new_password.required' => __('novaPasswordReset.newPasswordRequired'),
            'confirm_new_password.required' => __('novaPasswordReset.confirmPasswordRequired'),
            'confirm_new_password.same' => Nova::allTranslations()['novaPasswordReset.confirmNewPasswordSame'],
        ];
    }
}
