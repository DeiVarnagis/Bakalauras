<?php

namespace App\Http\Controllers;

use App\Models\Accessories;
use App\Models\AccessoriesLend;
use App\Models\DevicesLongTerm;
use App\Models\DevicesShortTerm;
use App\Models\DevicesTransfer;
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

        $path = null;
        if (request()->hasFile('src')) {
            $path = request()->file('src')->store('devicesImages', 'public');
        }

        $this->instance::create(array_merge($this->instance->validateData($request), ['src' => $path, 'user_id' => auth()->user()->id]));

        return response()->json(["message" => "Prietaisas sėkmingai buvo sukurtas"], 201);
    }


    public function show(Request $request)
    {
        if (!$this->checkIfClassExist($request['type'])) {
            return response()->json(["error" => 'Pretaiso tipas nerastas'], 404);
        }

        $device = $this->instance::find($request['id']);
        if ($device == null) {
            return response()->json(["error" => 'Pretaisas nerastas'], 404);
        }

        if (auth()->user()->id != $device->user_id || !auth()->user()->admin) {
            $accessories = $device->DevicesLends()->first();
            
            if($accessories)
            {
                $device->accessories =  $accessories->lendAccessories()->get();
                return response()->json(["data" => $device], 200);
            }

            $device->accessories =  [];
            return response()->json(["data" => $device], 200);

        }

        $device->accessories = $device->Accessories();
        return response()->json(["data" => $device], 200);
    }


    public function getDeviceAccessories(Request $request)
    {
        if (!$this->checkIfClassExist($request['type'])) {
            return response()->json(["error" => 'Pretaiso tipas nerastas'], 404);
        }

        $device = $this->instance::find($request['id']);
        if ($device == null) {
            return response()->json(["error" => 'Pretaisas nerastas'], 404);
        }

        return response()->json(["data" => $device->Accessories()], 200);
    }

    public function update(Request $request)
    {
        if (!$this->checkIfClassExist($request['type'])) {
            return response()->json(["error" => 'Pretaiso tipas nerastas'], 404);
        }

        $device = $this->instance::find($request['id']);

        if ($device == null) {
            return response()->json(["error" => 'Pretaisas nerastas'], 404);
        }

        $path = $device->src;

        if (request()->hasFile('src')) {

            $this->deleteFileIfExist($path);
            $path = request()->file('src')->store('devicesImages', 'public');
        }

        if ($request['type'] !== $request['deviceType']) {
            if (is_a($this->instance, DevicesShortTerm::class)) {
                $newInstance = DevicesLongTerm::class;
            } else {
                $newInstance = DevicesShortTerm::class;
            }
            $newDevice = $newInstance::create(array_merge($this->instance->validateUpdate($request['id']), ['src' => $path, 'user_id' => auth()->user()->id]));
            $this->instance->destroy($device->id);
            return response()->json($newDevice, 200);
        }

        $device->update(array_merge($this->instance->validateUpdate($request['id']), ['src' => $path]));

        return response()->json($device, 200);
    }


    public function destroy(Request $request)
    {
        if (!$this->checkIfClassExist($request['type'])) {
            return response()->json('Pretaiso tipas nerastas', 404);
        }

        $this->instance = new $this->model;
        $device = $this->instance->find($request['id']);
        if ($device == null) {
            return response()->json(["error" => 'Pretaisas nerastas'], 404);
        }

        if (auth()->user()->id == $device->user_id || auth()->user()->admin) {
            $this->deleteFileIfExist($device->src);
            $device->destroy($request['id']);
            return response()->json($device, 204);
        }
        return response()->json(["error" => 'Šis prietaisas priklauso ne jums'], 400);
    }



    public function history(Request $request)
    {
        if (!$this->checkIfClassExist($request['type'])) {
            return response()->json(["error" => 'Pretaiso tipas nerastas'], 404);
        }
        $device = $this->instance::find($request['id']);
        if ($device == null) {
            return response()->json(["error" => 'Pretaiso istorija nerasta'], 404);
        }
        return $device->getHistory();
    }

    public function transferedDeviceInfo(Request $request)
    {

       /* if (!$this->checkIfClassExist($request['type'])) {
            return response()->json(["error" => 'Pretaiso tipas nerastas'], 404);
        }

        $device = $this->instance::find($request['device_id']);

        if ($device == null) {
            return response()->json(["error" => 'Pretaisas nerastas'], 404);
        }

        $accessories = AccessoriesLend::where('transfer_id', $request['transfer_id']);*/


        

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

    protected function deleteFileIfExist($src)
    {
        if (Storage::disk('public')->exists($src)) {
            Storage::disk('public')->delete($src);
        }
    }
}
