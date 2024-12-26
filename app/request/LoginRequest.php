<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'email'     =>'required|email|exists:users,email',
            'password'  =>'required|string|min:6'
        ];
    }
    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'email.required'    => 'Email field is required',
            'email.exists'      => 'Email is not exist',
            'password.required' => 'Password is required',
        ];
    }
    /**
     * Prepare the data for validation.
     * If you need to prepare or sanitize any data from the request before you apply your validation rules.
     * you may use the prepareForValidation method.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => strtolower($this->emal),
        ]);
    }
    /**
     * Configure the validator instance.
     * If validation is not failed then execute after validation.
     * Check user is not deleted.
     * Then merge user object in request.
     */
    public function withValidator($validator): void
    {
        if(!$validator->fails()){
            $validator->after(function ($validator) {
                $user_not_delete = User::where('email', $this->email)->whereNull('deleted_at')->first();
                if (empty($user_not_delete)) {
                    $validator->errors()->add('email', 'Invalid email');
                }
                $this->merge(['user' => $user_not_delete]);
            });
        }
    }
}