<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class DetalleCombo extends Model
{
    protected $table = 'detalle_combo';
    protected $primaryKey = 'id_detalle_combo';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_producto',
        'id_item',
        'unidades',
    ];

    public function item()
    {
        return $this->belongsTo('\yura\Modelos\Producto', 'id_item');
    }

    public function producto()
    {
        return $this->belongsTo('\yura\Modelos\Producto', 'id_producto');
    }
}
