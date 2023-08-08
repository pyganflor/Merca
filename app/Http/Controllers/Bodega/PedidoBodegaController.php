<?php

namespace yura\Http\Controllers\Bodega;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use yura\Http\Controllers\Controller;
use yura\Modelos\CategoriaProducto;
use yura\Modelos\Producto;
use yura\Modelos\Submenu;

class PedidoBodegaController extends Controller
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

        return view('adminlte.gestion.bodega.pedido.inicio', [
            'url' => $request->getRequestUri(),
            'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
            'fincas' => $fincas
        ]);
    }

    public function listar_reporte(Request $request)
    {
        $listado = [];

        return view('adminlte.gestion.bodega.pedido.partials.listado', [
            'listado' => $listado
        ]);
    }

    public function add_pedido(Request $request)
    {
        $fincas = DB::table('configuracion_empresa as emp')
            ->join('usuario_finca as uf', 'uf.id_empresa', '=', 'emp.id_configuracion_empresa')
            ->select('emp.nombre', 'uf.id_empresa')->distinct()
            ->where('emp.proveedor', 0)
            ->where('emp.estado', 1)
            ->where('uf.id_usuario', session('id_usuario'))
            ->orderBy('emp.nombre')
            ->get();
        $categorias = CategoriaProducto::where('estado', 1)
            ->orderBy('nombre')
            ->get();

        return view('adminlte.gestion.bodega.pedido.forms.add_pedido', [
            'fincas' => $fincas,
            'categorias' => $categorias,
        ]);
    }

    public function listar_catalogo(Request $request)
    {
        $listado = Producto::Where(function ($q) use ($request) {
            $q->Where('nombre', 'like', '%' . mb_strtoupper($request->busqueda) . '%')
                ->orWhere('codigo', 'like', '%' . mb_strtoupper($request->busqueda) . '%');
        });
        if ($request->categoria != 'T')
            $listado = $listado->where('id_categoria_producto', $request->categoria);
        $listado = $listado->orderBy('nombre')
            ->get();

        return view('adminlte.gestion.bodega.pedido.forms._listar_catalogo', [
            'listado' => $listado
        ]);
    }

    public function seleccionar_finca(Request $request)
    {
        $listado = DB::table('usuario_finca as uf')
            ->join('usuario as u', 'u.id_usuario', '=', 'uf.id_usuario')
            ->select('uf.id_usuario', 'u.nombre_completo')->distinct()
            ->where('uf.id_empresa', $request->finca)
            ->orderBy('u.nombre_completo')
            ->get();

        $options_usuarios = '<option value="">Seleccione</option>';
        foreach ($listado as $item) {
            $options_usuarios .= '<option value="' . $item->id_usuario . '">' . $item->nombre_completo . '</option>';
        }

        return [
            'options_usuarios' => $options_usuarios
        ];
    }
}