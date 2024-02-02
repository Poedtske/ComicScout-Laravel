<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Post;

class PostFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool//contains logic for authorization
    {
        return true;
        // $post=Post::find($this->route('post'));
        // return $post&&$this->user()->can('update',$post);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array//offers way to implement validation
    {
        return [
            'title'=>'required',
            'description'=>['required','min:10'],
            'cover'=>['nullable','image',],
            'date'=>['required','date'],
            'time'=>['required','string'],
        ];
    }
}
