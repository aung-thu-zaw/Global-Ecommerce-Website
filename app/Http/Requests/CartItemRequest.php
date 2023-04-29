<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CartItemRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            "cart_id"=>["nullable","numeric",Rule::exists("carts", "id")],
            "product_id"=>["required","numeric",Rule::exists("products", "id")],
            "shop_id"=>["required","numeric",Rule::exists("users", "id")],
            "size"=>["nullable","string"],
            "color"=>["nullable","string"],
            "qty"=>["required","numeric"],
        ];
    }
}
