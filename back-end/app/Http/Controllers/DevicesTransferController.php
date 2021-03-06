<?php

namespace App\Http\Controllers;

use App\Events\NotificationSend;
use App\Models\AccessoriesLend;
use App\Models\DevicesTransfer;
use App\Models\User;
use App\Models\DeviceLend;
use App\Rules\BelongsToUserLongTerm;
use App\Rules\BelongsToUserShortTerm;
use Illuminate\Http\Request;

class DevicesTransferController extends Controller
{

    protected $directory = "App\Models\Devices";

    public function index()
    {
        return DevicesTransfer::all();
    }


    public function store(Request $request)
    {
        $transfer = DevicesTransfer::create($this->validateData());

        if (request('accessories') != null) {
            foreach (request('accessories') as $accessory) {
                AccessoriesLend::create(array_merge($accessory, ['transfer_id' => $transfer->id]));
            }
        }
        else
        {
            $device = $this->getDevice($transfer);
  
            $deviceLend = $device->DevicesLends()->first();
            if($deviceLend)
            {
                $accessories = $deviceLend->lendAccessories()->get();
                 foreach ($accessories as $accessory) {
                     AccessoriesLend::create(
                     [
                         'name' => $accessory->name,
                         'amount' => $accessory->amount,
                         'transfer_id' => $transfer->id,            
                     ]);
                 }
            }
    
        }
       
        event(new NotificationSend(User::find($request['user_id'])->messagesCount(), $request['user_id']));
        $transfer->updateDevices($transfer->action, 1, 0);
        return response()->json(['message'=>"Užklausa sėkmingai išsiųsta"], 201);
    }

    public function confirmTransfer(Request $request)
    {
        $transfer = DevicesTransfer::find($request['id']);

        if ($transfer == null || $transfer->state != 0) {
            return response()->json(["error" => "Užklausa neegzistuoja"], 404);
        }

        switch ($transfer->action) {
            case "1":
                $values = $transfer->returnDeviceType();
                $user = User::find($transfer->owner_id);
                $user->confirmDeiviceTransfer($transfer->user_id, $values['id'], $values['type']);

                if ($values['type'] == "LongTerm") {
                    DevicesTransfer::where("longTerm_id", $transfer->longTerm_id)->delete();
                }

                if ($values['type'] == "ShortTerm") {
                    DevicesTransfer::where("shortTerm_id", $transfer->shortTerm_id)->delete();
                }

                $transfer->updateDevices(0, 0, 1);
                event(new NotificationSend(User::find(auth()->user()->id)->messagesCount(), auth()->user()->id));
                return response()->json(["message" => "Transfer was confirmed"], 200);
                break;
            case "2":
                $lendRow = $this->getLendRow($transfer->shortTerm_id, $transfer->longTerm_id);
                $this->createLendRow($lendRow, $transfer);
                $transfer->updateDevices(0, 2, 1);
                event(new NotificationSend(User::find(auth()->user()->id)->messagesCount(), auth()->user()->id));
                return response()->json(["message" => "Device was lend successfully"], 200);
                break;
            case "3":
                $lendRow = $this->getLendRow($transfer->shortTerm_id, $transfer->longTerm_id);
                $lendRow->delete();
                $transfer->updateDevices(0, 0, 1);
                event(new NotificationSend(User::find(auth()->user()->id)->messagesCount(), auth()->user()->id));
                return response()->json(["message" => "Device was returned successfully"], 200);
                break;
        }
    }


    public function declineTransfer(Request $request)
    {
        $transfer = DevicesTransfer::find($request['id']);
        if ($transfer == null && $transfer->state != 0) {
            return response()->json(["error" => "Užklausa neegzistuoja"], 404);
        }

        if (auth()->user()->id == $transfer->owner_id || auth()->user()->id == $transfer->user_id) {

            $transfer->updateDevices(-1, 0, -1);
            event(new NotificationSend(User::find(auth()->user()->id)->messagesCount(), auth()->user()->id));
            return response()->json(["message" => "Užklausa atšaukta"], 200);
        }

        return response()->json(["error" => "Unauthorized"], 401);
    }

    public function getTranferInfo(Request $request)
    {
        $transfer = DevicesTransfer::find($request['id']);
        if ($transfer == null) {
            return response()->json(["error" => "Užklausa neegzistuoja"], 404);
        }

        if ($transfer->shortTerm_id != null) {
            $device = $transfer->DevicesShortTerm()->first();
        }

        if ($transfer->longTerm_id != null) {
            $device = $transfer->DevicesLongTerm()->first();
        }

        if($device->state == 2)
        {

        }

        if ($transfer->action == 2 || $transfer->action == 3) {
            $device->accessories = $transfer->lendAccessories()->get();
        }

        if ($transfer->action == 1) {
            $device->accessories = $device->accessories();
        }

        return response()->json($device, 200);
    }


    protected function getLendRow($short, $long)
    {
        if ($short == null) {
            return DeviceLend::where('longTerm_id', $long)->first();
        }
        return DeviceLend::where('shortTerm_id', $short)->first();
    }

    protected function createLendRow($lendRow, $transfer)
    {
        if ($lendRow != null) {
            $lendRow->delete();
        } 

        $lend = DeviceLend::create([
            'owner_id' =>  $transfer->owner_id,
            'user_id' => $transfer->user_id,
            'shortTerm_id' => $transfer->shortTerm_id,
            'longTerm_id' => $transfer->longTerm_id
        ]);

        $transfer->lendAccessories()->update(array("lend_device_id" => $lend->id));
        
    }

    public function getDevice($transfer)
    {
        if ($transfer->shortTerm_id != null) {
            $device = $transfer->DevicesShortTerm()->first();
        }
    
        if ($transfer->longTerm_id != null) {
            $device = $transfer->DevicesLongTerm()->first();
        }

        return $device;
    }

   

    public function validateData()
    {
        return request()->validate([
            'owner_id' => 'required|exists:users,id',
            'user_id' => 'required|exists:users,id',
            'action' => 'required|integer|between:1,3',
            'shortTerm_id' => ['nullable', 'numeric', 'required_without:longTerm_id', new BelongsToUserShortTerm(request())],
            'longTerm_id' => ['nullable', 'numeric', 'required_without:shortTerm_id', new BelongsToUserLongTerm(request())]
        ]);
    }
}
