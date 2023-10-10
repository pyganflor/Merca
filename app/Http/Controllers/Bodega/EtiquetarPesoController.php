<?php

namespace yura\Http\Controllers\Bodega;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use yura\Http\Controllers\Controller;
use yura\Modelos\DetallePedidoBodega;
use yura\Modelos\EtiquetaPeso;
use yura\Modelos\InventarioBodega;
use yura\Modelos\PedidoBodega;
use yura\Modelos\Submenu;
use Picqer\Barcode\BarcodeGeneratorHTML;
use Barryvdh\DomPDF\Facade as PDF;
use yura\Modelos\FechaEntrega;

class EtiquetarPesoController extends Controller
{
    public function inicio(Request $request)
    {
        $pedidos = PedidoBodega::join('detalle_pedido_bodega as d', 'd.id_pedido_bodega', '=', 'pedido_bodega.id_pedido_bodega')
            ->join('producto as prod', 'prod.id_producto', '=', 'd.id_producto')
            ->select('pedido_bodega.*')->distinct()
            ->where('pedido_bodega.armado', 0)
            ->where('prod.peso', 1)
            ->get();
        $fechas_entregas = [];
        foreach ($pedidos as $ped) {
            $entrega = $ped->getFechaEntrega();
            if ($entrega != '' && !in_array($entrega, $fechas_entregas)) {
                $fechas_entregas[] = $entrega;
            }
        }
        return view('adminlte.gestion.bodega.etiquetar_peso.inicio', [
            'url' => $request->getRequestUri(),
            'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
            'fechas_entregas' => $fechas_entregas
        ]);
    }

    public function listar_inventario(Request $request)
    {
        $listado = InventarioBodega::join('producto as p', 'p.id_producto', '=', 'inventario_bodega.id_producto')
            ->select('inventario_bodega.*', 'p.nombre as producto_nombre', 'p.conversion')->distinct()
            ->where('p.peso', 1)
            ->where('p.combo', 0)
            ->where('inventario_bodega.disponibles', '>', 0)
            ->orderBy('inventario_bodega.fecha_registro', 'asc')
            ->orderBy('p.orden')
            ->get();
        return view('adminlte.gestion.bodega.etiquetar_peso.partials.inventario', [
            'listado' => $listado
        ]);
    }

    public function seleccionar_inventario(Request $request)
    {
        $inv_bod = InventarioBodega::find($request->id);
        $detalle_pedidos = DetallePedidoBodega::join('producto as p', 'p.id_producto', '=', 'detalle_pedido_bodega.id_producto')
            ->join('pedido_bodega as ped', 'ped.id_pedido_bodega', '=', 'detalle_pedido_bodega.id_pedido_bodega')
            ->select('detalle_pedido_bodega.*')->distinct()
            ->where('detalle_pedido_bodega.id_producto', $inv_bod->id_producto)
            ->where('detalle_pedido_bodega.precio', 0)
            ->orderBy('ped.fecha', 'asc')
            ->get();
        $listado = [];
        foreach ($detalle_pedidos as $det_ped) {
            $pedido = $det_ped->pedido_bodega;
            $fecha_entrega = $pedido->getFechaEntrega();
            if ($fecha_entrega == $request->fecha) {
                $listado[] = [
                    'pedido' => $pedido,
                    'det_ped' => $det_ped,
                ];
            }
        }
        return view('adminlte.gestion.bodega.etiquetar_peso.partials.pedidos', [
            'listado' => $listado,
            'unidades' => $request->unidades,
            'inv_bod' => $inv_bod,
            'producto' => $inv_bod->producto,
        ]);
    }

    public function store_etiqueta(Request $request)
    {
        try {
            DB::beginTransaction();
            $inventario = InventarioBodega::find($request->id_inv);
            if ($inventario->disponibles > 0) { // hay piezas disponibles
                $model = new EtiquetaPeso();
                $model->id_detalle_pedido_bodega = $request->det_ped;
                $model->id_inventario_bodega = $request->id_inv;
                $model->peso = $request->peso;
                $model->precio_venta = $request->precio_venta;
                $model->save();

                $model = EtiquetaPeso::All()->last();
                $id = $model->id_etiqueta_peso;

                $inventario->disponibles -= 1;
                $inventario->save();

                $producto = $inventario->producto;
                $producto->disponibles -= 1;
                $producto->save();

                $success = true;
                $msg = 'Se ha <b>CREADO</b> la etiqueta correctamente';
                DB::commit();
            } else {
                DB::rollBack();
                $id = '';
                $success = false;
                $msg = '<div class="alert alert-danger text-center">' .
                    'No hay mas piezas disponibles</div>';
            }
        } catch (\Exception $e) {
            DB::rollBack();
            $id = '';
            $success = false;
            $msg = '<div class="alert alert-danger text-center">' .
                '<p> Ha ocurrido un problema al guardar la informacion al sistema</p>' .
                '<p>' . $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine() . '</p>'
                . '</div>';
        }
        return [
            'success' => $success,
            'mensaje' => $msg,
            'id' => $id,
        ];
    }

    public function imprimir_etiqueta(Request $request)
    {
        $barCode = new BarcodeGeneratorHTML();
        $model = EtiquetaPeso::find($request->id);
        $datos = [
            'model' => $model,
        ];
        return PDF::loadView('adminlte.gestion.bodega.etiquetar_peso.partials.pdf_etiqueta', compact('datos', 'barCode'))
            ->setPaper(array(0, 0, 250, 195), 'landscape')->stream();
    }

    public function ver_etiquetas(Request $request)
    {
        $det_ped = DetallePedidoBodega::find($request->det_ped);
        $listado = $det_ped->etiquetas_peso;
        $producto = $det_ped->producto;
        return view('adminlte.gestion.bodega.etiquetar_peso.partials.ver_etiquetas', [
            'listado' => $listado,
            'det_ped' => $det_ped,
            'producto' => $producto,
        ]);
    }

    public function delete_etiqueta(Request $request)
    {
        try {
            DB::beginTransaction();
            $etiqueta = EtiquetaPeso::find($request->id);
            $inventario = $etiqueta->inventario_bodega;
            $inventario->disponibles += 1;
            $inventario->save();

            $producto = $inventario->producto;
            $producto->disponibles += 1;
            $producto->save();

            $etiqueta->delete();

            $success = true;
            $msg = 'Se ha <b>ELIMINADO</b> la etiqueta correctamente';
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
