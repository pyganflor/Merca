<?php

namespace yura\Http\Controllers\Bodega;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use yura\Http\Controllers\Controller;
use yura\Modelos\PedidoBodega;
use yura\Modelos\Submenu;
use Barryvdh\DomPDF\Facade as PDF;
use DateTime;
use yura\Modelos\DetallePedidoBodega;
use yura\Modelos\FechaEntrega;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ResumenPedidosController extends Controller
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

        return view('adminlte.gestion.bodega.resumen_pedidos.inicio', [
            'url' => $request->getRequestUri(),
            'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
            'fincas' => $fincas
        ]);
    }

    public function listar_reporte(Request $request)
    {
        $usuarios = DB::table('pedido_bodega as p')
            ->join('usuario as u', 'u.id_usuario', '=', 'p.id_usuario')
            ->select('p.id_usuario', 'u.nombre_completo', 'u.username')->distinct()
            ->where('p.estado', 1)
            ->where('p.fecha', '<=', $request->hasta);
        if ($request->finca != 'T')
            $usuarios = $usuarios->where('p.finca_nomina', $request->finca);
        $usuarios = $usuarios->orderBy('p.fecha')
            ->orderBy('u.nombre_completo', 'asc')
            ->get();

        $listado = [];
        foreach ($usuarios as $u) {
            if ($request->tipo == 'T') {    // total Venta
                $query_pedidos = PedidoBodega::where('id_usuario', $u->id_usuario)
                    ->where('estado', 1)
                    ->where('fecha', '<=', $request->hasta);
                if ($request->finca != 'T')
                    $query_pedidos = $query_pedidos->where('finca_nomina', $request->finca);
                $query_pedidos = $query_pedidos->get();

                $pedidos = [];
                foreach ($query_pedidos as $ped) {
                    $fecha_entrega = $ped->getFechaEntrega();
                    if ($fecha_entrega >= $request->desde && $fecha_entrega <= $request->hasta)
                        $pedidos[] = $ped;
                }

                $monto_subtotal = 0;
                $monto_total_iva = 0;
                $monto_total = 0;
                $num_diferido = [];
                foreach ($pedidos as $pedido) {
                    $fecha_entrega = $pedido->getFechaEntrega();
                    if ($fecha_entrega >= $request->desde && $fecha_entrega <= $request->hasta) {
                        foreach ($pedido->detalles as $det) {
                            $valida_diferido = true;
                            if ($det->diferido > 0) {
                                $diferido_selected = $det->diferido;
                                $diferido_mes_inicial = $pedido->diferido_mes_actual ? 0 : 1;
                                $diferido_mes_final = $pedido->diferido_mes_actual ? $diferido_selected - 1 : $diferido_selected;

                                $diferido_fecha_inicial = new DateTime($fecha_entrega);
                                $diferido_fecha_inicial->modify('+' . $diferido_mes_inicial . ' month');
                                $diferido_fecha_inicial = $diferido_fecha_inicial->format('Y-m-d');

                                $diferido_fecha_final = new DateTime($fecha_entrega);
                                $diferido_fecha_final->modify('+' . $diferido_mes_final . ' month');
                                $diferido_fecha_final = $diferido_fecha_final->format('Y-m-d');

                                if (($diferido_fecha_inicial >= $request->desde && $diferido_fecha_inicial <= $request->hasta) ||
                                    ($diferido_fecha_final >= $request->desde && $diferido_fecha_final <= $request->hasta) ||
                                    ($diferido_fecha_inicial <= $request->desde && $diferido_fecha_final >= $request->hasta)
                                )
                                    $valida_diferido = true;
                                else
                                    $valida_diferido = false;
                            }

                            if ($det->diferido != -1 && $valida_diferido) {
                                if ($det->producto->peso == 0) {    // producto que no es de peso
                                    $precio_prod = $det->cantidad * $det->precio;
                                    if ($det->iva == true) {
                                        $monto_subtotal += $precio_prod / 1.12;
                                        $monto_total_iva += ($precio_prod / 1.12) * 0.12;
                                    } else {
                                        $monto_subtotal += $precio_prod;
                                    }
                                    $monto_total += $precio_prod;
                                } else {    // producto tipo peso
                                    foreach ($det->etiquetas_peso as $e) {
                                        $precio_prod = $e->peso * $e->precio_venta;
                                        if ($det->iva == true) {
                                            $monto_subtotal += $precio_prod / 1.12;
                                            $monto_total_iva += ($precio_prod / 1.12) * 0.12;
                                        } else {
                                            $monto_subtotal += $precio_prod;
                                        }
                                        $monto_total += $precio_prod;
                                    }
                                }
                            }
                        }
                    }
                }

                if ($monto_total > 0)
                    $listado[] = [
                        'usuario' => $u,
                        'subtotal' => $monto_subtotal,
                        'total_iva' => $monto_total_iva,
                        'total' => $monto_total,
                        'num_diferido' => $num_diferido,
                    ];
            } else if ($request->tipo == 'D') { // Diferidos
                $query_pedidos = DetallePedidoBodega::join('pedido_bodega as p', 'p.id_pedido_bodega', '=', 'detalle_pedido_bodega.id_pedido_bodega')
                    ->select('detalle_pedido_bodega.*', 'p.fecha', 'p.id_empresa', 'p.finca_nomina', 'p.diferido_mes_actual')->distinct()
                    ->where('p.id_usuario', $u->id_usuario)
                    ->where('p.estado', 1)
                    ->where('p.fecha', '<=', $request->hasta)
                    ->where('detalle_pedido_bodega.diferido', '>', 0);
                if ($request->finca != 'T')
                    $query_pedidos = $query_pedidos->where('p.finca_nomina', $request->finca);
                $query_pedidos = $query_pedidos->get();

                $monto_subtotal = 0;
                $monto_total_iva = 0;
                $monto_diferido = 0;
                $num_diferido = [];
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

                    $dia_entrega = date('d', strtotime($fecha_entrega));

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

                    if (($diferido_fecha_inicial >= $request->desde && $diferido_fecha_inicial <= $request->hasta) ||
                        ($diferido_fecha_final >= $request->desde && $diferido_fecha_final <= $request->hasta) ||
                        ($diferido_fecha_inicial <= $request->desde && $diferido_fecha_final >= $request->hasta)
                    ) {
                        if ($det_ped->producto->peso == 0) {    // producto que no es tipo peso
                            $precio_prod = $det_ped->cantidad * $det_ped->precio;
                        } else {    // producto tipo peso
                            $precio_prod = 0;
                            foreach ($det_ped->etiquetas_peso as $e) {
                                $precio_prod += $e->peso * $e->precio_venta;
                            }
                        }
                        $diferido = $precio_prod / $det_ped->diferido;
                        if ($det_ped->iva == true) {
                            $subtotal = $precio_prod / 1.12;
                            $iva = ($precio_prod / 1.12) * 0.12;
                        } else {
                            $subtotal = $precio_prod;
                            $iva = 0;
                        }
                        $subtotal = $subtotal / $det_ped->diferido;
                        $iva = $iva / $det_ped->diferido;

                        $rango_diferido = $det_ped->getRangoDiferidoByFecha($fecha_entrega);
                        foreach ($rango_diferido as $pos_f => $f) {
                            if ($f >= $request->desde && $f <= $request->hasta) {
                                $monto_diferido += $diferido;
                                $monto_subtotal += $subtotal;
                                $monto_total_iva += $iva;

                                if (!in_array($pos_f, $num_diferido))
                                    $num_diferido[] = $pos_f;
                            }
                        }
                    }
                }
                if ($monto_diferido > 0)
                    $listado[] = [
                        'usuario' => $u,
                        'subtotal' => $monto_subtotal,
                        'total_iva' => $monto_total_iva,
                        'total' => $monto_diferido,
                        'num_diferido' => $num_diferido,
                    ];
            } else if ($request->tipo == 'N') { // NO Diferidos
                $query_pedidos = DetallePedidoBodega::join('pedido_bodega as p', 'p.id_pedido_bodega', '=', 'detalle_pedido_bodega.id_pedido_bodega')
                    ->select('detalle_pedido_bodega.*', 'p.fecha', 'p.id_empresa', 'p.finca_nomina')->distinct()
                    ->where('p.id_usuario', $u->id_usuario)
                    ->where('p.estado', 1)
                    ->where('p.fecha', '<=', $request->hasta)
                    ->where('detalle_pedido_bodega.diferido', '!=', -1);
                if ($request->finca != 'T')
                    $query_pedidos = $query_pedidos->where('p.finca_nomina', $request->finca);
                $query_pedidos = $query_pedidos->get();

                $monto_subtotal = 0;
                $monto_total_iva = 0;
                $monto_no_diferido = 0;
                $num_diferido = [];
                foreach ($query_pedidos as $det_ped) {
                    if ($det_ped->diferido == null || $det_ped->diferido == 0) {
                        if ($det_ped->producto->peso == 0) {    // producto que no es tipo peso
                            $precio_prod = $det_ped->cantidad * $det_ped->precio;
                        } else {    // producto tipo peso
                            $precio_prod = 0;
                            foreach ($det_ped->etiquetas_peso as $e) {
                                $precio_prod += $e->peso * $e->precio_venta;
                            }
                        }
                        if ($det_ped->iva == true) {
                            $subtotal = $precio_prod / 1.12;
                            $iva = ($precio_prod / 1.12) * 0.12;
                        } else {
                            $subtotal = $precio_prod;
                            $iva = 0;
                        }

                        $entrega = FechaEntrega::All()
                            ->where('desde', '<=', $det_ped->fecha)
                            ->where('hasta', '>=', $det_ped->fecha)
                            ->where('id_empresa', $det_ped->id_empresa)
                            ->first();
                        $f = $entrega != '' ? $entrega->entrega : '';
                        if ($f >= $request->desde && $f <= $request->hasta) {
                            $monto_no_diferido += $precio_prod;
                            $monto_subtotal += $subtotal;
                            $monto_total_iva += $iva;
                        }
                    }
                }
                if ($monto_no_diferido > 0)
                    $listado[] = [
                        'usuario' => $u,
                        'subtotal' => $monto_subtotal,
                        'total_iva' => $monto_total_iva,
                        'total' => $monto_no_diferido,
                        'num_diferido' => $num_diferido,
                    ];
            }
        }

        return view('adminlte.gestion.bodega.resumen_pedidos.partials.listado', [
            'listado' => $listado,
            'tipo' => $request->tipo
        ]);
    }

    public function exportar_reporte(Request $request)
    {
        $spread = new Spreadsheet();
        $this->excel_reporte($spread, $request);

        $fileName = "Descuentos.xlsx";
        $writer = new Xlsx($spread);

        //--------------------------- GUARDAR EL EXCEL -----------------------

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . urlencode($fileName) . '"');
        $writer->save('php://output');

        //$writer->save('/var/www/html/Dasalflor/storage/storage/excel/excel_prueba.xlsx');
    }

    public function excel_reporte($spread, $request)
    {
        $usuarios = DB::table('pedido_bodega as p')
            ->join('usuario as u', 'u.id_usuario', '=', 'p.id_usuario')
            ->select('p.id_usuario', 'u.nombre_completo', 'u.username')->distinct()
            ->where('p.estado', 1)
            ->where('p.fecha', '<=', $request->hasta);
        if ($request->finca != 'T')
            $usuarios = $usuarios->where('p.finca_nomina', $request->finca);
        $usuarios = $usuarios->orderBy('p.fecha')
            ->orderBy('u.nombre_completo', 'asc')
            ->get();

        $listado = [];
        foreach ($usuarios as $u) {
            if ($request->tipo == 'T') {    // total Venta
                $query_pedidos = PedidoBodega::where('id_usuario', $u->id_usuario)
                    ->where('estado', 1)
                    ->where('fecha', '<=', $request->hasta);
                if ($request->finca != 'T')
                    $query_pedidos = $query_pedidos->where('finca_nomina', $request->finca);
                $query_pedidos = $query_pedidos->get();

                $pedidos = [];
                foreach ($query_pedidos as $ped) {
                    $fecha_entrega = $ped->getFechaEntrega();
                    if ($fecha_entrega >= $request->desde && $fecha_entrega <= $request->hasta)
                        $pedidos[] = $ped;
                }

                $monto_subtotal = 0;
                $monto_total_iva = 0;
                $monto_total = 0;
                $num_diferido = [];
                foreach ($pedidos as $pedido) {
                    $fecha_entrega = $pedido->getFechaEntrega();
                    if ($fecha_entrega >= $request->desde && $fecha_entrega <= $request->hasta) {
                        foreach ($pedido->detalles as $det) {
                            $valida_diferido = true;
                            if ($det->diferido > 0) {
                                $diferido_selected = $det->diferido;
                                $diferido_mes_inicial = $pedido->diferido_mes_actual ? 0 : 1;
                                $diferido_mes_final = $pedido->diferido_mes_actual ? $diferido_selected - 1 : $diferido_selected;

                                $diferido_fecha_inicial = new DateTime($fecha_entrega);
                                $diferido_fecha_inicial->modify('+' . $diferido_mes_inicial . ' month');
                                $diferido_fecha_inicial = $diferido_fecha_inicial->format('Y-m-d');

                                $diferido_fecha_final = new DateTime($fecha_entrega);
                                $diferido_fecha_final->modify('+' . $diferido_mes_final . ' month');
                                $diferido_fecha_final = $diferido_fecha_final->format('Y-m-d');

                                if (($diferido_fecha_inicial >= $request->desde && $diferido_fecha_inicial <= $request->hasta) ||
                                    ($diferido_fecha_final >= $request->desde && $diferido_fecha_final <= $request->hasta) ||
                                    ($diferido_fecha_inicial <= $request->desde && $diferido_fecha_final >= $request->hasta)
                                )
                                    $valida_diferido = true;
                                else
                                    $valida_diferido = false;
                            }

                            if ($det->diferido != -1 && $valida_diferido) {
                                if ($det->producto->peso == 0) {    // producto que no es de peso
                                    $precio_prod = $det->cantidad * $det->precio;
                                    if ($det->iva == true) {
                                        $monto_subtotal += $precio_prod / 1.12;
                                        $monto_total_iva += ($precio_prod / 1.12) * 0.12;
                                    } else {
                                        $monto_subtotal += $precio_prod;
                                    }
                                    $monto_total += $precio_prod;
                                } else {    // producto tipo peso
                                    foreach ($det->etiquetas_peso as $e) {
                                        $precio_prod = $e->peso * $e->precio_venta;
                                        if ($det->iva == true) {
                                            $monto_subtotal += $precio_prod / 1.12;
                                            $monto_total_iva += ($precio_prod / 1.12) * 0.12;
                                        } else {
                                            $monto_subtotal += $precio_prod;
                                        }
                                        $monto_total += $precio_prod;
                                    }
                                }
                            }
                        }
                    }
                }

                if ($monto_total > 0)
                    $listado[] = [
                        'usuario' => $u,
                        'subtotal' => $monto_subtotal,
                        'total_iva' => $monto_total_iva,
                        'total' => $monto_total,
                        'num_diferido' => $num_diferido,
                    ];
            } else if ($request->tipo == 'D') { // Diferidos
                $query_pedidos = DetallePedidoBodega::join('pedido_bodega as p', 'p.id_pedido_bodega', '=', 'detalle_pedido_bodega.id_pedido_bodega')
                    ->select('detalle_pedido_bodega.*', 'p.fecha', 'p.id_empresa', 'p.finca_nomina', 'p.diferido_mes_actual')->distinct()
                    ->where('p.id_usuario', $u->id_usuario)
                    ->where('p.estado', 1)
                    ->where('p.fecha', '<=', $request->hasta)
                    ->where('detalle_pedido_bodega.diferido', '>', 0);
                if ($request->finca != 'T')
                    $query_pedidos = $query_pedidos->where('p.finca_nomina', $request->finca);
                $query_pedidos = $query_pedidos->get();

                $monto_subtotal = 0;
                $monto_total_iva = 0;
                $monto_diferido = 0;
                $num_diferido = [];
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

                    if (($diferido_fecha_inicial >= $request->desde && $diferido_fecha_inicial <= $request->hasta) ||
                        ($diferido_fecha_final >= $request->desde && $diferido_fecha_final <= $request->hasta) ||
                        ($diferido_fecha_inicial <= $request->desde && $diferido_fecha_final >= $request->hasta)
                    ) {
                        if ($det_ped->producto->peso == 0) {    // producto que no es tipo peso
                            $precio_prod = $det_ped->cantidad * $det_ped->precio;
                        } else {    // producto tipo peso
                            $precio_prod = 0;
                            foreach ($det_ped->etiquetas_peso as $e) {
                                $precio_prod += $e->peso * $e->precio_venta;
                            }
                        }
                        $diferido = $precio_prod / $det_ped->diferido;
                        if ($det_ped->iva == true) {
                            $subtotal = $precio_prod / 1.12;
                            $iva = ($precio_prod / 1.12) * 0.12;
                        } else {
                            $subtotal = $precio_prod;
                            $iva = 0;
                        }
                        $subtotal = $subtotal / $det_ped->diferido;
                        $iva = $iva / $det_ped->diferido;

                        $rango_diferido = $det_ped->getRangoDiferidoByFecha($fecha_entrega);
                        foreach ($rango_diferido as $pos_f => $f) {
                            if ($f >= $request->desde && $f <= $request->hasta) {
                                $monto_diferido += $diferido;
                                $monto_subtotal += $subtotal;
                                $monto_total_iva += $iva;

                                if (!in_array($pos_f, $num_diferido))
                                    $num_diferido[] = $pos_f;
                            }
                        }
                    }
                }
                if ($monto_diferido > 0)
                    $listado[] = [
                        'usuario' => $u,
                        'subtotal' => $monto_subtotal,
                        'total_iva' => $monto_total_iva,
                        'total' => $monto_diferido,
                        'num_diferido' => $num_diferido,
                    ];
            } else if ($request->tipo == 'N') { // NO Diferidos
                $query_pedidos = DetallePedidoBodega::join('pedido_bodega as p', 'p.id_pedido_bodega', '=', 'detalle_pedido_bodega.id_pedido_bodega')
                    ->select('detalle_pedido_bodega.*', 'p.fecha', 'p.id_empresa', 'p.finca_nomina')->distinct()
                    ->where('p.id_usuario', $u->id_usuario)
                    ->where('p.estado', 1)
                    ->where('p.fecha', '<=', $request->hasta)
                    ->where('detalle_pedido_bodega.diferido', '!=', -1);
                if ($request->finca != 'T')
                    $query_pedidos = $query_pedidos->where('p.finca_nomina', $request->finca);
                $query_pedidos = $query_pedidos->get();

                $monto_subtotal = 0;
                $monto_total_iva = 0;
                $monto_no_diferido = 0;
                $num_diferido = [];
                foreach ($query_pedidos as $det_ped) {
                    if ($det_ped->diferido == null || $det_ped->diferido == 0) {
                        if ($det_ped->producto->peso == 0) {    // producto que no es tipo peso
                            $precio_prod = $det_ped->cantidad * $det_ped->precio;
                        } else {    // producto tipo peso
                            $precio_prod = 0;
                            foreach ($det_ped->etiquetas_peso as $e) {
                                $precio_prod += $e->peso * $e->precio_venta;
                            }
                        }
                        if ($det_ped->iva == true) {
                            $subtotal = $precio_prod / 1.12;
                            $iva = ($precio_prod / 1.12) * 0.12;
                        } else {
                            $subtotal = $precio_prod;
                            $iva = 0;
                        }

                        $entrega = FechaEntrega::All()
                            ->where('desde', '<=', $det_ped->fecha)
                            ->where('hasta', '>=', $det_ped->fecha)
                            ->where('id_empresa', $det_ped->id_empresa)
                            ->first();
                        $f = $entrega != '' ? $entrega->entrega : '';
                        if ($f >= $request->desde && $f <= $request->hasta) {
                            $monto_no_diferido += $precio_prod;
                            $monto_subtotal += $subtotal;
                            $monto_total_iva += $iva;
                        }
                    }
                }
                if ($monto_no_diferido > 0)
                    $listado[] = [
                        'usuario' => $u,
                        'subtotal' => $monto_subtotal,
                        'total_iva' => $monto_total_iva,
                        'total' => $monto_no_diferido,
                        'num_diferido' => $num_diferido,
                    ];
            }
        }

        $tipo_reporte = '';
        if ($request->tipo == 'D')
            $tipo_reporte = 'DIFERIDOS';
        elseif ($request->tipo == 'N')
            $tipo_reporte = 'NORMALES';

        $datos = [
            'listado' => $listado,
            'desde' => $request->desde,
            'hasta' => $request->hasta,
            'finca' => getConfiguracionEmpresa($request->finca),
            'tipo_reporte' => $tipo_reporte,
            'tipo' => $request->tipo,
        ];

        $columnas = getColumnasExcel();
        $sheet = $spread->getActiveSheet();
        $sheet->setTitle('Descuentos ' . $datos['finca']->nombre);

        $row = 1;
        $col = 0;
        setValueToCeldaExcel($sheet, $columnas[$col] . $row, 'RESUMEN de DESCUENTOS ' . $datos['tipo_reporte'] . ' "' . $datos['finca']->nombre . '"');
        $sheet->mergeCells('A' . $row . ':G' . $row);
        setBgToCeldaExcel($sheet, 'A' . $row . ':' . $columnas[$col] . $row, '00B388');
        setColorTextToCeldaExcel($sheet, 'A' . $row . ':' . $columnas[$col] . $row, 'FFFFFF');
        $row++;
        $col = 0;
        setValueToCeldaExcel($sheet, $columnas[$col] . $row, convertDateToText($datos['desde']) . ' - ' . convertDateToText($datos['hasta']));
        $sheet->mergeCells('A' . $row . ':G' . $row);
        setBgToCeldaExcel($sheet, 'A' . $row . ':' . $columnas[$col] . $row, '00B388');
        setColorTextToCeldaExcel($sheet, 'A' . $row . ':' . $columnas[$col] . $row, 'FFFFFF');
        $row++;
        $col = 0;
        setValueToCeldaExcel($sheet, $columnas[$col] . $row, 'BENCHMARKET S.A.S. RUC 1793209142001');
        $sheet->mergeCells('A' . $row . ':G' . $row);
        setBgToCeldaExcel($sheet, 'A' . $row . ':' . $columnas[$col] . $row, '00B388');
        setColorTextToCeldaExcel($sheet, 'A' . $row . ':' . $columnas[$col] . $row, 'FFFFFF');

        $monto_subtotal = 0;
        $monto_total_iva = 0;
        $monto_total = 0;
        $row++;
        $col = 0;
        setValueToCeldaExcel($sheet, $columnas[$col] . $row, 'Finca');
        $col++;
        setValueToCeldaExcel($sheet, $columnas[$col] . $row, 'Cliente');
        $col++;
        setValueToCeldaExcel($sheet, $columnas[$col] . $row, 'CI');
        $col++;
        setValueToCeldaExcel($sheet, $columnas[$col] . $row, 'Subtotal');
        $col++;
        setValueToCeldaExcel($sheet, $columnas[$col] . $row, 'Iva');
        $col++;
        setValueToCeldaExcel($sheet, $columnas[$col] . $row, 'Total');
        if ($datos['tipo'] == 'D') {
            $col++;
            setValueToCeldaExcel($sheet, $columnas[$col] . $row, '#Pago');
        }
        setBgToCeldaExcel($sheet, 'A' . $row . ':' . $columnas[$col] . $row, '00B388');
        setColorTextToCeldaExcel($sheet, 'A' . $row . ':' . $columnas[$col] . $row, 'FFFFFF');
        foreach ($datos['listado'] as $pos_ped => $item) {
            $monto_subtotal += $item['subtotal'];
            $monto_total_iva += $item['total_iva'];
            $monto_total += $item['total'];

            $row++;
            $col = 0;
            setValueToCeldaExcel($sheet, $columnas[$col] . $row, $datos['finca']->nombre);
            $col++;
            setValueToCeldaExcel($sheet, $columnas[$col] . $row, $item['usuario']->nombre_completo);
            $col++;
            setValueToCeldaExcel($sheet, $columnas[$col] . $row, $item['usuario']->username);
            $col++;
            setValueToCeldaExcel($sheet, $columnas[$col] . $row, round($item['subtotal'], 2));
            $col++;
            setValueToCeldaExcel($sheet, $columnas[$col] . $row, round($item['total_iva'], 2));
            $col++;
            setValueToCeldaExcel($sheet, $columnas[$col] . $row, round($item['total'], 2));
            if ($datos['tipo'] == 'D') {
                $pagos = '';
                foreach ($item['num_diferido'] as $pos_dif => $dif) {
                    $pagos .= $pos_dif > 0 ? '-' : '';
                    $pagos .= $dif + 1 . '°';
                }
                $col++;
                setValueToCeldaExcel($sheet, $columnas[$col] . $row, $pagos);
            }
        }
        $row++;
        $col = 0;
        setValueToCeldaExcel($sheet, $columnas[$col] . $row, 'TOTALES');
        $col += 3;
        setValueToCeldaExcel($sheet, $columnas[$col] . $row, round($monto_subtotal, 2));
        $col++;
        setValueToCeldaExcel($sheet, $columnas[$col] . $row, round($monto_total_iva, 2));
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
