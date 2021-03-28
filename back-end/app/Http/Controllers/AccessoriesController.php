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
        if(request()->hasFile('src'))
        {
            $path = request()->file('src')->store('accessoriesImages', 'public');
        }

        $accessory = Accessories::create([
            'name' => request('name'),
            'amount' => request('amount'),
            'longTerm_id' => request('longTerm_id'),
            'shortTerm_id' => request('shortTerm_id'),
            'src' => $path

        ]);

        return response()->json(["data" => $accessory], 200) ;
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
        if( $accessory != null)
        {
            return response()->json(["data" => $accessory ], 200);
        }
        return response()->json(["error" => "aksesuaras nerastas"], 404);
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
        if( $accessory != null)
        {

            $this->validateData();
            $path = $accessory->src;
            if (request()->hasFile('src')) {

                if (Storage::disk('public')->exists($accessory->src, 9)) {
                    Storage::disk('public')->delete($accessory->src, 9);
                }
                $path = request()->file('src')->store('accessoriesImages', 'public');
            }

            $accessory->update([
                'name' => request('name'), 
                'amount' => request('amount'),
                'longTerm_id' => request('longTerm_id'),
                'shorTerm_id' => request('shorTerm_id'),
                'src' => $path
            ]);

            return response()->json(["data" => $accessory ], 200);
        }
        return response()->json(["error" => "aksesuaras nerastas"], 404);
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
        if( $accessory != null)
        {
            if(Storage::disk('public')->exists(substr($accessory->src, 9))){
                Storage::disk('public')->delete(substr($accessory->src, 9));
            }
            $accessory->destroy(request('id'));
            return response()->json(["data" => $accessory ], 204);
        }
        return response()->json(["error" => "aksesuaras nerastas"], 404);
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
