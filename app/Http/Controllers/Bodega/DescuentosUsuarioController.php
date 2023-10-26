<?php

namespace yura\Http\Controllers\Bodega;

use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use yura\Http\Controllers\Controller;
use yura\Modelos\DetallePedidoBodega;
use yura\Modelos\FechaEntrega;
use yura\Modelos\PedidoBodega;
use yura\Modelos\Submenu;

class DescuentosUsuarioController extends Controller
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

        return view('adminlte.gestion.bodega.descuentos_usuario.inicio', [
            'url' => $request->getRequestUri(),
            'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
            'fincas' => $fincas
        ]);
    }

    public function listar_reporte(Request $request)
    {
        $hoy = hoy();
        $primerDiaMes = date("Y-m-01", strtotime($hoy));
        $ultimoDiaMes = date("Y-m-t", strtotime($hoy));

        $query_pedidos = DetallePedidoBodega::join('pedido_bodega as p', 'p.id_pedido_bodega', '=', 'detalle_pedido_bodega.id_pedido_bodega')
            ->select('detalle_pedido_bodega.*', 'p.fecha', 'p.id_empresa', 'p.diferido_mes_actual')->distinct()
            ->where('p.id_usuario', $request->usuario)
            ->where('p.id_empresa', $request->finca)
            ->where('p.estado', 1)
            ->where('detalle_pedido_bodega.diferido', '>', 0)
            ->orderBy('p.fecha')
            ->get();

        $listado = [];
        foreach ($query_pedidos as $det_ped) {
            $entrega = FechaEntrega::All()
                ->where('desde', '<=', $det_ped->fecha)
                ->where('hasta', '>=', $det_ped->fecha)
                ->where('id_empresa', $det_ped->id_empresa)
                ->first();
            $fecha_entrega = $entrega != '' ? $entrega->entrega : '';

            $diferido_selected = $det_ped->diferido;
            $diferido_mes_inicial = $det_ped->diferido_mes_actual ? 0 : 1;
            $diferido_mes_final = $det_ped->diferido_mes_actual ? $diferido_selected - 1 : $diferido_selected;

            $diferido_fecha_inicial = new DateTime($fecha_entrega);
            $diferido_fecha_inicial->modify('+' . $diferido_mes_inicial . ' month');
            $diferido_fecha_inicial = $diferido_fecha_inicial->format('Y-m-d');

            $diferido_fecha_final = new DateTime($fecha_entrega);
            $diferido_fecha_final->modify('+' . $diferido_mes_final . ' month');
            $diferido_fecha_final = $diferido_fecha_final->format('Y-m-d');

            if (($diferido_fecha_inicial >= $primerDiaMes && $diferido_fecha_inicial <= $ultimoDiaMes) ||
                ($diferido_fecha_final >= $primerDiaMes && $diferido_fecha_final <= $ultimoDiaMes) ||
                ($diferido_fecha_inicial <= $primerDiaMes && $diferido_fecha_final >= $ultimoDiaMes)
            ) {
                $rango_diferido = $det_ped->getRangoDiferidoByFecha($fecha_entrega);
                $num_pago = 0;
                foreach ($rango_diferido as $pos_f => $f) {
                    if ($f >= $primerDiaMes && $f <= $ultimoDiaMes) {
                        $num_pago = $pos_f + 1;
                    }
                }
                $det_ped->fecha_entrega = $fecha_entrega;
                $det_ped->num_pago = $num_pago;
                $listado[] = $det_ped;
            }
        }
        return view('adminlte.gestion.bodega.descuentos_usuario.partials.listado', [
            'listado' => $listado
        ]);
    }

    public function seleccionar_finca_filtro(Request $request)
    {
        $listado = DB::table('usuario_finca as uf')
            ->join('usuario as u', 'u.id_usuario', '=', 'uf.id_usuario')
            ->select('uf.id_usuario', 'u.nombre_completo', 'u.username', 'u.telefono', 'u.saldo')->distinct()
            ->where('uf.id_empresa', $request->finca)
            ->where('u.estado', 'A')
            ->where('u.aplica', 1)
            ->orderBy('u.nombre_completo')
            ->get();

        $options_usuarios = '<option value="">Seleccione</option>';
        foreach ($listado as $item) {
            $options_usuarios .= '<option value="' . $item->id_usuario . '">' . $item->nombre_completo . ' CI:' . $item->username . ' Telf:' . $item->telefono . ' saldo:$' . round($item->saldo, 2) . '</option>';
        }

        return [
            'options_usuarios' => $options_usuarios
        ];
    }
}
