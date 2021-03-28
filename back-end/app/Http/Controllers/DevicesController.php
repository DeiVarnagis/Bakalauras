<?php

namespace App\Http\Controllers;

use App\Models\Accessories;
use App\Models\DevicesLongTerm;
use App\Models\DevicesShortTerm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class DevicesController extends Controller
{

    protected $instance;
    protected $directory = "App\Models\Devices";
    protected $model;

    public function index(Request $request)
    {
        if (!$this->checkIfClassExist($request['type'])) {
            return response()->json(["error" => 'Pretaiso tipas nerastas'], 404);
        }

        return response()->json($this->instance::all(), 200);
    }

    public function store(Request $request)
    {
        if (!$this->checkIfClassExist($request['type'])) {
            return response()->json(["error" => 'Pretaiso tipas nerastas'], 404);
        }

        $this->instance->validateData($request);

        $path = null;
        if (request()->hasFile('src')) {
            $path = request()->file('src')->store('devicesImages', 'public');
            $path = $path;
        }

        $this->instance::create([
            'name' => $request['name'],
            'code' => $request['code'],
            'serialNumber' => $request['serialNumber'],
            'amount' => $request['amount'],
            'user_id' => auth()->user()->id,
            'src' => $path
        ]);

        return response()->json(["message" => "Prietaisas sėkmingai buvo sukurtas"], 201);
    }

    public function show(Request $request)
    {
        if (!$this->checkIfClassExist($request['type'])) {
            return response()->json(["error" => 'Pretaiso tipas nerastas'], 404);
        } else {

            $device = $this->instance::find($request['id']);
            if ($device != null) {
                
                if (auth()->user()->id != $device->user_id) {
                    $accessories = $device->DevicesLends()->first();
                    $device->accessories =  $accessories->lendAccessories()->get();
                } else {
                    $device->accessories = $device->Accessories();
                }

                return response()->json(["data" => $device], 200);
            }
            return response()->json(["error" => 'Pretaisas nerastas'], 404);
        }
    }

    public function getDeviceAccessories(Request $request)
    {
        if (!$this->checkIfClassExist($request['type'])) {
            return response()->json(["error" => 'Pretaiso tipas nerastas'], 404);
        } else {
            $device = $this->instance::find($request['id']);
            if ($device != null) {
                return response()->json(["data" => $device->Accessories()], 200);
            }
            return response()->json(["error" => 'Pretaisas nerastas'], 404);
        }
    }

    public function getDeviceWithLendAccessories(Request $request)
    {
        if (!$this->checkIfClassExist($request['type'])) {
            return response()->json(["error" => 'Pretaiso tipas nerastas'], 404);
        } else {
            $device = $this->instance::find($request['id']);
            if ($device != null) {

                return response()->json(["data" => $device], 200);
            }
            return response()->json(["error" => 'Pretaisas nerastas'], 404);
        }
    }

    public function showDeviceWithAccessories(Request $request)
    {
        if (!$this->checkIfClassExist($request['type'])) {
            return response()->json(["error" => 'Pretaiso tipas nerastas'], 404);
        } else {

            $device = $this->instance::find($request['id']);
            if ($device != null) {

                return response()->json(["data" => $device], 200);
            }
            return response()->json(["error" => 'Pretaisas nerastas'], 404);
        }
    }

    public function update(Request $request)
    {
        if (!$this->checkIfClassExist($request['type'])) {
            return response()->json(["error" => 'Pretaiso tipas nerastas'], 404);
        } else {

            $device = $this->instance::find($request['id']);
            if ($device != null) {
                $path = $device->src;
                $this->instance->validateUpdate($request['id']);
                if (request()->hasFile('src')) {

                    if (Storage::disk('public')->exists($device->src)) {
                        Storage::disk('public')->delete($device->src);
                    }
                    $path = request()->file('src')->store('devicesImages', 'public');
                }

                $device->update([
                    'name' => $request['name'],
                    'code' => $request['code'],
                    'serialNumber' => $request['serialNumber'],
                    'amount' => $request['amount'],
                    'user_id' => auth()->user()->id,
                    'src' => $path
                ]);

                if ($request['type'] !== $request['deviceType']) {
                    return $this->switchTypes($device, $request);
                }

                return response()->json($device, 200);
            }
            return response()->json(["error" => 'Pretaisas nerastas'], 404);
        }
    }

    public function destroy(Request $request)
    {
        if (!$this->checkIfClassExist($request['type'])) {
            return response()->json('Pretaiso tipas nerastas', 404);
        } else {
            $this->instance = new $this->model;
            $device = $this->instance->find($request['id']);
            if ($device != null) {
                if (auth()->user()->id == $device->user_id) {

                    if (Storage::disk('public')->exists($device->src, 9)) {
                        Storage::disk('public')->delete($device->src);
                    }
                    $device->destroy($request['id']);
                    if ($device) {
                        return response()->json($device, 204);
                    }
                }
                return response()->json(["error" => 'Šis prietaisas priklauso ne jums'], 400);
            }
            return response()->json(["error" => 'Pretaisas nerastas'], 404);
        }
    }


    public function history(Request $request)
    {
        if (!$this->checkIfClassExist($request['type'])) {
            return response()->json(["error" => 'Pretaiso tipas nerastas'], 404);
        } else {
            $device = $this->instance::find($request['id']);
            if ($device != null) {
                return $device->getHistory();
            }
            return response()->json(["error" => 'Pretaiso istorija nerasta'], 404);
        }
    }

    protected function switchTypes($device)
    {
        if (is_a($this->instance, DevicesShortTerm::class)) {
            $newInstance = DevicesLongTerm::class;
        } elseif (is_a($this->instance, DevicesLongTerm::class)) {
            $newInstance = DevicesShortTerm::class;
        }

        $newDevice = $newInstance::create([
            'name' => $device->name,
            'code' => $device->code,
            'serialNumber' => $device->serialNumber,
            'amount' => $device->amount,
            'state' => $device->state,
            'action' => $device->action,
            'src' => $device->src,
            'user_id' => $device->user_id
        ]);
        $this->instance->destroy($device->id);
        return response()->json($newDevice, 200);
    }

    protected function checkIfClassExist(String $class)
    {
        $this->model = $this->directory . $class;

        if (class_exists($this->model)) {
            $this->instance = new $this->model;
            return true;
        }
        return false;
    }
}
