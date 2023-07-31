<?php

namespace yura\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use yura\Modelos\Cliente;
use yura\Modelos\DetalleCliente;
use yura\Modelos\DetalleEspecificacionEmpaque;
use yura\Modelos\Precio;
use yura\Modelos\Submenu;
use yura\Modelos\ClientePedidoEspecificacion;
use yura\Modelos\Especificacion;
use Validator;
use yura\Modelos\Planta;
use yura\Modelos\Variedad;

class PrecioController extends Controller
{
    public function inicio(Request $request)
    {
        $clientes = DB::table('detalle_cliente as dc')
            ->join('cliente as c', 'c.id_cliente', '=', 'dc.id_cliente')
            ->select('dc.id_cliente', 'dc.nombre')->distinct()
            ->where('dc.estado', 1)
            ->where('c.estado', 1)
            ->orderBy('dc.nombre')
            ->get();
        return view(
            'adminlte.gestion.postcocecha.precio.inicio',
            [
                'url' => $request->getRequestUri(),
                'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
                'text' => ['titulo' => 'Precios', 'subtitulo' => 'modulo de comercializacion'],
                'clientes' => $clientes
            ]
        );
    }
    public function seleccionar_cliente(Request $request)
    {
        $listado = Precio::join('variedad as v', 'v.id_variedad', '=', 'precio.id_variedad')
            ->select('precio.*')->distinct()
            ->where('precio.id_cliente', $request->cliente)
            ->orderBy('precio.longitud')
            ->orderBy('v.id_planta')
            ->orderBy('v.nombre')
            ->get();
        $plantas = Planta::where('estado', 1)
            ->orderBy('nombre')
            ->get();
        return view('adminlte.gestion.postcocecha.precio.forms.seleccionar_cliente', [
            'listado' => $listado,
            'plantas' => $plantas,
            'cliente' => $request->cliente,
        ]);
    }

    public function store_precio(Request $request)
    {
        try {
            DB::beginTransaction();
            if ($request->longitud != '')
                $longitudes = [$request->longitud];
            else {
                $longitudes = [40, 50, 60, 70, 80, 90, 100];
            }
            if ($request->variedad != '')
                $variedades = [$request->variedad];
            else {
                $variedades = DB::table('variedad')
                    ->select('id_variedad')
                    ->where('estado', 1)
                    ->where('id_planta', $request->planta)
                    ->get()->pluck('id_variedad')->toArray();
            }
            foreach ($longitudes as $l) {
                foreach ($variedades as $v) {
                    $existe = Precio::All()
                        ->where('id_cliente', $request->cliente)
                        ->where('id_variedad', $v)
                        ->where('longitud', $l)
                        ->first();
                    if ($existe == '') {
                        $model = new Precio();
                        $model->id_cliente = $request->cliente;
                        $model->id_variedad = $v;
                        $model->longitud = $l;
                        $model->cantidad = $request->precio;
                        $model->save();
                    }
                }
            }

            DB::commit();
            $success = true;
            $msg = 'Se han <strong>GRABADO</strong> los precios correctamente';
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

    public function update_precio(Request $request)
    {
        try {
            DB::beginTransaction();

            $model = Precio::find($request->id);
            $model->cantidad = $request->precio;
            $model->save();

            DB::commit();
            $success = true;
            $msg = 'Se ha <strong>MODIFICADO</strong> el precio correctamente';
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

    public function delete_precio(Request $request)
    {
        try {
            DB::beginTransaction();

            $model = Precio::find($request->id);
            $model->delete();

            DB::commit();
            $success = true;
            $msg = 'Se ha <strong>ELIMINADO</strong> el precio correctamente';
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
