<?php

namespace App\Http\Controllers\Admin;

use App\Events\AdminAddedEvent;
use App\User;

use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Hash;
use App\Http\Requests\UpdateInfoRequest;
use App\Http\Requests\UserCreateRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\UserRole;
use Symfony\Component\HttpFoundation\Response;


class UserController
{
    public function index() {

        \Gate::authorize('view', 'users');

        $users = User::paginate();

        return UserResource::collection($users);
    }

    public function show($id) {

         \Gate::authorize('view', 'users');

        $user = User::find($id);

        return new UserResource($user);

    }

    public function store(UserCreateRequest $request) {

        \Gate::authorize('edit', 'users');

        $user =  User::create($request->only('first_name','last_name', 'email') + ['password' => Hash::make(1234)]);

        UserRole::create([
            'user_id' => $user->id,
            'role_id' => $request->input('role_id')
        ]);

        event(new AdminAddedEvent($user));

        return response(new UserResource($user), Response::HTTP_CREATED);

   }

   public function update(UserUpdateRequest $request, $id) {

       \Gate::authorize('edit', 'users');

       $user = User::find($id);

       $user->update($request->only('first_name','last_name', 'email'));

       UserRole::where('user_id', $user->id)->delete();

       UserRole::create([
        'user_id' => $user->id,
        'role_id' => $request->input('role_id')
    ]);

       return response(new UserResource($user), Response::HTTP_ACCEPTED);
    }

    public function destroy($id) {

        \Gate::authorize('edit', 'users');

        User::destroy($id);

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function user() {

        $user = \Auth::user();
        return (new UserResource($user))->additional([
            'data' => [
                'permissions' => $user->permissions()
            ]
        ]);
     }

    public function updateInfo(UpdateInfoRequest $request) {

         $user = \Auth::user();

        $user->update($request->only('first_name','last_name', 'email'));

        return response(new UserResource($user), Response::HTTP_ACCEPTED);
    }

    public function updatePassword(UpdatePasswordRequest $request) {
        $user = \Auth::user();
        $user->update([
            'password' => Hash::make($request->input('password'))
        ]);

        return response(new UserResource($user), Response::HTTP_ACCEPTED);
    }


}
