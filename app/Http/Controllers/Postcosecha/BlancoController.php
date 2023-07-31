<?php

namespace yura\Http\Controllers\Postcosecha;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use yura\Http\Controllers\Controller;
use yura\Modelos\ClasificacionRamo;
use yura\Modelos\ConfiguracionEmpresa;
use yura\Modelos\Empaque;
use yura\Modelos\InventarioFrio;
use yura\Modelos\Planta;
use yura\Modelos\UnidadMedida;
use yura\Modelos\Variedad;
use Barryvdh\DomPDF\Facade as PDF;
use Picqer\Barcode\BarcodeGeneratorHTML;

class BlancoController extends Controller
{
    public function listar_blanco(Request $request)
    {
        $finca = getFincaActiva();

        $all_plantas = Planta::where('estado', 1);
        if ($request->planta != 'T')
            $all_plantas = $all_plantas->where('id_planta', $request->planta);
        $all_plantas = $all_plantas->orderBy('nombre')->get();

        $listado = [];
        foreach ($all_plantas as $p) {
            $variedades = DB::table('ciclo as c')
                ->join('variedad as v', 'v.id_variedad', '=', 'c.id_variedad')
                ->select('c.id_variedad', 'v.nombre')->distinct()
                ->where('c.estado', 1)
                ->where('v.estado', 1)
                ->where('v.id_planta', $p->id_planta)
                ->where('c.activo', 1)
                ->where('c.id_empresa', $finca)
                ->orderBy('nombre')
                ->get();
            $inventario = DB::table('inventario_frio as i')
                ->join('variedad as v', 'v.id_variedad', '=', 'i.id_variedad')
                ->select(DB::raw('sum(i.disponibles) as cantidad'))
                ->where('i.disponibilidad', 1)
                ->where('i.basura', 0)
                ->where('i.estado', 1)
                ->where('v.id_planta', $p->id_planta)
                ->where('i.id_empresa', $finca)
                ->get()[0]->cantidad;

            array_push($listado, [
                'planta' => $p,
                'variedades' => $variedades,
                'inventario' => $inventario,
            ]);
        }

        $clasificaciones_ramos = ClasificacionRamo::where('estado', 1)
            ->orderBy('nombre')
            ->get();

        $fincas = ConfiguracionEmpresa::get();

        return view('adminlte.gestion.postcocecha.ingreso_clasificacion.partials._blanco', [
            'fecha' => $request->fecha,
            'listado' => $listado,
            'clasificaciones_ramos' => $clasificaciones_ramos,
            'fincas' => $fincas,
        ]);
    }

    public function store_blanco(Request $request)
    {
        $finca = getFincaActiva();
        $model = InventarioFrio::All()
            ->where('fecha', $request->fecha)
            ->where('id_variedad', $request->variedad)
            ->where('id_modulo', $request->modulo)
            ->where('id_clasificacion_ramo', $request->clasificacion_ramo)
            ->where('finca_destino', $request->finca_destino)
            ->where('tallos_x_ramo', $request->tallos_x_ramo)
            ->where('basura', 0)
            ->where('disponibilidad', 1)
            ->where('id_empresa', $finca)
            ->first();
        if ($model == '') {
            $model = new InventarioFrio();
            $model->fecha = $request->fecha;
            $model->cantidad = $request->cantidad;
            $model->disponibles = $request->cantidad;
            $model->id_variedad = $request->variedad;
            $model->id_modulo = $request->modulo != '' ? $request->modulo : -1;
            $model->id_clasificacion_ramo = $request->clasificacion_ramo;
            $model->finca_destino = $request->finca_destino;
            $model->tallos_x_ramo = $request->tallos_x_ramo;
            $model->disponibilidad = 1;
            $model->basura = 0;
            $model->id_empresa = $finca;
        } else {
            $model->cantidad += $request->cantidad;
            $model->disponibles += $request->cantidad;
        }

        $model->save();

        return [
            'success' => true,
            'mensaje' => 'Se han <strong>guardado</strong> los ramos satisfactoriamente',
        ];
    }

    public function buscar_inventario(Request $request)
    {
        $finca = getFincaActiva();
        $v = DB::table('inventario_frio')
            ->select(DB::raw('sum(disponibles) as cantidad'))
            ->where('disponibilidad', 1)
            ->where('basura', 0)
            ->where('estado', 1)
            ->where('id_variedad', $request->variedad)
            ->where('id_modulo', $request->modulo)
            ->where('id_clasificacion_ramo', $request->clasificacion_ramo)
            //->where('id_empaque', $request->empaque)
            ->where('finca_destino', $request->finca_destino)
            ->where('tallos_x_ramo', $request->tallos_x_ramo)
            ->where('id_empresa', $finca)
            ->get()[0]->cantidad;

        $p = DB::table('inventario_frio as i')
            ->join('variedad as v', 'v.id_variedad', '=', 'i.id_variedad')
            ->select(DB::raw('sum(i.disponibles) as cantidad'))
            ->where('i.disponibilidad', 1)
            ->where('i.basura', 0)
            ->where('i.estado', 1)
            ->where('v.id_planta', $request->planta)
            ->where('i.id_empresa', $finca)
            ->get()[0]->cantidad;

        return [
            'variedad' => $v,
            'planta' => number_format($p),
        ];
    }

    public function inventario_frio(Request $request)
    {
        $finca = getFincaActiva();

        $listado = InventarioFrio::join('variedad as v', 'v.id_variedad', '=', 'inventario_frio.id_variedad')
            ->select('inventario_frio.*')->distinct()
            ->where('v.id_planta', $request->planta)
            ->where('inventario_frio.id_empresa', $finca)
            ->where('inventario_frio.basura', 0)
            ->where('inventario_frio.disponibilidad', 1)
            ->orderBy('inventario_frio.fecha', 'asc')
            ->orderBy('v.nombre')
            ->get();

        $p = DB::table('inventario_frio as i')
            ->join('variedad as v', 'v.id_variedad', '=', 'i.id_variedad')
            ->select(DB::raw('sum(i.disponibles) as cantidad'))
            ->where('i.disponibilidad', 1)
            ->where('i.basura', 0)
            ->where('i.estado', 1)
            ->where('v.id_planta', $request->planta)
            ->where('i.id_empresa', $finca)
            ->get()[0]->cantidad;

        return view('adminlte.gestion.postcocecha.ingreso_clasificacion.forms._inventario', [
            'listado' => $listado,
            'planta' => $request->planta,
            'total_planta' => $p != '' ? $p : 0,
        ]);
    }

    public function update_inventario(Request $request)
    {
        $model = InventarioFrio::find($request->id);
        $model->disponibles = $request->disponibles;
        $model->cantidad = $request->disponibles;
        $model->save();

        return [
            'success' => true,
            'mensaje' => 'Se han <strong>modificado</strong> los ramos satisfactoriamente',
        ];
    }

    public function botar_inventario(Request $request)
    {
        $model = InventarioFrio::find($request->id);
        $model->disponibles = 0;
        $model->disponibilidad = 0;
        $model->basura = 1;
        $model->save();

        return [
            'success' => true,
            'mensaje' => 'Se han <strong>botado</strong> los ramos satisfactoriamente',
        ];
    }

    public function buscar_modulos(Request $request)
    {
        $finca = $request->finca;
        $modulos = DB::table('ciclo as c')
            ->join('modulo as m', 'm.id_modulo', '=', 'c.id_modulo')
            ->join('sector as s', 's.id_sector', '=', 'm.id_sector')
            ->select('c.id_modulo', 'm.nombre', 's.nombre as nombre_sector')->distinct()
            ->where('c.activo', 1)
            ->where('c.id_variedad', $request->variedad)
            ->where('c.id_empresa', $finca)
            ->orderBy('s.nombre')
            ->orderBy('m.nombre')
            ->get();

        $options = '';
        foreach ($modulos as $mod)
            $options .= '<option value="' . $mod->id_modulo . '">' . $mod->nombre_sector . '</option>';

        return [
            'options' => $options,
            'variedad' => Variedad::find($request->variedad),
        ];
    }

    public function ver_pdf_etiquetas(Request $request)
    {
        set_time_limit(600);
        ini_set('memory_limit', '-1');
        $barCode = new BarcodeGeneratorHTML();
        $finca = getFincaActiva();
        $variedad = Variedad::find($request->variedad);
        $longitud = ClasificacionRamo::find($request->clasificacion_ramo);
        $finca_destino = ConfiguracionEmpresa::find($request->finca_destino);
        $tallos_x_ramo = $request->tallos_x_ramo;
        $cantidad = $request->cantidad;
        $fecha = $request->fecha;

        $inventario_frio = InventarioFrio::All()
            ->where('fecha', $request->fecha)
            ->where('id_variedad', $request->variedad)
            ->where('id_modulo', $request->modulo)
            ->where('id_clasificacion_ramo', $request->clasificacion_ramo)
            ->where('finca_destino', $request->finca_destino)
            ->where('tallos_x_ramo', $request->tallos_x_ramo)
            ->where('basura', 0)
            ->where('disponibilidad', 1)
            ->where('id_empresa', $finca)
            ->first();

        $datos = [
            'variedad' => $variedad,
            'inventario_frio' => $inventario_frio,
            'longitud' => $longitud,
            'tallos_x_ramo' => $tallos_x_ramo,
            'cantidad' => $cantidad,
            'finca' => $finca_destino,
            'fecha' => $fecha,
        ];

        return PDF::loadView('adminlte.gestion.postcocecha.ingreso_clasificacion.partials.pdf_etiqueta', compact('datos', 'barCode'))
            ->setPaper(array(0, 0, 360, 360), 'landscape')->stream();
    }

    public function store_all_blanco(Request $request)
    {
        DB::beginTransaction();
        $data = [];
        try {
            $finca = getFincaActiva();
            foreach (json_decode($request->data) as $d) {
                $model = InventarioFrio::All()
                    ->where('fecha', $request->fecha)
                    ->where('id_variedad', $d->variedad)
                    ->where('id_modulo', $d->modulo)
                    ->where('id_clasificacion_ramo', $d->clasificacion_ramo)
                    ->where('finca_destino', $d->finca_destino)
                    ->where('tallos_x_ramo', $d->tallos_x_ramo)
                    ->where('basura', 0)
                    ->where('disponibilidad', 1)
                    ->where('id_empresa', $finca)
                    ->first();
                if ($model == '') {
                    $model = new InventarioFrio();
                    $model->fecha = $request->fecha;
                    $model->cantidad = $d->cantidad;
                    $model->disponibles = $d->cantidad;
                    $model->id_variedad = $d->variedad;
                    $model->id_modulo = $d->modulo != '' ? $d->modulo : -1;
                    $model->id_clasificacion_ramo = $d->clasificacion_ramo;
                    $model->finca_destino = $d->finca_destino;
                    $model->tallos_x_ramo = $d->tallos_x_ramo;
                    $model->disponibilidad = 1;
                    $model->basura = 0;
                    $model->id_empresa = $finca;
                    $model->save();
                    $model = InventarioFrio::All()->last();
                    $data[] = $model->id_inventario_frio . '|' . $d->cantidad;
                } else {
                    $model->cantidad += $d->cantidad;
                    $model->disponibles += $d->cantidad;
                    $model->save();
                    $data[] = $model->id_inventario_frio . '|' . $d->cantidad;
                }
            }

            $success = true;
            $msg = 'Se han <strong>guardado</strong> los ramos satisfactoriamente';

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
            'data' => $data,
        ];
    }

    public function ver_all_pdf_etiquetas(Request $request)
    {
        set_time_limit(600);
        ini_set('memory_limit', '-1');
        $barCode = new BarcodeGeneratorHTML();
        $datos = [];
        foreach (explode(',', $request->data) as $d) {
            $id = explode('|', $d)[0];
            $cantidad = explode('|', $d)[1];
            $model = InventarioFrio::find($id);
            $variedad = Variedad::find($model->id_variedad);
            $longitud = ClasificacionRamo::find($model->id_clasificacion_ramo);
            $finca = ConfiguracionEmpresa::find($model->finca_destino);
            $tallos_x_ramo = $model->tallos_x_ramo;
            //$cantidad = $cantidad;
            $fecha = $model->fecha;
            $datos[] = [
                'variedad' => $variedad,
                'inventario_frio' => $model,
                'longitud' => $longitud,
                'tallos_x_ramo' => $tallos_x_ramo,
                'cantidad' => $cantidad,
                'finca' => $finca,
                'fecha' => $fecha,
            ];
        }
        return PDF::loadView('adminlte.gestion.postcocecha.ingreso_clasificacion.partials.all_pdf_etiqueta', compact('datos', 'barCode'))
            ->setPaper(array(0, 0, 360, 360), 'landscape')->stream();
    }

    public function view_pdf_inventario(Request $request)
    {
        set_time_limit(600);
        ini_set('memory_limit', '-1');
        $barCode = new BarcodeGeneratorHTML();
        $id = $request->inventario;
        $cantidad = $request->cantidad;
        $model = InventarioFrio::find($id);
        $variedad = Variedad::find($model->id_variedad);
        $longitud = ClasificacionRamo::find($model->id_clasificacion_ramo);
        $finca = ConfiguracionEmpresa::find($model->finca_destino);
        $tallos_x_ramo = $model->tallos_x_ramo;
        $cantidad = $cantidad;
        $fecha = $model->fecha;
        $datos = [
            'inventario_frio' => $model,
            'variedad' => $variedad,
            'longitud' => $longitud,
            'tallos_x_ramo' => $tallos_x_ramo,
            'cantidad' => $cantidad,
            'finca' => $finca,
            'fecha' => $fecha,
        ];
        return PDF::loadView('adminlte.gestion.postcocecha.ingreso_clasificacion.partials.pdf_etiqueta', compact('datos', 'barCode'))
            ->setPaper(array(0, 0, 360, 360), 'landscape')->stream();
    }
}
