<?php

namespace yura\Http\Controllers\Bodega;

use DB;
use Illuminate\Http\Request;
use yura\Http\Controllers\Controller;
use yura\Modelos\CategoriaProducto;
use yura\Modelos\IngresoBodega;
use yura\Modelos\Producto;
use yura\Modelos\Proveedor;
use yura\Modelos\SalidaBodega;
use yura\Modelos\Sector;
use yura\Modelos\Submenu;

class MovimientosBodegaController extends Controller
{
    public function inicio(Request $request)
    {
        $categorias = CategoriaProducto::where('estado', 1)
            ->orderBy('nombre')
            ->get();
        return view('adminlte.gestion.bodega.movimientos_bodega.inicio', [
            'url' => $request->getRequestUri(),
            'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
            'categorias' => $categorias
        ]);
    }

    public function listar_reporte(Request $request)
    {
        $listado = Producto::Where(function ($q) use ($request) {
            $q->Where('nombre', 'like', '%' . mb_strtoupper($request->busqueda) . '%')
                ->orWhere('codigo', 'like', '%' . mb_strtoupper($request->busqueda) . '%');
        });
        if ($request->categoria != 'T')
            $listado = $listado->where('id_categoria_producto', $request->categoria);
        $listado = $listado->orderBy('orden')
            ->get();

        return view('adminlte.gestion.bodega.movimientos_bodega.partials.listado', [
            'listado' => $listado,
        ]);
    }

    public function add_ingresos(Request $request)
    {
        $listado = Producto::orderBy('orden')
            ->get();
        $proveedores = Proveedor::where('estado', 1)
            ->orderBy('nombre')
            ->get();

        return view('adminlte.gestion.bodega.movimientos_bodega.forms.add_ingresos', [
            'listado' => $listado,
            'proveedores' => $proveedores,
        ]);
    }

    public function store_ingresos(Request $request)
    {
        try {
            DB::beginTransaction();
            foreach (json_decode($request->data) as $d) {
                $producto = Producto::find($d->id_prod);
                $producto->disponibles += $d->unidades;
                $producto->save();
                bitacora('producto', $producto->id_producto, 'U', 'INGRESO A BODEGA de ' . $d->unidades . ' UNIDADES');

                /* INGRESO_BODEGA */
                $ingreso = new IngresoBodega();
                $ingreso->id_producto = $d->id_prod;
                $ingreso->fecha = $request->fecha;
                $ingreso->cantidad = $d->unidades;
                $ingreso->precio = $d->precio;
                $ingreso->id_empresa = getFincaActiva();
                $ingreso->save();
                $ingreso = IngresoBodega::All()->last();
                bitacora('ingreso_bodega', $ingreso->id_ingreso_bodega, 'I', 'INGRESO A BODEGA de ' . $d->unidades . ' UNIDADES de ' . $producto->nombre);
            }

            DB::commit();
            $success = true;
            $msg = 'Se han <strong>GRABADO</strong> los ingresos correctamente';
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

    public function add_salidas(Request $request)
    {
        $listado = Producto::orderBy('orden')
            ->get();
        $empresas = DB::table('configuracion_empresa')
            ->where('proveedor', 0)
            ->orderBy('nombre')
            ->get();

        return view('adminlte.gestion.bodega.movimientos_bodega.forms.add_salidas', [
            'listado' => $listado,
            'empresas' => $empresas,
        ]);
    }

    public function store_salidas(Request $request)
    {
        try {
            DB::beginTransaction();
            foreach (json_decode($request->data) as $d) {
                $producto = Producto::find($d->id_prod);
                if ($producto->disponibles >= $d->unidades) {
                    $producto->disponibles -= $d->unidades;
                    $producto->save();
                    bitacora('producto', $producto->id_producto, 'U', 'SALIDA DE BODEGA de ' . $d->unidades . ' UNIDADES');

                    /* INGRESO_BODEGA */
                    $ingreso = new SalidaBodega();
                    $ingreso->id_producto = $d->id_prod;
                    $ingreso->fecha = $request->fecha;
                    $ingreso->cantidad = $d->unidades;
                    $ingreso->save();
                    $ingreso = SalidaBodega::All()->last();
                    bitacora('salida_bodega', $ingreso->id_salida_bodega, 'I', 'SALIDA A BODEGA de ' . $d->unidades . ' UNIDADES de ' . $producto->nombre);
                } else {
                    DB::rollBack();
                    $success = false;
                    $msg = '<div class="alert alert-danger text-center">La cantidad a sacar del producto: <b>' . $producto->nombre . '</b> es <b>MAYOR</b> que el <strong>DISPONIBLE</strong> en bodega</div>';

                    return [
                        'success' => $success,
                        'mensaje' => $msg,
                    ];
                }
            }

            DB::commit();
            $success = true;
            $msg = 'Se han <strong>GRABADO</strong> los ingresos correctamente';
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

    public function seleccionar_finca(Request $request)
    {
        $sectores = Sector::where('id_empresa', $request->finca)
            ->where('estado', 1)
            ->orderBy('nombre')
            ->get();
        $options = '<option value="">Seleccione</option>';
        foreach ($sectores as $s)
            $options .= '<option value="' . $s->id_sector . '">' . $s->nombre . '</option>';
        return [
            'sectores' => $options,
        ];
    }
}
