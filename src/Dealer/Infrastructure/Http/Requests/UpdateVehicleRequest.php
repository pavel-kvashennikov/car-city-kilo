<?php

declare(strict_types=1);

namespace Src\Dealer\Infrastructure\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVehicleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'brand_id' => ['sometimes', 'exists:car_brands,id'],
            'model_id' => ['sometimes', 'exists:car_models,id'],
            'generation_id' => ['nullable', 'exists:car_generations,id'],
            'year' => ['sometimes', 'integer', 'min:1900', 'max:'.(date('Y') + 1)],
            'vin' => ['nullable', 'string', 'size:17', 'unique:vehicles,vin,'.$this->route('vehicle')],
            'mileage' => ['sometimes', 'integer', 'min:0'],
            'color' => ['nullable', 'string', 'max:50'],
            'engine_type' => ['nullable', 'string', 'in:petrol,diesel,hybrid,electric,gas'],
            'engine_volume' => ['nullable', 'numeric', 'min:0', 'max:20'],
            'engine_power' => ['nullable', 'integer', 'min:0'],
            'transmission' => ['nullable', 'string', 'in:manual,automatic,robot,cvt'],
            'drive_type' => ['nullable', 'string', 'in:fwd,rwd,awd,4wd'],
            'body_type' => ['nullable', 'string', 'max:50'],
            'condition' => ['nullable', 'string', 'in:new,used,damaged'],
            'price' => ['sometimes', 'integer', 'min:0'],
            'description' => ['nullable', 'string', 'max:10000'],
            'features' => ['nullable', 'array'],
            'attributes' => ['nullable', 'array'],
        ];
    }
}
