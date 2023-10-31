<?php

namespace yura\Modelos;

use DateTime;
use Illuminate\Database\Eloquent\Model;

class DetallePedidoBodega extends Model
{
    protected $table = 'detalle_pedido_bodega';
    protected $primaryKey = 'id_detalle_pedido_bodega';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_pedido_bodega',
        'id_producto',
        'cantidad',
    ];

    public function pedido_bodega()
    {
        return $this->belongsTo('\yura\Modelos\PedidoBodega', 'id_pedido_bodega');
    }

    public function producto()
    {
        return $this->belongsTo('\yura\Modelos\Producto', 'id_producto');
    }

    public function etiquetas_peso()
    {
        return $this->hasMany('\yura\Modelos\EtiquetaPeso', 'id_detalle_pedido_bodega');
    }

    public function getRangoDiferidoByFecha($fecha)
    {
        $fechas = [];
        $dia_entrega = date('d', strtotime($fecha));
        $diferido_mes_inicial = $this->pedido_bodega->diferido_mes_actual ? 0 : 1;
        $diferido_mes_final = $this->pedido_bodega->diferido_mes_actual ? $this->diferido - 1 : $this->diferido;
        for ($m = $diferido_mes_inicial; $m <= $diferido_mes_final; $m++) {
            $f = new DateTime($fecha);
            $f->modify('first day of +' . $m . ' month');
            $f = $f->format('Y-m-d');

            $f = date('Y', strtotime($f)) . '-' . date('m', strtotime($f)) . '-' . $dia_entrega;
            list($ano, $mes, $dia) = explode('-', $f);
            $d = 1;
            while (!checkdate($mes, $dia, $ano)) {
                $f = new DateTime($f);
                $f->modify('-' . $d . ' day');
                $f = $f->format('Y-m-d');
                list($ano, $mes, $dia) = explode('-', $f);
                $d++;
            }
            $fechas[] = $f;
        }
        return $fechas;
    }
}
