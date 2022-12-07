<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AvatarUserRequest;
use Backpack\CRUD\app\Http\Requests\AccountInfoRequest;
use Backpack\CRUD\app\Http\Requests\ChangePasswordRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Backpack\CRUD\app\Library\Widget;

class MyAccountController extends \Backpack\CRUD\app\Http\Controllers\MyAccountController
{
    //


 public function postChangeAvatarForm( AvatarUserRequest $request){
      if($request->hasFile('avatar'))
      {
          $extension = pathinfo($request->file('avatar')->getClientOriginalName(),PATHINFO_BASENAME);
          $avatar = Str::of(backpack_auth()->user()->name)->slug('-') . time() . '.' . $extension;
     $request->avatar->storeAs('public/avatar', $avatar);
        backpack_auth()->user()->avatar = "storage/avatar/". $avatar;
      $result =   backpack_auth()->user()->save();


          if ($result) {

              \Alert::add('success', '<strong>Updated </strong><br>Successfully!.')->flash();

          } else {
              \Alert::add('error', '<strong>Faiure </strong><br>have some errors.')->flash();

          }
      }


      return redirect()->back();

 }
    /**
     * Get the guard to be used for account manipulation.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */

}
