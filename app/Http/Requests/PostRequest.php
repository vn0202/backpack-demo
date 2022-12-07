<?php

namespace App\Http\Requests;

use App\Rules\Base64image;
use Illuminate\Foundation\Http\FormRequest;


class PostRequest extends FormRequest
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
            //
            'title' => ['required'],
            'category_id' => ['required'],
            'description' => ['required'],
            'content' => ['required'],
            'thumb'=>['required', new Base64image()]// create a new rule to  validate type of base64 image

        ];
    }

    public function messages()
    {
        return [
            'title.required' => "Bạn chưa đặt tiêu đề bài viêt ",
            'category_id.required' => "Bạn chưa chọn danh mục bài viết ",
            'description.required' => "Bạn cần nhập mô tả bài viết",
            'content.required' => "Bạn cần nhập nội dung bài viết",
            'thumb.base64image'=>"Chỉ hỗ trợ định dạng ảnh",
            'thumb.required'=>"Bạn chưa chọn ảnh bài viết",
        ];
    }
}
