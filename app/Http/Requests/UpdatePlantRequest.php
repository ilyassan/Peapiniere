<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;

class UpdatePlantRequest extends FormRequest
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
            "name" => ["string"],
            "category_id" => ["integer", "exists:categories,id"],
            "images" => ["array"],
            "images.*" => ["url"],
            "images" => ["max:4"],
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
            "name.required" => "Name is required",
            "name.string" => "Name must be a string",
            "category_id.required" => "Category is required",
            "category_id.exists" => "Category not found",
            "images.required" => "Images is required",
            "images.array" => "Images must be an array",
            "images.*.required" => "Image url is required",
            "images.*.url" => "Image url must be a valid URL",
            "images.max" => "Max images is 4",
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
