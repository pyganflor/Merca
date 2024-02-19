<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class MesHistorico extends Model
{
    protected $table = 'mes_historico';
    protected $primaryKey = 'id_mes_historico';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'anno',
        'mes',
        'valor_inventario',
    ];
}
