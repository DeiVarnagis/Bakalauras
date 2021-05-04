<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegistrationController extends Controller
{
    public function register(Request $request)
    {
        
        $user = User::create(array_merge($this->validateRegistration(),[ 'password' => Hash::make($request['password'])]));

        $user->sendApiEmailVerificationNotification();

        return response()->json($user, 201);
    }

    protected function validateRegistration()
    {
        return request()->validate([
            'name' => 'required|string|max:125',
            'surname' => 'required|string|max:125',
            'email' => 'required|string|email|max:125|unique:users',
            'password' => 'required|string|confirmed|min:8',
        ]);
    }
}
