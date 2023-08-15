<?php

namespace yura\Http\Controllers\Bodega;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use yura\Http\Controllers\Controller;
use yura\Modelos\FechaEntrega;
use yura\Modelos\Submenu;

class FechaEntregaController extends Controller
{
    public function inicio(Request $request)
    {
        $desde = opDiasFecha('+', 0, hoy());
        $hasta = opDiasFecha('+', 7, hoy());

        return view('adminlte.gestion.bodega.fecha_entrega.inicio', [
            'url' => $request->getRequestUri(),
            'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
            'desde' => $desde,
            'hasta' => $hasta,
        ]);
    }
    public function listar_reporte(Request $request)
    {
        $listado = FechaEntrega::orderBy('entrega')->get();

        return view('adminlte.gestion.bodega.fecha_entrega.partials.listado', [
            'listado' => $listado,
        ]);
    }

    public function store_fecha(Request $request)
    {
        DB::beginTransaction();
        try {
            $pedido = new FechaEntrega();
            $pedido->desde = $request->desde;
            $pedido->hasta = $request->hasta;
            $pedido->entrega = $request->entrega;
            $pedido->save();

            $success = true;
            $msg = 'Se ha <b>CREADO</b> la fecha de entrega correctamente';

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $success = false;
            $msg = '<div class="alert alert-danger text-center">' .
                '<p> Ha ocurrido un problema al guardar la informacion al sistema</p>' .
                '<p>' . $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine() . '</p>'
                . '</div>';
        }
        return [
            'success' => $success,
            'mensaje' => $msg,
        ];
    }
}
