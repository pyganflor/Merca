<?php

namespace yura\Http\Controllers\Bodega;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use yura\Http\Controllers\Controller;
use yura\Modelos\CategoriaProducto;
use yura\Modelos\FechaEntrega;
use yura\Modelos\Producto;
use yura\Modelos\Submenu;
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
                ->orderBy('nombre')
                ->get();
            $combos = Producto::where('id_categoria_producto', $cat->id_categoria_producto)
                ->where('estado', 1)
                ->where('combo', 1)
                ->orderBy('nombre')
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
        ]);
    }
}
