<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InvitationForm extends FormRequest
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
            'id' => 'required|integer',
            'email.*' => 'required|string|max:255|email:filter',
        ];
    }

    public function attributes()
    {
        return [
            'email.0' => 'Email',
            'email.1' => 'Email',
            'email.2' => 'Email',
            'email.3' => 'Email',
            'email.4' => 'Email',
            'email.5' => 'Email',
            'email.6' => 'Email',
            'email.7' => 'Email',
            'email.8' => 'Email',
            'email.9' => 'Email',
            'email.10' => 'Email',
            'email.11' => 'Email',
            'email.12' => 'Email',
            'email.13' => 'Email',
            'email.14' => 'Email',
            'email.15' => 'Email',
            'email.16' => 'Email',
            'email.17' => 'Email',
            'email.18' => 'Email',
            'email.19' => 'Email',
            'email.20' => 'Email',
            'email.21' => 'Email',
            'email.22' => 'Email',
            'email.23' => 'Email',
            'email.24' => 'Email',
            'email.25' => 'Email',
            'email.26' => 'Email',
            'email.27' => 'Email',
            'email.28' => 'Email',
            'email.29' => 'Email',
            'email.30' => 'Email',
            'email.31' => 'Email',
            'email.32' => 'Email',
            'email.33' => 'Email',
            'email.34' => 'Email',
            'email.35' => 'Email',
            'email.36' => 'Email',
            'email.37' => 'Email',
            'email.38' => 'Email',
            'email.39' => 'Email',
            'email.40' => 'Email',
            'email.41' => 'Email',
            'email.42' => 'Email',
            'email.43' => 'Email',
            'email.44' => 'Email',
            'email.45' => 'Email',
            'email.46' => 'Email',
            'email.47' => 'Email',
            'email.48' => 'Email',
            'email.49' => 'Email',
            'email.50' => 'Email',
            'email.51' => 'Email',
            'email.52' => 'Email',
            'email.53' => 'Email',
            'email.54' => 'Email',
            'email.55' => 'Email',
            'email.56' => 'Email',
            'email.57' => 'Email',
            'email.58' => 'Email',
            'email.59' => 'Email',
            'email.60' => 'Email',
            'email.61' => 'Email',
            'email.62' => 'Email',
            'email.63' => 'Email',
            'email.64' => 'Email',
            'email.65' => 'Email',
            'email.66' => 'Email',
            'email.67' => 'Email',
            'email.68' => 'Email',
            'email.69' => 'Email',
            'email.70' => 'Email',
            'email.71' => 'Email',
            'email.72' => 'Email',
            'email.73' => 'Email',
            'email.74' => 'Email',
            'email.75' => 'Email',
            'email.76' => 'Email',
            'email.77' => 'Email',
            'email.78' => 'Email',
            'email.79' => 'Email',
            'email.80' => 'Email',
            'email.81' => 'Email',
            'email.82' => 'Email',
            'email.83' => 'Email',
            'email.84' => 'Email',
            'email.85' => 'Email',
            'email.86' => 'Email',
            'email.87' => 'Email',
            'email.88' => 'Email',
            'email.89' => 'Email',
            'email.90' => 'Email',
            'email.91' => 'Email',
            'email.92' => 'Email',
            'email.93' => 'Email',
            'email.94' => 'Email',
            'email.95' => 'Email',
            'email.96' => 'Email',
            'email.97' => 'Email',
            'email.98' => 'Email',
            'email.99' => 'Email',
            'email.100' => 'Email',
        ];
    }
}
