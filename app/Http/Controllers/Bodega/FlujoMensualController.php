<?php

namespace yura\Http\Controllers\Bodega;

use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use yura\Http\Controllers\Controller;
use yura\Modelos\DetallePedidoBodega;
use yura\Modelos\PedidoBodega;
use yura\Modelos\Submenu;
use yura\Modelos\FechaEntrega;
use yura\Modelos\MesHistorico;
use yura\Modelos\OtrosGastos;

class FlujoMensualController extends Controller
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
        return view('adminlte.gestion.bodega.flujo_mensual.inicio', [
            'url' => $request->getRequestUri(),
            'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
            'fincas' => $fincas,
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
        $mes_desde = new DateTime($request->desde);
        $mes_desde = $mes_desde->format('Y-m');
        $mes_hasta = new DateTime($request->hasta);
        $mes_hasta = $mes_hasta->format('Y-m');
        $meses = [];
        $f = $mes_desde . '-01';
        while ($f <= $mes_hasta . '-01') {
            $mes = [
                'anno' => substr($f, 0, 4),
                'mes' => substr($f, 5, 2),
            ];
            if (!in_array($mes, $meses)) {
                $meses[] = $mes;
            }
            $f = opDiasFecha('+', 1, $f);
        }
        $total_costos = [];
        $total_otros_costos = [];
        $total_descuentos_diferidos = [];
        $total_descuentos_normales = [];
        $total_al_contado = [];
        $gastos_administrativos = [];
        $valores_inventario = [];
        foreach ($meses as $m) {
            $ga = OtrosGastos::where('mes', $m['mes'])
                ->where('anno', $m['anno'])
                ->get()
                ->first();
            $gastos_administrativos[] = $ga;
            $total_costos[] = 0;
            $total_otros_costos[] = 0;
            $total_descuentos_diferidos[] = 0;
            $total_descuentos_normales[] = 0;
            $total_al_contado[] = 0;
        }

        $listado = [];
        foreach ($fincas as $finca) {
            $valores_costos = [];
            $valores_descuentos_diferidos = [];
            $valores_descuentos_normales = [];
            $valores_al_contado = [];
            foreach ($meses as $pos_m => $mes) {
                $monto_costos = 0;
                $monto_descuento_diferido = 0;
                $monto_descuento_normal = 0;
                $monto_al_contado = 0;

                $fecha = $mes['anno'] . '-' . $mes['mes'] . '-01';
                $primerDiaMes = date("Y-m-01", strtotime($fecha));
                $ultimoDiaMes = date("Y-m-t", strtotime($fecha));

                $mes_anterior = new DateTime($fecha);
                $mes_anterior->modify('first day of -1 month');
                $mes_anterior = $mes_anterior->format('Y-m-d');
                $primerDiaMesAnterior = date("Y-m-01", strtotime($mes_anterior));
                $ultimoDiaMesAnterior = date("Y-m-t", strtotime($mes_anterior));

                $mes_anterior2 = new DateTime($fecha);
                $mes_anterior2->modify('first day of -2 month');
                $mes_anterior2 = $mes_anterior2->format('Y-m-d');
                $primerDiaMesAnterior2 = date("Y-m-01", strtotime($mes_anterior2));
                $ultimoDiaMesAnterior2 = date("Y-m-t", strtotime($mes_anterior2));

                /* DIFERIDOS */
                $query_pedidos = DetallePedidoBodega::join('pedido_bodega as p', 'p.id_pedido_bodega', '=', 'detalle_pedido_bodega.id_pedido_bodega')
                    ->select('detalle_pedido_bodega.*', 'p.fecha', 'p.id_empresa', 'p.diferido_mes_actual')->distinct()
                    ->where('p.id_empresa', $finca->id_empresa)
                    ->where('p.estado', 1)
                    ->where('p.fecha', '<=', $ultimoDiaMesAnterior)
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

                    if (($diferido_fecha_inicial >= $primerDiaMesAnterior && $diferido_fecha_inicial <= $ultimoDiaMesAnterior) ||
                        ($diferido_fecha_final >= $primerDiaMesAnterior && $diferido_fecha_final <= $ultimoDiaMesAnterior) ||
                        ($diferido_fecha_inicial <= $primerDiaMesAnterior && $diferido_fecha_final >= $ultimoDiaMesAnterior)
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
                            if ($f >= $primerDiaMesAnterior && $f <= $ultimoDiaMesAnterior) {
                                $monto_descuento_diferido += $diferido;
                            }
                        }
                    }
                }

                /* NO DIFERIDOS */
                $query_pedidos = DetallePedidoBodega::join('pedido_bodega as p', 'p.id_pedido_bodega', '=', 'detalle_pedido_bodega.id_pedido_bodega')
                    ->select('detalle_pedido_bodega.*', 'p.fecha', 'p.id_empresa', 'p.finca_nomina')->distinct()
                    ->where('p.id_empresa', $finca->id_empresa)
                    ->where('p.estado', 1)
                    ->where('detalle_pedido_bodega.diferido', '!=', -1)
                    ->where('p.fecha', '<=', $ultimoDiaMesAnterior2)
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
                        if ($fecha_entrega >= $primerDiaMesAnterior2 && $fecha_entrega <= $ultimoDiaMesAnterior2) {
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

                /* AL CONTADO */
                $query_pedidos = DetallePedidoBodega::join('pedido_bodega as p', 'p.id_pedido_bodega', '=', 'detalle_pedido_bodega.id_pedido_bodega')
                    ->select('detalle_pedido_bodega.*', 'p.fecha', 'p.id_empresa', 'p.finca_nomina')->distinct()
                    ->where('p.id_empresa', $finca->id_empresa)
                    ->where('p.estado', 1)
                    ->where('detalle_pedido_bodega.diferido', -1)
                    ->where('p.fecha', '<=', $ultimoDiaMes)
                    ->orderBy('p.fecha')
                    ->get();

                foreach ($query_pedidos as $det_ped) {
                    $entrega = FechaEntrega::All()
                        ->where('desde', '<=', $det_ped->fecha)
                        ->where('hasta', '>=', $det_ped->fecha)
                        ->where('id_empresa', $det_ped->id_empresa)
                        ->first();
                    $fecha_entrega = $entrega != '' ? $entrega->entrega : '';
                    if ($fecha_entrega >= $primerDiaMes && $fecha_entrega <= $ultimoDiaMes) {
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

                        $monto_al_contado += $monto_pedido;
                    }
                }

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
                        $monto_costos += $costo;
                    }
                }

                $valores_costos[] = $monto_costos;
                $valores_descuentos_diferidos[] = $monto_descuento_diferido;
                $valores_descuentos_normales[] = $monto_descuento_normal;
                $valores_al_contado[] = $monto_al_contado;

                $total_costos[$pos_m] += $monto_costos;
                $total_descuentos_diferidos[$pos_m] += $monto_descuento_diferido;
                $total_descuentos_normales[$pos_m] += $monto_descuento_normal;
                $total_al_contado[$pos_m] += $monto_al_contado;
            }
            $listado[] = [
                'finca' => $finca,
                'valores_costos' => $valores_costos,
                'valores_descuentos_diferidos' => $valores_descuentos_diferidos,
                'valores_descuentos_normales' => $valores_descuentos_normales,
                'valores_al_contado' => $valores_al_contado,
            ];
        }
        foreach ($meses as $pos_m => $mes) {
            $fecha = $mes['anno'] . '-' . $mes['mes'] . '-01';
            $primerDiaMes = date("Y-m-01", strtotime($fecha));
            $ultimoDiaMes = date("Y-m-t", strtotime($fecha));

            $monto_costos = DB::table('ingreso_bodega')
                ->select(DB::raw('sum(precio * cantidad) as cant'))
                ->where('fecha', '>=', $primerDiaMes)
                ->where('fecha', '<=', $ultimoDiaMes)
                ->get()[0]->cant;
            $total_otros_costos[$pos_m] = $monto_costos;
            $mes_historico = MesHistorico::where('anno', $mes['anno'])
                ->where('mes', $mes['mes'])
                ->get()
                ->first();
            $valor_inventario = $mes_historico != '' ? $mes_historico->valor_inventario : 0;
            $valores_inventario[$pos_m] = $valor_inventario;
        }

        return view('adminlte.gestion.bodega.flujo_mensual.partials.listado', [
            'meses' => $meses,
            'listado' => $listado,
            'gastos_administrativos' => $gastos_administrativos,
            'total_costos' => $total_costos,
            'total_otros_costos' => $total_otros_costos,
            'total_descuentos_diferidos' => $total_descuentos_diferidos,
            'total_descuentos_normales' => $total_descuentos_normales,
            'total_al_contado' => $total_al_contado,
            'valores_inventario' => $valores_inventario,
        ]);
    }

    public function update_ga(Request $request)
    {
        DB::beginTransaction();
        try {
            $model = OtrosGastos::where('mes', $request->mes)
                ->where('anno', $request->anno)
                ->get()
                ->first();
            if ($model == '') {
                $model = new OtrosGastos();
                $model->mes = $request->mes;
                $model->anno = $request->anno;
            }
            $model->ga = $request->valor;
            $model->save();

            $success = true;
            $msg = 'Se ha <b>GRABADO</b> el gasto administrativo correctamente';
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
