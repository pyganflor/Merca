<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class OtrosGastos extends Model
{
    protected $table = 'otros_gastos';
    protected $primaryKey = 'id_otros_gastos';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'mes',
        'anno',
        'ga',
    ];
}