<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class SalidaInventarioBodega extends Model
{
    protected $table = 'salida_inventario_bodega';
    protected $primaryKey = 'id_salida_inventario_bodega';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_inventario_bodega',
        'id_salida_bodega',
        'cantidad',
    ];

    public function inventario_bodega()
    {
        return $this->belongsTo('\yura\Modelos\InventarioBodega', 'id_inventario_bodega');
    }

    public function salida_bodega()
    {
        return $this->belongsTo('\yura\Modelos\SalidaBodega', 'id_salida_bodega');
    }
}
