<?php
 
namespace App\Http\Requests;
 
use Illuminate\Foundation\Http\FormRequest;
 
class CategoryRequest extends FormRequest
{
    public function authorize()
    {
        //return false;
        return true;
    }
 
    public function rules()
    {
        if(request()->isMethod('post')) {
            return [
                'name' => 'required|string|max:255|unique:categories,name',
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
               
            ];
        } else {
            return [
                'name' => 'required|string|max:258',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    
            ];
        }
    }
 
    public function messages()
    {
        if(request()->isMethod('post')) {
            return [
                'name.required' => 'Name is required!',
                'image.required' => 'Image is required!',
               
            ];
        } else {
            return [
                'name.required' => 'Name is required!',
               
            ];   
        }
    }
}