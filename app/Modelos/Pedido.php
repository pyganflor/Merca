<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Pedido extends Model
{
    protected $table = 'pedido';
    protected $primaryKey = 'id_pedido';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_cliente',
        'estado',
        'descripcion',
        'fecha_pedido',
        'empaquetado',
        'confirmado',
        'historico',
        'id_configuracion_empresa',
        'id_exportador'
    ];

    public function detalles()
    {
        return $this->hasMany('\yura\Modelos\DetallePedido', 'id_pedido');
    }

    public function agencia_carga()
    {
        return $this->belongsTo('yura\Modelos\AgenciaCarga', 'id_agencia_carga');
    }

    public function consignatario()
    {
        return $this->belongsTo('yura\Modelos\Consignatario', 'id_consignatario');
    }

    public function cliente()
    {
        return $this->belongsTo('\yura\Modelos\Cliente', 'id_cliente');
    }

    public function getTotales()
    {
        $tallos = 0;
        $ramos = 0;
        $monto = 0;
        $full_box = 0;
        foreach ($this->detalles as $det) {
            $caja_frio = $det->caja_frio;
            switch ($caja_frio->tipo) {
                case 'HB':
                    $full_box += 0.5;
                    break;
                case 'QB':
                    $full_box += 0.25;
                    break;
                case 'EB':
                    $full_box += 0.125;
                    break;

                default:
                    break;
            }
            foreach ($det->caja_frio->detalles as $item) {
                $tallos += $item->ramos * $item->tallos_x_ramo;
                $ramos += $item->ramos;
                $monto += $item->ramos * $item->tallos_x_ramo * $item->precio;
            }
        }
        return [
            'tallos' => $tallos,
            'ramos' => $ramos,
            'monto' => $monto,
            'full_box' => $full_box,
        ];
    }

    public function empresa()
    {
        return $this->belongsTo('\yura\Modelos\ConfiguracionEmpresa', 'id_configuracion_empresa');
    }
    public function etiqueta_factura()
    {
        return $this->hasOne('\yura\Modelos\EtiquetaFactura', 'id_pedido');
    }

    public function getResumenTipoCaja()
    {
        $tipos_caja = DB::table('detalle_pedido as d')
            ->join('caja_frio as c', 'c.id_caja_frio', '=', 'd.id_caja_frio')
            ->select(
                'c.tipo',
                DB::raw('count(*) as cantidad')
            )
            ->where('d.id_pedido', $this->id_pedido)
            ->whereNotNull('c.tipo')
            ->groupBy('c.tipo')
            ->get();
        $listado = [];
        foreach ($tipos_caja as $t) {
            $plantas = DB::table('detalle_pedido as d')
                ->join('caja_frio as c', 'c.id_caja_frio', '=', 'd.id_caja_frio')
                ->join('detalle_caja_frio as dc', 'dc.id_caja_frio', '=', 'c.id_caja_frio')
                ->join('variedad as v', 'v.id_variedad', '=', 'dc.id_variedad')
                ->join('planta as p', 'p.id_planta', '=', 'v.id_planta')
                ->select(
                    'p.id_planta',
                    'p.nombre',
                    'p.nandina',
                    'p.tarifa as hts',
                    DB::raw('sum(dc.ramos * dc.tallos_x_ramo) as tallos'),
                    DB::raw('sum(dc.ramos * dc.tallos_x_ramo * dc.precio) as monto'),
                    DB::raw('sum(dc.ramos) as ramos')
                )
                ->where('d.id_pedido', $this->id_pedido)
                ->where('c.tipo', $t->tipo)
                ->groupBy(
                    'p.id_planta',
                    'p.nombre',
                    'p.nandina',
                    'p.tarifa'
                )
                ->get();
            $listado[] = [
                't' => $t,
                'plantas' => $plantas,
            ];
        }
        return $listado;
    }
}
