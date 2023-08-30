<?php

namespace yura\Http\Controllers\Bodega;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use phpDocumentor\Reflection\PseudoTypes\False_;
use yura\Http\Controllers\Controller;
use yura\Modelos\CategoriaProducto;
use yura\Modelos\DetallePedidoBodega;
use yura\Modelos\FechaEntrega;
use yura\Modelos\PedidoBodega;
use yura\Modelos\Producto;
use yura\Modelos\Submenu;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Barryvdh\DomPDF\Facade as PDF;
use Picqer\Barcode\BarcodeGeneratorHTML;

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
        $query = PedidoBodega::where('estado', 1);
        if ($request->finca != 'T')
            $query = $query->where('id_empresa', $request->finca);
        if (!in_array(session('id_usuario'), [1, 2]))
            $query = $query->where('id_usuario', session('id_usuario'));
        $query = $query->orderBy('fecha')
            ->orderBy('id_empresa')
            ->orderBy('id_usuario')
            ->get();
        $listado = [];
        foreach ($query as $q) {
            $fecha_entrega = $q->getFechaEntrega();
            if ($fecha_entrega == $request->entrega) {
                $listado[] = $q;
            }
        }

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
        $listado = $listado->orderBy('orden')
            ->get();

        return view('adminlte.gestion.bodega.pedido.forms._listar_catalogo', [
            'listado' => $listado
        ]);
    }

    public function seleccionar_finca_filtro(Request $request)
    {
        $listado = FechaEntrega::orderBy('entrega', 'asc')->get();
        if ($request->finca != 'T')
            $listado = $listado->where('id_empresa', $request->finca);

        $pos_selected = -1;
        foreach ($listado as $pos => $item) {
            if ($item->entrega >= hoy()) {
                $pos_selected = $pos;
                break;
            }
        }

        $options = '<option value="">Seleccione</option>';
        foreach ($listado as $pos => $item) {
            $selected = '';
            if ($pos_selected == $pos)
                $selected = 'selected';
            $options .= '<option value="' . $item->entrega . '" ' . $selected . '>' . convertDateToText($item->entrega) . ' - ' . $item->empresa->nombre . '</option>';
        }

        return [
            'options' => $options
        ];
    }

    public function seleccionar_finca(Request $request)
    {
        $listado = DB::table('usuario_finca as uf')
            ->join('usuario as u', 'u.id_usuario', '=', 'uf.id_usuario')
            ->select('uf.id_usuario', 'u.nombre_completo', 'u.username', 'u.saldo')->distinct()
            ->where('uf.id_empresa', $request->finca)
            ->where('u.estado', 'A')
            ->where('u.aplica', 1)
            ->orderBy('u.nombre_completo')
            ->get();

        $options_usuarios = '<option value="">Seleccione</option>';
        foreach ($listado as $item) {
            $options_usuarios .= '<option value="' . $item->id_usuario . '">' . $item->nombre_completo . ' CI:' . $item->username . ' saldo:$' . $item->saldo . '</option>';
        }

        return [
            'options_usuarios' => $options_usuarios
        ];
    }

    public function store_pedido(Request $request)
    {
        DB::beginTransaction();
        try {
            $usuario = getUsuario($request->usuario);
            if ($usuario->saldo >= $request->monto_total || in_array($request->usuario, [1, 2])) {
                $pedido = new PedidoBodega();
                $pedido->fecha = $request->fecha;
                $pedido->id_usuario = $request->usuario;
                $pedido->id_empresa = $request->finca;
                $pedido->save();
                $pedido = PedidoBodega::All()->last();

                foreach (json_decode($request->detalles) as $det) {
                    $detalle = new DetallePedidoBodega();
                    $detalle->id_pedido_bodega = $pedido->id_pedido_bodega;
                    $detalle->id_producto = $det->producto;
                    $detalle->cantidad = $det->cantidad;
                    $detalle->precio = $det->precio_venta;
                    $detalle->iva = $det->iva;
                    $detalle->save();
                }

                if (!in_array($request->usuario, [1, 2])) {
                    $usuario->saldo -= $request->monto_total;
                    $usuario->save();
                    $pedido->saldo_usuario = $usuario->saldo;
                    $pedido->save();
                }

                $success = true;
                $msg = 'Se ha <b>CREADO</b> el pedido correctamente';
            } else {
                $success = false;
                $msg = '<div class="alert alert-danger text-center">' .
                    'El Usuario no tiene cupo disponible (<b>$' . $usuario->saldo . ' actualmente</b>)</div>';
            }

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

    public function delete_pedido(Request $request)
    {
        DB::beginTransaction();
        try {
            $pedido = PedidoBodega::find($request->ped);
            if (!in_array($pedido->id_usuario, [1, 2])) {
                $monto_total = $pedido->getTotalMonto();
                $usuario = $pedido->usuario;
                $usuario->saldo += $monto_total;
                $usuario->save();
            }
            $pedido->delete();

            $success = true;
            $msg = 'Se ha <b>CANCELADO</b> el pedido correctamente';

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

    public function ver_pedido(Request $request)
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
        $pedido = PedidoBodega::find($request->ped);

        return view('adminlte.gestion.bodega.pedido.forms.ver_pedido', [
            'fincas' => $fincas,
            'categorias' => $categorias,
            'pedido' => $pedido,
        ]);
    }

    public function update_pedido(Request $request)
    {
        DB::beginTransaction();
        try {
            $pedido_delete = PedidoBodega::find($request->ped);
            $usuario = $pedido_delete->usuario;
            $valida_saldo = true;
            if (!in_array($pedido_delete->id_usuario, [1, 2])) {
                $monto_total_anterior = $pedido_delete->getTotalMonto();
                $saldo_anterior = $usuario->saldo;
                $usuario->saldo += $monto_total_anterior;

                if ($usuario->saldo >= $request->monto_total) {
                    $usuario->saldo -= $request->monto_total;
                    $usuario->save();
                    $valida_saldo = true;
                } else {
                    $valida_saldo = false;
                }
            }
            if ($valida_saldo) {
                $pedido_delete->delete();

                $pedido = new PedidoBodega();
                $pedido->fecha = $request->fecha;
                $pedido->id_usuario = $request->usuario;
                $pedido->id_empresa = $request->finca;
                $pedido->saldo_usuario = $usuario->saldo;
                $pedido->save();
                $pedido = PedidoBodega::All()->last();

                foreach (json_decode($request->detalles) as $det) {
                    $detalle = new DetallePedidoBodega();
                    $detalle->id_pedido_bodega = $pedido->id_pedido_bodega;
                    $detalle->id_producto = $det->producto;
                    $detalle->cantidad = $det->cantidad;
                    $detalle->precio = $det->precio_venta;
                    $detalle->iva = $det->iva;
                    $detalle->save();
                }

                $success = true;
                $msg = 'Se ha <b>MODIFICADO</b> el pedido correctamente';
            } else {
                $success = false;
                $msg = '<div class="alert alert-danger text-center">' .
                    'El Usuario no tiene cupo disponible (<b>$' . $saldo_anterior . ' actualmente</b>)</div>';
            }

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

    public function armar_pedido(Request $request)
    {
        DB::beginTransaction();
        try {
            $pedido = PedidoBodega::find($request->ped);
            $models_productos = [];
            foreach ($pedido->detalles as $det) {
                $producto = $det->producto;
                if ($producto->disponibles >= $det->cantidad) {
                    $producto->disponibles -= $det->cantidad;
                    $models_productos[] = $producto;
                } else {
                    return [
                        'success' => false,
                        'mensaje' => '<div class="alert alert-danger text-center">' .
                            'No hay disponibilidad para el producto: "' . $producto->nombre . '"' .
                            '</div>',
                    ];
                }
            }
            $pedido->armado = 1;
            $pedido->save();

            foreach ($models_productos as $p) {
                $p->save();
            }

            $success = true;
            $msg = 'Se ha <b>ARMADO</b> el pedido correctamente';

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

    public function exportar_resumen_pedidos(Request $request)
    {
        $spread = new Spreadsheet();
        $this->excel_resumen_pedidos($spread, $request);

        $fileName = "RESUMEN PEDIDOS.xlsx";
        $writer = new Xlsx($spread);

        //--------------------------- GUARDAR EL EXCEL -----------------------

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . urlencode($fileName) . '"');
        $writer->save('php://output');

        //$writer->save('/var/www/html/Dasalflor/storage/storage/excel/excel_prueba.xlsx');
    }

    public function excel_resumen_pedidos($spread, $request)
    {
        $query = PedidoBodega::where('estado', 1);
        if ($request->finca != 'T')
            $query = $query->where('id_empresa', $request->finca);
        if (!in_array(session('id_usuario'), [1, 2]))
            $query = $query->where('id_usuario', session('id_usuario'));
        $query = $query->orderBy('fecha')
            ->orderBy('id_empresa')
            ->orderBy('id_usuario')
            ->get();
        $pedidos = [];
        foreach ($query as $q) {
            $fecha_entrega = $q->getFechaEntrega();
            if ($fecha_entrega == $request->entrega) {
                $pedidos[] = $q;
            }
        }
        $listado = [];
        foreach ($pedidos as $pedido) {
            foreach ($pedido->detalles as $det) {
                $producto = $det->producto;
                $cantidad = $det->cantidad;

                $pos_existe = -1;
                foreach ($listado as $pos => $item) {
                    if ($item['producto']->id_producto == $producto->id_producto) {
                        $pos_existe = $pos;
                    }
                }
                if ($pos_existe == -1) {
                    $listado[] = [
                        'producto' => $producto,
                        'cantidad' => $cantidad,
                    ];
                } else {
                    $listado[$pos_existe]['cantidad'] += $cantidad;
                }
            }
        }

        $columnas = getColumnasExcel();
        $sheet = $spread->getActiveSheet();
        $sheet->setTitle('RESUMEN ' . convertDateToText($request->entrega));

        $row = 1;
        $col = 0;
        setValueToCeldaExcel($sheet, $columnas[$col] . $row, 'Producto');
        $col++;
        setValueToCeldaExcel($sheet, $columnas[$col] . $row, 'Inventario');
        $col++;
        setValueToCeldaExcel($sheet, $columnas[$col] . $row, 'Pedido');
        $col++;
        setValueToCeldaExcel($sheet, $columnas[$col] . $row, 'Compra');
        setBgToCeldaExcel($sheet, 'A' . $row . ':' . $columnas[$col] . $row, '00B388');
        setColorTextToCeldaExcel($sheet, 'A' . $row . ':' . $columnas[$col] . $row, 'FFFFFF');

        foreach ($listado as $r) {
            $row++;
            $col = 0;
            setValueToCeldaExcel($sheet, $columnas[$col] . $row, $r['producto']->nombre);
            setBgToCeldaExcel($sheet, $columnas[$col] . $row, '5a7177');
            setColorTextToCeldaExcel($sheet, $columnas[$col] . $row, 'FFFFFF');
            $col++;
            setValueToCeldaExcel($sheet, $columnas[$col] . $row, $r['producto']->disponibles);
            $col++;
            setValueToCeldaExcel($sheet, $columnas[$col] . $row, $r['cantidad']);
            $saldo = $r['producto']->disponibles - $r['cantidad'];
            $col++;
            setValueToCeldaExcel($sheet, $columnas[$col] . $row, $saldo < 0 ? abs($saldo) : 0);
        }

        setTextCenterToCeldaExcel($sheet, 'A1:' . $columnas[$col] . $row);
        setBorderToCeldaExcel($sheet, 'A1:' . $columnas[$col] . $row);

        for ($i = 0; $i <= $col; $i++)
            $sheet->getColumnDimension($columnas[$i])->setAutoSize(true);
    }

    public function imprimir_pedido(Request $request)
    {
        $barCode = new BarcodeGeneratorHTML();
        $pedido = PedidoBodega::find($request->pedido);
        $datos = [
            'pedido' => $pedido,
        ];
        return PDF::loadView('adminlte.gestion.bodega.pedido.partials.pdf_pedido', compact('datos', 'barCode'))
            ->setPaper(array(0, 0, 500, 195), 'landscape')->stream();
    }

    public function imprimir_pedidos_all(Request $request)
    {
        $barCode = new BarcodeGeneratorHTML();
        $query = PedidoBodega::where('estado', 1);
        if ($request->finca != 'T')
            $query = $query->where('id_empresa', $request->finca);
        if (!in_array(session('id_usuario'), [1, 2]))
            $query = $query->where('id_usuario', session('id_usuario'));
        $query = $query->orderBy('fecha')
            ->orderBy('id_empresa')
            ->orderBy('id_usuario')
            ->get();
        $pedidos = [];
        foreach ($query as $q) {
            $fecha_entrega = $q->getFechaEntrega();
            if ($fecha_entrega == $request->entrega) {
                $pedidos[] = $q;
            }
        }
        $datos = [
            'pedidos' => $pedidos,
        ];
        return PDF::loadView('adminlte.gestion.bodega.pedido.partials.pdf_pedido_all', compact('datos', 'barCode'))
            ->setPaper(array(0, 0, 360, 250), 'landscape')->stream();
    }
}
