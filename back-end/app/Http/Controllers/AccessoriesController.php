<?php

namespace App\Http\Controllers;

use App\Models\Accessories;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AccessoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Accessories::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        $this->validateData();
        $path = null;

        if (request()->hasFile('src')) {
            $path = request()->file('src')->store('accessoriesImages', 'public');
        }

        $accessory = Accessories::create(array_merge($this->validateData(), ['src' => $path]));

        return response()->json(["data" => $accessory], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        $accessory = Accessories::find(request('id'));
        if ($accessory == null) {
            return response()->json(["error" => "aksesuaras nerastas"], 404);
        }
        return response()->json(["data" => $accessory], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update()
    {
        $accessory = Accessories::find(request('id'));

        if ($accessory == null) {
            return response()->json(["error" => "aksesuaras nerastas"], 404);
        }

        $this->validateData();
        $path = $accessory->src;

        if (request()->hasFile('src')) {

            if (Storage::disk('public')->exists($accessory->src)) {
                Storage::disk('public')->delete($accessory->src);
            }
            $path = request()->file('src')->store('accessoriesImages', 'public');
        }

        $accessory->update(array_merge($this->validateData(), ['src' => $path]));

        return response()->json(["data" => $accessory], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy()
    {
        $accessory = Accessories::find(request('id'));
        if ($accessory == null) {
            return response()->json(["error" => "aksesuaras nerastas"], 404);
        }

        if (Storage::disk('public')->exists($accessory->src)) {
            Storage::disk('public')->delete($accessory->src);
        }

        $accessory->destroy(request('id'));
        return response()->json(["data" => $accessory], 204);
    }


    protected function validateData()
    {
        return request()->validate([
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric',
            'longTerm_id' => ['nullable', 'required_without:shortTerm_id', 'exists:devices_long_terms,id'],
            'shortTerm_id' => ['nullable', 'required_without:longTerm_id', 'exists:devices_short_terms,id'],
            'src' => ['nullable']
        ]);
    }
}
