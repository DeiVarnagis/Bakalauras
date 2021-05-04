<?php

namespace App\Http\Controllers;

use App\Models\Inventorization;
use Illuminate\Http\Request;

class InventorizationController extends Controller
{
    public function store()
    {
       return response()->json(Inventorization::create($this->validateData()),201);
    }

    public function index()
    {
        return response()->json(Inventorization::where('inventorization_time', '>=', date('Y-m-d'))->get(), 200);
    }

    public function closest()
    {
        $time = Inventorization::where('inventorization_time', '>=', date('Y-m-d'))->min('inventorization_time');
        if($time == null)
        {
            return response()->json(['error' => "Inventorizacijos laikas nerastas"], 404);
        }
        return response()->json($time, 200);
    }

    public function delete()
    {
        $date = Inventorization::find(request('id'));
        if($date == null)
        {
            return response()->json(['error' => "Laikas nerastas"], 404);
        }
        $date->delete();
        return response()->json("Inventorizacijos laikas sėkmingai ištrintas", 204);
    }


    public function show()
    {
        $time = Inventorization::find(request('id'));
        if($time == null)
        {
            return response()->json(['error' => "Inventorizacijos laikas nerastas"], 404);
        }
        return response()->json($time, 200);
    }

    protected function validateData()
    {
        return request()->validate([
            'inventorization_time' => 'required|date|date_format:Y-m-d|after:yesterday'
        ]);
    }
}
