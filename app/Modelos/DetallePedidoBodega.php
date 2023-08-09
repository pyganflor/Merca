<?php

namespace yura\Modelos;

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
}
