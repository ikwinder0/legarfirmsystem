<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RunnerTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // only allow updates if the user is logged in
        return backpack_auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
		    'title'         => 'required|string',
            'description'   => 'string|nullable',
            'remarks'       => 'string|nullable',
            'upload_multiple.*' => [
                'nullable',
                'max:2048', // file size in KB
            ],
        ];
    }

    /**
     * Get the validation attributes that apply to the request.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            //
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            //
        ];
    }
}
