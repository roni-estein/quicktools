<?php

namespace App\Http\Requests\Forms;

use Illuminate\Foundation\Http\FormRequest;

class poop5Form extends FormRequest
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