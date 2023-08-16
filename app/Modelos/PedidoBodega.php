<?php

namespace yura\Modelos;

use DB;
use Illuminate\Database\Eloquent\Model;

class PedidoBodega extends Model
{
    protected $table = 'pedido_bodega';
    protected $primaryKey = 'id_pedido_bodega';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_usuario',
        'estado',
        'fecha',
        'id_empresa',
        'estado',
    ];

    public function usuario()
    {
        return $this->belongsTo('\yura\Modelos\Usuario', 'id_usuario');
    }

    public function empresa()
    {
        return $this->belongsTo('\yura\Modelos\ConfiguracionEmpresa', 'id_empresa');
    }

    public function detalles()
    {
        return $this->hasMany('\yura\Modelos\DetallePedidoBodega', 'id_pedido_bodega');
    }

    public function getTotalProductos()
    {
        return DB::table('detalle_pedido_bodega')
            ->select(DB::raw('sum(cantidad) as cantidad'))
            ->where('id_pedido_bodega', $this->id_pedido_bodega)
            ->get()[0]->cantidad;
    }

    public function getTotalMonto()
    {
        $monto = 0;
        foreach ($this->detalles as $det) {
            $producto = $det->producto;
            $monto += $det->cantidad * $producto->precio_venta;
            if ($producto->tiene_iva == 1) {
                $monto += (12 * $monto) / 100;
            }
        }
        return round($monto, 2);
    }

    public function getFechaEntrega()
    {
        $entrega = FechaEntrega::All()
            ->where('desde', '<=', $this->fecha)
            ->where('hasta', '>=', $this->fecha)
            ->where('id_empresa', $this->id_empresa)
            ->first();
        return $entrega != '' ? $entrega->entrega : '';
    }
}
