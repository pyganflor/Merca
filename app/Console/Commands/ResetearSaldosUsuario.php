<?php

namespace yura\Console\Commands;

use DateTime;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use yura\Modelos\DetallePedidoBodega;
use yura\Modelos\FechaEntrega;
use yura\Modelos\Usuario;

class ResetearSaldosUsuario extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'resetear:saldos';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Comando para resetear los saldos cada mes';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $ini = date('Y-m-d H:i:s');
        dump('<<<<< ! >>>>> Ejecutando comando "resetear:saldos" <<<<< ! >>>>>');
        Log::info('<<<<< ! >>>>> Ejecutando comando "resetear:saldos" <<<<< ! >>>>>');

        $hoy = hoy();
        $primerDiaMes = date("Y-m-01", strtotime($hoy));
        $ultimoDiaMes = date("Y-m-t", strtotime($hoy));

        $usuarios = Usuario::where('estado', 'A')
            ->where('aplica', 1)
            ->get();
        foreach ($usuarios as $pos => $u) {
            dump('usuario: ' . ($pos + 1) . '/' . count($usuarios));
            $u->saldo = $u->cupo_disponible;

            $query_pedidos = DetallePedidoBodega::join('pedido_bodega as p', 'p.id_pedido_bodega', '=', 'detalle_pedido_bodega.id_pedido_bodega')
                ->select('detalle_pedido_bodega.*', 'p.fecha', 'p.id_empresa', 'p.diferido_mes_actual')->distinct()
                ->where('p.id_usuario', $u->id_usuario)
                ->where('p.estado', 1)
                ->where('detalle_pedido_bodega.diferido', '>', 0)
                ->get();

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
                    ($diferido_fecha_inicial <= $primerDiaMes && $diferido_fecha_final >= $ultimoDiaMes)
                ) {
                    $producto = $det_ped->producto;
                    $precio_prod = 0;
                    if ($producto->peso == 1) {
                        foreach ($det_ped->etiquetas_peso as $e) {
                            $precio_prod += $e->peso * $e->precio_venta;
                        }
                    } else {
                        $precio_prod = $det_ped->cantidad * $det_ped->precio;
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
                        if ($f >= $primerDiaMes && $f <= $ultimoDiaMes) {
                            $monto_diferido += $diferido;
                            $monto_subtotal += $subtotal;
                            $monto_total_iva += $iva;

                            if (!in_array($pos_f, $num_diferido))
                                $num_diferido[] = $pos_f;
                        }
                    }
                }
            }

            $mes_anterior = new DateTime($hoy);
            $mes_anterior->modify('first day of -1 month');
            $mes_anterior = $mes_anterior->format('Y-m-d');

            $primerDiaMesAnterior = date("Y-m-01", strtotime($mes_anterior));
            $ultimoDiaMesAnterior = date("Y-m-t", strtotime($mes_anterior));
            $query_pedidos = DetallePedidoBodega::join('pedido_bodega as p', 'p.id_pedido_bodega', '=', 'detalle_pedido_bodega.id_pedido_bodega')
                ->select('detalle_pedido_bodega.*', 'p.fecha', 'p.id_empresa', 'p.finca_nomina')->distinct()
                ->where('p.id_usuario', $u->id_usuario)
                ->where('p.estado', 1)
                ->where('detalle_pedido_bodega.diferido', '!=', -1)
                ->where('p.fecha', '<=', $ultimoDiaMesAnterior)
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
                    if ($fecha_entrega >= $primerDiaMesAnterior && $fecha_entrega <= $ultimoDiaMesAnterior) {
                        $producto = $det_ped->producto;
                        $precio_prod = 0;
                        if ($producto->peso == 1) {
                            foreach ($det_ped->etiquetas_peso as $e) {
                                $precio_prod += $e->peso * $e->precio_venta;
                            }
                        } else {
                            $precio_prod = $det_ped->cantidad * $det_ped->precio;
                        }
                        $monto_pedido = $precio_prod;
                        if ($det_ped->iva == true) {
                            $subtotal = $precio_prod / 1.12;
                            $iva = ($precio_prod / 1.12) * 0.12;
                        } else {
                            $subtotal = $precio_prod;
                            $iva = 0;
                        }

                        $monto_diferido += $monto_pedido;
                        $monto_subtotal += $subtotal;
                        $monto_total_iva += $iva;
                    }
                }
            }
            if ($monto_diferido > 0) {
                $u->saldo = $u->cupo_disponible - $monto_diferido;
                if ($u->saldo < 0)
                    $u->saldo = 0;
            }
            $u->save();
        }

        $time_duration = difFechas(date('Y-m-d H:i:s'), $ini)->h . ':' . difFechas(date('Y-m-d H:i:s'), $ini)->m . ':' . difFechas(date('Y-m-d H:i:s'), $ini)->s;
        dump('<*> DURACION: ' . $time_duration . '  <*>');
        dump('<<<<< * >>>>> Fin satisfactorio del comando "resetear:saldos" <<<<< * >>>>>');
        Log::info('<*> DURACION: ' . $time_duration . '  <*>');
        Log::info('<<<<< * >>>>> Fin satisfactorio del comando "resetear:saldos" <<<<< * >>>>>');
    }
}
