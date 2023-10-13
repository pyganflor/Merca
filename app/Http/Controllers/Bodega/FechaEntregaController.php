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
        $fincas = getAllFincas();

        return view('adminlte.gestion.bodega.fecha_entrega.inicio', [
            'url' => $request->getRequestUri(),
            'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
            'desde' => $desde,
            'hasta' => $hasta,
            'fincas' => $fincas,
        ]);
    }
    public function listar_reporte(Request $request)
    {
        if ($request->finca != 'T') {
            $listado = FechaEntrega::where('id_empresa', $request->finca)
                ->where('entrega', '>=', $request->desde)
                ->where('entrega', '<=', $request->hasta)
                ->orderBy('entrega')
                ->get();
        } else {
            $listado = FechaEntrega::where('entrega', '>=', $request->desde)
                ->where('entrega', '<=', $request->hasta)
                ->orderBy('entrega')->get();
        }
        $fincas = getAllFincas();

        return view('adminlte.gestion.bodega.fecha_entrega.partials.listado', [
            'listado' => $listado,
            'fincas' => $fincas,
        ]);
    }

    public function store_fecha(Request $request)
    {
        DB::beginTransaction();
        try {
            $model = new FechaEntrega();
            $model->desde = $request->desde;
            $model->hasta = $request->hasta;
            $model->entrega = $request->entrega;
            $model->id_empresa = $request->finca;
            $model->save();

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

    public function update_fecha(Request $request)
    {
        DB::beginTransaction();
        try {
            $model = FechaEntrega::find($request->id);
            $model->desde = $request->desde;
            $model->hasta = $request->hasta;
            $model->entrega = $request->entrega;
            $model->id_empresa = $request->finca;
            $model->save();

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

    public function eliminar_fecha(Request $request)
    {
        DB::beginTransaction();
        try {
            $model = FechaEntrega::find($request->id);
            $model->delete();

            $success = true;
            $msg = 'Se ha <b>ELIMINADO</b> la fecha de entrega correctamente';

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
    public function copiar_fechas(Request $request)
    {
        $fincas = getAllFincas();

        return view('adminlte.gestion.bodega.fecha_entrega.forms.copiar_fechas', [
            'fincas' => $fincas,
        ]);
    }

    public function store_copiar_fechas(Request $request)
    {
        DB::beginTransaction();
        try {
            foreach (json_decode($request->data_fincas) as $f) {
                foreach (json_decode($request->data_entregas) as $e) {
                    $entrega = FechaEntrega::find($e);
                    $existe = FechaEntrega::All()
                        ->where('entrega', $entrega->entrega)
                        ->where('id_empresa', $f)
                        ->first();
                    if ($existe == '') {
                        $model = new FechaEntrega();
                        $model->desde = $entrega->desde;
                        $model->hasta = $entrega->hasta;
                        $model->entrega = $entrega->entrega;
                        $model->id_empresa = $f;
                        $model->save();
                    }
                }
            }

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
