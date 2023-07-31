<?php

namespace yura\Http\Controllers\Comercializacion;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use yura\Http\Controllers\Controller;
use yura\Modelos\CodigoDae;
use yura\Modelos\Pedido;
use yura\Modelos\Submenu;

class IngresoGuiasController extends Controller
{
    public function inicio(Request $request)
    {
        $clientes = DB::table('pedido as p')
            ->join('detalle_cliente as c', 'c.id_cliente', '=', 'p.id_cliente')
            ->select('p.id_cliente', 'c.nombre')->distinct()
            ->where('c.estado', 1)
            ->get();
        $agencias = DB::table('pedido as p')
            ->join('agencia_carga as a', 'a.id_agencia_carga', '=', 'p.id_agencia_carga')
            ->select('p.id_agencia_carga', 'a.nombre')->distinct()
            ->where('a.estado', 1)
            ->get();

        return view('adminlte.gestion.comercializacion.ingreso_guias.inicio', [
            'url' => $request->getRequestUri(),
            'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
            'clientes' => $clientes,
            'agencias' => $agencias,
        ]);
    }

    public function listar_reporte(Request $request)
    {
        $pedidos = Pedido::join('detalle_cliente as dc', 'dc.id_cliente', '=', 'pedido.id_cliente')
            ->join('agencia_carga as a', 'a.id_agencia_carga', '=', 'pedido.id_agencia_carga')
            ->join('consignatario as c', 'c.id_consignatario', '=', 'pedido.id_consignatario')
            ->join('pais as p', 'p.codigo', '=', 'c.codigo_pais')
            ->select(
                'pedido.*',
                'dc.nombre as cliente_nombre',
                'a.nombre as agencia_nombre',
                'c.nombre as consignatario_nombre',
                'c.codigo_pais',
                'p.nombre as pais_nombre',
            )->distinct()
            ->where('dc.estado', 1)
            ->where('a.estado', 1)
            ->where('pedido.fecha_pedido', $request->fecha);
        if ($request->cliente != '')
            $pedidos = $pedidos->where('pedido.id_cliente', $request->cliente);
        if ($request->agencia != '')
            $pedidos = $pedidos->where('pedido.id_agencia_carga', $request->agencia);
        $pedidos = $pedidos->orderBy('c.nombre')
            ->get();

        $listado = [];
        foreach ($pedidos as $item) {
            $anno = substr($item->fecha_pedido, 0, 4);
            $mes = substr($item->fecha_pedido, 5, 2);
            $codigo_dae = CodigoDae::All()
                ->where('anno', $anno)
                ->where('mes', $mes)
                ->where('codigo_pais', $item->codigo_pais)
                ->first();
            $listado[] = [
                'pedido' => $item,
                'codigo_dae' => $codigo_dae,
            ];
        }

        return view('adminlte.gestion.comercializacion.ingreso_guias.partials.listado', [
            'listado' => $listado,
        ]);
    }

    public function store_guias(Request $request)
    {
        try {
            foreach (json_decode($request->data) as $d) {
                DB::beginTransaction();
                $model = Pedido::find($d->id);
                $model->codigo_dae = $d->dae;
                $model->guia_madre = $d->madre;
                $model->guia_hija = $d->hija;
                $model->save();
                DB::commit();
            }

            $success = true;
            $msg = 'Se han <b>GRABADO</b> las guias correctamente';
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
