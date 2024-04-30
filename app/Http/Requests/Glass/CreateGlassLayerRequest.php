<?php

namespace App\Http\Requests\Glass;

use App\Enums\UserGender;
use App\Enums\UserStatus;
use App\Rules\MobileRule;
use App\Rules\PasswordRule;
use Illuminate\Foundation\Http\FormRequest;

class CreateGlassLayerRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'product_id' => 'required|exists:products,id',
            'type_id' => 'required|exists:glass_types,id',
            'width_id' => 'required|exists:glass_widths,id',
            'material_id' => 'required|exists:glass_materials,id',
        ];
    }
}
