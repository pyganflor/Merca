<?php

namespace yura\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Picqer\Barcode\BarcodeGeneratorHTML;
use yura\Modelos\CajaFrio;
use yura\Modelos\DetalleCajaFrio;
use yura\Modelos\InventarioFrio;
use yura\Modelos\Submenu;

class InventarioCajasController extends Controller
{
    public function inicio(Request $request)
    {
        return view('adminlte.gestion.postcocecha.inventario_cajas.inicio', [
            'url' => $request->getRequestUri(),
            'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
        ]);
    }

    public function listar_reporte(Request $request)
    {
        $listado = CajaFrio::where('armada', 0)
            ->where('nombre', 'like', '%' . espacios(mb_strtoupper($request->busqueda)) . '%');
        if ($request->fecha != '')
            $listado = $listado->where('fecha', $request->fecha);
        $listado = $listado->orderBy('fecha')
            ->orderBy('nombre')
            ->get();
        return view('adminlte.gestion.postcocecha.inventario_cajas.partials.listado', [
            'listado' => $listado
        ]);
    }

    public function eliminar_caja(Request $request)
    {
        DB::beginTransaction();
        try {
            $caja = CajaFrio::find($request->caja);
            if ($request->devolver) {
                foreach ($caja->detalles as $det) {
                    $inventario = $det->inventario_frio;
                    if ($inventario != '') {
                        $inventario->disponibles += $det->ramos;
                        $inventario->disponibilidad = 1;
                        $inventario->save();
                    }
                }
            }

            $caja->delete();

            $success = true;
            $msg = 'Se ha <b>ELIMINADO</b> la caja correctamente';

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

    public function editar_caja(Request $request)
    {
        $caja = CajaFrio::find($request->caja);
        return view('adminlte.gestion.postcocecha.inventario_cajas.forms.editar_caja', [
            'caja' => $caja
        ]);
    }

    public function eliminar_detalle(Request $request)
    {
        DB::beginTransaction();
        try {
            $detalle = DetalleCajaFrio::find($request->det);
            if ($request->devolver) {
                $inventario = $detalle->inventario_frio;
                if ($inventario != '') {
                    $inventario->disponibles += $detalle->ramos;
                    $inventario->disponibilidad = 1;
                    $inventario->save();
                }
            }

            $detalle->delete();

            $success = true;
            $msg = 'Se ha <b>ELIMINADO</b> la FLOR de la CAJA correctamente';

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

    public function cambiar_caja(Request $request)
    {
        $detalle = DetalleCajaFrio::find($request->det);
        $cajas = CajaFrio::where('armada', 0)
            ->where('reservado', 0)
            ->where('id_caja_frio', '!=', $detalle->id_caja_frio)
            ->orderBy('nombre')
            ->get();

        return view('adminlte.gestion.postcocecha.inventario_cajas.forms.cambiar_caja', [
            'detalle' => $detalle,
            'cajas' => $cajas,
        ]);
    }

    public function store_cambiar_caja(Request $request)
    {
        DB::beginTransaction();
        try {
            $detalle = DetalleCajaFrio::find($request->det);
            if (isset($detalle)) {
                if ($request->ramos == $detalle->ramos) {
                    $existe_det = DetalleCajaFrio::All()
                        ->where('id_caja_frio', $request->caja)
                        ->where('id_inventario_frio', $detalle->id_inventario_frio)
                        ->first();
                    if ($existe_det != '') {
                        $existe_det->ramos += $request->ramos;
                        $existe_det->save();

                        $detalle->delete();
                    } else {
                        $detalle->id_caja_frio = $request->caja;
                        $detalle->save();
                    }
                } else {
                    $detalle->ramos -= $request->ramos;
                    $detalle->save();

                    $existe_det = DetalleCajaFrio::All()
                        ->where('id_caja_frio', $request->caja)
                        ->where('id_inventario_frio', $detalle->id_inventario_frio)
                        ->first();
                    if ($existe_det != '') {
                        $existe_det->ramos += $request->ramos;
                        $existe_det->save();
                    } else {
                        $new_detalle = new DetalleCajaFrio();
                        $new_detalle->id_caja_frio = $request->caja;
                        $new_detalle->id_inventario_frio = $detalle->id_inventario_frio;
                        $new_detalle->ramos = $request->ramos;
                        $new_detalle->id_variedad = $detalle->id_variedad;
                        $new_detalle->tallos_x_ramo = $detalle->tallos_x_ramo;
                        $new_detalle->longitud = $detalle->longitud;
                        $new_detalle->fecha = $detalle->fecha;
                        $new_detalle->finca_destino = $detalle->finca_destino;
                        $new_detalle->id_modulo = $detalle->id_modulo;
                        $new_detalle->save();
                    }
                }
            }

            $success = true;
            $msg = 'Se ha <b>CAMBIADO</b> la FLOR de CAJA correctamente';

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

    public function add_detalle(Request $request)
    {
        $caja = CajaFrio::find($request->caja);

        return view('adminlte.gestion.postcocecha.inventario_cajas.forms.add_detalle', [
            'caja' => $caja,
        ]);
    }

    public function escanear_codigo(Request $request)
    {
        $barCode = new BarcodeGeneratorHTML();
        $inventario_frio = InventarioFrio::find($request->codigo);
        return view('adminlte.gestion.postcocecha.inventario_cajas.forms.escanear_codigo', [
            'inventario_frio' => $inventario_frio,
            'barCode' => $barCode,
        ]);
    }

    public function update_caja(Request $request)
    {
        DB::beginTransaction();
        try {
            $caja = CajaFrio::find($request->caja);
            $caja->nombre = espacios(mb_strtoupper($request->nombre));
            $caja->fecha = $request->fecha;
            $caja->tipo = $request->tipo;
            $caja->save();

            foreach (json_decode($request->data) as $d) {
                $inventario_frio = InventarioFrio::find($d->id_inv);
                $existe_det = DetalleCajaFrio::All()
                    ->where('id_caja_frio', $request->caja)
                    ->where('id_inventario_frio', $d->id_inv)
                    ->first();
                if ($existe_det == '') {
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
                } else {
                    $existe_det->ramos += $d->ramos;
                    $existe_det->save();
                }

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
