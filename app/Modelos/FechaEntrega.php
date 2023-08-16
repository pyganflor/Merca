<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class FechaEntrega extends Model
{
    protected $table = 'fecha_entrega';
    protected $primaryKey = 'id_fecha_entrega';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'desde',
        'hasta',
        'entrega',
    ];

    public function empresa()
    {
        return $this->belongsTo('\yura\Modelos\ConfiguracionEmpresa', 'id_empresa');
    }
}
