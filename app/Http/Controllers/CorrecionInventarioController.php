<?php

namespace yura\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use Picqer\Barcode\BarcodeGeneratorHTML;
use yura\Modelos\InventarioFrio;
use yura\Modelos\Submenu;

class CorrecionInventarioController extends Controller
{
    public function inicio(Request $request)
    {
        return view('adminlte.gestion.postcocecha.correcion_inventario.inicio', [
            'url' => $request->getRequestUri(),
            'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
        ]);
    }

    public function escanear_codigo(Request $request)
    {
        $barCode = new BarcodeGeneratorHTML();
        $inventario_frio = InventarioFrio::find($request->codigo);
        return view('adminlte.gestion.postcocecha.correcion_inventario.partials.escanear_codigo', [
            'inventario_frio' => $inventario_frio,
            'barCode' => $barCode,
            'consulta' => $request->consulta,
        ]);
    }

    public function corregir_inventario_selected(Request $request)
    {
        DB::beginTransaction();
        try {
            foreach (json_decode($request->data) as $d) {
                $inventario_frio = InventarioFrio::find($d->id_inv);
                $inventario_frio->disponibles = $d->ramos;
                if ($inventario_frio->disponibles == 0)
                    $inventario_frio->disponibilidad = 0;
                else
                    $inventario_frio->disponibilidad = 1;
                $inventario_frio->save();
            }

            $success = true;
            $msg = 'Se ha <b>CORREGIDO</b> el inventario correctamente';

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

    public function corregir_all_inventario(Request $request)
    {
        DB::beginTransaction();
        try {
            $ids_inventario = [];
            foreach (json_decode($request->data) as $d) {
                $ids_inventario[] = $d->id_inv;

                $inventario_frio = InventarioFrio::find($d->id_inv);
                $inventario_frio->disponibles = $d->ramos;
                if ($inventario_frio->disponibles == 0)
                    $inventario_frio->disponibilidad = 0;
                else
                    $inventario_frio->disponibilidad = 1;
                $inventario_frio->save();
            }
            $inventarios_faltante = InventarioFrio::whereNotIn('id_inventario_frio', $ids_inventario)
                ->where('disponibles', '>', 0)
                ->get();
            foreach ($inventarios_faltante as $item) {
                $item->disponibles = 0;
                $item->disponibilidad = 0;
                $item->save();
            }

            $success = true;
            $msg = 'Se ha <b>CORREGIDO</b> el inventario correctamente';

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
