<?php

namespace yura\Http\Controllers\Bodega;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use yura\Http\Controllers\Controller;
use yura\Modelos\PedidoBodega;
use yura\Modelos\Submenu;

class crmBodegaController extends Controller
{
    public function inicio(Request $request)
    {
        /* ======= INDICADORES ======= */
        $pedidos = PedidoBodega::where('armado', 1)
            ->orderBy('fecha', 'desc')
            ->get();
        $meses = [];
        foreach ($pedidos as $ped) {
            $entrega = $ped->getFechaEntrega();
            $mes = [
                'anno' => substr($entrega, 0, 4),
                'mes' => substr($entrega, 5, 2),
            ];
            if (!in_array($mes, $meses) && count($meses) < 4) {
                $meses[] = $mes;
            }
        }

        $indicadores = [];
        foreach ($meses as $mes) {
            $fecha = $mes['anno'] . '-' . $mes['mes'] . '-01';
            $primerDiaMes = date("Y-m-01", strtotime($fecha));
            $ultimoDiaMes = date("Y-m-t", strtotime($fecha));

            /* VENTA Y COSTOS TOTALES */
            $pedidos = PedidoBodega::where('estado', 1)
                ->where('fecha', '<=', $ultimoDiaMes)
                ->orderBy('fecha')
                ->get();
            $total_costo = 0;
            $total_venta = 0;
            foreach ($pedidos as $pedido) {
                $fecha_entrega = $pedido->getFechaEntrega();
                if ($fecha_entrega >= $primerDiaMes && $fecha_entrega <= $ultimoDiaMes) {
                    $costo = $pedido->getCostos();
                    $venta = $pedido->getTotalMonto();
                    $total_costo += $costo;
                    $total_venta += $venta;
                }
            }
            $indicadores[] = [
                'mes' => $mes,
                'costo' => $total_costo,
                'venta' => $total_venta,
            ];
        }

        /* ======= GRAFICAS ======= */
        $annos = DB::table('pedido_bodega')
            ->select(DB::raw('year(fecha) as anno'))->distinct()
            ->where('estado', 1)
            ->orderBy('anno', 'desc')
            ->get();
        $fincas = DB::table('configuracion_empresa as emp')
            ->join('usuario_finca as uf', 'uf.id_empresa', '=', 'emp.id_configuracion_empresa')
            ->select('emp.nombre', 'uf.id_empresa')->distinct()
            ->where('emp.proveedor', 0)
            ->where('emp.estado', 1)
            ->where('uf.id_usuario', session('id_usuario'))
            ->orderBy('emp.nombre')
            ->get();
        return view('adminlte.gestion.bodega.crm.inicio', [
            'url' => $request->getRequestUri(),
            'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
            'annos' => $annos,
            'indicadores' => $indicadores,
            'fincas' => $fincas,
        ]);
    }

    public function listar_graficas(Request $request)
    {
        if ($request->annos == '') {
            $view = 'graficas_rango';

            if ($request->rango == 'D') {   // diario
                $labels = DB::table('fecha_entrega')
                    ->select(
                        'desde',
                        'hasta',
                        'entrega'
                    )->distinct()
                    ->where('entrega', '>=', $request->desde)
                    ->where('entrega', '<=', $request->hasta);
                if ($request->finca != 'T')
                    $labels = $labels->where('id_empresa', $request->finca);
                $labels = $labels->orderBy('entrega')
                    ->get();

                $data = [];
                foreach ($labels as $l) {
                    $pedidos = PedidoBodega::where('estado', 1)
                        ->where('fecha', '>=', $l->desde)
                        ->where('fecha', '<=', $l->hasta);
                    if ($request->finca != 'T')
                        $pedidos = $pedidos->where('id_empresa', $request->finca);
                    $pedidos = $pedidos
                        ->orderBy('fecha')
                        ->get();
                    $total_venta = 0;
                    $total_costo = 0;
                    foreach ($pedidos as $pedido) {
                        $costo = $pedido->getCostos();
                        $venta = $pedido->getTotalMonto();
                        $total_venta += $venta;
                        $total_costo += $costo;
                    }

                    $data[] = [
                        'costo' => $total_costo,
                        'venta' => $total_venta,
                        'margen' => $total_venta - $total_costo,
                        'porcentaje_margen' => porcentaje($total_venta - $total_costo, $total_venta, 1),
                    ];
                }
            } else if ($request->rango == 'M') {   // mensual
                $labels = DB::table('fecha_entrega')
                    ->select(DB::raw('DISTINCT DATE_FORMAT(entrega, "%Y-%m") AS mes'))
                    ->where('entrega', '>=', $request->desde)
                    ->where('entrega', '<=', $request->hasta);
                if ($request->finca != 'T')
                    $labels = $labels->where('id_empresa', $request->finca);
                $labels = $labels->orderBy('entrega')
                    ->get()->pluck('mes')->toArray();


                $data = [];
                foreach ($labels as $l) {
                    $fecha = $l . '-01';
                    $primerDiaMes = date("Y-m-01", strtotime($fecha));
                    $ultimoDiaMes = date("Y-m-t", strtotime($fecha));

                    /* VENTA Y COSTOS TOTALES */
                    $pedidos = PedidoBodega::where('estado', 1)
                        ->where('fecha', '<=', $ultimoDiaMes)
                        ->orderBy('fecha')
                        ->get();
                    $total_costo = 0;
                    $total_venta = 0;
                    foreach ($pedidos as $pedido) {
                        $fecha_entrega = $pedido->getFechaEntrega();
                        if ($fecha_entrega >= $primerDiaMes && $fecha_entrega <= $ultimoDiaMes) {
                            $costo = $pedido->getCostos();
                            $venta = $pedido->getTotalMonto();
                            $total_costo += $costo;
                            $total_venta += $venta;
                        }
                    }

                    $data[] = [
                        'costo' => $total_costo,
                        'venta' => $total_venta,
                        'margen' => $total_venta - $total_costo,
                        'porcentaje_margen' => porcentaje($total_venta - $total_costo, $total_venta, 1),
                    ];
                }
            }

            if ($request->tipo_grafica == 'line') {
                $tipo_grafica = 'line';
                $fill_grafica = 'false';
            } else if ($request->tipo_grafica == 'area') {
                $tipo_grafica = 'line';
                $fill_grafica = 'true';
            } else {
                $tipo_grafica = 'bar';
                $fill_grafica = 'true';
            }
            $datos = [
                'labels' => $labels,
                'data' => $data,
                'tipo_grafica' => $tipo_grafica,
                'fill_grafica' => $fill_grafica,
                'rango' => $request->rango,
            ];
        } else {
            $view = 'graficas_annos';
            $annos = explode(' - ', $request->annos);

            en_desarrollo();
        }

        return view('adminlte.gestion.bodega.crm.partials.' . $view, $datos);
    }

    public function listar_ranking(Request $request)
    {
        $pedidos = PedidoBodega::where('armado', 1)
            ->orderBy('fecha', 'desc')
            ->get();
        $meses = [];
        foreach ($pedidos as $ped) {
            $entrega = $ped->getFechaEntrega();
            $mes = [
                'anno' => substr($entrega, 0, 4),
                'mes' => substr($entrega, 5, 2),
            ];
            if (!in_array($mes, $meses) && count($meses) < 4) {
                $meses[] = $mes;
            }
        }

        $fincas = DB::table('configuracion_empresa as emp')
            ->join('usuario_finca as uf', 'uf.id_empresa', '=', 'emp.id_configuracion_empresa')
            ->select('emp.nombre', 'uf.id_empresa')->distinct()
            ->where('emp.proveedor', 0)
            ->where('emp.estado', 1)
            ->where('uf.id_usuario', session('id_usuario'))
            ->orderBy('emp.nombre')
            ->get();

        $listado = [];
        foreach ($fincas as $f) {
            $venta_finca = 0;
            $costo_finca = 0;
            foreach ($meses as $mes) {
                $fecha = $mes['anno'] . '-' . $mes['mes'] . '-01';
                $primerDiaMes = date("Y-m-01", strtotime($fecha));
                $ultimoDiaMes = date("Y-m-t", strtotime($fecha));

                /* VENTA Y COSTOS TOTALES */
                $pedidos = PedidoBodega::where('id_empresa', $f->id_empresa)
                    ->where('estado', 1)
                    ->where('fecha', '<=', $ultimoDiaMes)
                    ->orderBy('fecha')
                    ->get();
                foreach ($pedidos as $pedido) {
                    $fecha_entrega = $pedido->getFechaEntrega();
                    if ($fecha_entrega >= $primerDiaMes && $fecha_entrega <= $ultimoDiaMes) {
                        $costo = $pedido->getCostos();
                        $venta = $pedido->getTotalMonto();
                        $costo_finca += $costo;
                        $venta_finca += $venta;
                    }
                }
            }
            if ($costo_finca > 0 || $venta_finca > 0)
                $listado[] = [
                    'finca' => $f,
                    'costo' => $costo_finca,
                    'venta' => $venta_finca,
                    'margen' => $venta_finca - $costo_finca,
                    'porcentaje_margen' => porcentaje($venta_finca - $costo_finca, $venta_finca, 1),
                ];
        }
        for ($i = 0; $i < count($listado) - 1; $i++) {
            for ($x = $i + 1; $x < count($listado); $x++) {
                $item_i = $listado[$i];
                $item_x = $listado[$x];
                if ($request->criterio_ranking == 'V')
                    if ($item_i['venta'] < $item_x['venta']) {
                        $temp = $item_i;
                        $listado[$i] = $listado[$x];
                        $listado[$x] = $temp;
                    }
                if ($request->criterio_ranking == 'C')
                    if ($item_i['costo'] < $item_x['costo']) {
                        $temp = $item_i;
                        $listado[$i] = $listado[$x];
                        $listado[$x] = $temp;
                    }
                if ($request->criterio_ranking == 'M') {
                    if ($item_i['margen'] < $item_x['margen']) {
                        $temp = $item_i;
                        $listado[$i] = $listado[$x];
                        $listado[$x] = $temp;
                    }
                }
                if ($request->criterio_ranking == 'P') {
                    if ($item_i['porcentaje_margen'] < $item_x['porcentaje_margen']) {
                        $temp = $item_i;
                        $listado[$i] = $listado[$x];
                        $listado[$x] = $temp;
                    }
                }
            }
        }

        return view('adminlte.gestion.bodega.crm.partials.listar_ranking', [
            'listado' => $listado,
            'criterio' => $request->criterio_ranking,
        ]);
    }
}
