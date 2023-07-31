<?php

namespace yura\Http\Controllers\CRM;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use yura\Http\Controllers\Controller;
use yura\Modelos\Indicador;
use yura\Modelos\Pedido;
use yura\Modelos\ProyeccionVentaSemanalReal;
use yura\Modelos\ResumenVentaDiaria;
use yura\Modelos\Variedad;
use yura\Modelos\Semana;
use yura\Modelos\Submenu;

class crmVentasController extends Controller
{
    public function inicio(Request $request)
    {
        /* ======= INDICADORES ======= */
        $semana_desde = getSemanaByDate(opDiasFecha('-', 28, hoy()));
        $semana_hasta = getSemanaByDate(opDiasFecha('-', 7, hoy()));
        $indicadores = DB::table('historico_ventas as h')
            ->select(
                'h.semana',
                DB::raw('sum(h.monto) as dinero'),
                DB::raw('sum(h.ramos) as ramos'),
                DB::raw('sum(h.tallos) as tallos')
            )
            ->where('h.semana', '>=', $semana_desde->codigo)
            ->where('h.semana', '<=', $semana_hasta->codigo)
            ->groupBy('h.semana')
            ->orderBy('h.semana')
            ->get();

        /* ======= GRAFICAS ======= */
        $annos = DB::table('historico_ventas')
            ->select('anno')->distinct()
            ->orderBy('anno', 'desc')
            ->get();
        $variedades = Variedad::where('estado', 1)
            ->orderBy('nombre')
            ->get();
        
        return view('adminlte.crm.ventas.inicio', [
            'url' => $request->getRequestUri(),
            'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
            'annos' => $annos,
            'indicadores' => $indicadores,
            'variedades' => $variedades,
            'clientes' => getClientes(),
        ]);
    }

    public function listar_graficas(Request $request)
    {
        if ($request->annos == '') {
            $view = 'graficas_rango';

            if ($request->rango == 'D') {   // diario
                $labels = DB::table('historico_ventas')
                ->select('fecha')->distinct()
                ->where('fecha', '>=', $request->desde)
                ->where('fecha', '<=', $request->hasta)
                ->orderBy('fecha')
                ->get()->pluck('fecha')->toArray();
            } else if ($request->rango == 'M') {   // mensual
                $labels = DB::table('historico_ventas')
                ->select(DB::raw('DISTINCT DATE_FORMAT(fecha, "%Y-%m") AS mes'))
                ->where('fecha', '>=', $request->desde)
                ->where('fecha', '<=', $request->hasta)
                ->orderBy('fecha')
                ->groupBy('mes', 'fecha')
                ->get()
                ->pluck('mes')
                ->toArray();
            } else {    // semanal o cualquier otro
                $labels = DB::table('historico_ventas')
                ->select('semana')->distinct()
                ->where('fecha', '>=', $request->desde)
                ->where('fecha', '<=', $request->hasta)
                ->orderBy('semana')
                ->get()->pluck('semana')->toArray();
            }
            $data = [];
            foreach ($labels as $l) {
                $query = DB::table('historico_ventas as h')
                    ->select(
                        DB::raw('sum(h.monto) as dinero'),
                        DB::raw('sum(h.ramos) as ramos'),
                        DB::raw('sum(h.tallos) as tallos')
                    );
                if ($request->rango == 'D') // diario
                {
                    $query = $query->where('h.fecha', $l);
                } else if ($request->rango == 'M') // mensual
                {
                    $query = $query->whereMonth('h.fecha', '=', date('m', strtotime($l)))
                    ->whereYear('h.fecha', '=', date('Y', strtotime($l)));
                } else { // semanal
                    $query = $query->where('h.semana', $l);
                }
                if ($request->cliente != 'T')
                    $query = $query->where('h.id_cliente', $request->cliente);
                if ($request->variedad != 'T')
                    $query = $query->where('h.id_variedad', $request->variedad);
                $query = $query->get()[0];

                $data[] = $query;
            }
            if ($request->tipo_grafica == 'line') {
                $tipo_grafica = 'line';
                $fill_grafica = 'false';
            } else if ($request->tipo_grafica == 'area') {
                $tipo_grafica = 'line';
                $fill_grafica = 'true';
            } else {
                $tipo_grafica = 'bar';
                $fill_grafica = 'true';
            }
            $datos = [
                'labels' => $labels,
                'data' => $data,
                'tipo_grafica' => $tipo_grafica,
                'fill_grafica' => $fill_grafica,
            ];
        } else {
            $view = 'graficas_annos';
            $annos = explode(' - ', $request->annos);

            en_desarrollo();
        }

        return view('adminlte.crm.ventas.partials.' . $view, $datos);
    }

    public function listar_ranking(Request $request)
    {
        if ($request->tipo_ranking == 'C') {  // clientes
            $query = DB::table('historico_ventas as h')
                ->join('detalle_cliente as c', 'c.id_cliente', '=', 'h.id_cliente')
                ->select(
                    'h.id_cliente',
                    'c.nombre',
                    DB::raw('sum(h.monto) as dinero'),
                    DB::raw('sum(h.ramos) as ramos'),
                    DB::raw('sum(h.tallos) as tallos')
                )
                ->where('c.estado', 1)
                ->where('h.fecha', '>=', $request->desde)
                ->where('h.fecha', '<=', $request->hasta)
                ->groupBy(
                    'h.id_cliente',
                    'c.nombre'
                );
            if ($request->criterio_ranking == 'D')   // Dinero
                $query = $query->orderBy('dinero', 'desc');
            if ($request->criterio_ranking == 'R')   // Dinero
                $query = $query->orderBy('ramos', 'desc');
            if ($request->criterio_ranking == 'T')   // Dinero
                $query = $query->orderBy('tallos', 'desc');
            $query = $query->limit(4)->get();
        } else {    // flores
            $query = DB::table('historico_ventas as h')
                ->join('variedad as v', 'v.id_variedad', '=', 'h.id_variedad')
                ->join('planta as p', 'p.id_planta', '=', 'v.id_planta')
                ->select(
                    'v.id_planta',
                    'p.nombre',
                    DB::raw('sum(h.monto) as dinero'),
                    DB::raw('sum(h.ramos) as ramos'),
                    DB::raw('sum(h.tallos) as tallos')
                )
                ->where('h.fecha', '>=', $request->desde)
                ->where('h.fecha', '<=', $request->hasta)
                ->groupBy(
                    'v.id_planta',
                    'p.nombre'
                );
            if ($request->criterio_ranking == 'D')   // Dinero
                $query = $query->orderBy('dinero', 'desc');
            if ($request->criterio_ranking == 'R')   // Dinero
                $query = $query->orderBy('ramos', 'desc');
            if ($request->criterio_ranking == 'T')   // Dinero
                $query = $query->orderBy('tallos', 'desc');
            $query = $query->limit(4)->get();
        }
        return view('adminlte.crm.ventas.partials.listar_ranking', [
            'query' => $query,
            'criterio' => $request->criterio_ranking,
        ]);
    }

    public function filtrar_graficas(Request $request)
    {
        $desde = $request->desde;
        $hasta = $request->hasta;

        $arreglo_annos = [];
        if ($request->has('annos') && count($request->annos) > 0) {
            $view = '_annos';

            $fechas = [];

            $data = [];
            $periodo = 'mensual';

            foreach ($request->annos as $anno) {
                $arreglo_valores = [];
                $arreglo_fisicas = [];
                $arreglo_cajas = [];
                $arreglo_precios = [];

                foreach (getMeses(TP_NUMERO) as $mes) {
                    $query = DB::table('historico_ventas')
                        ->select(DB::raw('sum(valor) as valor'), DB::raw('sum(cajas_fisicas) as cajas_fisicas'),
                            DB::raw('sum(cajas_equivalentes) as cajas_equivalentes'),
                            DB::raw('sum(precio_x_ramo) as precio_x_ramo'))
                        ->where('anno', '=', $anno)
                        ->where('mes', '=', $mes);
                    $count_query = DB::table('historico_ventas')
                        ->select(DB::raw('count(*) as count'))
                        ->where('anno', '=', $anno)
                        ->where('mes', '=', $mes);

                    if ($request->id_variedad != '') {
                        $query = $query->where('id_variedad', '=', $request->id_variedad);
                        $count_query = $count_query->where('id_variedad', '=', $request->id_variedad);
                    }
                    if ($request->x_cliente == 'true' && $request->id_cliente != '') {
                        $query = $query->where('id_cliente', '=', $request->id_cliente);
                        $count_query = $count_query->where('id_cliente', '=', $request->id_cliente);
                    }
                    $query = $query->get();
                    $count_query = $count_query->get();

                    array_push($arreglo_valores, count($query) > 0 ? round($query[0]->valor, 2) : 0);
                    array_push($arreglo_fisicas, count($query) > 0 ? round($query[0]->cajas_fisicas, 2) : 0);
                    array_push($arreglo_cajas, count($query) > 0 ? round($query[0]->cajas_equivalentes, 2) : 0);
                    array_push($arreglo_precios, (count($query) > 0 && $count_query[0]->count > 0) ? round($query[0]->precio_x_ramo / $count_query[0]->count, 2) : 0);
                }
                array_push($arreglo_annos, [
                    'anno' => $anno,
                    'valores' => $arreglo_valores,
                    'fisicas' => $arreglo_fisicas,
                    'equivalentes' => $arreglo_cajas,
                    'precios' => $arreglo_precios,
                ]);
            }
        } else {
            $view = 'graficas';

            if ($request->diario == 'true') {
                $periodo = 'diario';

                $array_valor = [];
                $array_cajas = [];
                $array_precios = [];
                if ($request->total == 'true') {
                    $fechas = DB::table('pedido as p')
                        ->select('p.fecha_pedido as dia')->distinct()
                        ->where('p.estado', '=', 1)
                        ->where('p.fecha_pedido', '>=', $request->desde)
                        ->where('p.fecha_pedido', '<=', $request->hasta)
                        ->orderBy('p.fecha_pedido')
                        ->get();

                    foreach ($fechas as $f) {

                        $objResumenVentaDiaria = ResumenVentaDiaria::where('fecha_pedido',$f->dia)->get();
                        foreach($objResumenVentaDiaria as $ventaDiaria){
                            $array_valor[]=$ventaDiaria->valor;
                            $array_cajas[]=$ventaDiaria->cajas_equivalentes;
                            $array_precios[]=$ventaDiaria->precio_x_ramo;
                        }
                        /*$pedidos_semanal = Pedido::All()->where('estado', 1)
                            ->where('fecha_pedido', '=', $f->dia);
                        $valor = 0;
                        $cajas = 0;
                        $tallos = 0;
                        foreach ($pedidos_semanal as $p) {
                            if (!getFacturaAnulada($p->id_pedido)) {
                                $valor += $p->getPrecioByPedido();
                                $cajas += $p->getCajas();
                                $tallos += $p->getTallos();
                            }
                        }
                        $ramos_estandar = $cajas * getConfiguracionEmpresa()->ramos_x_caja;
                        $precio_x_ramo = $ramos_estandar > 0 ? round($valor / $ramos_estandar, 2) : 0;
                        $precio_x_tallo = $tallos > 0 ? round($valor / $tallos, 2) : 0;

                        array_push($array_valor, $valor);
                        array_push($array_cajas, $cajas);
                        array_push($array_precios, $precio_x_ramo);*/
                    }

                } else if ($request->x_cliente == 'true' && $request->id_cliente != '') {
                    $fechas = DB::table('pedido as p')
                        ->select('p.fecha_pedido as dia')->distinct()
                        ->where('p.estado', '=', 1)
                        ->where('p.fecha_pedido', '>=', $request->desde)
                        ->where('p.fecha_pedido', '<=', $request->hasta)
                        ->where('p.id_cliente', '=', $request->id_cliente)
                        ->orderBy('p.fecha_pedido')
                        ->get();

                    foreach ($fechas as $f) {
                        $pedidos_semanal = Pedido::All()->where('estado', 1)
                            ->where('fecha_pedido', '=', $f->dia)
                            ->where('id_cliente', '=', $request->id_cliente);
                        $valor = 0;
                        $cajas = 0;
                        $tallos = 0;
                        foreach ($pedidos_semanal as $p) {
                            if (!getFacturaAnulada($p->id_pedido)) {
                                $valor += $p->getPrecioByPedido();
                                $cajas += $p->getCajas();
                                $tallos += $p->getTallos();
                            }
                        }
                        $ramos_estandar = $cajas * getConfiguracionEmpresa()->ramos_x_caja;
                        $precio_x_ramo = $ramos_estandar > 0 ? round($valor / $ramos_estandar, 2) : 0;
                        //$precio_x_tallo = $tallos > 0 ? round($valor / $tallos, 2) : 0;

                        array_push($array_valor, $valor);
                        array_push($array_cajas, $cajas);
                        array_push($array_precios, $precio_x_ramo);
                    }
                }
                $data = [
                    'valores' => $array_valor,
                    'cajas' => $array_cajas,
                    'precios' => $array_precios,
                ];
            } else if ($request->semanal == 'true') {
                $periodo = 'semanal';

                $array_valor = [];
                $array_cajas = [];
                $array_precios = [];

                $fechas = DB::table('semana as s')
                    ->select('s.codigo as semana')->distinct()
                    ->Where(function ($q) use ($desde, $hasta) {
                        $q->where('s.fecha_inicial', '>=', $desde)
                            ->where('s.fecha_inicial', '<=', $hasta);
                    })->orWhere(function ($q) use ($desde, $hasta) {
                        $q->where('s.fecha_final', '>=', $desde)
                            ->Where('s.fecha_final', '<=', $hasta);
                    })->orderBy('codigo')->get();

                if ($request->total == 'true') {

                    $intevalo=[];
                    foreach ($fechas as $fecha)
                        $intevalo[]=$fecha->semana;

                    $dataProyeccionVentalSemanalReal = ProyeccionVentaSemanalReal::whereIn('codigo_semana',$intevalo)
                        ->select('codigo_semana',
                            DB::raw('SUM(cajas_equivalentes) as cajas'),
                            DB::raw('SUM(valor)as valor')
                        )->groupBy('codigo_semana')->get();

                    $defRamosXCaja =getConfiguracionEmpresa()->ramos_x_caja;
                    foreach($dataProyeccionVentalSemanalReal as $data){
                        $ramos_estandar = $data->cajas * $defRamosXCaja;
                        $array_valor[]=round($data->valor,2); // Dinero
                        $array_cajas[]=round($data->cajas,2); //cajas equivalentes
                        $array_precios[]= $ramos_estandar > 0 ? round($data->valor / $ramos_estandar,2 ) : 0; //precio x ramo
                    }
                    /*foreach ($fechas as $codigo) {
                        $semana = Semana::All()->where('codigo', '=', $codigo->semana)->first();
                        $pedidos = Pedido::All()->where('estado', 1)
                            ->where('fecha_pedido', '>=', $semana->fecha_inicial)
                            ->where('fecha_pedido', '<=', $semana->fecha_final);
                        $valor = 0;
                        $cajas = 0;
                        $tallos = 0;

                        foreach ($pedidos as $p) {
                            if (!getFacturaAnulada($p->id_pedido)) {
                                $valor += $p->getPrecioByPedido();
                                $cajas += $p->getCajas();
                                $tallos += $p->getTallos();
                            }
                        }
                        $ramos_estandar = $cajas * getConfiguracionEmpresa()->ramos_x_caja;
                        $precio_x_ramo = $ramos_estandar > 0 ? round($valor / $ramos_estandar, 2) : 0;
                        $precio_x_tallo = $tallos > 0 ? round($valor / $tallos, 2) : 0;

                        array_push($array_valor, $valor);
                        array_push($array_cajas, $cajas);
                        array_push($array_precios, $precio_x_ramo);
                    }*/

                } else if ($request->x_cliente == 'true' && $request->id_cliente != '') {

                    foreach ($fechas as $fecha)
                        $intevalo[]=$fecha->semana;

                    $dataProyeccionVentalSemanalReal = ProyeccionVentaSemanalReal::whereIn('codigo_semana',$intevalo)
                            ->select('codigo_semana',
                                DB::raw('SUM(cajas_equivalentes) as cajas'),
                                DB::raw('SUM(valor)as valor')
                            )->groupBy('codigo_semana')->where('id_cliente','=',$request->id_cliente)->get();

                    $defRamosXCaja =getConfiguracionEmpresa()->ramos_x_caja;
                    foreach($dataProyeccionVentalSemanalReal as $data){
                        $ramos_estandar = $data->cajas * $defRamosXCaja;
                        $array_valor[]=round($data->valor,2); // Dinero
                        $array_cajas[]=round($data->cajas,2); //cajas equivalentes
                        $array_precios[]= $ramos_estandar > 0 ? round($data->valor / $ramos_estandar,2 ) : 0; //precio x ramo
                    }
                    /*foreach ($fechas as $codigo) {
                        $semana = Semana::All()->where('codigo', '=', $codigo->semana)->first();
                        $pedidos = Pedido::All()->where('estado', 1)
                            ->where('id_cliente', '>=', $request->id_cliente)
                            ->where('fecha_pedido', '>=', $semana->fecha_inicial)
                            ->where('fecha_pedido', '<=', $semana->fecha_final);
                        $valor = 0;
                        $cajas = 0;
                        $tallos = 0;

                        foreach ($pedidos as $p) {
                            if (!getFacturaAnulada($p->id_pedido)) {
                                $valor += $p->getPrecioByPedido();
                                $cajas += $p->getCajas();
                                $tallos += $p->getTallos();
                            }
                        }
                        $ramos_estandar = $cajas * getConfiguracionEmpresa()->ramos_x_caja;
                        $precio_x_ramo = $ramos_estandar > 0 ? round($valor / $ramos_estandar, 2) : 0;
                        $precio_x_tallo = $tallos > 0 ? round($valor / $tallos, 2) : 0;

                        array_push($array_valor, $valor);
                        array_push($array_cajas, $cajas);
                        array_push($array_precios, $precio_x_ramo);
                    }*/
                }

                $data = [
                    'valores' => $array_valor,
                    'cajas' => $array_cajas,
                    'precios' => $array_precios,
                ];
            }
        }

        return view('adminlte.crm.ventas.partials.' . $view, [
            'labels' => $fechas,
            'arreglo_annos' => $arreglo_annos,
            'data' => $data,
            'periodo' => $periodo,
        ]);
    }

    public function desglose_indicador(Request $request)
    {
        $fechas = DB::table('pedido')
            ->select('fecha_pedido as dia')->distinct()
            ->where('estado', 1)
            ->where('fecha_pedido', '>=', opDiasFecha('-', 7, date('Y-m-d')))
            ->where('fecha_pedido', '<=', opDiasFecha('-', 1, date('Y-m-d')))
            ->orderBy('fecha_pedido')
            ->get();

        $arreglo_variedades = [];
        foreach (getVariedades() as $v) {
            $array_valores = [];
            $array_cajas = [];
            $array_precios = [];
            $array_tallos = [];

            $flag = false;
            foreach ($fechas as $dia) {
                $pedidos_semanal = Pedido::All()->where('estado', 1)
                    ->where('fecha_pedido', '=', $dia->dia);
                $valor = 0;
                $cajas = 0;
                $tallos = 0;
                foreach ($pedidos_semanal as $p) {
                    if (!getFacturaAnulada($p->id_pedido)) {
                        $valor += $p->getPrecioByPedidoVariedad($v->id_variedad);
                        $cajas += $p->getCajasByVariedad($v->id_variedad);
                        $tallos += $p->getTallosByVariedad($v->id_variedad);
                        if ($valor > 0)
                            $flag = true;
                    }
                }
                $ramos_estandar = $cajas * getConfiguracionEmpresa()->ramos_x_caja;
                $precio_x_ramo = $ramos_estandar > 0 ? round($valor / $ramos_estandar, 2) : 0;
                $precio_x_tallo = $tallos > 0 ? round($valor / $tallos, 2) : 0;

                array_push($array_valores, $valor);
                array_push($array_cajas, $cajas);
                array_push($array_precios, $precio_x_ramo);
                array_push($array_tallos, $precio_x_tallo);
            }

            if ($flag == true)
                array_push($arreglo_variedades, [
                    'variedad' => $v,
                    'valores' => $array_valores,
                    'cajas' => $array_cajas,
                    'precios' => $array_precios,
                    'tallos' => $array_tallos,
                ]);
        }

        return view('adminlte.crm.ventas.partials._desglose_indicador', [
            'labels' => $fechas,
            'arreglo_variedades' => $arreglo_variedades,
            'option' => $request->option,
        ]);
    }
}
