<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class SalidaBodega extends Model
{
    protected $table = 'salida_bodega';
    protected $primaryKey = 'id_salida_bodega';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_producto',
        'fecha',
        'fecha_registro',
        'cantidad',
        'id_sector',
        'id_empresa',
    ];

    public function producto()
    {
        return $this->belongsTo('\yura\Modelos\Producto', 'id_producto');
    }

    public function sector()
    {
        return $this->belongsTo('\yura\Modelos\Sector', 'id_sector');
    }
}
