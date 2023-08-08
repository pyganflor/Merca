<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class CategoriaProducto extends Model
{
    protected $table = 'categoria_producto';
    protected $primaryKey = 'id_categoria_producto';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'estado',
    ];

    public function productos()
    {
        return $this->hasMany('\yura\Modelos\Producto', 'id_categoria_producto');
    }
}
