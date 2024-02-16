<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Producto extends Model
{
    protected $table = 'producto';
    protected $primaryKey = 'id_producto';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'estado',
        'fecha_registro',
    ];

    public function actividades()
    {
        return $this->hasMany('\yura\Modelos\ActividadProducto', 'id_producto');
    }

    public function categoria_producto()
    {
        return $this->belongsTo('\yura\Modelos\CategoriaProducto', 'id_categoria_producto');
    }

    public function proveedor()
    {
        return $this->belongsTo('\yura\Modelos\Proveedor', 'id_proveedor');
    }

    public function detalles_combo()
    {
        return $this->hasMany('\yura\Modelos\DetalleCombo', 'id_producto');
    }

    public function getCostoCombo()
    {
        $r = DB::table('detalle_combo as dc')
            ->join('producto as p', 'p.id_producto', '=', 'dc.id_item')
            ->select(DB::raw('sum(p.precio * dc.unidades) as cantidad'))
            ->where('dc.id_producto', $this->id_producto)
            ->get()[0]->cantidad;
        return round($r, 2);
    }

    public function getDisponibles()
    {
        return DB::table('inventario_bodega')
            ->select(DB::raw('sum(disponibles) as cant'))
            ->where('id_producto', $this->id_producto)
            ->get()[0]->cant;
    }

    public function getVelorActual()
    {
        return DB::table('inventario_bodega')
            ->select(DB::raw('sum(disponibles * precio) as cant'))
            ->where('id_producto', $this->id_producto)
            ->where('disponibles', '>', 0)
            ->get()[0]->cant;
    }
}
