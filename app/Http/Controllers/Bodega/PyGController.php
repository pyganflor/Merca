<?php

namespace yura\Http\Controllers\Bodega;

use DateTime;
use DB;
use Illuminate\Http\Request;
use yura\Http\Controllers\Controller;
use yura\Modelos\DetallePedidoBodega;
use yura\Modelos\FechaEntrega;
use yura\Modelos\OtrosGastos;
use yura\Modelos\PedidoBodega;
use yura\Modelos\Submenu;

class PyGController extends Controller
{
    public function inicio(Request $request)
    {
        $fincas = DB::table('configuracion_empresa as emp')
            ->join('usuario_finca as uf', 'uf.id_empresa', '=', 'emp.id_configuracion_empresa')
            ->select('emp.nombre', 'uf.id_empresa')->distinct()
            ->where('emp.proveedor', 0)
            ->where('emp.estado', 1)
            ->where('uf.id_usuario', session('id_usuario'))
            ->orderBy('emp.nombre')
            ->get();

        $pedidos = PedidoBodega::where('armado', 1)
            ->orderBy('fecha')
            ->get();
        $meses = [];
        foreach ($pedidos as $ped) {
            $entrega = $ped->getFechaEntrega();
            $mes = [
                'anno' => substr($entrega, 0, 4),
                'mes' => substr($entrega, 5, 2),
            ];
            if (!in_array($mes, $meses)) {
                $meses[] = $mes;
            }
        }
        return view('adminlte.gestion.bodega.pg_bodega.inicio', [
            'url' => $request->getRequestUri(),
            'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
            'fincas' => $fincas,
            'meses' => $meses,
        ]);
    }

    public function listar_reporte(Request $request)
    {
        $fincas = DB::table('pedido_bodega as p')
            ->join('configuracion_empresa as f', 'f.id_configuracion_empresa', '=', 'p.id_empresa')
            ->select('f.nombre', 'p.id_empresa')->distinct();
        if ($request->finca != 'T')
            $fincas = $fincas->where('p.id_empresa', $request->finca);
        $fincas = $fincas->orderBy('f.nombre')
            ->get();

        $pedidos = PedidoBodega::where('armado', 1)
            ->orderBy('fecha')
            ->get();
        $meses = [];
        foreach ($pedidos as $ped) {
            $entrega = $ped->getFechaEntrega();
            $mes = [
                'anno' => substr($entrega, 0, 4),
                'mes' => substr($entrega, 5, 2),
            ];
            if ($mes['anno'] . '-' . $mes['mes'] . '-01' >= $request->desde . '-01' && $mes['anno'] . '-' . $mes['mes'] . '-01' <= $request->hasta . '-01')
                if (!in_array($mes, $meses)) {
                    $meses[] = $mes;
                }
        }
        $total_ventas = [];
        $total_costos = [];
        $total_personal = [];
        $gastos_administrativos = [];
        foreach ($meses as $m) {
            $ga = OtrosGastos::where('mes', $m['mes'])
                ->where('anno', $m['anno'])
                ->get()
                ->first();
            $gastos_administrativos[] = $ga;
            $total_ventas[] = 0;
            $total_costos[] = 0;
            $total_personal[] = 0;
        }

        $listado = [];
        foreach ($fincas as $finca) {
            $valores_ventas = [];
            $valores_costos = [];
            $valores_personal = [];
            foreach ($meses as $pos_m => $mes) {
                $monto_ventas = 0;
                $monto_costos = 0;
                $personas = [];

                $fecha = $mes['anno'] . '-' . $mes['mes'] . '-01';
                $primerDiaMes = date("Y-m-01", strtotime($fecha));
                $ultimoDiaMes = date("Y-m-t", strtotime($fecha));

                /* VENTA Y COSTOS TOTALES */
                $pedidos = PedidoBodega::where('id_empresa', $finca->id_empresa)
                    ->where('estado', 1)
                    ->where('fecha', '<=', $ultimoDiaMes)
                    ->orderBy('fecha')
                    ->get();
                foreach ($pedidos as $pedido) {
                    $fecha_entrega = $pedido->getFechaEntrega();
                    if ($fecha_entrega >= $primerDiaMes && $fecha_entrega <= $ultimoDiaMes) {
                        $costo = $pedido->getCostos();
                        $venta = $pedido->getTotalMonto();
                        $monto_costos += $costo;
                        $monto_ventas += $venta;
                        if (!in_array($pedido->id_usuario, $personas))
                            $personas[] = $pedido->id_usuario;
                    }
                }

                $valores_ventas[] = $monto_ventas;
                $valores_costos[] = $monto_costos;
                $valores_personal[] = $personas;

                $total_ventas[$pos_m] += $monto_ventas;
                $total_costos[$pos_m] += $monto_costos;
                $total_personal[$pos_m] += count($personas);
            }
            $listado[] = [
                'finca' => $finca,
                'valores_ventas' => $valores_ventas,
                'valores_costos' => $valores_costos,
                'valores_personal' => $valores_personal,
            ];
        }
        return view('adminlte.gestion.bodega.pg_bodega.partials.listado', [
            'meses' => $meses,
            'listado' => $listado,
            'gastos_administrativos' => $gastos_administrativos,
            'total_ventas' => $total_ventas,
            'total_costos' => $total_costos,
            'total_personal' => $total_personal,
        ]);
    }
}
