<?php

namespace yura\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use yura\Jobs\DeleteRecepciones;
use yura\Jobs\jobActualizarCicloByModulo;
use yura\Jobs\jobActualizarCosecha;
use yura\Jobs\jobActualizarFenogramaEjecucion;
use yura\Jobs\jobActualizarSemProyPerenne;
use yura\Jobs\jobResumenAreaSemanal;
use yura\Jobs\jobUpdateResumenTotalSemanalExportcalas;
use yura\Jobs\ProyeccionUpdateSemanal;
use yura\Jobs\ResumenSemanaCosecha;
use yura\Jobs\UpdateTallosCosechadosProyeccion;
use yura\Modelos\Apertura;
use yura\Modelos\Ciclo;
use yura\Modelos\ClasificacionVerde;
use yura\Modelos\ConfiguracionEmpresa;
use yura\Modelos\Cosecha;
use yura\Modelos\CosechaDiaria;
use yura\Modelos\CosechaPersonal;
use yura\Modelos\DesgloseRecepcion;
use yura\Modelos\Modulo;
use yura\Modelos\Planta;
use yura\Modelos\ProyeccionModuloSemana;
use yura\Modelos\Recepcion;
use yura\Modelos\ResumenTotalSemanalExportcalas;
use yura\Modelos\Semana;
use yura\Modelos\Submenu;
use yura\Modelos\Variedad;
use Validator;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Worksheet;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Fill;
use PHPExcel_Style_Border;
use PHPExcel_Style_Color;
use yura\Modelos\Cosechador;

class RecepcionController extends Controller
{
    public function inicio(Request $request)
    {
        return view('adminlte.gestion.postcocecha.recepciones.inicio', [
            'url' => $request->getRequestUri(),
            'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
        ]);
    }

    public function buscar_listado_recepcion(Request $request)
    {
        $finca = getFincaActiva();
        $listado = DesgloseRecepcion::join('recepcion as r', 'r.id_recepcion', '=', 'desglose_recepcion.id_recepcion')
            ->where('r.fecha_ingreso', $request->fecha)
            ->where('desglose_recepcion.id_empresa', $finca)
            ->get();
        $plantas = DB::table('ciclo as c')
            ->join('variedad as v', 'v.id_variedad', '=', 'c.id_variedad')
            ->join('planta as p', 'p.id_planta', '=', 'v.id_planta')
            ->select('v.id_planta', 'p.nombre')->distinct()
            ->where('c.estado', 1)
            ->where('v.estado', 1)
            ->where('p.estado', 1)
            ->where('c.activo', 1)
            ->where('c.id_empresa', $finca)
            ->orderBy('nombre')
            ->get();
        $cosechadores = Cosechador::where('id_empresa', $finca)
            ->orderBy('nombre')
            ->get();
        return view('adminlte.gestion.postcocecha.recepciones.partials.listado', [
            'listado' => $listado,
            'plantas' => $plantas,
            'cosechadores' => $cosechadores,
        ]);
    }

    public function add_recepcion(Request $request)
    {
        $finca = getFincaActiva();
        $plantas = DB::table('ciclo as c')
            ->join('variedad as v', 'v.id_variedad', '=', 'c.id_variedad')
            ->join('planta as p', 'p.id_planta', '=', 'v.id_planta')
            ->select('v.id_planta', 'p.nombre')->distinct()
            ->where('c.estado', 1)
            ->where('v.estado', 1)
            ->where('p.estado', 1)
            ->where('c.activo', 1)
            ->where('c.id_empresa', $finca)
            ->orderBy('nombre')
            ->get();
        $cosechadores = Cosechador::where('id_empresa', $finca)
            ->orderBy('nombre')
            ->get();
        return view('adminlte.gestion.postcocecha.recepciones.forms.add_recepcion', [
            'plantas' => $plantas,
            'cosechadores' => $cosechadores,
        ]);
    }

    public function select_variedad_recepcion(Request $request)
    {
        $finca_actual = getFincaActiva();
        $modulos = DB::table('ciclo as c')
            ->join('modulo as m', 'm.id_modulo', '=', 'c.id_modulo')
            ->join('sector as s', 's.id_sector', '=', 'm.id_sector')
            ->select('c.id_modulo', 'm.nombre', 's.nombre as nombre_sector')->distinct()
            ->where('c.activo', 1)
            ->where('c.id_variedad', $request->variedad)
            ->where('c.id_empresa', $finca_actual)
            ->orderBy('s.nombre')
            ->orderBy('m.nombre')
            ->get();

        $options = '';
        foreach ($modulos as $mod)
            $options .= '<option value="' . $mod->id_modulo . '">' . $mod->nombre_sector . ': ' . $mod->nombre . '</option>';

        return [
            'options_modulos' => $options,
            'variedad' => Variedad::find($request->variedad),
        ];
    }

    public function store_recepcion(Request $request)
    {
        DB::beginTransaction();
        try {
            $finca = getFincaActiva();
            $cosecha = Cosecha::All()
                ->where('fecha_ingreso', $request->fecha)
                ->first();
            if ($cosecha == '') {
                $cosecha = new Cosecha();
                $cosecha->fecha_ingreso = $request->fecha;
                $cosecha->personal = 1;
                $cosecha->hora_inicio = '08:00';
                $cosecha->fecha_registro = date('Y-m-d H:i:s');
                $cosecha->save();
                $cosecha = Cosecha::All()->last();
            }
            $recepcion = new Recepcion();
            $recepcion->id_semana = getSemanaByDate($request->fecha)->id_semana;
            $recepcion->id_cosecha = $cosecha->id_cosecha;
            $recepcion->fecha_ingreso = $request->fecha;
            $recepcion->fecha_registro = date('Y-m-d H:i:s');
            $recepcion->save();
            $recepcion = Recepcion::All()->last();
            foreach ($request->data as $d) {
                $desglose = new DesgloseRecepcion();
                $desglose->id_variedad = $d['variedad'];
                $desglose->id_modulo = $d['modulo'];
                $desglose->tallos_x_malla = $d['tallos_x_malla'];
                $desglose->cantidad_mallas = $d['mallas'];
                $desglose->id_cosechador = $d['cosechador'];
                $desglose->id_recepcion = $recepcion->id_recepcion;
                $desglose->fecha_registro = date('Y-m-d H:i:s');
                $desglose->id_empresa = $finca;
                $desglose->save();

                /* ======= ACTUALIZAR LA TABLA COSECHA_DIARIA ========== */
                jobActualizarCosecha::dispatch($d['variedad'], substr($recepcion->fecha_ingreso, 0, 10), $finca, $d['modulo'])
                    ->onQueue('proy_cosecha');
            }

            $success = true;
            $msg = 'Se ha <strong>GUARDADO</strong> la cosecha correctamente';

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

    public function update_desglose(Request $request)
    {
        DB::beginTransaction();
        try {
            $desglose = DesgloseRecepcion::find($request->id);
            $desglose->id_variedad = $request->variedad;
            $desglose->id_modulo = $request->modulo;
            $desglose->tallos_x_malla = $request->tallos_x_malla;
            $desglose->cantidad_mallas = $request->mallas;
            $desglose->id_cosechador = $request->cosechador;
            $desglose->save();

            /* ======= ACTUALIZAR LA TABLA COSECHA_DIARIA ========== */
            jobActualizarCosecha::dispatch($request->variedad, substr($desglose->recepcion->fecha_ingreso, 0, 10), getFincaActiva(), $request->modulo)
                ->onQueue('proy_cosecha');

            $success = true;
            $msg = 'Se ha <strong>GUARDADO</strong> la cosecha correctamente';

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

    public function delete_desglose(Request $request)
    {
        try {
            DB::beginTransaction();
            $desglose = DesgloseRecepcion::find($request->id);
            $variedad = $desglose->id_variedad;
            $id_modulo = $desglose->id_modulo;
            $fecha = substr($desglose->recepcion->fecha_ingreso, 0, 10);
            $desglose->delete();

            /* ======= ACTUALIZAR LA TABLA COSECHA_DIARIA ========== */
            jobActualizarCosecha::dispatch($variedad, $fecha, getFincaActiva(), $id_modulo)
                ->onQueue('proy_cosecha');

            $success = true;
            $msg = 'Se ha <strong>ELIMINADO</strong> la cosecha correctamente';

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
