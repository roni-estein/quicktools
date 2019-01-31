<?php

namespace DummyNamespace;

use Illuminate\Foundation\Http\FormRequest;

class DummyClass extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        return [
            //
        ];
    }

    /**
    * Persist the requested data to the database.
    *
    * @return array
    */
    public function persist()
    {
        return [
            //
        ];
    }
}