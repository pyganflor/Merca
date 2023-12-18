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
use yura\Modelos\Usuario;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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
            //->where('p.finca_nomina', $request->finca)
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
            $dia_entrega = date('d', strtotime($fecha_entrega));

            $diferido_selected = $det_ped->diferido;
            $diferido_mes_inicial = $det_ped->diferido_mes_actual ? 0 : 1;
            $diferido_mes_final = $det_ped->diferido_mes_actual ? $diferido_selected - 1 : $diferido_selected;

            $diferido_fecha_inicial = new DateTime($fecha_entrega);
            $diferido_fecha_inicial->modify('first day of +' . $diferido_mes_inicial . ' month');
            $diferido_fecha_inicial = $diferido_fecha_inicial->format('Y-m-d');

            $diferido_fecha_inicial = date('Y', strtotime($diferido_fecha_inicial)) . '-' . date('m', strtotime($diferido_fecha_inicial)) . '-' . $dia_entrega;
            list($ano, $mes, $dia) = explode('-', $diferido_fecha_inicial);
            $d = 1;
            while (!checkdate($mes, $dia, $ano)) {
                $diferido_fecha_inicial = new DateTime($diferido_fecha_inicial);
                $diferido_fecha_inicial->modify('-' . $d . ' day');
                $diferido_fecha_inicial = $diferido_fecha_inicial->format('Y-m-d');
                list($ano, $mes, $dia) = explode('-', $diferido_fecha_inicial);
                $d++;
            }

            $diferido_fecha_final = new DateTime($fecha_entrega);
            $diferido_fecha_final->modify('first day of +' . $diferido_mes_final . ' month');
            $diferido_fecha_final = $diferido_fecha_final->format('Y-m-d');

            $diferido_fecha_final = date('Y', strtotime($diferido_fecha_final)) . '-' . date('m', strtotime($diferido_fecha_final)) . '-' . $dia_entrega;
            list($ano, $mes, $dia) = explode('-', $diferido_fecha_final);
            $d = 1;
            if (!checkdate($mes, $dia, $ano)) {
                $diferido_fecha_final = new DateTime($diferido_fecha_final);
                $diferido_fecha_final->modify('-' . $d . ' day');
                $diferido_fecha_final = $diferido_fecha_final->format('Y-m-d');
                list($ano, $mes, $dia) = explode('-', $diferido_fecha_final);
                $d++;
            }

            if (($diferido_fecha_inicial >= $primerDiaMes && $diferido_fecha_inicial <= $ultimoDiaMes) ||
                ($diferido_fecha_final >= $primerDiaMes && $diferido_fecha_final <= $ultimoDiaMes) ||
                ($diferido_fecha_inicial <= $primerDiaMes && $diferido_fecha_final >= $ultimoDiaMes) ||
                $diferido_fecha_inicial >= $primerDiaMes
            ) {
                $rango_diferido = $det_ped->getRangoDiferidoByFecha($fecha_entrega);
                $pagos_pendientes = [];
                foreach ($rango_diferido as $pos_f => $f) {
                    if ($f >= $primerDiaMes) {
                        $num_pago = $pos_f + 1;
                        $pagos_pendientes[] = $num_pago;
                    }
                }
                $det_ped->fecha_entrega = $fecha_entrega;
                $det_ped->pagos_pendientes = $pagos_pendientes;
                $listado[] = $det_ped;
            }
        }

        $mes_anterior = new DateTime($hoy);
        $mes_anterior->modify('first day of -1 month');
        $mes_anterior = $mes_anterior->format('Y-m-d');

        $primerDiaMes = date("Y-m-01", strtotime($mes_anterior));
        $ultimoDiaMes = date("Y-m-t", strtotime($mes_anterior));
        $query_pedidos = DetallePedidoBodega::join('pedido_bodega as p', 'p.id_pedido_bodega', '=', 'detalle_pedido_bodega.id_pedido_bodega')
            ->select('detalle_pedido_bodega.*', 'p.fecha', 'p.id_empresa', 'p.finca_nomina')->distinct()
            ->where('p.id_usuario', $request->usuario)
            ->where('p.finca_nomina', $request->finca)
            ->where('p.estado', 1)
            ->where('detalle_pedido_bodega.diferido', '!=', -1)
            ->orderBy('p.fecha')
            ->get();

        foreach ($query_pedidos as $det_ped) {
            if ($det_ped->diferido == null || $det_ped->diferido == 0) {
                $entrega = FechaEntrega::All()
                    ->where('desde', '<=', $det_ped->fecha)
                    ->where('hasta', '>=', $det_ped->fecha)
                    ->where('id_empresa', $det_ped->id_empresa)
                    ->first();
                $fecha_entrega = $entrega != '' ? $entrega->entrega : '';
                if ($fecha_entrega >= $primerDiaMes) {
                    $det_ped->fecha_entrega = $fecha_entrega;
                    $listado[] = $det_ped;
                }
            }
        }
        return view('adminlte.gestion.bodega.descuentos_usuario.partials.listado', [
            'listado' => $listado,
            'usuario' => Usuario::find($request->usuario)
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

    public function exportar_reporte(Request $request)
    {
        $spread = new Spreadsheet();
        $this->excel_reporte($spread, $request);

        $fileName = "Descuentos del usuario.xlsx";
        $writer = new Xlsx($spread);

        //--------------------------- GUARDAR EL EXCEL -----------------------

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . urlencode($fileName) . '"');
        $writer->save('php://output');

        //$writer->save('/var/www/html/Dasalflor/storage/storage/excel/excel_prueba.xlsx');
    }

    public function excel_reporte($spread, $request)
    {
        $hoy = hoy();
        $primerDiaMes = date("Y-m-01", strtotime($hoy));
        $ultimoDiaMes = date("Y-m-t", strtotime($hoy));

        $query_pedidos = DetallePedidoBodega::join('pedido_bodega as p', 'p.id_pedido_bodega', '=', 'detalle_pedido_bodega.id_pedido_bodega')
            ->select('detalle_pedido_bodega.*', 'p.fecha', 'p.id_empresa', 'p.diferido_mes_actual')->distinct()
            ->where('p.id_usuario', $request->usuario)
            ->where('p.finca_nomina', $request->finca)
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
            $dia_entrega = date('d', strtotime($fecha_entrega));

            $diferido_selected = $det_ped->diferido;
            $diferido_mes_inicial = $det_ped->diferido_mes_actual ? 0 : 1;
            $diferido_mes_final = $det_ped->diferido_mes_actual ? $diferido_selected - 1 : $diferido_selected;

            $diferido_fecha_inicial = new DateTime($fecha_entrega);
            $diferido_fecha_inicial->modify('first day of +' . $diferido_mes_inicial . ' month');
            $diferido_fecha_inicial = $diferido_fecha_inicial->format('Y-m-d');

            $diferido_fecha_inicial = date('Y', strtotime($diferido_fecha_inicial)) . '-' . date('m', strtotime($diferido_fecha_inicial)) . '-' . $dia_entrega;
            list($ano, $mes, $dia) = explode('-', $diferido_fecha_inicial);
            $d = 1;
            while (!checkdate($mes, $dia, $ano)) {
                $diferido_fecha_inicial = new DateTime($diferido_fecha_inicial);
                $diferido_fecha_inicial->modify('-' . $d . ' day');
                $diferido_fecha_inicial = $diferido_fecha_inicial->format('Y-m-d');
                list($ano, $mes, $dia) = explode('-', $diferido_fecha_inicial);
                $d++;
            }

            $diferido_fecha_final = new DateTime($fecha_entrega);
            $diferido_fecha_final->modify('first day of +' . $diferido_mes_final . ' month');
            $diferido_fecha_final = $diferido_fecha_final->format('Y-m-d');

            $diferido_fecha_final = date('Y', strtotime($diferido_fecha_final)) . '-' . date('m', strtotime($diferido_fecha_final)) . '-' . $dia_entrega;
            list($ano, $mes, $dia) = explode('-', $diferido_fecha_final);
            $d = 1;
            if (!checkdate($mes, $dia, $ano)) {
                $diferido_fecha_final = new DateTime($diferido_fecha_final);
                $diferido_fecha_final->modify('-' . $d . ' day');
                $diferido_fecha_final = $diferido_fecha_final->format('Y-m-d');
                list($ano, $mes, $dia) = explode('-', $diferido_fecha_final);
                $d++;
            }

            if (($diferido_fecha_inicial >= $primerDiaMes && $diferido_fecha_inicial <= $ultimoDiaMes) ||
                ($diferido_fecha_final >= $primerDiaMes && $diferido_fecha_final <= $ultimoDiaMes) ||
                ($diferido_fecha_inicial <= $primerDiaMes && $diferido_fecha_final >= $ultimoDiaMes) ||
                $diferido_fecha_inicial >= $primerDiaMes
            ) {
                $rango_diferido = $det_ped->getRangoDiferidoByFecha($fecha_entrega);
                $pagos_pendientes = [];
                foreach ($rango_diferido as $pos_f => $f) {
                    if ($f >= $primerDiaMes) {
                        $num_pago = $pos_f + 1;
                        $pagos_pendientes[] = $num_pago;
                    }
                }
                $det_ped->fecha_entrega = $fecha_entrega;
                $det_ped->pagos_pendientes = $pagos_pendientes;
                $listado[] = $det_ped;
            }
        }

        $mes_anterior = new DateTime($hoy);
        $mes_anterior->modify('first day of -1 month');
        $mes_anterior = $mes_anterior->format('Y-m-d');

        $primerDiaMes = date("Y-m-01", strtotime($mes_anterior));
        $ultimoDiaMes = date("Y-m-t", strtotime($mes_anterior));
        $query_pedidos = DetallePedidoBodega::join('pedido_bodega as p', 'p.id_pedido_bodega', '=', 'detalle_pedido_bodega.id_pedido_bodega')
            ->select('detalle_pedido_bodega.*', 'p.fecha', 'p.id_empresa', 'p.finca_nomina')->distinct()
            ->where('p.id_usuario', $request->usuario)
            ->where('p.finca_nomina', $request->finca)
            ->where('p.estado', 1)
            ->where('detalle_pedido_bodega.diferido', '!=', -1)
            ->orderBy('p.fecha')
            ->get();

        foreach ($query_pedidos as $det_ped) {
            if ($det_ped->diferido == null || $det_ped->diferido == 0) {
                $entrega = FechaEntrega::All()
                    ->where('desde', '<=', $det_ped->fecha)
                    ->where('hasta', '>=', $det_ped->fecha)
                    ->where('id_empresa', $det_ped->id_empresa)
                    ->first();
                $fecha_entrega = $entrega != '' ? $entrega->entrega : '';
                if ($fecha_entrega >= $primerDiaMes) {
                    $det_ped->fecha_entrega = $fecha_entrega;
                    $listado[] = $det_ped;
                }
            }
        }
        $usuario = Usuario::find($request->usuario);

        $columnas = getColumnasExcel();
        $sheet = $spread->getActiveSheet();
        $sheet->setTitle('Descuentos');

        $row = 1;
        $col = 0;
        setValueToCeldaExcel($sheet, $columnas[$col] . $row, 'Descuentos de ' . $usuario->nombre_completo . ' fecha ' . explode(' del ', convertDateToText(hoy()))[0]);
        $sheet->mergeCells('A' . $row . ':G' . $row);

        $row++;
        $col = 0;
        setValueToCeldaExcel($sheet, $columnas[$col] . $row, 'Fecha');
        $col++;
        setValueToCeldaExcel($sheet, $columnas[$col] . $row, 'Producto');
        $col++;
        setValueToCeldaExcel($sheet, $columnas[$col] . $row, 'Subtotal');
        $col++;
        setValueToCeldaExcel($sheet, $columnas[$col] . $row, 'Iva');
        $col++;
        setValueToCeldaExcel($sheet, $columnas[$col] . $row, 'Monto Unitario');
        $col++;
        setValueToCeldaExcel($sheet, $columnas[$col] . $row, 'Monto Total');
        $col++;
        setValueToCeldaExcel($sheet, $columnas[$col] . $row, '#Pago');
        setBgToCeldaExcel($sheet, 'A' . $row . ':' . $columnas[$col] . $row, '00B388');
        setColorTextToCeldaExcel($sheet, 'A' . $row . ':' . $columnas[$col] . $row, 'FFFFFF');

        $monto_subtotal = 0;
        $monto_total_iva = 0;
        $monto_unitario = 0;
        $monto_total = 0;
        foreach ($listado as $item) {
            $producto = $item->producto;
            $precio_prod = 0;
            if ($producto->peso == 1) {
                foreach ($item->etiquetas_peso as $e) {
                    $precio_prod += $e->peso * $e->precio_venta;
                }
            } else {
                $precio_prod = $item->cantidad * $item->precio;
            }
            if ($item->diferido == 0 || $item->diferido == null) {
                $monto_pedido = $precio_prod;
            } else {
                $monto_pedido = $precio_prod / $item->diferido;
            }
            if ($item->iva == true) {
                $subtotal = $precio_prod / 1.12;
                $iva = ($precio_prod / 1.12) * 0.12;
            } else {
                $subtotal = $precio_prod;
                $iva = 0;
            }
            if ($item->diferido > 0) {
                $subtotal = $subtotal / $item->diferido;
                $iva = $iva / $item->diferido;
            }

            $monto_subtotal += $subtotal;
            $monto_total_iva += $iva;
            $monto_unitario += $monto_pedido;
            if ($item->diferido > 0) {
                $monto_total += $monto_pedido * count($item->pagos_pendientes);
            }

            $row++;
            $col = 0;
            setValueToCeldaExcel($sheet, $columnas[$col] . $row, convertDateToText($item->fecha_entrega));
            $col++;
            setValueToCeldaExcel($sheet, $columnas[$col] . $row, $producto->nombre);
            $col++;
            setValueToCeldaExcel($sheet, $columnas[$col] . $row, round($subtotal, 2));
            $col++;
            setValueToCeldaExcel($sheet, $columnas[$col] . $row, round($iva, 2));
            $col++;
            setValueToCeldaExcel($sheet, $columnas[$col] . $row, round($monto_pedido, 2));
            $col++;
            if ($item->diferido > 0)
                setValueToCeldaExcel($sheet, $columnas[$col] . $row, round($monto_pedido * count($item->pagos_pendientes), 2));
            else
                setValueToCeldaExcel($sheet, $columnas[$col] . $row, round($monto_pedido, 2));
            $col++;
            if ($item->diferido > 0) {
                $pagos = '';
                foreach ($item->pagos_pendientes as $pos_p => $p) {
                    if ($pos_p == 0)
                        $pagos .= $p . '°';
                    else
                        $pagos .= ' | ' . $p . '°';
                }
            } else {
                $pagos = 'No Dif.';
            }
            setValueToCeldaExcel($sheet, $columnas[$col] . $row, $pagos);
        }
        $row++;
        $col = 0;
        setValueToCeldaExcel($sheet, $columnas[$col] . $row, 'Totales');
        $col += 2;
        setValueToCeldaExcel($sheet, $columnas[$col] . $row, round($monto_subtotal, 2));
        $col++;
        setValueToCeldaExcel($sheet, $columnas[$col] . $row, round($monto_total_iva, 2));
        $col++;
        setValueToCeldaExcel($sheet, $columnas[$col] . $row, round($monto_unitario, 2));
        $col++;
        setValueToCeldaExcel($sheet, $columnas[$col] . $row, round($monto_total, 2));
        $col++;
        setBgToCeldaExcel($sheet, 'A' . $row . ':' . $columnas[$col] . $row, '00B388');
        setColorTextToCeldaExcel($sheet, 'A' . $row . ':' . $columnas[$col] . $row, 'FFFFFF');

        setTextCenterToCeldaExcel($sheet, 'A1:' . $columnas[$col] . $row);
        setBorderToCeldaExcel($sheet, 'A1:' . $columnas[$col] . $row);

        for ($i = 0; $i <= $col; $i++)
            $sheet->getColumnDimension($columnas[$i])->setAutoSize(true);

        /*return PDF::loadView('adminlte.gestion.bodega.resumen_pedidos.partials.pdf_reporte', compact('datos'))
            ->setPaper(array(0, 0, 750, 530), 'landscape')->stream();*/
    }
}
