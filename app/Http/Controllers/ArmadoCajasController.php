<?php

namespace yura\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Picqer\Barcode\BarcodeGeneratorHTML;
use yura\Modelos\CajaFrio;
use yura\Modelos\DetalleCajaFrio;
use yura\Modelos\InventarioFrio;
use yura\Modelos\Submenu;

class ArmadoCajasController extends Controller
{
    public function inicio(Request $request)
    {
        return view('adminlte.gestion.postcocecha.armado_cajas.inicio', [
            'url' => $request->getRequestUri(),
            'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
        ]);
    }

    public function escanear_codigo(Request $request)
    {
        $barCode = new BarcodeGeneratorHTML();
        $inventario_frio = InventarioFrio::find($request->codigo);
        return view('adminlte.gestion.postcocecha.armado_cajas.partials.escanear_codigo', [
            'inventario_frio' => $inventario_frio,
            'barCode' => $barCode,
            'consulta' => $request->consulta,
        ]);
    }

    public function store_caja(Request $request)
    {
        DB::beginTransaction();
        try {
            $finca = getFincaActiva();
            $caja = new CajaFrio();
            $caja->nombre = espacios(mb_strtoupper($request->nombre));
            $caja->fecha = $request->fecha;
            $caja->tipo = $request->tipo;
            $caja->id_empresa = $finca;
            $caja->save();
            $caja = CajaFrio::All()
                ->last();

            foreach (json_decode($request->data) as $d) {
                $inventario_frio = InventarioFrio::find($d->id_inv);
                $detalle = new DetalleCajaFrio();
                $detalle->id_caja_frio = $caja->id_caja_frio;
                $detalle->id_inventario_frio = $d->id_inv;
                $detalle->ramos = $d->ramos;
                $detalle->id_variedad = $inventario_frio->id_variedad;
                $detalle->tallos_x_ramo = $inventario_frio->tallos_x_ramo;
                $detalle->longitud = $inventario_frio->clasificacion_ramo->nombre;
                $detalle->fecha = $inventario_frio->fecha;
                $detalle->finca_destino = $inventario_frio->finca_destino;
                $detalle->id_modulo = $inventario_frio->id_modulo;
                $detalle->save();

                $inventario_frio->disponibles -= $d->ramos;
                if ($inventario_frio->disponibles == 0)
                    $inventario_frio->disponibilidad = 0;
                $inventario_frio->save();
            }

            $success = true;
            $msg = 'Se ha <b>GUARDADO</b> la caja correctamente';

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
