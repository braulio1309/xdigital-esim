<?php

namespace App\Http\Requests\App;

use Illuminate\Foundation\Http\FormRequest;

class StorageRequest extends FormRequest
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
            'storage_type' => ['required', 'in:local,public,s3'],
            'aws_bucket_name' => ['required_if:storage_type,==,s3', 'nullable', 'string'],
            'aws_region' => ['required_if:storage_type,==,s3', 'nullable', 'string'],
            'aws_access_key' => ['required_if:storage_type,==,s3', 'nullable', 'string'],
            'aws_secret_key' => ['required_if:storage_type,==,s3', 'nullable', 'string'],
        ];
    }
}
