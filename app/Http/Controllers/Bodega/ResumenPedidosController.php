<?php

namespace yura\Http\Controllers\Bodega;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use yura\Http\Controllers\Controller;
use yura\Modelos\PedidoBodega;
use yura\Modelos\Submenu;
use Barryvdh\DomPDF\Facade as PDF;

class ResumenPedidosController extends Controller
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

        return view('adminlte.gestion.bodega.resumen_pedidos.inicio', [
            'url' => $request->getRequestUri(),
            'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
            'fincas' => $fincas
        ]);
    }

    public function listar_reporte(Request $request)
    {
        $usuarios = DB::table('pedido_bodega as p')
            ->join('usuario as u', 'u.id_usuario', '=', 'p.id_usuario')
            ->select('p.id_usuario', 'u.nombre_completo', 'u.username')->distinct()
            ->where('p.estado', 1)
            ->where('p.fecha', '<=', $request->hasta);
        if ($request->finca != 'T')
            $usuarios = $usuarios->where('p.id_empresa', $request->finca);
        $usuarios = $usuarios->orderBy('p.fecha')
            ->orderBy('u.nombre_completo')
            ->get();
        $listado = [];
        foreach ($usuarios as $u) {
            $query_pedidos = PedidoBodega::where('id_usuario', $u->id_usuario)
                ->where('estado', 1)
                ->where('fecha', '<=', $request->hasta);
            if ($request->finca != 'T')
                $query_pedidos = $query_pedidos->where('id_empresa', $request->finca);
            $query_pedidos = $query_pedidos->get();

            $pedidos = [];
            foreach ($query_pedidos as $ped) {
                $fecha_entrega = $ped->getFechaEntrega();
                if ($fecha_entrega >= $request->desde && $fecha_entrega <= $request->hasta)
                    $pedidos[] = $ped;
            }

            $monto_subtotal = 0;
            $monto_total_iva = 0;
            $monto_total = 0;
            foreach ($pedidos as $pedido) {
                $fecha_entrega = $pedido->getFechaEntrega();
                if ($fecha_entrega >= $request->desde && $fecha_entrega <= $request->hasta) {
                    foreach ($pedido->detalles as $det) {
                        $precio_prod = $det->cantidad * $det->precio;
                        if ($det->iva == true) {
                            $monto_subtotal += $precio_prod / 1.12;
                            $monto_total_iva += ($precio_prod / 1.12) * 0.12;
                        } else {
                            $monto_subtotal += $precio_prod;
                        }
                        $monto_total += $precio_prod;
                    }
                }
            }
            if ($monto_total > 0)
                $listado[] = [
                    'usuario' => $u,
                    'subtotal' => $monto_subtotal,
                    'total_iva' => $monto_total_iva,
                    'total' => $monto_total,
                ];
        }

        return view('adminlte.gestion.bodega.resumen_pedidos.partials.listado', [
            'listado' => $listado
        ]);
    }

    public function exportar_reporte(Request $request)
    {
        $usuarios = DB::table('pedido_bodega as p')
            ->join('usuario as u', 'u.id_usuario', '=', 'p.id_usuario')
            ->select('p.id_usuario', 'u.nombre_completo', 'u.username')
            ->where('p.estado', 1);
        if ($request->finca != 'T')
            $usuarios = $usuarios->where('p.id_empresa', $request->finca);
        $usuarios = $usuarios->orderBy('p.fecha')
            ->orderBy('u.nombre_completo')
            ->get();
        $listado = [];
        foreach ($usuarios as $u) {
            $pedidos = PedidoBodega::where('id_usuario', $u->id_usuario)
                ->where('estado', 1)
                ->where('fecha', '>=', $request->desde)
                ->where('fecha', '<=', $request->hasta);
            if ($request->finca != 'T')
                $pedidos = $pedidos->where('id_empresa', $request->finca);
            $pedidos = $pedidos->get();

            $monto_subtotal = 0;
            $monto_total_iva = 0;
            $monto_total = 0;
            foreach ($pedidos as $pedido) {
                $fecha_entrega = $pedido->getFechaEntrega();
                if ($fecha_entrega >= $request->desde && $fecha_entrega <= $request->hasta) {
                    foreach ($pedido->detalles as $det) {
                        $precio_prod = $det->cantidad * $det->precio;
                        if ($det->iva == true) {
                            $monto_subtotal += $precio_prod / 1.12;
                            $monto_total_iva += ($precio_prod / 1.12) * 0.12;
                        } else {
                            $monto_subtotal += $precio_prod;
                        }
                        $monto_total += $precio_prod;
                    }
                }
            }
            if ($monto_total > 0)
                $listado[] = [
                    'usuario' => $u,
                    'subtotal' => $monto_subtotal,
                    'total_iva' => $monto_total_iva,
                    'total' => $monto_total,
                ];
        }

        $datos = [
            'listado' => $listado,
            'desde' => $request->desde,
            'hasta' => $request->hasta,
            'finca' => getConfiguracionEmpresa($request->finca),
        ];
        return PDF::loadView('adminlte.gestion.bodega.resumen_pedidos.partials.pdf_reporte', compact('datos'))
            ->setPaper(array(0, 0, 750, 530), 'landscape')->stream();
    }
}
