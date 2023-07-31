<?php

namespace yura\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use yura\Modelos\ConfiguracionEmpresa;
use yura\Modelos\HistoricoVentas;
use yura\Modelos\Pedido;

class UpdateHistoricoVentas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'historico_ventas:update {desde=0} {hasta=0}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Añadir los pedidos a la tabla historico_ventas';

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
        Log::info('<<<<< ! >>>>> Ejecutando comando "historico_ventas:update" <<<<< ! >>>>>');

        $desde_par = $this->argument('desde');
        $hasta_par = $this->argument('hasta');

        DB::beginTransaction();
        try {
            if ($desde_par <= $hasta_par) {
                if ($desde_par == 0)
                    $desde_par = opDiasFecha('-', 1, hoy());
                if ($hasta_par == 0)
                    $hasta_par = opDiasFecha('-', 0, hoy());

                $empresas = ConfiguracionEmpresa::where('proveedor', 0)->get();
                foreach ($empresas as $emp)
                    for ($f = $desde_par; $f <= $hasta_par; $f = opDiasFecha('+', 1, $f)) {
                        dump('ELIMINANDO fecha: ' . $f . '; finca: ' . $emp->nombre);
                        DB::select('delete from historico_ventas where fecha = "' . $f . '" and id_empresa = ' . $emp->id_configuracion_empresa);
                        $pedidos = Pedido::where('estado', 1)
                            ->where('fecha_pedido', $f)
                            ->where('id_exportador', $emp->id_configuracion_empresa)
                            ->get();
                        $semana = getSemanaByDate($f);
                        $mes = substr($f, 5, 2);
                        $anno = substr($f, 0, 4);
                        foreach ($pedidos as $pos => $ped) {
                            dump('GRABANDO fecha: ' . $f . '; finca: ' . $emp->nombre . '; ped: ' . ($pos + 1) . '/' . count($pedidos));
                            foreach ($ped->detalles as $det_ped) {
                                foreach ($det_ped->caja_frio->detalles as $det_caja) {
                                    $model = HistoricoVentas::All()
                                        ->where('id_cliente', $ped->id_cliente)
                                        ->where('id_variedad', $det_caja->id_variedad)
                                        ->where('longitud', $det_caja->longitud)
                                        ->where('fecha', $f)
                                        ->where('id_empresa', $emp->id_configuracion_empresa)
                                        ->first();
                                    if ($model == '') {
                                        $model = new HistoricoVentas();
                                        $model->id_cliente = $ped->id_cliente;
                                        $model->id_variedad = $det_caja->id_variedad;
                                        $model->longitud = $det_caja->longitud;
                                        $model->fecha = $f;
                                        $model->mes = $mes;
                                        $model->anno = $anno;
                                        $model->semana = $semana->codigo;
                                        $model->id_empresa = $emp->id_configuracion_empresa;

                                        $model->tallos = $det_caja->ramos * $det_caja->tallos_x_ramo;
                                        $model->ramos = $det_caja->ramos;
                                        $model->monto = $det_caja->ramos * $det_caja->tallos_x_ramo * $det_caja->precio;
                                    } else {
                                        $model->tallos += $det_caja->ramos * $det_caja->tallos_x_ramo;
                                        $model->ramos += $det_caja->ramos;
                                        $model->monto += $det_caja->ramos * $det_caja->tallos_x_ramo * $det_caja->precio;
                                    }
                                    $model->save();
                                }
                            }
                        }
                    }
            }

           DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            dd('************ Ha ocurrido un problema al guardar la informacion al sistema ***********' .
                $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine());
        }
        $time_duration = difFechas(date('Y-m-d H:i:s'), $ini)->h . ':' . difFechas(date('Y-m-d H:i:s'), $ini)->m . ':' . difFechas(date('Y-m-d H:i:s'), $ini)->s;
        Log::info('<*> DURACION: ' . $time_duration . '  <*>');
        Log::info('<<<<< * >>>>> Fin satisfactorio del comando "historico_ventas:update" <<<<< * >>>>>');
    }
}
