<?php
 
namespace App\Http\Requests;
 
use Illuminate\Foundation\Http\FormRequest;
 
class StoreRequest extends FormRequest
{
    public function authorize()
    {
        //return false;
        return true;
    }
    public function rules()
    {
        return [
            'name' => 'required|string|max:255', 
            'address' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'facebook_link' => 'nullable|url',
            'instagram_link' => 'nullable|url',
            'rating' => 'required|integer',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];
    }
    
    public function messages()
    {
        return [
            'name.required' => 'Name is required!',
            'address.required' => 'Address is required!',
            'phone.required' => 'Phone is required!',
            'facebook_link.url' => 'Link must be a valid URL!',
            'instagram_link.url' => 'Link must be a valid URL!',
            'rating.required' => 'Rating is required!',
            'rating.integer' => 'Rating must be an integer!',
            'image.required' => 'Image is required!',
            'image.image' => 'The file must be an image!',
        ];
}
}