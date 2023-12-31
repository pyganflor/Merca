<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class Precio extends Model
{
    protected $table = 'precio';
    protected $primaryKey = 'id_precio';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_precio',
        'id_cliente',
        'fecha_registro',
        'estado',
        'cantidad',
        'id_variedad',
        'longitud'
    ];

    public function cliente()
    {
        return $this->belongsTo('\yura\Modelos\Cliente', 'id_cliente');
    }

    public function variedad()
    {
        return $this->belongsTo('\yura\Modelos\Variedad', 'id_variedad');
    }
}
