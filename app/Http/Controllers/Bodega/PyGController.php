<?php

namespace yura\Http\Controllers\Bodega;

use DateTime;
use DB;
use Illuminate\Http\Request;
use yura\Http\Controllers\Controller;
use yura\Modelos\DetallePedidoBodega;
use yura\Modelos\FechaEntrega;
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
            ->select('f.nombre', 'p.id_empresa')->distinct()
            ->where('p.armado', 1);
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
        $total_descuentos_diferidos = [];
        $total_descuentos_normales = [];
        $total_ventas = [];
        $total_costos = [];
        $total_personal = [];
        foreach ($meses as $m) {
            $total_descuentos_diferidos[] = 0;
            $total_descuentos_normales[] = 0;
            $total_ventas[] = 0;
            $total_costos[] = 0;
            $total_personal[] = 0;
        }

        $listado = [];
        foreach ($fincas as $finca) {
            $valores_descuentos_diferidos = [];
            $valores_descuentos_normales = [];
            $valores_ventas = [];
            $valores_costos = [];
            $valores_personal = [];
            foreach ($meses as $pos_m => $mes) {
                $monto_descuento_diferido = 0;
                $monto_descuento_normal = 0;
                $monto_ventas = 0;
                $monto_costos = 0;
                $personas = [];

                $fecha = $mes['anno'] . '-' . $mes['mes'] . '-01';
                $primerDiaMes = date("Y-m-01", strtotime($fecha));
                $ultimoDiaMes = date("Y-m-t", strtotime($fecha));

                /* DIFERIDOS */
                $query_pedidos = DetallePedidoBodega::join('pedido_bodega as p', 'p.id_pedido_bodega', '=', 'detalle_pedido_bodega.id_pedido_bodega')
                    ->select('detalle_pedido_bodega.*', 'p.fecha', 'p.id_empresa', 'p.diferido_mes_actual')->distinct()
                    ->where('p.id_empresa', $finca->id_empresa)
                    ->where('p.estado', 1)
                    ->where('p.armado', 1)
                    ->where('p.fecha', '<=', $ultimoDiaMes)
                    ->where('detalle_pedido_bodega.diferido', '>', 0)
                    ->get();
                foreach ($query_pedidos as $det_ped) {
                    $entrega = FechaEntrega::All()
                        ->where('desde', '<=', $det_ped->fecha)
                        ->where('hasta', '>=', $det_ped->fecha)
                        ->where('id_empresa', $det_ped->id_empresa)
                        ->first();
                    $fecha_entrega = $entrega != '' ? $entrega->entrega : '';
                    $dia_entrega = date('d', strtotime($fecha_entrega));

                    $diferido_selected = $det_ped->diferido;
                    $diferido_mes_inicial = $det_ped->diferido_mes_actual ? 0 : 1;
                    $diferido_mes_final = $det_ped->diferido_mes_actual ? $diferido_selected - 1 : $diferido_selected;

                    $diferido_fecha_inicial = new DateTime($fecha_entrega);
                    $diferido_fecha_inicial->modify('first day of +' . $diferido_mes_inicial . ' month');
                    $diferido_fecha_inicial = $diferido_fecha_inicial->format('Y-m-d');

                    $diferido_fecha_inicial = date('Y', strtotime($diferido_fecha_inicial)) . '-' . date('m', strtotime($diferido_fecha_inicial)) . '-' . $dia_entrega;
                    list($ano, $mes, $dia) = explode('-', $diferido_fecha_inicial);
                    $d = 1;
                    while (!checkdate($mes, $dia, $ano)) {
                        $diferido_fecha_inicial = new DateTime($diferido_fecha_inicial);
                        $diferido_fecha_inicial->modify('-' . $d . ' day');
                        $diferido_fecha_inicial = $diferido_fecha_inicial->format('Y-m-d');
                        list($ano, $mes, $dia) = explode('-', $diferido_fecha_inicial);
                        $d++;
                    }

                    $diferido_fecha_final = new DateTime($fecha_entrega);
                    $diferido_fecha_final->modify('first day of +' . $diferido_mes_final . ' month');
                    $diferido_fecha_final = $diferido_fecha_final->format('Y-m-d');

                    $diferido_fecha_final = date('Y', strtotime($diferido_fecha_final)) . '-' . date('m', strtotime($diferido_fecha_final)) . '-' . $dia_entrega;
                    list($ano, $mes, $dia) = explode('-', $diferido_fecha_final);
                    $d = 1;
                    if (!checkdate($mes, $dia, $ano)) {
                        $diferido_fecha_final = new DateTime($diferido_fecha_final);
                        $diferido_fecha_final->modify('-' . $d . ' day');
                        $diferido_fecha_final = $diferido_fecha_final->format('Y-m-d');
                        list($ano, $mes, $dia) = explode('-', $diferido_fecha_final);
                        $d++;
                    }

                    if (($diferido_fecha_inicial >= $primerDiaMes && $diferido_fecha_inicial <= $ultimoDiaMes) ||
                        ($diferido_fecha_final >= $primerDiaMes && $diferido_fecha_final <= $ultimoDiaMes) ||
                        ($diferido_fecha_inicial <= $primerDiaMes && $diferido_fecha_final >= $ultimoDiaMes)
                    ) {
                        $producto = $det_ped->producto;
                        $precio_prod = 0;
                        if ($producto->peso == 1) {
                            foreach ($det_ped->etiquetas_peso as $e) {
                                $precio_prod += $e->peso * $e->precio_venta;
                            }
                        } else {
                            $precio_prod = $det_ped->cantidad * $det_ped->precio;
                        }
                        $diferido = $precio_prod / $det_ped->diferido;
                        if ($det_ped->iva == true) {
                            $subtotal = $precio_prod / 1.12;
                            $iva = ($precio_prod / 1.12) * 0.12;
                        } else {
                            $subtotal = $precio_prod;
                            $iva = 0;
                        }
                        $subtotal = $subtotal / $det_ped->diferido;
                        $iva = $iva / $det_ped->diferido;

                        $rango_diferido = $det_ped->getRangoDiferidoByFecha($fecha_entrega);
                        foreach ($rango_diferido as $pos_f => $f) {
                            if ($f >= $primerDiaMes && $f <= $ultimoDiaMes) {
                                $monto_descuento_diferido += $diferido;
                            }
                        }
                    }
                }

                $mes_anterior = new DateTime($fecha);
                $mes_anterior->modify('first day of -1 month');
                $mes_anterior = $mes_anterior->format('Y-m-d');

                /* NO DIFERIDOS */
                $primerDiaMesAnterior = date("Y-m-01", strtotime($mes_anterior));
                $ultimoDiaMesAnterior = date("Y-m-t", strtotime($mes_anterior));
                $query_pedidos = DetallePedidoBodega::join('pedido_bodega as p', 'p.id_pedido_bodega', '=', 'detalle_pedido_bodega.id_pedido_bodega')
                    ->select('detalle_pedido_bodega.*', 'p.fecha', 'p.id_empresa', 'p.finca_nomina')->distinct()
                    ->where('p.id_empresa', $finca->id_empresa)
                    ->where('p.estado', 1)
                    ->where('p.armado', 1)
                    ->where('detalle_pedido_bodega.diferido', '!=', -1)
                    ->where('p.fecha', '<=', $ultimoDiaMesAnterior)
                    ->orderBy('p.fecha')
                    ->get();

                foreach ($query_pedidos as $det_ped) {
                    if ($det_ped->diferido == null || $det_ped->diferido == 0) {
                        $entrega = FechaEntrega::All()
                            ->where('desde', '<=', $det_ped->fecha)
                            ->where('hasta', '>=', $det_ped->fecha)
                            ->where('id_empresa', $det_ped->id_empresa)
                            ->first();
                        $fecha_entrega = $entrega != '' ? $entrega->entrega : '';
                        if ($fecha_entrega >= $primerDiaMesAnterior && $fecha_entrega <= $ultimoDiaMesAnterior) {
                            $producto = $det_ped->producto;
                            $precio_prod = 0;
                            if ($producto->peso == 1) {
                                foreach ($det_ped->etiquetas_peso as $e) {
                                    $precio_prod += $e->peso * $e->precio_venta;
                                }
                            } else {
                                $precio_prod = $det_ped->cantidad * $det_ped->precio;
                            }
                            $monto_pedido = $precio_prod;
                            if ($det_ped->iva == true) {
                                $subtotal = $precio_prod / 1.12;
                                $iva = ($precio_prod / 1.12) * 0.12;
                            } else {
                                $subtotal = $precio_prod;
                                $iva = 0;
                            }

                            $monto_descuento_normal += $monto_pedido;
                        }
                    }
                }

                /* VENTA Y COSTOS TOTALES */
                $pedidos = PedidoBodega::where('id_empresa', $finca->id_empresa)
                    ->where('armado', 1)
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

                $valores_descuentos_diferidos[] = $monto_descuento_diferido;
                $valores_descuentos_normales[] = $monto_descuento_normal;
                $valores_ventas[] = $monto_ventas;
                $valores_costos[] = $monto_costos;
                $valores_personal[] = $personas;

                $total_descuentos_diferidos[$pos_m] += $monto_descuento_diferido;
                $total_descuentos_normales[$pos_m] += $monto_descuento_normal;
                $total_ventas[$pos_m] += $monto_ventas;
                $total_costos[$pos_m] += $monto_costos;
                $total_personal[$pos_m] += count($personas);
            }
            $listado[] = [
                'finca' => $finca,
                'valores_descuentos_diferidos' => $valores_descuentos_diferidos,
                'valores_descuentos_normales' => $valores_descuentos_normales,
                'valores_ventas' => $valores_ventas,
                'valores_costos' => $valores_costos,
                'valores_personal' => $valores_personal,
            ];
        }
        return view('adminlte.gestion.bodega.pg_bodega.partials.listado', [
            'meses' => $meses,
            'listado' => $listado,
            'total_descuentos_diferidos' => $total_descuentos_diferidos,
            'total_descuentos_normales' => $total_descuentos_normales,
            'total_ventas' => $total_ventas,
            'total_costos' => $total_costos,
            'total_personal' => $total_personal,
        ]);
    }
}
