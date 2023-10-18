<?php

namespace yura\Modelos;

use DB;
use Illuminate\Database\Eloquent\Model;

class PedidoBodega extends Model
{
    protected $table = 'pedido_bodega';
    protected $primaryKey = 'id_pedido_bodega';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_usuario',
        'estado',
        'fecha',
        'id_empresa',
        'finca_nomina',
        'estado',
    ];

    public function usuario()
    {
        return $this->belongsTo('\yura\Modelos\Usuario', 'id_usuario');
    }

    public function empresa()
    {
        return $this->belongsTo('\yura\Modelos\ConfiguracionEmpresa', 'id_empresa');
    }

    public function getFincaNomina()
    {
        return $this->belongsTo('\yura\Modelos\ConfiguracionEmpresa', 'finca_nomina');
    }

    public function detalles()
    {
        return $this->hasMany('\yura\Modelos\DetallePedidoBodega', 'id_pedido_bodega');
    }

    public function getTotalProductos()
    {
        return DB::table('detalle_pedido_bodega')
            ->select(DB::raw('sum(cantidad) as cantidad'))
            ->where('id_pedido_bodega', $this->id_pedido_bodega)
            ->get()[0]->cantidad;
    }

    public function tieneProductoPeso()
    {
        $r = DB::table('detalle_pedido_bodega as d')
            ->join('producto as p', 'p.id_producto', '=', 'd.id_producto')
            ->select(DB::raw('count(*) as cantidad'))
            ->where('d.id_pedido_bodega', $this->id_pedido_bodega)
            ->where('p.peso', 1)
            ->get()[0]->cantidad;
        return $r > 0 ? true : false;
    }

    public function getTotalMonto()
    {
        $monto = 0;
        foreach ($this->detalles as $det) {
            if ($det->producto->peso == 0)  // producto que no es de peso
                $monto += $det->cantidad * $det->precio;
            else {    // producto tipo peso
                foreach ($det->etiquetas_peso as $e) {
                    $monto += $e->peso * $e->precio_venta;
                }
            }
        }
        return round($monto, 2);
    }

    public function getTotalMontoDiferido()
    {
        $monto_diferido = 0;
        $monto_total = 0;
        $diferido_selected = 0;
        foreach ($this->detalles as $det) {
            if ($det->producto->peso == 0) {    // producto que no es de peso
                $precio_prod = $det->cantidad * $det->precio;
                $monto_total += $precio_prod;
                if ($det->diferido > 0) {
                    $monto_diferido += $precio_prod / $det->diferido;
                    if ($diferido_selected == 0) {
                        $diferido_selected = $det->diferido;
                    }
                }
            } else {    // producto tipo peso
                foreach ($det->etiquetas_peso as $e) {
                    $precio_prod = $e->peso * $e->precio_venta;
                    $monto_total += $precio_prod;
                    if ($det->diferido > 0) {
                        $monto_diferido += $precio_prod / $det->diferido;
                        if ($diferido_selected == 0) {
                            $diferido_selected = $det->diferido;
                        }
                    }
                }
            }
        }
        if ($diferido_selected > 0)
            return round($monto_total - ($monto_diferido * ($diferido_selected - 1)), 2);
        else
            return round($monto_total, 2);
    }

    public function getTotalDiferido()
    {
        $monto_diferido = 0;
        foreach ($this->detalles as $det) {
            if ($det->producto->peso == 0) {    // producto que no es de peso
                $precio_prod = $det->cantidad * $det->precio;
                if ($det->diferido > 0) {
                    $monto_diferido += $precio_prod / $det->diferido;
                }
            } else {    // producto tipo peso
                foreach ($det->etiquetas_peso as $e) {
                    $precio_prod = $e->peso * $e->precio_venta;
                    if ($det->diferido > 0) {
                        $monto_diferido += $precio_prod / $det->diferido;
                    }
                }
            }
        }
        return round($monto_diferido, 2);
    }

    public function getFechaEntrega()
    {
        $entrega = FechaEntrega::All()
            ->where('desde', '<=', $this->fecha)
            ->where('hasta', '>=', $this->fecha)
            ->where('id_empresa', $this->id_empresa)
            ->first();
        return $entrega != '' ? $entrega->entrega : '';
    }

    public function getCostos()
    {
        $r = 0;
        if ($this->armado == 0 || $this->getFechaEntrega() < '2023-10-03') {   // sin armar
            foreach ($this->detalles as $det) {
                $producto = $det->producto;
                if ($producto->peso == 0) { // producto que no es de peso
                    if ($producto->combo == 0) {    // producto normal
                        $r += $producto->precio * $det->cantidad;
                    } else {    // producto tipo combo
                        $r += $producto->getCostoCombo() * $det->cantidad;
                    }
                } else {    // producto tipo peso
                    foreach ($det->etiquetas_peso as $e) {
                        $r += $e->inventario_bodega->precio * $e->peso;
                    }
                }
            }
        } else {    // armado
            /* PRODUCTOS QUE NO SON TIPO PESO */
            $r = DB::table('salida_inventario_bodega as s')
                ->join('inventario_bodega as i', 'i.id_inventario_bodega', '=', 's.id_inventario_bodega')
                ->select(DB::raw('sum(s.cantidad * i.precio) as cantidad'))
                ->where('s.id_pedido_bodega', $this->id_pedido_bodega)
                ->get()[0]->cantidad;
            /* PRODUCTOS QUE SI SON TIPO PESO */
            foreach ($this->detalles as $det) {
                $producto = $det->producto;
                if ($producto->peso == 1) {
                    foreach ($det->etiquetas_peso as $e) {
                        $r += $e->inventario_bodega->precio * $e->peso;
                    }
                }
            }
        }
        return round($r, 2);
    }
}
