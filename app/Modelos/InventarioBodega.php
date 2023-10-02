<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class InventarioBodega extends Model
{
    protected $table = 'inventario_bodega';
    protected $primaryKey = 'id_inventario_bodega';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_producto',
        'fecha_ingreso',
        'fecha_registro',
        'cantidad',
        'precio',
        'disponibles',
    ];

    public function producto()
    {
        return $this->belongsTo('\yura\Modelos\Producto', 'id_producto');
    }
}
