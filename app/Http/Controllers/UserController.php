<?php

namespace App\Http\Controllers;


use App\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\UserCreateRequest;
use App\Http\Requests\UserUpdateRequest;
use Symfony\Component\HttpFoundation\Response;


class UserController extends Controller
{
    public function index() {
        return User::paginate();
    }

    public function show($id) {
        return User::find($id);
    }

    public function store(UserCreateRequest $request) {
        $user =  User::create($request->only('first_name','last_name', 'email') + ['password' => Hash::make(1234)]);
    
       return response($user, Response::HTTP_CREATED);

   }

   public function update(UserUpdateRequest $request, $id) {
       $user = User::find($id);

       $user->update($request->only('first_name','last_name', 'email'));

       return response($user, Response::HTTP_ACCEPTED);
    }

    public function destroy($id) {
        User::destroy($id);

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function user() {
       return \Auth::user();
    }

    public function updateInfo(UpdateInfoRequest $request) {
        
         $user = \Auth::user();

        $user->update($request->only('first_name','last_name', 'email'));

        return response($user, Response::HTTP_ACCEPTED);
    }

    public function updatePassword(UpdatePasswordRequest $request) {
        $user = \Auth::user();
        $user->update([
            'password' => Hash::make($request->input('password'))
        ]);

        return response($user, Response::HTTP_ACCEPTED);
    }

    
}
