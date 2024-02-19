<?php

namespace yura\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use yura\Modelos\MesHistorico;

class cronUpdateMesHistorico extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:mes_historico';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Comando para grabar en la tabla MES_HISTORICO los valores resumenes del mes anterior';

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
        dump('<<<<< ! >>>>> Ejecutando comando "cron:proy_no_perennes" <<<<< ! >>>>>');
        Log::info('<<<<< ! >>>>> Ejecutando comando "cron:proy_no_perennes" <<<<< ! >>>>>');

        $fecha = opDiasFecha('-', 1, hoy());
        $anno = substr($fecha, 0, 4);
        $mes = substr($fecha, 5, 2);
        $valor_inventario = DB::table('inventario_bodega')
            ->select(DB::raw('sum(disponibles * precio) as cant'))
            ->where('disponibles', '>', 0)
            ->get()[0]->cant;
        $model = MesHistorico::where('anno', $anno)
            ->where('mes', $mes)
            ->get()
            ->first();
        if ($model == '') {
            $model = new MesHistorico();
            $model->anno = $anno;
            $model->mes = $mes;
        }
        $model->valor_inventario = $valor_inventario;
        $model->save();

        $time_duration = difFechas(date('Y-m-d H:i:s'), $ini)->h . ':' . difFechas(date('Y-m-d H:i:s'), $ini)->m . ':' . difFechas(date('Y-m-d H:i:s'), $ini)->s;
        dump('<*> DURACION: ' . $time_duration . '  <*>');
        dump('<<<<< * >>>>> Fin satisfactorio del comando "cron:proy_no_perennes" <<<<< * >>>>>');
        Log::info('<*> DURACION: ' . $time_duration . '  <*>');
        Log::info('<<<<< * >>>>> Fin satisfactorio del comando "cron:proy_no_perennes" <<<<< * >>>>>');
    }
}
