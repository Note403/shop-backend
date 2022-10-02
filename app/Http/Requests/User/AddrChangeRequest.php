<?php

namespace App\Http\Requests\User;

use App\Models\Address;
use Illuminate\Auth\Middleware\Authorize;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class AddrChangeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return (Auth::user() != null);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            Address::COUNTRY => 'required|String',
            Address::CITY => 'required|String',
            Address::ZIP => 'required|String',
            Address::HOUSE_NUMBER => 'required|String'
        ];
    }
}
