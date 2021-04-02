<?php

namespace App\Http\Controllers;

use App\Events\NotificationSend;
use App\Models\User;
use Illuminate\Http\Request;
use App\Helpers\CollectionHelper;
use App\Models\DevicesTransfer;

class MessagesController extends Controller
{
    
    public function messagesCount()
    {
        $user = User::where('id', auth()->user()->id)->first();
        return $user->messagesCount();
    }

    public function index()
    {
        $user = User::where('id', auth()->user()->id)->first(); 
        $filter = request()->get('filter');
        $search = request()->get('search');
        
        if($filter < -1 && $filter > 1)
        {
            return response()->json(["error" => "Filtracijos duomenys neteisingi"], 404);          
        }
       
        return response()->json(["data" => CollectionHelper::paginateWithoutKey($user->messages($filter, $search), 8)], 200);
    }

    public function messagesGeneral()
    {
       $decliend = DevicesTransfer::where('user_id', auth()->user()->id)->where('state', '-1')->count();
       $accepted = DevicesTransfer::where('user_id', auth()->user()->id)->where('state', '1')->count();

       return response()->json(['decliend' => $decliend, 'accepted' => $accepted], 200);

    }

}
