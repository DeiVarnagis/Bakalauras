<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Helpers\CollectionHelper;

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

}
