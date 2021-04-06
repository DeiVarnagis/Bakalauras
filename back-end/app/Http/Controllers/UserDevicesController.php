<?php

namespace App\Http\Controllers;

use App\Events\NotificationSend;
use App\Models\User;
use App\Helpers\CollectionHelper;
use App\Models\LeavingWork;

class UserDevicesController extends Controller
{
    protected $user;

    public function index()
    {
        $state =  request()->get('state');
        $search = request()->get('search');

        if (($state >= -1 && $state <= 2) ||  $state == "all") {
            $user = User::find(auth()->user()->id);
            switch (request()->get('type')) {
                case "LongTerm":
                    return response()->json(["data" => CollectionHelper::paginateWithoutKey($user->getFilteredLong($state, $search), 8)], 200);
                    break;
                case "ShortTerm":
                    return response()->json(["data" => CollectionHelper::paginateWithoutKey($user->getFilteredShort($state, $search), 8)], 200);
                case "Yours":
                    return response()->json(["data" => CollectionHelper::paginateWithoutKey($user->userDevices($state, $search), 8)], 200);
                    break;
                    return response()->json(["data" => CollectionHelper::paginateWithoutKey($user->getFilteredShort($state, $search), 8)], 200);
                case "All":
                    return response()->json(["data" => CollectionHelper::paginateWithoutKey($user->allDevices($state, $search), 8)], 200);
                    break;
                case "Borrowed":
                    return response()->json(["data" => CollectionHelper::paginateWithoutKey($user->getBorrowedDevices($state, $search), 8)], 200);
                    break;
                default:
                    return response()->json(["error" => 'Duomenų nėra'], 404);
            }
        }

        return response()->json(["error" => 'Duomenų nėra'], 404);
    }

    
    public function devicesCount()
    {
       $user = User::find(auth()->user()->id);
       $borrowed = $user->DevicesLends()->count(); 
       $lended = $user->DevicesLongTerm()->where('state', 2)->count() + $user->DevicesShortTerm()->where('state', 2)->count();
       $allDevices = $user->DevicesLongTerm()->count() + $user->DevicesShortTerm()->count();     

       return response()->json(['borrowed' => $borrowed, 'lended' => $lended, 'allDevices' => $allDevices ], 200);

    }
}
