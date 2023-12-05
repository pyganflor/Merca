<?php

namespace yura\Http\Controllers\Bodega;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use yura\Http\Controllers\Controller;
use yura\Modelos\CategoriaProducto;
use yura\Modelos\DetallePedidoBodega;
use yura\Modelos\FechaEntrega;
use yura\Modelos\PedidoBodega;
use yura\Modelos\Producto;
use yura\Modelos\Submenu;
use yura\Modelos\Usuario;
use yura\Modelos\UsuarioFinca;

class PedidoClienteController extends Controller
{
    public function inicio(Request $request)
    {
        $finca = UsuarioFinca::where('id_usuario', session('id_usuario'))
            ->get()
            ->first();
        $finca = $finca->empresa;
        $fecha_entrega = FechaEntrega::where('id_empresa', $finca->id_configuracion_empresa)
            ->where('hasta', '>=', hoy())
            ->get()
            ->first();

        $categorias = CategoriaProducto::where('estado', 1)
            ->orderBy('nombre')
            ->get();

        $listado = [];
        foreach ($categorias as $cat) {
            $productos = Producto::where('id_categoria_producto', $cat->id_categoria_producto)
                ->where('estado', 1)
                ->where('combo', 0)
                ->orderBy('orden')
                ->get();
            $combos = Producto::where('id_categoria_producto', $cat->id_categoria_producto)
                ->where('estado', 1)
                ->where('combo', 1)
                ->orderBy('orden')
                ->get();
            $listado[] = [
                'categoria' => $cat,
                'productos' => $productos,
                'combos' => $combos,
            ];
        }

        return view('adminlte.gestion.bodega.pedido_cliente.inicio', [
            'url' => $request->getRequestUri(),
            'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
            'finca' => $finca,
            'fecha_entrega' => $fecha_entrega,
            'listado' => $listado,
            'usuario' => Usuario::find(session('id_usuario'))
        ]);
    }

    public function store_pedido(Request $request)
    {
        DB::beginTransaction();
        //dd($request->all(), json_decode($request->data_productos_peso), json_decode($request->data_productos_no_peso));
        try {
            $usuario = Usuario::find($request->usuario);
            if ($usuario->saldo >= $request->monto_saldo || $request->diferido == -1 || in_array($request->usuario, [1, 2])) {
                /* PEDIDO PARA PRODUCTOS DE NO PESO */
                $pedido = new PedidoBodega();
                $pedido->fecha = hoy();
                $pedido->id_usuario = $request->usuario;
                $pedido->finca_nomina = $request->finca;
                $pedido->id_empresa = $request->finca;
                $pedido->diferido_mes_actual = $request->mes_actual == 'true' ? 1 : 0;
                $pedido->save();
                $pedido->id_pedido_bodega = DB::table('pedido_bodega')
                    ->select(DB::raw('max(id_pedido_bodega) as id'))
                    ->get()[0]->id;

                foreach (json_decode($request->data_productos_no_peso) as $det) {
                    $detalle = new DetallePedidoBodega();
                    $detalle->id_pedido_bodega = $pedido->id_pedido_bodega;
                    $detalle->id_producto = $det->id_prod;
                    $detalle->cantidad = $det->cantidad;
                    $detalle->precio = $det->precio_venta;
                    $detalle->diferido = $request->diferido;
                    $detalle->iva = $det->tiene_iva;
                    $detalle->save();
                }
                if (!in_array($request->usuario, [1, 2]) && $request->diferido != -1) {
                    $usuario->saldo -= $request->monto_saldo;
                    $usuario->save();
                }
                $pedido->saldo_usuario = $usuario->saldo;
                $pedido->save();

                $success = true;
                $msg = '<div class="alert alert-success text-center"><h3><i class="fa fa-fw fa-check"></i>Se ha <b>CREADO</b> el pedido correctamente</h3></div>';

                /* PEDIDO PARA PRODUCTOS DE PESO */
                if (count(json_decode($request->data_productos_peso)) > 0) {
                    $pedido = new PedidoBodega();
                    $pedido->fecha = hoy();
                    $pedido->id_usuario = $request->usuario;
                    $pedido->finca_nomina = $request->finca;
                    $pedido->id_empresa = $request->finca;
                    $pedido->diferido_mes_actual = $request->mes_actual == 'true' ? 1 : 0;
                    $pedido->saldo_usuario = $usuario->saldo;
                    $pedido->save();
                    $pedido->id_pedido_bodega = DB::table('pedido_bodega')
                        ->select(DB::raw('max(id_pedido_bodega) as id'))
                        ->get()[0]->id;

                    foreach (json_decode($request->data_productos_peso) as $det) {
                        $detalle = new DetallePedidoBodega();
                        $detalle->id_pedido_bodega = $pedido->id_pedido_bodega;
                        $detalle->id_producto = $det->id_prod;
                        $detalle->cantidad = $det->cantidad;
                        $detalle->precio = 0;
                        $detalle->diferido = $request->diferido;
                        $detalle->iva = $det->tiene_iva;
                        $detalle->save();
                    }
                    $msg .= '<div class="alert alert-warning text-center"><h3><i class="fa fa-fw fa-exclamation-triangle"></i>Su pedido contiene productos cuyo precio final variará una vez pesados.</h3></div>';
                }
                DB::commit();
            } else {
                DB::rollBack();
                $success = false;
                $msg = '<div class="alert alert-danger text-center">' .
                    'El Usuario no tiene cupo disponible (<b>$' . $usuario->saldo . ' actualmente</b>)</div>';
            }
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
