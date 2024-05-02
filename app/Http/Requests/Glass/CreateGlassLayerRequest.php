<?php

namespace App\Http\Requests\Glass;


use App\Models\GlassLayer;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Support\Facades\Gate;


class CreateGlassLayerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        /*$Layer = GlassLayer::find($this->id);
        if (!$Layer) {
            throw new NotFoundHttpException();
        }
        return Gate::allows('update-glass-layer', $Layer);*/
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
