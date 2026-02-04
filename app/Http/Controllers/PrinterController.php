<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PrinterController extends Controller
{
    
    public function status()
    {
        try {
            $res = Http::timeout(2)->get('http://localhost:3000/printer/status');
            return $res->json();
        } catch (\Exception $e) {
            return response()->json([
                'connected' => false,
                'message' => 'Servicio de impresión no disponible'
            ], 500);
        }
    }

    public function test()
    {
        try {
            $res = Http::post('http://localhost:3000/printer/test');
            return $res->json();
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'No se pudo imprimir'
            ], 500);
        }
    }

    public function printerStatus()
    {
        try {
            $response = Http::timeout(2)->get('http://localhost:3000/printer/status');

            return response()->json($response->json());
        } catch (\Exception $e) {
            return response()->json([
                'connected' => false,
                'message' => 'Servicio de impresión no disponible'
            ], 500);
        }
    }
}
