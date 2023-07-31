<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DetallePedido extends Model
{
    protected $table = 'detalle_pedido';
    protected $primaryKey = 'id_detalle_pedido';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_pedido',
        'orden',
        'id_caja_frio',
    ];

    public function pedido()
    {
        return $this->belongsTo('yura\Modelos\Pedido', 'id_pedido');
    }

    public function caja_frio()
    {
        return $this->belongsTo('yura\Modelos\CajaFrio', 'id_caja_frio');
    }

    public function getTotales()
    {
        $tallos = 0;
        $ramos = 0;
        $monto = 0;
        foreach ($this->caja_frio->detalles as $item) {
            $tallos += $item->ramos * $item->tallos_x_ramo;
            $ramos += $item->ramos;
            $monto += $item->ramos * $item->tallos_x_ramo * $item->precio;
        }
        return [
            'tallos' => $tallos,
            'ramos' => $ramos,
            'monto' => $monto,
        ];
    }
}
