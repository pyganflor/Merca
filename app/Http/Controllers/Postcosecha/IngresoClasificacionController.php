<?php

namespace yura\Http\Controllers\Postcosecha;

use Illuminate\Http\Request;
use yura\Http\Controllers\Controller;
use yura\Modelos\Planta;
use yura\Modelos\Submenu;

class IngresoClasificacionController extends Controller
{
    public function inicio(Request $request)
    {
        $plantas = Planta::where('estado', 1)
            ->orderBy('nombre')
            ->get();
        return view('adminlte.gestion.postcocecha.ingreso_clasificacion.inicio', [
            'url' => $request->getRequestUri(),
            'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
            'plantas' => $plantas,
        ]);
    }
}
