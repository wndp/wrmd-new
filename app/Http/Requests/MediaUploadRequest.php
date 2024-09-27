<?php

namespace App\Http\Requests;

use App\Enums\MediaCollection;
use App\Enums\MediaResource;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MediaUploadRequest extends FormRequest
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
            'resource' => [
                'required',
                Rule::enum(MediaResource::class),
            ],
            'resource_id' => [
                'required',
                'uuid',
            ],
            'key' => [
                'required',
                'string',
            ],
            'name' => [
                'required',
                'string',
            ],
            'collection' => [
                'required',
                Rule::enum(MediaCollection::class)
            ],
            'extension' => [
                'required',
                'string'
            ],
            'uuid' => [
                'required',
                'uuid',
            ]
        ];
    }
}
