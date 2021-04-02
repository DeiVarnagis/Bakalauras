<?php

namespace App\Http\Controllers;

use App\Models\User;
use Dompdf\Dompdf;
use Illuminate\Support\Facades\Response;

class PdfController extends Controller
{
    public function downloadPdf()
    {
        $user = User::find(request('id'));
        if ($user == null) {
            return response()->json(['error' => "Vartotojas nerastas"], 404);
        }

        if (request('type') == 1) {
            $data =  $user->DevicesLongTerm()->get();
        } 
        
        if (request('type') == 2) {
            $data =  $user->DevicesShortTerm()->get();
        }

        $dompdf = new Dompdf();

        $dompdf->loadHtml(view('invoice', ['data' => $data, 'user' => $user, 'type' => request('type')]), 'UTF-8');

        $dompdf->render();

        $response = Response::make($dompdf->output(), 200);
        $response->header('Content-Type', 'application/pdf');
        return $response;
    }
}
