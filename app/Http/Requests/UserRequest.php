<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserRequest extends FormRequest
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
             'name' => 'required|min:5|max:255',
            'email'=>['required','email','unique:users,email'],
            'password'=>['required',Password::min(6)->symbols()->mixedCase()->numbers()],
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
            'name'=>"tên",
            'email'=>"đia chỉ email",
            'password'=>"mật khẩu",
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
            '*.required'=>"Bạn chưa nhập :attribute của bạn",
            '*.min'=> ":attribute của bạn tối thiểu :min ký tự",
            '*.max'=>":attribute của bạn tối đa :max ký tự",
            'email.email'=>"Địa chỉ email không hợp lệ",
            'email.unique'=>'Địa chỉ email này đã được đăng ký trên  hệ thống',

        ];
    }
}
