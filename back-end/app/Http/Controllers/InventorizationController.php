<?php

namespace App\Http\Controllers;

use App\Models\Inventorization;
use Illuminate\Http\Request;

class InventorizationController extends Controller
{
    public function store()
    {
       return response()->json(Inventorization::create($this->validateData()),200);
    }


    public function index()
    {
        $time = Inventorization::min('inventorization_time');
        if($time == null)
        {
            return response()->json(['error' => "Inventorizacijos laikas nerastas"], 404);
        }
        return response()->json($time, 200);
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
            'inventorization_time' => 'required|date|date_format:Y-m-d|after:'. date('Y-m-d')
        ]);
    }
}
