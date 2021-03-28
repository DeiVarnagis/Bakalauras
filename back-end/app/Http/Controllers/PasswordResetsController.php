<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class PasswordResetsController extends Controller
{

    public function forgot(Request $request)
    {
        $credentials = $this->validateEmail();
        Password::sendResetLink($credentials);
        return response()->json(["message" => 'Užklausa dėl slaptažodžio pekeitimo buvo nusiųsta']);
    }

    public function reset()
    {
        $credentials = request()->validate([
            'email' => 'required|email',
            'token' => 'required|string',
            'password' => 'required|string|confirmed'
        ]);

        $reset_password_status = Password::reset($credentials, function ($user, $password) {
            $user->password = Hash::make($password);
            $user->save();
        });

        if ($reset_password_status == Password::INVALID_TOKEN) {
            return response()->json(["message" => "Suteiktas netinkamas žetonas"], 400);
        }

        return response()->json(["message" => "Slaptažodis sėkmingai buvo atnaujintas"]);
    }


    protected function validateEmail()
    {
        return request()->validate([
            'email' => 'required|string|email|max:255|exists:users',
        ]);
    }
}
