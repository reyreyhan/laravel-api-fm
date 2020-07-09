<?php

namespace App\Http\Controllers\API;

use App\Absent;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\User;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->token = JWTAuth::fromUser($user);

        return response()->json(compact('user'),201);
    }

    public function login(Request $request) {
        $credentials = $request->only('email', 'password');

        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'invalid_credentials'], 400);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'could_not_create_token'], 500);
        }

        return response()->json(compact('token'));
    }

    public function getAuthenticatedUser(Request $request) {
        $user = $request->user;
        return response()->json(compact('user'));
    }

    public function update(Request $request) {
        $user = $request->user;

        $data = User::find($user->id);

        if ($request->photo) {
            if ($data->photo) {
                unlink(public_path('photo/') . $data->photo);
            }

            $imageName = Str::random(4) . time().'.'.request()->photo->getClientOriginalExtension();
            request()->photo->move(public_path('photo'), $imageName);
            $data->photo = $imageName;
        }

        $data->name = $request->name;
        $data->address = $request->address;
        $data->save();

        return response()->json(compact('data'));
    }

    public function absent(Request $request) {
        $user = $request->user;

        $data = Absent::latest('created_at')->first();

        if ($data) {
            $dateAbsent = date('Y-m-d', $data->created_at->timestamp);
            $dateNow = date('Y-m-d');

            if ($dateAbsent == $dateNow) {
                return response()->json('You have absent on this day!');
            }
        }

        $data = Absent::create([
            'long' => $request->long,
            'lat' => $request->lat,
            'user_id' => $user->id,
        ]);

        return response()->json(compact('data'));
    }

    public function getAbsent() {
        $data = User::with(['absent'])->get();
        return response()->json($data);
    }
}
