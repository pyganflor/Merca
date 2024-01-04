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
                $labels = DB::table('pedido')
                    ->select('fecha_pedido')->distinct()
                    ->where('estado', 1)
                    ->where('fecha_pedido', '>=', $request->desde)
                    ->where('fecha_pedido', '<=', $request->hasta)
                    ->orderBy('fecha_pedido')
                    ->get()->pluck('fecha_pedido')->toArray();

                $data = [];
                foreach ($labels as $l) {
                    $query = DB::table('pedido as p')
                        ->join('detalle_pedido as dp', 'dp.id_pedido', '=', 'p.id_pedido')
                        ->join('caja_frio as c', 'c.id_caja_frio', '=', 'dp.id_caja_frio')
                        ->join('detalle_caja_frio as dc', 'dc.id_caja_frio', '=', 'c.id_caja_frio')
                        ->select(
                            DB::raw('sum(dc.ramos * dc.tallos_x_ramo * dc.precio) as monto'),
                            DB::raw('sum(dc.ramos) as ramos'),
                            DB::raw('sum(dc.ramos * dc.tallos_x_ramo) as tallos'),
                        )
                        ->where('p.estado', 1)
                        ->where('p.fecha_pedido', $l);
                    if ($request->variedad != 'T')
                        $query = $query->where('dc.id_variedad', $request->variedad);
                    if ($request->longitud != '')
                        $query = $query->where('dc.longitud', $request->longitud);
                    $query = $query->get()[0];

                    $data[] = $query;
                }
            } else if ($request->rango == 'M') {   // mensual
                $labels = DB::table('pedido')
                    ->select(DB::raw('DISTINCT DATE_FORMAT(fecha_pedido, "%Y-%m") AS mes'))
                    ->where('estado', 1)
                    ->where('fecha_pedido', '>=', $request->desde)
                    ->where('fecha_pedido', '<=', $request->hasta)
                    ->groupBy('mes', 'fecha_pedido')
                    ->orderBy('fecha_pedido')
                    ->get()->pluck('mes')->toArray();

                $data = [];
                foreach ($labels as $l) {
                    $query = DB::table('pedido as p')
                        ->join('detalle_pedido as dp', 'dp.id_pedido', '=', 'p.id_pedido')
                        ->join('caja_frio as c', 'c.id_caja_frio', '=', 'dp.id_caja_frio')
                        ->join('detalle_caja_frio as dc', 'dc.id_caja_frio', '=', 'c.id_caja_frio')
                        ->select(
                            DB::raw('sum(dc.ramos * dc.tallos_x_ramo * dc.precio) as monto'),
                            DB::raw('sum(dc.ramos) as ramos'),
                            DB::raw('sum(dc.ramos * dc.tallos_x_ramo) as tallos'),
                        )
                        ->where('p.estado', 1)
                        ->whereMonth('p.fecha_pedido', date('m', strtotime($l)))
                        ->whereYear('p.fecha_pedido', '=', date('Y', strtotime($l)));
                    if ($request->variedad != 'T')
                        $query = $query->where('dc.id_variedad', $request->variedad);
                    if ($request->longitud != '')
                        $query = $query->where('dc.longitud', $request->longitud);
                    $query = $query->get()[0];

                    $data[] = $query;
                }
            } else {    // semanal
                $labels = DB::table('semana')
                    ->select('codigo', 'fecha_inicial', 'fecha_final')->distinct()
                    ->where('fecha_final', '>=', $request->desde)
                    ->where('fecha_final', '<=', $request->hasta)
                    ->orderBy('codigo')
                    ->get();

                $data = [];
                foreach ($labels as $pos => $l) {
                    $query = DB::table('pedido as p')
                        ->join('detalle_pedido as dp', 'dp.id_pedido', '=', 'p.id_pedido')
                        ->join('caja_frio as c', 'c.id_caja_frio', '=', 'dp.id_caja_frio')
                        ->join('detalle_caja_frio as dc', 'dc.id_caja_frio', '=', 'c.id_caja_frio')
                        ->select(
                            DB::raw('sum(dc.ramos * dc.tallos_x_ramo * dc.precio) as monto'),
                            DB::raw('sum(dc.ramos) as ramos'),
                            DB::raw('sum(dc.ramos * dc.tallos_x_ramo) as tallos'),
                        )
                        ->where('p.estado', 1)
                        ->where('p.fecha_pedido', '>=', $l->fecha_inicial)
                        ->where('p.fecha_pedido', '<=', $l->fecha_final);
                    if ($request->variedad != 'T')
                        $query = $query->where('dc.id_variedad', $request->variedad);
                    if ($request->longitud != '')
                        $query = $query->where('dc.longitud', $request->longitud);
                    $query = $query->get()[0];

                    $data[] = $query;
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

        return view('adminlte.crm.ventas.partials.' . $view, $datos);
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
                ];
        }
        for ($i = 0; $i < count($listado) - 1; $i++) {
            for ($x = $i + 1; $x < count($listado); $x++) {
                $item_i = $listado[$i];
                $item_x = $listado[$x];
                if ($item_i['venta'] < $item_x['venta']) {
                    $temp = $item_i;
                    $listado[$i] = $listado[$x];
                    $listado[$x] = $temp;
                }
            }
        }
        dd($listado);

        return view('adminlte.gestion.bodega.crm.partials.listar_ranking', [
            'query' => $query,
            'criterio' => $request->criterio_ranking,
        ]);
    }
}
