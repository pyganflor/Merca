<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class DetalleCajaFrio extends Model
{
    protected $table = 'detalle_caja_frio';
    protected $primaryKey = 'id_detalle_caja_frio';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_caja_frio',
        'id_inventario_frio',
        'ramos',
        'id_variedad',
        'tallos_x_ramo',
        'longitud',
        'fecha',
    ];

    public function caja_frio()
    {
        return $this->belongsTo('\yura\Modelos\CajaFrio', 'id_caja_frio');
    }

    public function variedad()
    {
        return $this->belongsTo('\yura\Modelos\Variedad', 'id_variedad');
    }

    public function inventario_frio()
    {
        return $this->belongsTo('\yura\Modelos\InventarioFrio', 'id_inventario_frio');
    }
}
