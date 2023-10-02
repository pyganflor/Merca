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

    public function getTotalMonto()
    {
        $monto = 0;
        foreach ($this->detalles as $det) {
            $monto += $det->cantidad * $det->precio;
        }
        return round($monto, 2);
    }

    public function getTotalMontoDiferido()
    {
        $monto_diferido = 0;
        $monto_total = 0;
        $diferido_selected = 0;
        foreach ($this->detalles as $det) {
            $precio_prod = $det->cantidad * $det->precio;
            $monto_total += $precio_prod;
            if ($det->diferido > 0) {
                $monto_diferido += $precio_prod / $det->diferido;
                if ($diferido_selected == 0) {
                    $diferido_selected = $det->diferido;
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
            $precio_prod = $det->cantidad * $det->precio;
            if ($det->diferido > 0) {
                $monto_diferido += $precio_prod / $det->diferido;
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
                if ($producto->combo == 0) {    // producto normal
                    $r += $producto->precio * $det->cantidad;
                } else {    // producto tipo combo
                    $r += $producto->getCostoCombo() * $det->cantidad;
                }
            }
        } else {    // armado
            $r = DB::table('salida_inventario_bodega as s')
                ->join('inventario_bodega as i', 'i.id_inventario_bodega', '=', 's.id_inventario_bodega')
                ->select(DB::raw('sum(s.cantidad * i.precio) as cantidad'))
                ->where('s.id_pedido_bodega', $this->id_pedido_bodega)
                ->get()[0]->cantidad;
        }
        return round($r, 2);
    }
}
