<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateProfileForm extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|min:3|max:30',
            'username' => 'required|min:3|max:30|regex:/^[a-zA-Z0-9_]+$/|unique:users,username,' . Auth::user()->id,
            'email' => 'required|string|max:255|email:filter|unique:users,email,' . Auth::user()->id,
            'password' => 'nullable|regex:/^(?=.*[!@#$%^&*-])(?=.*[0-9])(?=.*[A-Z]).{8,200}$/',
        ];
    }

    /*
     * Use the following function to customize other error messages
     */
    public function messages()
    {
        return [
            'password.regex' => 'Your password should have a minimum of 8 characters, at least 1 uppercase letter, at least 1 digit, and at least one of !@#$%^&-* special characters.',
            'username.unique' => 'This Username has already been taken.',
            'username.regex' => 'Only letters, numbers, and underscores are acceptable',
        ];
    }

    public function attributes()
    {
        return [
            'name' => 'Full name',
            'username' => 'Username',
            'email' => 'Email',
        ];
    }
}
