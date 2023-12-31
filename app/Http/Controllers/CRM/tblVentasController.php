<?php

namespace yura\Http\Controllers\CRM;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use yura\Http\Controllers\Controller;
use yura\Modelos\HistoricoVentas;
use yura\Modelos\Pais;
use yura\Modelos\Submenu;
use yura\Modelos\Planta;

class tblVentasController extends Controller
{
    public function inicio(Request $request)
    {
        $annos = DB::table('historico_ventas')
            ->select('anno')->distinct()
            ->orderBy('anno')
            ->get();
        $plantas = Planta::where('estado', 1)
            ->get();
        $semana_pasada = getSemanaByDate(opDiasFecha('-', 7, hoy()));

        return view('adminlte.crm.tbl_ventas.inicio', [
            'annos' => $annos,
            'plantas' => $plantas,
            'semana_pasada' => $semana_pasada,
            'clientes' => getClientes(),

            'url' => $request->getRequestUri(),
            'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
        ]);
    }

    public function filtrar_tablas(Request $request)
    {
        $finca = getFincaActiva();
        if ($request->annos == '')
            $annos = [date('Y')];
        else
            $annos = explode(' - ', $request->annos);

        $listado = [];
        if ($request->rango == 'S') { // SEMANAL
            if ($request->desde_semanal != '' && $request->hasta_semanal != '') {
                $listado_annos = [];
                foreach ($annos as $a) {
                    $request->desde_semanal = strlen($request->desde_semanal) < 2 ? '0' . $request->desde_semanal : $request->desde_semanal;
                    $request->desde_semanal = substr($a, 2, 2) . $request->desde_semanal;
                    $request->hasta_semanal = strlen($request->hasta_semanal) < 2 ? '0' . $request->hasta_semanal : $request->hasta_semanal;
                    $request->hasta_semanal = substr($a, 2, 2) . $request->hasta_semanal;
                    $semanas = getSemanasByCodigos($request->desde_semanal, $request->hasta_semanal);
                    $listado_annos[] = [
                        'anno' => $a,
                        'semanas' => $semanas,
                    ];
                }

                if ($request->tipo_listado == 'C') {    // por clientes
                    $view = 'semanal_clientes';
                    $clientes = DB::table('historico_ventas as h')
                        ->join('detalle_cliente as c', 'c.id_cliente', '=', 'h.id_cliente')
                        ->select('h.id_cliente', 'c.nombre')->distinct()
                        ->where('h.id_empresa', $finca)
                        ->where('c.estado', 1);
                    if ($request->cliente != 'T')
                        $clientes = $clientes->where('h.id_cliente', $request->cliente);
                    $clientes = $clientes->orderBy('c.nombre')
                        ->get();
                    foreach ($clientes as $c) {
                        $valores_anno = [];
                        foreach ($listado_annos as $a) {
                            $valores_semanas = [];
                            foreach ($a['semanas'] as $sem) {
                                $query = DB::table('historico_ventas as h')
                                    ->join('variedad as v', 'v.id_variedad', '=', 'h.id_variedad')
                                    ->select(
                                        DB::raw('sum(h.monto) as dinero'),
                                        DB::raw('sum(h.ramos) as ramos'),
                                        DB::raw('sum(h.tallos) as tallos'),
                                    )
                                    ->where('h.id_cliente', $c->id_cliente)
                                    ->where('h.id_empresa', $finca)
                                    ->where('h.semana', $sem->codigo);
                                if ($request->planta != 'T')
                                    $query = $query->where('v.id_planta', $request->planta);
                                if ($request->variedad != 'T')
                                    $query = $query->where('h.id_variedad', $request->variedad);
                                $query = $query->get()[0];

                                $valor = 0;
                                if ($request->criterio == 'D') {
                                    $valor = round($query->dinero, 2);
                                }
                                if ($request->criterio == 'R') {
                                    $valor = $query->ramos;
                                }
                                if ($request->criterio == 'T') {
                                    $valor = $query->tallos;
                                }
                                if ($request->criterio == 'P') {
                                    $valor = $query->tallos > 0 ? round($query->dinero / $query->tallos, 2) : 0;
                                }

                                $valores_semanas[] = [
                                    'semana' => $sem->codigo,
                                    'valor' => $valor != '' ? $valor : 0,
                                ];
                            }
                            $valores_anno[] = [
                                'anno' => $a['anno'],
                                'valores_semanas' => $valores_semanas
                            ];
                        }
                        $listado[] = [
                            'cliente' => $c,
                            'valores_anno' => $valores_anno
                        ];
                    }
                } else {    // por flores
                    $view = 'semanal_flores';
                    $plantas = DB::table('historico_ventas as h')
                        ->join('variedad as v', 'v.id_variedad', '=', 'h.id_variedad')
                        ->join('planta as p', 'p.id_planta', '=', 'v.id_planta')
                        ->select('v.id_planta', 'p.nombre')->distinct()
                        ->where('h.id_empresa', $finca);
                    if ($request->planta != 'T')
                        $plantas = $plantas->where('v.id_planta', $request->planta);
                    $plantas = $plantas->orderBy('p.nombre')
                        ->get();
                    foreach ($plantas as $p) {
                        $valores_anno = [];
                        foreach ($listado_annos as $a) {
                            $valores_semanas = [];
                            foreach ($a['semanas'] as $sem) {
                                $query = DB::table('historico_ventas as h')
                                    ->join('variedad as v', 'v.id_variedad', '=', 'h.id_variedad')
                                    ->select(
                                        DB::raw('sum(h.monto) as dinero'),
                                        DB::raw('sum(h.ramos) as ramos'),
                                        DB::raw('sum(h.tallos) as tallos')
                                    )
                                    ->where('v.id_planta', $p->id_planta)
                                    ->where('h.id_empresa', $finca)
                                    ->where('h.semana', $sem->codigo);
                                if ($request->cliente != 'T')
                                    $query = $query->where('h.id_cliente', $request->cliente);
                                if ($request->variedad != 'T')
                                    $query = $query->where('h.id_variedad', $request->variedad);
                                $query = $query->get()[0];

                                $valor = 0;
                                if ($request->criterio == 'D') {
                                    $valor = round($query->dinero, 2);
                                }
                                if ($request->criterio == 'R') {
                                    $valor = $query->ramos;
                                }
                                if ($request->criterio == 'T') {
                                    $valor = $query->tallos;
                                }
                                if ($request->criterio == 'P') {
                                    $valor = $query->tallos > 0 ? round($query->dinero / $query->tallos, 2) : 0;
                                }

                                $valores_semanas[] = [
                                    'semana' => $sem->codigo,
                                    'valor' => $valor != '' ? $valor : 0,
                                ];
                            }
                            if (count($valores_semanas))
                                $valores_anno[] = [
                                    'anno' => $a['anno'],
                                    'valores_semanas' => $valores_semanas
                                ];
                        }
                        if (count($valores_anno))
                            $listado[] = [
                                'planta' => $p,
                                'valores_anno' => $valores_anno
                            ];
                    }
                }
            } else {
                return '<div class="alert alert-warning text-center">Las Semanas "desde" y "hasta" son incorrectas.</div>';
            }
        } else {    // MENSUAL
            //dd($request->all());
            if ($request->desde_mensual != '' && $request->hasta_mensual != '') {
                $listado_annos = [];
                foreach ($annos as $a) {
                    $meses = [];
                    for ($m = $request->desde_mensual; $m <= $request->hasta_mensual; $m++) {
                        $meses[] = strlen($m) == 1 ? '0' . $m : $m;
                    }
                    $listado_annos[] = [
                        'anno' => $a,
                        'meses' => $meses,
                    ];
                }

                if ($request->tipo_listado == 'C') {    // por clientes
                    $view = 'mensual_clientes';
                    $clientes = DB::table('historico_ventas as h')
                        ->join('detalle_cliente as c', 'c.id_cliente', '=', 'h.id_cliente')
                        ->select('h.id_cliente', 'c.nombre')->distinct()
                        //->where('h.id_empresa', $finca)
                        ->where('c.estado', 1);
                    if ($request->cliente != 'T')
                        $clientes = $clientes->where('h.id_cliente', $request->cliente);
                    $clientes = $clientes->orderBy('c.nombre')
                        ->get();
                    foreach ($clientes as $c) {
                        $valores_anno = [];
                        foreach ($listado_annos as $a) {
                            $valores_meses = [];
                            foreach ($a['meses'] as $mes) {
                                $query = DB::table('historico_ventas as h')
                                    ->join('variedad as v', 'v.id_variedad', '=', 'h.id_variedad')
                                    ->select(
                                        DB::raw('sum(h.monto) as dinero'),
                                        DB::raw('sum(h.ramos) as ramos'),
                                        DB::raw('sum(h.tallos) as tallos')
                                    )
                                    ->where('h.id_cliente', $c->id_cliente)
                                    //->where('h.id_empresa', $finca)
                                    ->where('h.mes', $mes);
                                if ($request->planta != 'T')
                                    $query = $query->where('v.id_planta', $request->planta);
                                if ($request->variedad != 'T')
                                    $query = $query->where('h.id_variedad', $request->variedad);
                                $query = $query->get()[0];

                                $valor = 0;
                                if ($request->criterio == 'D') {
                                    $valor = round($query->dinero, 2);
                                }
                                if ($request->criterio == 'R') {
                                    $valor = $query->ramos;
                                }
                                if ($request->criterio == 'T') {
                                    $valor = $query->tallos;
                                }
                                if ($request->criterio == 'P') {
                                    $valor = $query->tallos > 0 ? round($query->dinero / $query->tallos, 2) : 0;
                                }

                                $valores_meses[] = [
                                    'mes' => $mes,
                                    'valor' => $valor != '' ? $valor : 0,
                                ];
                            }
                            $valores_anno[] = [
                                'anno' => $a['anno'],
                                'valores_meses' => $valores_meses
                            ];
                        }
                        $listado[] = [
                            'cliente' => $c,
                            'valores_anno' => $valores_anno
                        ];
                    }
                } else {    // por flores
                    $view = 'mensual_flores';
                    $plantas = DB::table('historico_ventas as h')
                        ->join('variedad as v', 'v.id_variedad', '=', 'h.id_variedad')
                        ->join('planta as p', 'p.id_planta', '=', 'v.id_planta')
                        ->select('v.id_planta', 'p.nombre')->distinct()
                        /*->where('h.id_empresa', $finca)*/;
                    if ($request->planta != 'T')
                        $plantas = $plantas->where('v.id_planta', $request->planta);
                    $plantas = $plantas->orderBy('p.nombre')
                        ->get();
                    foreach ($plantas as $p) {
                        $valores_anno = [];
                        foreach ($listado_annos as $a) {
                            $valores_meses = [];
                            foreach ($a['meses'] as $mes) {
                                $query = DB::table('historico_ventas as h')
                                    ->join('variedad as v', 'v.id_variedad', '=', 'h.id_variedad')
                                    ->select(
                                        DB::raw('sum(h.monto) as dinero'),
                                        DB::raw('sum(h.ramos) as ramos'),
                                        DB::raw('sum(h.tallos) as tallos')
                                    )
                                    ->where('v.id_planta', $p->id_planta)
                                    //->where('h.id_empresa', $finca)
                                    ->where('h.mes', $mes);
                                if ($request->cliente != 'T')
                                    $query = $query->where('h.id_cliente', $request->cliente);
                                if ($request->variedad != 'T')
                                    $query = $query->where('h.id_variedad', $request->variedad);
                                $query = $query->get()[0];

                                $valor = 0;
                                if ($request->criterio == 'D') {
                                    $valor = round($query->dinero, 2);
                                }
                                if ($request->criterio == 'R') {
                                    $valor = $query->ramos;
                                }
                                if ($request->criterio == 'T') {
                                    $valor = $query->tallos;
                                }
                                if ($request->criterio == 'P') {
                                    $valor = $query->tallos > 0 ? round($query->dinero / $query->tallos, 2) : 0;
                                }

                                $valores_meses[] = [
                                    'mes' => $mes,
                                    'valor' => $valor != '' ? $valor : 0,
                                ];
                            }
                            if (count($valores_meses))
                                $valores_anno[] = [
                                    'anno' => $a['anno'],
                                    'valores_meses' => $valores_meses
                                ];
                        }
                        if (count($valores_anno))
                            $listado[] = [
                                'planta' => $p,
                                'valores_anno' => $valores_anno
                            ];
                    }
                }
            } else {
                return '<div class="alert alert-warning text-center">Los Meses "desde" y "hasta" son incorrectos.</div>';
            }
        }

        return view('adminlte.crm.tbl_ventas.partials.' . $view, [
            'listado' => $listado,
            'listado_annos' => $listado_annos,
            'criterio' => $request->criterio,
        ]);
    }

    public function select_planta_semanal(Request $request)
    {
        $finca = getFincaActiva();
        if ($request->annos == '')
            $annos = [date('Y')];
        else
            $annos = explode(' - ', $request->annos);

        $listado = [];
        if ($request->desde_semanal != '' && $request->hasta_semanal != '') {
            $listado_annos = [];
            foreach ($annos as $a) {
                $request->desde_semanal = strlen($request->desde_semanal) < 2 ? '0' . $request->desde_semanal : $request->desde_semanal;
                $request->desde_semanal = substr($a, 2, 2) . $request->desde_semanal;
                $request->hasta_semanal = strlen($request->hasta_semanal) < 2 ? '0' . $request->hasta_semanal : $request->hasta_semanal;
                $request->hasta_semanal = substr($a, 2, 2) . $request->hasta_semanal;
                $semanas = getSemanasByCodigos($request->desde_semanal, $request->hasta_semanal);
                $listado_annos[] = [
                    'anno' => $a,
                    'semanas' => $semanas,
                ];
            }

            $longitudes = DB::table('historico_ventas as h')
                ->join('variedad as v', 'v.id_variedad', '=', 'h.id_variedad')
                ->select('h.longitud')->distinct()
                ->where('v.id_planta', $request->planta)
                ->where('h.id_empresa', $finca)
                ->where('h.semana', '>=', $request->desde_semanal)
                ->where('h.semana', '<=', $request->hasta_semanal)
                ->where('h.tallos', '>', 0)
                ->orderBy('h.longitud', 'desc')
                ->get()->pluck('longitud')->toArray();

            foreach ($longitudes as $l) {
                $variedades = DB::table('historico_ventas as h')
                    ->join('variedad as v', 'v.id_variedad', '=', 'h.id_variedad')
                    ->select('h.id_variedad', 'v.nombre')->distinct()
                    ->where('v.id_planta', $request->planta)
                    ->where('h.longitud', $l)
                    ->where('h.id_empresa', $finca)
                    ->where('h.semana', '>=', $request->desde_semanal)
                    ->where('h.semana', '<=', $request->hasta_semanal)
                    ->where('h.tallos', '>', 0)
                    ->orderBy('v.nombre')
                    ->get();
                $valores_variedades = [];
                foreach ($variedades as $v) {
                    $valores_anno = [];
                    foreach ($listado_annos as $a) {
                        $valores_semanas = [];
                        foreach ($a['semanas'] as $sem) {
                            $query = DB::table('historico_ventas as h')
                                ->join('variedad as v', 'v.id_variedad', '=', 'h.id_variedad')
                                ->select(
                                    DB::raw('sum(h.monto) as dinero'),
                                    DB::raw('sum(h.ramos) as ramos'),
                                    DB::raw('sum(h.tallos) as tallos')
                                )
                                ->where('h.id_empresa', $finca)
                                ->where('v.id_planta', $request->planta)
                                ->where('h.id_variedad', $v->id_variedad)
                                ->where('h.longitud', $l)
                                ->where('h.semana', $sem->codigo);
                            if ($request->cliente != 'T')
                                $query = $query->where('h.id_cliente', $request->cliente);
                            $query = $query->get()[0];

                            $valor = 0;
                            if ($request->criterio == 'D') {
                                $valor = round($query->dinero, 2);
                            }
                            if ($request->criterio == 'R') {
                                $valor = $query->ramos;
                            }
                            if ($request->criterio == 'T') {
                                $valor = $query->tallos;
                            }
                            if ($request->criterio == 'P') {
                                $valor = $query->tallos > 0 ? round($query->dinero / $query->tallos, 2) : 0;
                            }
                            $valores_semanas[] = [
                                'semana' => $sem->codigo,
                                'valor' => $valor != '' ? $valor : 0,
                            ];
                        }
                        $valores_anno[] = [
                            'anno' => $a['anno'],
                            'valores_semanas' => $valores_semanas
                        ];
                    }
                    $valores_variedades[] = [
                        'variedad' => $v,
                        'valores_anno' => $valores_anno
                    ];
                }
                $listado[] = [
                    'longitud' => $l,
                    'valores_variedades' => $valores_variedades
                ];
            }
            return view('adminlte.crm.tbl_ventas.partials.detalles.select_planta_semanal', [
                'listado' => $listado,
                'listado_annos' => $listado_annos,
                'criterio' => $request->criterio,
                'planta' => Planta::find($request->planta),
            ]);
        } else {
            return '<div class="alert alert-warning text-center">Las Semanas "desde" y "hasta" son incorrectas.</div>';
        }
    }

    public function select_planta_diario(Request $request)
    {
        $finca = getFincaActiva();
        $fechas = DB::table('historico_ventas as h')
            ->join('variedad as v', 'v.id_variedad', '=', 'h.id_variedad')
            ->select('h.fecha')->distinct()
            ->where('v.id_planta', $request->planta)
            ->where('h.semana', $request->semana)
            ->where('h.id_empresa', $finca)
            ->orderBy('h.fecha')
            ->get()->pluck('fecha')->toArray();

        $longitudes = DB::table('historico_ventas as h')
            ->join('variedad as v', 'v.id_variedad', '=', 'h.id_variedad')
            ->select('h.longitud')->distinct()
            ->where('v.id_planta', $request->planta)
            ->where('h.semana', $request->semana)
            ->where('h.id_empresa', $finca)
            ->orderBy('h.longitud', 'desc')
            ->get()->pluck('longitud')->toArray();

        $listado = [];
        foreach ($longitudes as $l) {
            $variedades = DB::table('historico_ventas as h')
                ->join('variedad as v', 'v.id_variedad', '=', 'h.id_variedad')
                ->select('h.id_variedad', 'v.nombre')->distinct()
                ->where('v.id_planta', $request->planta)
                ->where('h.longitud', $l)
                ->where('h.id_empresa', $finca)
                ->where('h.semana', $request->semana)
                ->where('h.tallos', '>', 0)
                ->orderBy('v.nombre')
                ->get();
            $valores_variedades = [];
            foreach ($variedades as $v) {
                $valores_fechas = [];
                foreach ($fechas as $f) {
                    $query = DB::table('historico_ventas as h')
                        ->join('variedad as v', 'v.id_variedad', '=', 'h.id_variedad')
                        ->select(
                            DB::raw('sum(h.monto) as dinero'),
                            DB::raw('sum(h.ramos) as ramos'),
                            DB::raw('sum(h.tallos) as tallos')
                        )
                        ->where('h.id_empresa', $finca)
                        ->where('v.id_planta', $request->planta)
                        ->where('h.id_variedad', $v->id_variedad)
                        ->where('h.longitud', $l)
                        ->where('h.fecha', $f);
                    if ($request->cliente != 'T')
                        $query = $query->where('h.id_cliente', $request->cliente);
                    $query = $query->get()[0];

                    $valor = 0;
                    if ($request->criterio == 'D') {
                        $valor = round($query->dinero, 2);
                    }
                    if ($request->criterio == 'R') {
                        $valor = $query->ramos;
                    }
                    if ($request->criterio == 'T') {
                        $valor = $query->tallos;
                    }
                    if ($request->criterio == 'P') {
                        $valor = $query->tallos > 0 ? round($query->dinero / $query->tallos, 2) : 0;
                    }
                    $valores_fechas[] = [
                        'fecha' => $f,
                        'valor' => $valor != '' ? $valor : 0,
                    ];
                }
                $valores_variedades[] = [
                    'variedad' => $v,
                    'valores_fechas' => $valores_fechas
                ];
            }
            $listado[] = [
                'longitud' => $l,
                'valores_variedades' => $valores_variedades
            ];
        }
        return view('adminlte.crm.tbl_ventas.partials.detalles.select_planta_diario', [
            'listado' => $listado,
            'fechas' => $fechas,
            'criterio' => $request->criterio,
            'planta' => Planta::find($request->planta),
        ]);
    }

    /* ================= EXCEL ================= */

    public function exportar_tabla(Request $request)
    {
        //---------------------- EXCEL --------------------------------------
        $objPHPExcel = new PHPExcel;
        $objPHPExcel->getDefaultStyle()->getFont()->setName('Calibri');
        $objPHPExcel->getDefaultStyle()->getFont()->setSize(12);
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");
        $currencyFormat = '#,#0.## \€;[Red]-#,#0.## \€';
        $numberFormat = '#,#0.##;[Red]-#,#0.##';

        $objPHPExcel->removeSheetByIndex(0); //Eliminar la hoja inicial por defecto

        $this->excel_hoja($objPHPExcel, $request);

        //--------------------------- GUARDAR EL EXCEL -----------------------

        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header('Content-Disposition:inline;filename="Reporte tabla-ventas.xlsx"');
        header("Content-Transfer-Encoding: binary");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Pragma: no-cache");
        $objWriter->save('php://output');
    }

    public function excel_hoja($objPHPExcel, $request)
    {
        $columnas = [
            0 => 'A', 1 => 'B', 2 => 'C', 3 => 'D', 4 => 'E', 5 => 'F', 6 => 'G', 7 => 'H', 8 => 'I', 9 => 'J', 10 => 'K', 11 => 'L',
            12 => 'M', 13 => 'N', 14 => 'O', 15 => 'P', 16 => 'Q', 17 => 'R', 18 => 'S', 19 => 'T', 20 => 'U', 21 => 'V', 22 => 'W', 23 => 'X',
            24 => 'Y', 25 => 'Z', 26 => 'AA', 27 => 'AB', 28 => 'AC', 29 => 'AD', 30 => 'AE', 31 => 'AF', 32 => 'AG', 33 => 'AH', 34 => 'AI',
            35 => 'AJ', 36 => 'AK', 37 => 'AL', 38 => 'AM', 39 => 'AN', 40 => 'AO', 41 => 'AP', 42 => 'AQ', 43 => 'AR', 44 => 'AS', 45 => 'AT',
            46 => 'AU', 47 => 'AV', 48 => 'AW', 49 => 'AX', 50 => 'AY', 51 => 'AZ', 52 => 'BA', 53 => 'BB', 54 => 'BC', 55 => 'BD', 56 => 'BE',
            57 => 'BF', 58 => 'BG', 59 => 'BH', 60 => 'BI', 61 => 'BJ', 62 => 'BK', 63 => 'BL', 64 => 'BM', 65 => 'BN', 66 => 'BO', 67 => 'BP',
            68 => 'BQ', 69 => 'BR', 70 => 'BS', 71 => 'BT', 72 => 'BU', 73 => 'BV', 74 => 'BW', 75 => 'BX', 76 => 'BY', 77 => 'BZ'
        ];

        if ($request->annos == '')
            $annos = [date('Y')];
        else
            $annos = explode(' - ', $request->annos);

        if ($request->desde != '' && $request->hasta != '') {
            if ($request->rango == 'A') {   // Anual
                $view = 'anual';
                if ($request->desde >= 1 && $request->desde <= 12 && $request->hasta >= 1 && $request->hasta <= 12 && $request->desde <= $request->hasta) {
                    $data = $this->getTablasByRangoAnual($request->desde, $request->hasta, $request->cliente, $request->variedad, $annos, $request->criterio);
                } else {
                    return '<div class="alert alert-warning text-center">Los meses ingresados están incorrectos</div>';
                }
            } else {    // Mensual
                $view = 'mensual';
                if ($request->desde >= 1 && $request->desde <= 12 && $request->hasta >= 1 && $request->hasta <= 12 && $request->desde <= $request->hasta) {
                    $data = $this->getTablasByRangoMensual($request->desde, $request->hasta, $request->cliente, $request->variedad, $annos, $request->criterio, $request->acumulado);
                } else {
                    return '<div class="alert alert-warning text-center">Los meses ingresados están incorrectos</div>';
                }
            }

            ([
                'data' => $data,
                'acumulado' => $request->acumulado,
                'criterio' => $request->criterio,
                'cliente' => $request->cliente,
                'desde' => $request->desde,
                'hasta' => $request->hasta,
            ]);
            $criterios = ['V' => 'Valor', 'F' => 'Cajas Físicas', 'Q' => 'Cajas Equivalentes', 'P' => 'Precios'];
            $title_variedad = 'Acumulado';
            if ($request->variedad != 'A')
                $title_variedad = getVariedad($request->variedad)->siglas;
            $objSheet = new PHPExcel_Worksheet($objPHPExcel, $criterios[$request->criterio] . ' - ' . $title_variedad);
            $objPHPExcel->addSheet($objSheet, 0);
            if ($view == 'mensual') {
                /* ============== MERGE CELDAS =============*/
                $objSheet->mergeCells('A1:A2');

                /* ============== ENCABEZADO =============*/
                $objSheet->getCell('A1')->setValue($request->cliente == 'P' ? 'País' : 'Cliente');

                $array_totales = [];
                $array_subtotales = [];
                $pos_col = 1;
                foreach ($data['labels'] as $anno) {
                    $inicio = $pos_col;
                    array_push($array_subtotales, [
                        'valor' => 0,
                        'positivos' => 0,
                    ]);
                    foreach ($data['meses'] as $mes) {
                        array_push($array_totales, [
                            'valor' => 0,
                            'positivos' => 0,
                        ]);
                        /* ============== BACKGROUND COLOR =============*/
                        $objSheet->getStyle($columnas[$pos_col] . '2')
                            ->getFill()
                            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                            ->getStartColor()
                            ->setRGB('e9ecef');
                        $objSheet->getCell($columnas[$pos_col] . '2')->setValue($mes);      // <th> mes
                        $pos_col++;
                    }

                    if ($request->acumulado == 'false') {
                        /* ============== BACKGROUND COLOR =============*/
                        $objSheet->getStyle($columnas[$pos_col] . '2')
                            ->getFill()
                            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                            ->getStartColor()
                            ->setRGB('d2d6de');
                        $objSheet->getCell($columnas[$pos_col] . '2')->setValue('Subtotal');        // <th> subtotal
                        $pos_col++;
                    }

                    $objSheet->mergeCells($columnas[$inicio] . '1:' . $columnas[$pos_col - 1] . '1');

                    $objSheet->getCell($columnas[$inicio] . '1')->setValue($anno);      // <th> año
                }

                /* ============== MERGE CELDAS =============*/
                $objSheet->mergeCells($columnas[$pos_col] . '1:' . $columnas[$pos_col] . '2');
                /* ============== LETRAS NEGRITAS =============*/
                $objSheet->getStyle('A1:' . $columnas[$pos_col] . '2')->getFont()->setBold(true)->setSize(12);
                /* ============== CENTRAR =============*/
                $objSheet->getStyle('A1:' . $columnas[$pos_col] . intval(2 + count($data['filas']) + 1))
                    ->getAlignment()
                    ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
                    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                /* ============== BACKGROUND COLOR =============*/
                $objSheet->getStyle('A1:' . $columnas[$pos_col] . '1')
                    ->getFill()
                    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setRGB('357ca5');
                /* ============== TEXT COLOR =============*/
                $objSheet->getStyle('A1:' . $columnas[$pos_col] . '1')
                    ->getFont()
                    ->getColor()
                    ->setRGB('ffffff');
                /* ============== BORDE COLOR =============*/
                $objSheet->getStyle('A1:' . $columnas[$pos_col] . intval(2 + count($data['filas']) + 1))
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM)
                    ->getColor()
                    ->setRGB('000000');

                if ($request->acumulado == 'false') {
                    $objSheet->getCell($columnas[$pos_col] . '1')
                        ->setValue($request->criterio == 'V' || $request->criterio == 'F' || $request->criterio == 'Q' ? 'Total' : 'Promedio');
                }

                //--------------------------- LLENAR LA TABLA ---------------------------------------------
                $pos_fila = 3;
                foreach ($data['filas'] as $fila) {
                    if ($fila['encabezado'] != '')
                        $objSheet->getCell('A' . $pos_fila)
                            ->setValue($request->cliente == 'P' ? $fila['encabezado']->nombre : $fila['encabezado']->detalle()->nombre);
                    else
                        $objSheet->getCell('A' . $pos_fila)->setValue('Todos');

                    $col_fil = 0;
                    $col = 1;
                    $total_fila = 0;
                    $total_positivos = 0;
                    for ($a = 1; $a <= count($data['labels']); $a++) {  // for años
                        $subtotal = 0;
                        $positivos = 0;
                        for ($m = 1; $m <= count($data['meses']); $m++) {  // for meses
                            $objSheet->getCell($columnas[$col] . $pos_fila)->setValue(number_format($fila['valores'][$col_fil], 2));
                            $subtotal += $fila['valores'][$col_fil];
                            $array_totales[$col_fil]['valor'] += $fila['valores'][$col_fil];
                            if ($fila['valores'][$col_fil] > 0) {
                                $array_totales[$col_fil]['positivos']++;
                                $positivos++;
                                $total_positivos++;
                                $array_subtotales[$a - 1]['positivos']++;
                            }
                            $col_fil++;
                            $col++;
                        }
                        if ($request->acumulado == 'false') {
                            /* ============== LETRAS NEGRITAS =============*/
                            $objSheet->getStyle($columnas[$col] . $pos_fila)->getFont()->setBold(true)->setSize(12);
                            /* ============== BACKGROUND COLOR =============*/
                            $objSheet->getStyle($columnas[$col] . $pos_fila)
                                ->getFill()
                                ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                                ->getStartColor()
                                ->setRGB('d2d6de');

                            if ($request->criterio == 'V' || $request->criterio == 'F' || $request->criterio == 'Q') {
                                $objSheet->getCell($columnas[$col] . $pos_fila)->setValue(number_format($subtotal, 2)); // subtotal
                            } else {
                                $objSheet->getCell($columnas[$col] . $pos_fila)
                                    ->setValue(number_format($positivos > 0 ? round($subtotal / $positivos, 2) : 0, 2)); // subtotal
                            }
                            $array_subtotales[$a - 1]['valor'] += $subtotal;

                            $col++;
                        }
                        $total_fila += $subtotal;
                    }
                    if ($request->acumulado == 'false') {
                        /* ============== LETRAS NEGRITAS =============*/
                        $objSheet->getStyle($columnas[$col] . $pos_fila)->getFont()->setBold(true)->setSize(12);

                        if ($request->criterio == 'V' || $request->criterio == 'F' || $request->criterio == 'Q')
                            $objSheet->getCell($columnas[$col] . $pos_fila)->setValue(number_format($total_fila, 2)); // total fila
                        else
                            $objSheet->getCell($columnas[$col] . $pos_fila)
                                ->setValue(number_format($total_positivos > 0 ? round($total_fila / $total_positivos, 2) : 0, 2)); // total fila

                    }
                    $pos_fila++;
                }

                /* ---------------------------- FILA TOTALES ---------------------------- */
                /* ============== LETRAS NEGRITAS =============*/
                $objSheet->getStyle('A' . $pos_fila . ':' . $columnas[$pos_col] . $pos_fila)->getFont()->setBold(true)->setSize(12);
                /* ============== BACKGROUND COLOR =============*/
                $objSheet->getStyle('A' . $pos_fila . ':' . $columnas[$pos_col] . $pos_fila)->getFill()
                    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('357ca5');
                /* ============== TEXT COLOR =============*/
                $objSheet->getStyle('A' . $pos_fila . ':' . $columnas[$pos_col] . $pos_fila)->getFont()->getColor()->setRGB('ffffff');
                /* ============================================================================================================================ */
                $objSheet->getCell('A' . $pos_fila)
                    ->setValue($request->criterio == 'V' || $request->criterio == 'F' || $request->criterio == 'Q' ? 'Total' : 'Promedio');
                $col_fil = 0;
                $col = 1;
                $total_fila = 0;
                $total_positivos = 0;
                for ($a = 1; $a <= count($data['labels']); $a++) {  // for años
                    for ($m = 1; $m <= count($data['meses']); $m++) {  // for meses
                        if ($request->criterio == 'V' || $request->criterio == 'F' || $request->criterio == 'Q')
                            $objSheet->getCell($columnas[$col] . $pos_fila)
                                ->setValue(number_format($array_totales[$col_fil]['valor'], 2));
                        else
                            $objSheet->getCell($columnas[$col] . $pos_fila)
                                ->setValue(number_format($array_totales[$col_fil]['positivos'] > 0 ? round($array_totales[$col_fil]['valor'] / $array_totales[$col_fil]['positivos'], 2) : 0, 2));
                        $total_fila += $array_totales[$col_fil]['valor'];
                        $total_positivos += $array_totales[$col_fil]['positivos'];
                        $col_fil++;
                        $col++;
                    }
                    if ($request->acumulado == 'false') {
                        /* ============== LETRAS NEGRITAS =============*/
                        $objSheet->getStyle($columnas[$col] . $pos_fila)->getFont()->setBold(true)->setSize(12);
                        /* ============== BACKGROUND COLOR =============*/
                        $objSheet->getStyle($columnas[$col] . $pos_fila)->getFill()
                            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('d2d6de');
                        /* ============== TEXT COLOR =============*/
                        $objSheet->getStyle($columnas[$col] . $pos_fila)->getFont()->getColor()->setRGB('000000');

                        if ($request->criterio == 'V' || $request->criterio == 'F' || $request->criterio == 'Q')
                            $objSheet->getCell($columnas[$col] . $pos_fila)->setValue(number_format($array_subtotales[$a - 1]['valor'], 2)); // subtotal
                        else
                            $objSheet->getCell($columnas[$col] . $pos_fila)
                                ->setValue(number_format($array_subtotales[$a - 1]['positivos'] > 0 ? round($array_subtotales[$a - 1]['valor'] / $array_subtotales[$a - 1]['positivos'], 2) : 0, 2)); // subtotal

                        $col++;
                    }
                }

                if ($request->acumulado == 'false') {
                    /* ============== LETRAS NEGRITAS =============*/
                    $objSheet->getStyle($columnas[$col] . $pos_fila)->getFont()->setBold(true)->setSize(12);

                    if ($request->criterio == 'V' || $request->criterio == 'F' || $request->criterio == 'Q')
                        $objSheet->getCell($columnas[$col] . $pos_fila)->setValue(number_format($total_fila, 2)); // total fila
                    else
                        $objSheet->getCell($columnas[$col] . $pos_fila)
                            ->setValue(number_format($total_positivos > 0 ? round($total_fila / $total_positivos, 2) : 0, 2)); // total fila
                }

                /* ============== LETRAS NEGRITAS =============*/
                $objSheet->getStyle('A3:A' . intval(2 + count($data['filas'])))->getFont()->setBold(true)->setSize(12);
                /* ============== CENTRAR =============*/
                $objSheet->getStyle('A3:A' . intval(2 + count($data['filas'])))->getAlignment()
                    ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                /* ============== BACKGROUND COLOR =============*/
                $objSheet->getStyle('A3:A' . intval(2 + count($data['filas'])))->getFill()
                    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('e9ecef');

                /* ============== LETRAS NEGRITAS =============*/
                $objSheet->getStyle($columnas[$pos_col] . '3:' . $columnas[$pos_col] . intval(2 + count($data['filas'])))->getFont()->setBold(true)->setSize(12);
                /* ============== CENTRAR =============*/
                $objSheet->getStyle($columnas[$pos_col] . '3:' . $columnas[$pos_col] . intval(2 + count($data['filas'])))
                    ->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
                    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                /* ============== BACKGROUND COLOR =============*/
                $objSheet->getStyle($columnas[$pos_col] . '3:' . $columnas[$pos_col] . intval(2 + count($data['filas'])))
                    ->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('e9ecef');

                foreach ($columnas as $c) {
                    $objSheet->getColumnDimension($c)->setAutoSize(true);
                }
            } else {
                /* ============== ENCABEZADO =============*/
                $objSheet->getCell('A1')->setValue($request->cliente == 'P' ? 'País' : 'Cliente');

                $pos_col = 1;
                $array_totales = [];
                foreach ($data['labels'] as $anno) {
                    array_push($array_totales, [
                        'valor' => 0,
                        'positivos' => 0,
                    ]);
                    $objSheet->getCell($columnas[$pos_col] . '1')->setValue($anno);      // <th> año
                    $pos_col++;
                }
                $objSheet->getCell($columnas[$pos_col] . '1')
                    ->setValue($request->criterio == 'V' || $request->criterio == 'F' || $request->criterio == 'Q' ? 'Total' : 'Promedio');

                /* ============== LETRAS NEGRITAS =============*/
                $objSheet->getStyle('A1:' . $columnas[$pos_col] . '1')->getFont()->setBold(true)->setSize(12);
                /* ============== CENTRAR =============*/
                $objSheet->getStyle('A1:' . $columnas[$pos_col] . intval(2 + count($data['filas'])))
                    ->getAlignment()
                    ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
                    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                /* ============== BACKGROUND COLOR =============*/
                $objSheet->getStyle('A1:' . $columnas[$pos_col] . '1')
                    ->getFill()
                    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setRGB('357ca5');
                /* ============== TEXT COLOR =============*/
                $objSheet->getStyle('A1:' . $columnas[$pos_col] . '1')
                    ->getFont()
                    ->getColor()
                    ->setRGB('ffffff');
                /* ============== BORDE COLOR =============*/
                $objSheet->getStyle('A1:' . $columnas[$pos_col] . intval(2 + count($data['filas'])))
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM)
                    ->getColor()
                    ->setRGB('000000'); // <th> Total/Promedio

                //--------------------------- LLENAR LA TABLA ---------------------------------------------
                $pos_fila = 2;
                $positivos = 0;
                foreach ($data['filas'] as $fila) {
                    if ($fila['encabezado'] != '')
                        $objSheet->getCell('A' . $pos_fila)
                            ->setValue($request->cliente == 'P' ? $fila['encabezado']->nombre : $fila['encabezado']->detalle()->nombre);
                    else
                        $objSheet->getCell('A' . $pos_fila)->setValue('Todos');

                    $col = 1;
                    $total_fila = 0;
                    $total_positivos = 0;

                    foreach ($fila['valores'] as $pos_val => $valor) {
                        $objSheet->getCell($columnas[$col] . $pos_fila)->setValue($valor);
                        $total_fila += $valor;
                        $array_totales[$pos_val]['valor'] += $valor;
                        if ($valor > 0) {
                            $total_positivos++;
                            $array_totales[$pos_val]['positivos']++;
                            $positivos++;
                        }
                        $col++;
                    }

                    /* ============== LETRAS NEGRITAS =============*/
                    $objSheet->getStyle($columnas[$col] . $pos_fila)->getFont()->setBold(true)->setSize(12);
                    $objSheet->getStyle('A' . $pos_fila)->getFont()->setBold(true)->setSize(12);

                    if ($request->criterio == 'V' || $request->criterio == 'F' || $request->criterio == 'Q')
                        $objSheet->getCell($columnas[$col] . $pos_fila)->setValue(number_format($total_fila, 2)); // total fila
                    else
                        $objSheet->getCell($columnas[$col] . $pos_fila)
                            ->setValue(number_format($total_positivos > 0 ? round($total_fila / $total_positivos, 2) : 0, 2)); // total fila

                    $pos_fila++;
                }

                /* ============== LETRAS NEGRITAS =============*/
                $objSheet->getStyle($columnas[$col] . $pos_fila)->getFont()->setBold(true)->setSize(12);
                /* ============== BACKGROUND COLOR =============*/
                $objSheet->getStyle('A2:A' . intval(count($data['filas']) + 1))
                    ->getFill()
                    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setRGB('e9ecef');
                $objSheet->getStyle($columnas[$pos_col] . '2:' . $columnas[$pos_col] . intval(count($data['filas']) + 1))
                    ->getFill()
                    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setRGB('e9ecef');

                /* ---------------------------- FILA TOTALES ---------------------------- */
                /* ============== LETRAS NEGRITAS =============*/
                $objSheet->getStyle('A' . $pos_fila . ':' . $columnas[$pos_col] . $pos_fila)->getFont()->setBold(true)->setSize(12);
                /* ============== BACKGROUND COLOR =============*/
                $objSheet->getStyle('A' . $pos_fila . ':' . $columnas[$pos_col] . $pos_fila)->getFill()
                    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('357ca5');
                /* ============== TEXT COLOR =============*/
                $objSheet->getStyle('A' . $pos_fila . ':' . $columnas[$pos_col] . $pos_fila)->getFont()->getColor()->setRGB('ffffff');
                /* ============================================================================================================================ */
                $objSheet->getCell('A' . $pos_fila)
                    ->setValue($request->criterio == 'V' || $request->criterio == 'F' || $request->criterio == 'Q' ? 'Total' : 'Promedio');

                $col = 1;
                $total = 0;
                foreach ($array_totales as $valor) {
                    if ($request->criterio == 'V' || $request->criterio == 'F' || $request->criterio == 'Q')
                        $objSheet->getCell($columnas[$col] . $pos_fila)->setValue(number_format($valor['valor'], 2));
                    else
                        $objSheet->getCell($columnas[$col] . $pos_fila)
                            ->setValue(number_format($valor['positivos'] > 0 ? round($valor['valor'] / $valor['positivos'], 2) : 0, 2));
                    $total += $valor['valor'];
                    $col++;
                }

                if ($request->criterio == 'V' || $request->criterio == 'F' || $request->criterio == 'Q')
                    $objSheet->getCell($columnas[$pos_col] . $pos_fila)->setValue(number_format($total, 2)); // total fila
                else
                    $objSheet->getCell($columnas[$pos_col] . $pos_fila)
                        ->setValue(number_format($positivos > 0 ? round($total / $positivos, 2) : 0, 2)); // total fila

            }
        } else {
            return '<div class="alert alert-warning text-center" > Debes ingresar desde - hasta </div > ';
        }
    }
}
