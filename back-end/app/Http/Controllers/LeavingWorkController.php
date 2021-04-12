<?php

namespace App\Http\Controllers;

use App\Events\NotificationSend;
use App\Models\LeavingWork;
use App\Models\User;
use Illuminate\Http\Request;

class LeavingWorkController extends Controller
{
    
    public function store()
    {
        $row = LeavingWork::where('owner_id', auth()->user()->id)->first();
        $this->validateData();
        if ($row == null || $row->state != 0) {
            $leavingWork = LeavingWork::create([
                'owner_id' => auth()->user()->id,
                'user_id' => request('user_id')
            ]);
            
            event(new NotificationSend(User::find(request('user_id'))->messagesCount(), request('user_id')));
            return response()->json(["data" => $leavingWork], 200);
        }
        return response()->json(["error" => "Vartotojas jau pateikė užklausa dėl išėjimo iš darbo"], 400);
        
       
    }

    public function confirmLeaveWork()
    {
        $leavingWorkRow = LeavingWork::find(request('id'));
        if ($leavingWorkRow == null || $leavingWorkRow->state != 0) {
            return response()->json(["error" => "Užklausos rasti nepavyko "], 404);
        }
        $user = User::find($leavingWorkRow->owner_id);
        $user->DevicesShortTerm()->update(array("user_id" => $leavingWorkRow->user_id));
        $user->DevicesLongTerm()->update(array("user_id"=> $leavingWorkRow->user_id));
        $leavingWorkRow->state = 1;
        $leavingWorkRow->save();
        event(new NotificationSend(User::find(auth()->user()->id)->messagesCount(), auth()->user()->id));
        return response()->json(["message" => "Užklausa sėkmingai buvo patvirtinta"], 200);
        
        
    }

    public function declineLeaveWork()
    {
        $row = LeavingWork::find(request('id'));
        if ($row == null || $row->state != 0) {
            return response()->json(["error" => "Užklausos rasti nepavyko "], 404);
        }

        $row->state = -1;
        $row->save();
        event(new NotificationSend(User::find(auth()->user()->id)->messagesCount(), auth()->user()->id));
        return response()->json(["message" => "Užklausa atšaukta sėkmingai"], 200);
    }

    protected function validateData()
    {
        return request()->validate([
            'user_id' => 'required|exists:users,id',
        ]);
    }
}
