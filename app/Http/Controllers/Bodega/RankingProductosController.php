<?php

namespace yura\Http\Controllers\Bodega;

use DB;
use Illuminate\Http\Request;
use yura\Http\Controllers\Controller;
use yura\Modelos\DetallePedidoBodega;
use yura\Modelos\Producto;
use yura\Modelos\Submenu;

class RankingProductosController extends Controller
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

        return view('adminlte.gestion.bodega.ranking_productos.inicio', [
            'url' => $request->getRequestUri(),
            'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
            'fincas' => $fincas
        ]);
    }

    public function listar_reporte(Request $request)
    {
        $productos = Producto::join('detalle_pedido_bodega as d', 'd.id_producto', '=', 'producto.id_producto')
            ->join('pedido_bodega as p', 'p.id_pedido_bodega', '=', 'd.id_pedido_bodega')
            ->select('producto.*')->distinct()
            ->where('p.fecha', '<=', $request->hasta);
        if ($request->finca != 'T')
            $productos = $productos->where('p.id_empresa', $request->finca);
        $productos = $productos->orderBy('producto.nombre')
            ->get();

        $listado = [];
        foreach ($productos as $producto) {
            $detalles_pedido = DetallePedidoBodega::join('pedido_bodega as p', 'p.id_pedido_bodega', '=', 'detalle_pedido_bodega.id_pedido_bodega')
                ->select('detalle_pedido_bodega.*')->distinct()
                ->where('p.fecha', '<=', $request->hasta)
                ->where('detalle_pedido_bodega.id_producto', $producto->id_producto);
            if ($request->finca != 'T')
                $detalles_pedido = $detalles_pedido->where('p.id_empresa', $request->finca);
            $detalles_pedido = $detalles_pedido->orderBy('p.fecha')
                ->get();
            $cantidad = 0;
            $monto_subtotal = 0;
            $monto_total_iva = 0;
            $monto_total = 0;
            $costo_total = 0;
            $fechas_entregado = [];
            foreach ($detalles_pedido as $det_ped) {
                $pedido = $det_ped->pedido_bodega;
                $fecha_entrega = $pedido->getFechaEntrega();
                if ($fecha_entrega >= $request->desde && $fecha_entrega <= $request->hasta) {
                    if ($producto->peso == 0) {
                        $precio_prod = $det_ped->cantidad * $det_ped->precio;
                    } else {
                        $precio_prod = 0;
                        foreach ($det_ped->etiquetas_peso as $e) {
                            $precio_prod += $e->peso * $e->precio_venta;
                        }
                    }
                    if ($det_ped->iva == true) {
                        $monto_subtotal += $precio_prod / 1.12;
                        $monto_total_iva += ($precio_prod / 1.12) * 0.12;
                    } else {
                        $monto_subtotal += $precio_prod;
                    }
                    $monto_total += $precio_prod;
                    $cantidad += $det_ped->cantidad;

                    if ($pedido->armado == 0 || $fecha_entrega < '2023-10-03') {    // sin armar
                        if ($producto->peso == 0) { // producto que no es de peso
                            if ($producto->combo == 0) {    // producto normal
                                $costo_total += $producto->precio * $det_ped->cantidad;
                            } else {    // producto tipo combo
                                $costo_total += $producto->getCostoCombo() * $det_ped->cantidad;
                            }
                        } else {    // producto tipo peso
                            foreach ($det_ped->etiquetas_peso as $e) {
                                $costo_total += $e->inventario_bodega->precio;
                            }
                        }
                    } else {    // armado
                        if ($producto->peso == 1) {
                            /* PRODUCTOS QUE SI SON TIPO PESO */
                            foreach ($det_ped->etiquetas_peso as $e) {
                                $costo_total += $e->inventario_bodega->precio;
                            }
                        } else {
                            /* PRODUCTOS QUE NO SON TIPO PESO */
                            $costo_total += DB::table('salida_inventario_bodega as s')
                                ->join('inventario_bodega as i', 'i.id_inventario_bodega', '=', 's.id_inventario_bodega')
                                ->join('detalle_pedido_bodega as d', 'd.id_pedido_bodega', '=', 's.id_pedido_bodega')
                                ->select(DB::raw('sum(s.cantidad * i.precio) as cantidad'))
                                ->where('d.id_detalle_pedido_bodega', $det_ped->id_detalle_pedido_bodega)
                                ->where('i.id_producto', $producto->id_producto)
                                ->get()[0]->cantidad;
                        }
                    }
                    if (!in_array($fecha_entrega, $fechas_entregado))
                        $fechas_entregado[] = $fecha_entrega;
                }
            }

            if ($cantidad > 0) {
                $listado[] = [
                    'producto' => $producto,
                    'cantidad' => $cantidad,
                    'monto_subtotal' => $monto_subtotal,
                    'monto_total_iva' => $monto_total_iva,
                    'monto_total' => $monto_total,
                    'costo_total' => $costo_total,
                    'fechas_entregado' => $fechas_entregado,
                ];
            }
        }

        return view('adminlte.gestion.bodega.ranking_productos.partials.listado', [
            'listado' => $listado
        ]);
    }
}
