<?php

namespace App\Http\Requests\Order;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            Order::PRICE => 'required|Numeric',
            Order::STATUS => 'required|String',
            Order::PAYED => 'required|Boolean'
        ];
    }
}
