<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class VerificationApiController extends Controller
{

    public function verify(Request $request)
    {
        $userID = $request['id'];
        $user = User::findOrFail($userID);
        if (!Carbon::now()->greaterThan($user->created_at->addDay(30))) {  
            if ($user->email_verified_at == null) {
                $date = date('Y-m-d g:i:s');
                $user->email_verified_at = $date;
                $user->save();
                return response()->json(['message' => 'Jūsų elektroninis paštas buvo patvirtintas!!'], 200);
            }
            return response()->json(['message' => 'Jūsų elektroninis paštas jau yra patvirtintas'], 400);
        }
        return response()->json(['message' => 'Nuoroda nebegalioja'], 400);
    }


    public function resend(Request $request)
    {
        $email = $request['email'];
        $user = User::where('email', '=', $email)->first();
        if ($user !== null) {
            if ($user->email_verified_at == null) {

                $user->sendEmailVerificationNotification();
                return response()->json(['message' => "Žinutė persiųsta"], 200);
            }
            return response()->json(['message' => 'Jūsų elektroninis paštas jau yra patvirtintas'], 400);
        }
        return response()->json(['message' => "Toks elektroninis paštas neegzistuoja"], 400);
    }
}
