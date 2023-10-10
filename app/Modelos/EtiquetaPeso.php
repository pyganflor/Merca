<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class EtiquetaPeso extends Model
{
    protected $table = 'etiqueta_peso';
    protected $primaryKey = 'id_etiqueta_peso';
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = [
        'id_detalle_pedido_bodega',
        'id_inventario_bodega',
        'peso',
        'precio_venta',
        'fecha_registro'
    ];

    public function detalle_pedido_bodega()
    {
        return $this->belongsTo('yura\Modelos\DetallePedidoBodega', 'id_detalle_pedido_bodega');
    }

    public function inventario_bodega()
    {
        return $this->belongsTo('yura\Modelos\InventarioBodega', 'id_inventario_bodega');
    }
}
