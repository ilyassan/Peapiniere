<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class StoreOrderRequest extends FormRequest
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
            "plants" => "required|array|min:1",
            "plants.*.slug" => "required|string|exists:plants,slug",
            "plants.*.quantity" => "required|integer|min:1",
        ];
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            "plants.required" => "Plants are required",
            "plants.array" => "Plants must be an array",
            "plants.min" => "You must provide at least one plant",
            "plants.*.slug.required" => "Slug for each plant is required",
            "plants.*.slug.string" => "Each slug must be a string",
            "plants.*.slug.exists" => "The selected slug does not exist for any plant",
            "plants.*.quantity.required" => "Quantity for each plant is required",
            "plants.*.quantity.integer" => "Quantity must be an integer",
            "plants.*.quantity.min" => "Quantity must be at least 1",
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => false,
            'message' => 'Validation error',
            'errors' => $validator->errors(),
        ], 422));
    }
}
