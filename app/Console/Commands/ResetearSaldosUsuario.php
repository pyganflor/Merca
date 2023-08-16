<?php

namespace yura\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
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

        $usuarios = Usuario::where('estado', 'A')
            ->where('aplica', 1)
            ->get();
        foreach ($usuarios as $pos => $u) {
            dump('usuario: ' . ($pos + 1) . '/' . count($usuarios));
            $u->saldo = $u->cupo_disponible;
            $u->save();
        }

        $time_duration = difFechas(date('Y-m-d H:i:s'), $ini)->h . ':' . difFechas(date('Y-m-d H:i:s'), $ini)->m . ':' . difFechas(date('Y-m-d H:i:s'), $ini)->s;
        dump('<*> DURACION: ' . $time_duration . '  <*>');
        dump('<<<<< * >>>>> Fin satisfactorio del comando "resetear:saldos" <<<<< * >>>>>');
        Log::info('<*> DURACION: ' . $time_duration . '  <*>');
        Log::info('<<<<< * >>>>> Fin satisfactorio del comando "resetear:saldos" <<<<< * >>>>>');
    }
}
