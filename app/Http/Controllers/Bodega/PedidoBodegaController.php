<?php

namespace yura\Http\Controllers\Bodega;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use phpDocumentor\Reflection\PseudoTypes\False_;
use yura\Http\Controllers\Controller;
use yura\Modelos\CategoriaProducto;
use yura\Modelos\DetallePedidoBodega;
use yura\Modelos\FechaEntrega;
use yura\Modelos\PedidoBodega;
use yura\Modelos\Producto;
use yura\Modelos\Submenu;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Barryvdh\DomPDF\Facade as PDF;
use Picqer\Barcode\BarcodeGeneratorHTML;
use yura\Modelos\EtiquetaPeso;
use yura\Modelos\InventarioBodega;
use yura\Modelos\Proveedor;
use yura\Modelos\SalidaBodega;
use yura\Modelos\SalidaInventarioBodega;

class PedidoBodegaController extends Controller
{
    public function inicio(Request $request)
    {
        $fincas = DB::table('configuracion_empresa as emp')
            ->join('usuario_finca as uf', 'uf.id_empresa', '=', 'emp.id_configuracion_empresa')
            ->select('emp.nombre', 'uf.id_empresa')->distinct()
            ->where('emp.proveedor', 0)
            ->where('emp.estado', 1)
            ->where('uf.id_usuario', session('id_usuario'))
            ->orderBy('emp.nombre')
            ->get();

        return view('adminlte.gestion.bodega.pedido.inicio', [
            'url' => $request->getRequestUri(),
            'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
            'fincas' => $fincas
        ]);
    }

    public function listar_reporte(Request $request)
    {
        $query = PedidoBodega::where('estado', 1);
        if ($request->finca != 'T')
            $query = $query->where('id_empresa', $request->finca);
        if (!in_array(session('id_usuario'), [1, 2]))
            $query = $query->where('id_usuario', session('id_usuario'));
        $query = $query->orderBy('fecha')
            ->orderBy('id_empresa')
            ->orderBy('id_usuario')
            ->get();
        $listado = [];
        foreach ($query as $q) {
            $fecha_entrega = $q->getFechaEntrega();
            if ($fecha_entrega == $request->entrega) {
                $listado[] = $q;
            }
        }

        return view('adminlte.gestion.bodega.pedido.partials.listado', [
            'listado' => $listado
        ]);
    }

    public function add_pedido(Request $request)
    {
        $fincas = DB::table('configuracion_empresa as emp')
            ->join('usuario_finca as uf', 'uf.id_empresa', '=', 'emp.id_configuracion_empresa')
            ->select('emp.nombre', 'uf.id_empresa')->distinct()
            ->where('emp.proveedor', 0)
            ->where('emp.estado', 1)
            ->where('uf.id_usuario', session('id_usuario'))
            ->orderBy('emp.nombre')
            ->get();
        $categorias = CategoriaProducto::where('estado', 1)
            ->orderBy('nombre')
            ->get();

        return view('adminlte.gestion.bodega.pedido.forms.add_pedido', [
            'fincas' => $fincas,
            'categorias' => $categorias,
            'finca_selected' => $request->finca,
        ]);
    }

    public function get_armar_pedido(Request $request)
    {
        return view('adminlte.gestion.bodega.pedido.forms.get_armar_pedido', []);
    }

    public function modal_contabilidad(Request $request)
    {
        return view('adminlte.gestion.bodega.pedido.forms.modal_contabilidad', []);
    }

    public function escanear_codigo_pedido(Request $request)
    {
        $pedido = PedidoBodega::find($request->codigo);

        $disponible = true;
        $mensaje = '';
        foreach ($pedido->detalles as $det) {
            $producto = $det->producto;
            if ($producto->combo == 0) {
                if ($producto->disponibles < $det->cantidad) {
                    $disponible = false;
                    $mensaje = 'No hay disponibilidad para el producto: "' . $producto->nombre . '" en el pedido #' . $pedido->id_pedido_bodega;
                }
            } else {    // producto combo
                foreach ($producto->detalles_combo as $item) {
                    $item_producto = $item->item;

                    if ($item_producto->disponibles < $det->cantidad) {
                        $disponible = false;
                        $mensaje = 'No hay disponibilidad para el producto: "' . $item_producto->nombre . '" dentro del combo "' . $producto->nombre . '" en el pedido #' . $pedido->id_pedido_bodega;
                    }
                }
            }
        }

        return view('adminlte.gestion.bodega.pedido.forms._escanear_codigo_pedido', [
            'pedido' => $pedido,
            'disponible' => $disponible,
            'mensaje' => $mensaje,
        ]);
    }

    public function store_armar_pedidos(Request $request)
    {
        DB::beginTransaction();
        try {
            foreach (json_decode($request->data) as $data) {
                $pedido = PedidoBodega::find($data);
                if ($pedido->armado == 0) {
                    $models_productos = [];
                    $tiene_peso = false;
                    foreach ($pedido->detalles as $det) {
                        $producto = $det->producto;
                        if ($producto->peso == 0) { // producto que no es tipo peso
                            if ($producto->combo == 0) {    // producto normal
                                if ($producto->disponibles >= $det->cantidad) {
                                    $producto->disponibles -= $det->cantidad;
                                    $models_productos[] = $producto;

                                    /* SALIDA_BODEGA */
                                    $salida = new SalidaBodega();
                                    $salida->id_producto = $det->id_producto;
                                    $salida->fecha = hoy();
                                    $salida->cantidad = $det->cantidad;
                                    $salida->save();
                                    $salida = SalidaBodega::All()->last();
                                    bitacora('salida_bodega', $salida->id_salida_bodega, 'I', 'SALIDA A BODEGA de ' . $det->cantidad . ' UNIDADES de ' . $producto->nombre);

                                    /* SACAR DEL INVENTARIO */
                                    $inventarios = InventarioBodega::where('disponibles', '>', 0)
                                        ->where('id_producto', '=', $det->id_producto)
                                        ->orderBy('fecha_ingreso', 'asc')
                                        ->orderBy('fecha_registro', 'asc')
                                        ->get();

                                    $meta = $det->cantidad;
                                    foreach ($inventarios as $model) {
                                        if ($meta >= 0) {
                                            $disponible = $model->disponibles;
                                            if ($meta >= $disponible) {
                                                $meta = $meta - $disponible;
                                                $usados = $disponible;
                                                $disponible = 0;
                                            } else {
                                                $disponible = $disponible - $meta;
                                                $usados = $meta;
                                                $meta = 0;
                                            }

                                            $model->disponibles = $disponible;
                                            $model->save();

                                            bitacora('inventario_bodega', $model->id_inventario_bodega, 'U', 'SACAR de la BODEGA');

                                            $salida_inventario = new SalidaInventarioBodega();
                                            $salida_inventario->id_salida_bodega =  $salida->id_salida_bodega;
                                            $salida_inventario->id_inventario_bodega =  $model->id_inventario_bodega;
                                            $salida_inventario->id_pedido_bodega =  $pedido->id_pedido_bodega;
                                            $salida_inventario->cantidad =  $usados;
                                            $salida_inventario->save();
                                        }
                                    }
                                } else {
                                    DB::rollBack();
                                    return [
                                        'success' => false,
                                        'mensaje' => '<div class="alert alert-danger text-center">' .
                                            'No hay disponibilidad para el producto: "' . $producto->nombre . '" en el pedido #' . $pedido->id_pedido_bodega .
                                            '</div>',
                                    ];
                                }
                            } else {    // producto combo
                                foreach ($producto->detalles_combo as $item) {
                                    $item_producto = $item->item;

                                    if ($item_producto->disponibles >= $det->cantidad) {
                                        $item_producto->disponibles -= $det->cantidad;
                                        $models_productos[] = $item_producto;

                                        /* SALIDA_BODEGA */
                                        $salida = new SalidaBodega();
                                        $salida->id_producto = $item_producto->id_producto;
                                        $salida->fecha = hoy();
                                        $salida->cantidad = $det->cantidad;
                                        $salida->save();
                                        $salida = SalidaBodega::All()->last();
                                        bitacora('salida_bodega', $salida->id_salida_bodega, 'I', 'SALIDA A BODEGA de ' . $det->cantidad . ' UNIDADES de ' . $item_producto->nombre);

                                        /* SACAR DEL INVENTARIO */
                                        $inventarios = InventarioBodega::where('disponibles', '>', 0)
                                            ->where('id_producto', '=', $item_producto->id_producto)
                                            ->orderBy('fecha_ingreso', 'asc')
                                            ->orderBy('fecha_registro', 'asc')
                                            ->get();

                                        $meta = $det->cantidad;
                                        foreach ($inventarios as $model) {
                                            if ($meta >= 0) {
                                                $disponible = $model->disponibles;
                                                if ($meta >= $disponible) {
                                                    $meta = $meta - $disponible;
                                                    $usados = $disponible;
                                                    $disponible = 0;
                                                } else {
                                                    $disponible = $disponible - $meta;
                                                    $usados = $meta;
                                                    $meta = 0;
                                                }

                                                $model->disponibles = $disponible;
                                                $model->save();

                                                bitacora('inventario_bodega', $model->id_inventario_bodega, 'U', 'SACAR de la BODEGA');

                                                $salida_inventario = new SalidaInventarioBodega();
                                                $salida_inventario->id_salida_bodega = $salida->id_salida_bodega;
                                                $salida_inventario->id_inventario_bodega = $model->id_inventario_bodega;
                                                $salida_inventario->cantidad = $usados;
                                                $salida_inventario->save();
                                            }
                                        }
                                    } else {
                                        return [
                                            'success' => false,
                                            'mensaje' => '<div class="alert alert-danger text-center">' .
                                                'No hay disponibilidad para el producto: "' . $item_producto->nombre . '" dentro del combo "' . $producto->nombre . '"' .
                                                '</div>',
                                        ];
                                    }
                                }
                            }
                        } else {
                            $tiene_peso = true;
                        }
                    }
                    $pedido->armado = 1;
                    $pedido->save();

                    foreach ($models_productos as $p) {
                        $p->save();
                    }

                    if ($tiene_peso) {
                        $monto_saldo = $pedido->getTotalMontoDiferido();
                        $usuario = $pedido->usuario;
                        if ($usuario->saldo >= $monto_saldo || in_array($usuario->id_usuario, [1, 2])) {
                            $usuario->saldo -= $monto_saldo;
                            $usuario->save();
                            $pedido->saldo_usuario = $usuario->saldo;
                            $pedido->save();
                        } else {
                            DB::rollBack();
                            $success = false;
                            $msg = '<div class="alert alert-danger text-center">' .
                                'El Usuario no tiene cupo disponible (<b>$' . $usuario->saldo . ' actualmente</b>)</div>';

                            return [
                                'success' => $success,
                                'mensaje' => $msg,
                            ];
                        }
                    }
                }
            }

            $success = true;
            $msg = 'Se ha <b>ARMADO</b> el pedido correctamente';

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $success = false;
            $msg = '<div class="alert alert-danger text-center">' .
                '<p> Ha ocurrido un problema al guardar la informacion al sistema</p>' .
                '<p>' . $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine() . '</p>'
                . '</div>';
        }
        return [
            'success' => $success,
            'mensaje' => $msg,
        ];
    }

    public function listar_catalogo(Request $request)
    {
        $listado = Producto::Where(function ($q) use ($request) {
            $q->Where('nombre', 'like', '%' . mb_strtoupper($request->busqueda) . '%')
                ->orWhere('codigo', 'like', '%' . mb_strtoupper($request->busqueda) . '%');
        });
        if ($request->tipo == 'N') {    // productos normales
            $listado = $listado->where('combo', 0)
                ->where('peso', 0);
            if ($request->categoria != 'T')
                $listado = $listado->where('id_categoria_producto', $request->categoria);
        } elseif ($request->tipo == 'C')    // combos
            $listado = $listado->where('combo', 1)
                ->where('peso', 0);
        elseif ($request->tipo == 'P') {    // peso
            $listado = $listado->where('combo', 0)
                ->where('peso', 1);
            if ($request->categoria != 'T')
                $listado = $listado->where('id_categoria_producto', $request->categoria);
        }

        $listado = $listado->orderBy('orden')
            ->get();

        return view('adminlte.gestion.bodega.pedido.forms._listar_catalogo', [
            'listado' => $listado
        ]);
    }

    public function seleccionar_finca_filtro(Request $request)
    {
        $listado = FechaEntrega::orderBy('entrega', 'asc')->get();
        if ($request->finca != 'T')
            $listado = $listado->where('id_empresa', $request->finca);

        $pos_selected = -1;
        foreach ($listado as $pos => $item) {
            if ($item->entrega >= hoy()) {
                $pos_selected = $pos;
                break;
            }
        }

        $options = '<option value="">Seleccione</option>';
        foreach ($listado as $pos => $item) {
            $selected = '';
            if ($pos_selected == $pos)
                $selected = 'selected';
            $options .= '<option value="' . $item->entrega . '" ' . $selected . '>' . convertDateToText($item->entrega) . ' - ' . $item->empresa->nombre . '</option>';
        }

        return [
            'options' => $options
        ];
    }

    public function seleccionar_finca(Request $request)
    {
        $listado = DB::table('usuario_finca as uf')
            ->join('usuario as u', 'u.id_usuario', '=', 'uf.id_usuario')
            ->select('uf.id_usuario', 'u.nombre_completo', 'u.username', 'u.telefono', 'u.saldo')->distinct()
            ->where('uf.id_empresa', $request->finca)
            ->where('u.estado', 'A')
            ->where('u.aplica', 1)
            ->orderBy('u.nombre_completo')
            ->get();

        $options_usuarios = '<option value="">Seleccione</option>';
        foreach ($listado as $item) {
            $options_usuarios .= '<option value="' . $item->id_usuario . '">' . $item->nombre_completo . ' CI:' . $item->username . ' Telf:' . $item->telefono . ' saldo:$' . $item->saldo . '</option>';
        }

        return [
            'options_usuarios' => $options_usuarios
        ];
    }

    public function store_pedido(Request $request)
    {
        DB::beginTransaction();
        try {
            $usuario = getUsuario($request->usuario);
            $al_contado = false;
            foreach (json_decode($request->detalles) as $det) {
                if ($det->diferido == -1)
                    $al_contado = true;
            }
            if ($usuario->saldo >= $request->monto_saldo || $al_contado || in_array($request->usuario, [1, 2])) {
                $pedido = new PedidoBodega();
                $pedido->fecha = $request->fecha;
                $pedido->id_usuario = $request->usuario;
                $pedido->finca_nomina = $request->finca_nomina;
                $pedido->id_empresa = $request->finca;
                $pedido->diferido_mes_actual = $request->diferido_mes_actual == 'true' ? 1 : 0;
                $pedido->save();
                $pedido = PedidoBodega::All()->last();

                $tiene_peso = false;
                foreach (json_decode($request->detalles) as $det) {
                    $detalle = new DetallePedidoBodega();
                    $detalle->id_pedido_bodega = $pedido->id_pedido_bodega;
                    $detalle->id_producto = $det->producto;
                    $detalle->cantidad = $det->cantidad;
                    $detalle->precio = $det->precio_venta;
                    $detalle->diferido = $det->diferido;
                    $detalle->iva = $det->iva;
                    $detalle->save();

                    $producto = Producto::find($det->producto);
                    if ($producto->peso == 1)
                        $tiene_peso = true;
                }

                if (!in_array($request->usuario, [1, 2]) && !$al_contado && !$tiene_peso) {
                    $usuario->saldo -= $request->monto_saldo;
                    $usuario->save();
                }
                $pedido->saldo_usuario = $usuario->saldo;
                $pedido->save();

                $success = true;
                $msg = 'Se ha <b>CREADO</b> el pedido correctamente';
            } else {
                $success = false;
                $msg = '<div class="alert alert-danger text-center">' .
                    'El Usuario no tiene cupo disponible (<b>$' . $usuario->saldo . ' actualmente</b>)</div>';
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $success = false;
            $msg = '<div class="alert alert-danger text-center">' .
                '<p> Ha ocurrido un problema al guardar la informacion al sistema</p>' .
                '<p>' . $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine() . '</p>'
                . '</div>';
        }
        return [
            'success' => $success,
            'mensaje' => $msg,
        ];
    }

    public function delete_pedido(Request $request)
    {
        DB::beginTransaction();
        try {
            $pedido = PedidoBodega::find($request->ped);
            $tiene_etiquetas_peso = DB::table('etiqueta_peso as e')
                ->join('detalle_pedido_bodega as d', 'd.id_detalle_pedido_bodega', '=', 'e.id_detalle_pedido_bodega')
                ->select('e.id_etiqueta_peso')->distinct()
                ->where('d.id_pedido_bodega', $pedido->id_pedido_bodega)
                ->get();
            if (count($tiene_etiquetas_peso) == 0) {
                if (!in_array($pedido->id_usuario, [1, 2])) {
                    $tieneProductoPeso = $pedido->tieneProductoPeso();
                    if (!$tieneProductoPeso) {
                        $monto_total = $pedido->getTotalMontoDiferido();
                        $usuario = $pedido->usuario;
                        $usuario->saldo += $monto_total;
                        $usuario->save();
                    }
                }
                $pedido->delete();

                $success = true;
                $msg = 'Se ha <b>CANCELADO</b> el pedido correctamente';
                DB::commit();
            } else {
                DB::rollBack();
                $success = false;
                $msg = '<div class="alert alert-danger text-center">' .
                    'Este pedido (<b>TIENE ETIQUETAS DE PESO</b>) asignadas. Comuniquese con el administrador</div>';
            }
        } catch (\Exception $e) {
            DB::rollBack();
            $success = false;
            $msg = '<div class="alert alert-danger text-center">' .
                '<p> Ha ocurrido un problema al guardar la informacion al sistema</p>' .
                '<p>' . $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine() . '</p>'
                . '</div>';
        }
        return [
            'success' => $success,
            'mensaje' => $msg,
        ];
    }

    public function ver_pedido(Request $request)
    {
        $fincas = DB::table('configuracion_empresa as emp')
            ->join('usuario_finca as uf', 'uf.id_empresa', '=', 'emp.id_configuracion_empresa')
            ->select('emp.nombre', 'uf.id_empresa')->distinct()
            ->where('emp.proveedor', 0)
            ->where('emp.estado', 1)
            ->where('uf.id_usuario', session('id_usuario'))
            ->orderBy('emp.nombre')
            ->get();
        $categorias = CategoriaProducto::where('estado', 1)
            ->orderBy('nombre')
            ->get();
        $pedido = PedidoBodega::find($request->ped);

        return view('adminlte.gestion.bodega.pedido.forms.ver_pedido', [
            'fincas' => $fincas,
            'categorias' => $categorias,
            'pedido' => $pedido,
        ]);
    }

    public function update_pedido(Request $request)
    {
        DB::beginTransaction();
        try {
            $pedido_delete = PedidoBodega::find($request->ped);
            $usuario = $pedido_delete->usuario;
            $valida_saldo = true;
            if (!in_array($pedido_delete->id_usuario, [1, 2])) {
                $tiene_peso = $pedido_delete->tieneProductoPeso();
                if (!$tiene_peso) {
                    $monto_total_anterior = $pedido_delete->getTotalMontoDiferido();
                    $saldo_anterior = $usuario->saldo;
                    $usuario->saldo += $monto_total_anterior;
                    $usuario->save();
                }

                $tiene_peso = false;
                foreach (json_decode($request->detalles) as $det) {
                    $producto = Producto::find($det->producto);
                    if ($producto->peso == 1)
                        $tiene_peso = true;
                }
                if (!$tiene_peso)
                    if ($usuario->saldo >= $request->monto_saldo) {
                        $usuario->saldo -= $request->monto_saldo;
                        $usuario->save();
                        $valida_saldo = true;
                    } else {
                        $valida_saldo = false;
                    }
            }
            if ($valida_saldo) {
                $tiene_etiquetas_peso = DB::table('etiqueta_peso as e')
                    ->join('detalle_pedido_bodega as d', 'd.id_detalle_pedido_bodega', '=', 'e.id_detalle_pedido_bodega')
                    ->select('e.id_etiqueta_peso')->distinct()
                    ->where('d.id_pedido_bodega', $pedido_delete->id_pedido_bodega)
                    ->get();
                if (count($tiene_etiquetas_peso) == 0) {
                    $pedido_delete->delete();

                    $pedido = new PedidoBodega();
                    $pedido->id_pedido_bodega = $request->ped;
                    $pedido->fecha = $request->fecha;
                    $pedido->id_usuario = $request->usuario;
                    $pedido->id_empresa = $request->finca;
                    $pedido->finca_nomina = $request->finca_nomina;
                    $pedido->diferido_mes_actual = $request->diferido_mes_actual == 'true' ? 1 : 0;
                    $pedido->saldo_usuario = $usuario->saldo;
                    $pedido->save();
                    $pedido = PedidoBodega::find($request->ped);

                    foreach (json_decode($request->detalles) as $det) {
                        $detalle = new DetallePedidoBodega();
                        $detalle->id_pedido_bodega = $pedido->id_pedido_bodega;
                        $detalle->id_producto = $det->producto;
                        $detalle->cantidad = $det->cantidad;
                        $detalle->precio = $det->precio_venta;
                        $detalle->diferido = $det->diferido;
                        $detalle->iva = $det->iva;
                        $detalle->save();
                    }

                    $success = true;
                    $msg = 'Se ha <b>MODIFICADO</b> el pedido correctamente';
                } else {
                    $success = false;
                    $msg = '<div class="alert alert-danger text-center">' .
                        'Este pedido (<b>TIENE ETIQUETAS DE PESO</b>) asignadas. Comuniquese con el administrador</div>';
                }
            } else {
                $success = false;
                $msg = '<div class="alert alert-danger text-center">' .
                    'El Usuario no tiene cupo disponible (<b>$' . $saldo_anterior . ' actualmente</b>)</div>';
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $success = false;
            $msg = '<div class="alert alert-danger text-center">' .
                '<p> Ha ocurrido un problema al guardar la informacion al sistema</p>' .
                '<p>' . $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine() . '</p>'
                . '</div>';
        }
        return [
            'success' => $success,
            'mensaje' => $msg,
        ];
    }

    public function armar_pedido(Request $request)
    {
        DB::beginTransaction();
        try {
            $pedido = PedidoBodega::find($request->ped);
            $models_productos = [];
            $tiene_peso = false;
            foreach ($pedido->detalles as $det) {
                $producto = $det->producto;
                if ($producto->peso == 0) { // producto que no es tipo peso
                    if ($producto->combo == 0) {    // producto normal
                        if ($producto->disponibles >= $det->cantidad) {
                            $producto->disponibles -= $det->cantidad;
                            $models_productos[] = $producto;

                            /* SALIDA_BODEGA */
                            $salida = new SalidaBodega();
                            $salida->id_producto = $det->id_producto;
                            $salida->fecha = hoy();
                            $salida->cantidad = $det->cantidad;
                            $salida->save();
                            $salida = SalidaBodega::All()->last();
                            bitacora('salida_bodega', $salida->id_salida_bodega, 'I', 'SALIDA A BODEGA de ' . $det->cantidad . ' UNIDADES de ' . $producto->nombre);

                            /* SACAR DEL INVENTARIO */
                            $inventarios = InventarioBodega::where('disponibles', '>', 0)
                                ->where('id_producto', '=', $det->id_producto)
                                ->orderBy('fecha_ingreso', 'asc')
                                ->orderBy('fecha_registro', 'asc')
                                ->get();

                            $meta = $det->cantidad;
                            foreach ($inventarios as $model) {
                                if ($meta >= 0) {
                                    $disponible = $model->disponibles;
                                    if ($meta >= $disponible) {
                                        $meta = $meta - $disponible;
                                        $usados = $disponible;
                                        $disponible = 0;
                                    } else {
                                        $disponible = $disponible - $meta;
                                        $usados = $meta;
                                        $meta = 0;
                                    }

                                    $model->disponibles = $disponible;
                                    $model->save();

                                    bitacora('inventario_bodega', $model->id_inventario_bodega, 'U', 'SACAR de la BODEGA');

                                    $salida_inventario = new SalidaInventarioBodega();
                                    $salida_inventario->id_salida_bodega =  $salida->id_salida_bodega;
                                    $salida_inventario->id_inventario_bodega =  $model->id_inventario_bodega;
                                    $salida_inventario->id_pedido_bodega =  $pedido->id_pedido_bodega;
                                    $salida_inventario->cantidad =  $usados;
                                    $salida_inventario->save();
                                }
                            }
                        } else {
                            return [
                                'success' => false,
                                'mensaje' => '<div class="alert alert-danger text-center">' .
                                    'No hay disponibilidad para el producto: "' . $producto->nombre . '"' .
                                    '</div>',
                            ];
                        }
                    } else {    // producto combo
                        foreach ($producto->detalles_combo as $item) {
                            $item_producto = $item->item;

                            if ($item_producto->disponibles >= $det->cantidad) {
                                $item_producto->disponibles -= $det->cantidad;
                                $models_productos[] = $item_producto;

                                /* SALIDA_BODEGA */
                                $salida = new SalidaBodega();
                                $salida->id_producto = $item_producto->id_producto;
                                $salida->fecha = hoy();
                                $salida->cantidad = $det->cantidad;
                                $salida->save();
                                $salida = SalidaBodega::All()->last();
                                bitacora('salida_bodega', $salida->id_salida_bodega, 'I', 'SALIDA A BODEGA de ' . $det->cantidad . ' UNIDADES de ' . $item_producto->nombre);

                                /* SACAR DEL INVENTARIO */
                                $inventarios = InventarioBodega::where('disponibles', '>', 0)
                                    ->where('id_producto', '=', $item_producto->id_producto)
                                    ->orderBy('fecha_ingreso', 'asc')
                                    ->orderBy('fecha_registro', 'asc')
                                    ->get();

                                $meta = $det->cantidad;
                                foreach ($inventarios as $model) {
                                    if ($meta >= 0) {
                                        $disponible = $model->disponibles;
                                        if ($meta >= $disponible) {
                                            $meta = $meta - $disponible;
                                            $usados = $disponible;
                                            $disponible = 0;
                                        } else {
                                            $disponible = $disponible - $meta;
                                            $usados = $meta;
                                            $meta = 0;
                                        }

                                        $model->disponibles = $disponible;
                                        $model->save();

                                        bitacora('inventario_bodega', $model->id_inventario_bodega, 'U', 'SACAR de la BODEGA');

                                        $salida_inventario = new SalidaInventarioBodega();
                                        $salida_inventario->id_salida_bodega =  $salida->id_salida_bodega;
                                        $salida_inventario->id_inventario_bodega =  $model->id_inventario_bodega;
                                        $salida_inventario->cantidad =  $usados;
                                        $salida_inventario->save();
                                    }
                                }
                            } else {
                                return [
                                    'success' => false,
                                    'mensaje' => '<div class="alert alert-danger text-center">' .
                                        'No hay disponibilidad para el producto: "' . $item_producto->nombre . '" dentro del combo "' . $producto->nombre . '"' .
                                        '</div>',
                                ];
                            }
                        }
                    }
                } else {
                    $tiene_peso = true;
                }
            }
            $pedido->armado = 1;
            $pedido->save();

            foreach ($models_productos as $p) {
                $p->save();
            }

            if ($tiene_peso) {
                $monto_saldo = $pedido->getTotalMontoDiferido();
                $usuario = $pedido->usuario;
                if ($usuario->saldo >= $monto_saldo || in_array($usuario->id_usuario, [1, 2])) {
                    $usuario->saldo -= $monto_saldo;
                    $usuario->save();
                    $pedido->saldo_usuario = $usuario->saldo;
                    $pedido->save();
                } else {
                    DB::rollBack();
                    $success = false;
                    $msg = '<div class="alert alert-danger text-center">' .
                        'El Usuario no tiene cupo disponible (<b>$' . $usuario->saldo . ' actualmente</b>)</div>';

                    return [
                        'success' => $success,
                        'mensaje' => $msg,
                    ];
                }
            }

            $success = true;
            $msg = 'Se ha <b>ARMADO</b> el pedido correctamente';
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $success = false;
            $msg = '<div class="alert alert-danger text-center">' .
                '<p> Ha ocurrido un problema al guardar la informacion al sistema</p>' .
                '<p>' . $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine() . '</p>'
                . '</div>';
        }
        return [
            'success' => $success,
            'mensaje' => $msg,
        ];
    }

    public function devolver_pedido(Request $request)
    {
        DB::beginTransaction();
        try {
            $pedido = PedidoBodega::find($request->ped);
            $pedido->armado = 0;
            $pedido->save();

            /* PRODUCTOS QUE NO SON TIPO PESO */
            $salidas_inv = SalidaInventarioBodega::where('id_pedido_bodega', $pedido->id_pedido_bodega)
                ->get();
            foreach ($salidas_inv as $salida_inv) {
                $cantidad = $salida_inv->cantidad;
                $inventario = $salida_inv->inventario_bodega;
                $inventario->disponibles += $cantidad;
                $inventario->save();
                $producto = $inventario->producto;
                $producto->disponibles += $cantidad;
                $producto->save();
                $salida_bodega = $salida_inv->salida_bodega;
                $salida_bodega->delete();
            }
            SalidaInventarioBodega::where('id_pedido_bodega', $pedido->id_pedido_bodega)
                ->delete();

            /* PRODUCTOS QUE SON TIPO PESO */
            $etiquetas_peso = EtiquetaPeso::join('detalle_pedido_bodega as d', 'd.id_detalle_pedido_bodega', '=', 'etiqueta_peso.id_detalle_pedido_bodega')
                ->select('etiqueta_peso.*')->distinct()
                ->where('d.id_pedido_bodega', $pedido->id_pedido_bodega)
                ->get();
            foreach ($etiquetas_peso as $pos_e => $e) {
                if ($pos_e == 0 && !in_array($pedido->id_usuario, [1, 2])) {
                    $monto_saldo = $pedido->getTotalMontoDiferido();
                    $usuario = $pedido->usuario;
                    $usuario->saldo += $monto_saldo;
                    $usuario->save();
                }
                $cantidad = 1;
                $inventario = $e->inventario_bodega;
                $inventario->disponibles += $cantidad;
                $inventario->save();
                $producto = $inventario->producto;
                $producto->disponibles += $cantidad;
                $producto->save();
            }
            EtiquetaPeso::join('detalle_pedido_bodega as d', 'd.id_detalle_pedido_bodega', '=', 'etiqueta_peso.id_detalle_pedido_bodega')
                ->select('etiqueta_peso.*')->distinct()
                ->where('d.id_pedido_bodega', $pedido->id_pedido_bodega)
                ->delete();

            $success = true;
            $msg = 'Se ha <b>DEVUELTO</b> el pedido correctamente';
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $success = false;
            $msg = '<div class="alert alert-danger text-center">' .
                '<p> Ha ocurrido un problema al guardar la informacion al sistema</p>' .
                '<p>' . $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine() . '</p>'
                . '</div>';
        }
        return [
            'success' => $success,
            'mensaje' => $msg,
        ];
    }

    public function exportar_resumen_pedidos(Request $request)
    {
        $spread = new Spreadsheet();
        $this->excel_resumen_pedidos($spread, $request);

        $fileName = "RESUMEN PEDIDOS.xlsx";
        $writer = new Xlsx($spread);

        //--------------------------- GUARDAR EL EXCEL -----------------------

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . urlencode($fileName) . '"');
        $writer->save('php://output');

        //$writer->save('/var/www/html/Dasalflor/storage/storage/excel/excel_prueba.xlsx');
    }

    public function excel_resumen_pedidos($spread, $request)
    {
        $query = PedidoBodega::where('estado', 1);
        if ($request->finca != 'T')
            $query = $query->where('id_empresa', $request->finca);
        if (!in_array(session('id_usuario'), [1, 2]))
            $query = $query->where('id_usuario', session('id_usuario'));
        $query = $query->orderBy('fecha')
            ->orderBy('id_empresa')
            ->orderBy('id_usuario')
            ->get();
        $pedidos = [];
        foreach ($query as $q) {
            $fecha_entrega = $q->getFechaEntrega();
            if ($fecha_entrega == $request->entrega) {
                $pedidos[] = $q;
            }
        }
        $listado = [];
        foreach ($pedidos as $pedido) {
            foreach ($pedido->detalles as $det) {
                $producto = $det->producto;
                if ($producto->combo == 0) {    // producto normal
                    $cantidad = $det->cantidad;

                    $pos_existe = -1;
                    foreach ($listado as $pos => $item) {
                        if ($item['producto']->id_producto == $producto->id_producto && $item['finca'] == $pedido->id_empresa) {
                            $pos_existe = $pos;
                        }
                    }
                    if ($pos_existe == -1) {
                        $listado[] = [
                            'producto' => $producto,
                            'cantidad' => $cantidad,
                            'finca' => $pedido->id_empresa,
                        ];
                    } else {
                        $listado[$pos_existe]['cantidad'] += $cantidad;
                    }
                } else {    // producto tipo combo
                    foreach ($producto->detalles_combo as $item_combo) {
                        $item_prod = $item_combo->item;
                        $cantidad = $det->cantidad * $item_combo->unidades;

                        $pos_existe = -1;
                        foreach ($listado as $pos => $item) {
                            if ($item['producto']->id_producto == $item_prod->id_producto && $item['finca'] == $pedido->id_empresa) {
                                $pos_existe = $pos;
                            }
                        }
                        if ($pos_existe == -1) {
                            $listado[] = [
                                'producto' => $item_prod,
                                'cantidad' => $cantidad,
                                'finca' => $pedido->id_empresa,
                            ];
                        } else {
                            $listado[$pos_existe]['cantidad'] += $cantidad;
                        }
                    }
                }
            }
        }

        $columnas = getColumnasExcel();
        $sheet = $spread->getActiveSheet();
        $sheet->setTitle('RESUMEN ' . convertDateToText($request->entrega));

        $row = 1;
        $col = 0;
        setValueToCeldaExcel($sheet, $columnas[$col] . $row, 'Proveedor');
        $col++;
        setValueToCeldaExcel($sheet, $columnas[$col] . $row, 'Producto');
        $col++;
        setValueToCeldaExcel($sheet, $columnas[$col] . $row, 'Finca');
        $col++;
        setValueToCeldaExcel($sheet, $columnas[$col] . $row, 'Inventario');
        $col++;
        setValueToCeldaExcel($sheet, $columnas[$col] . $row, 'Pedido');
        $col++;
        setValueToCeldaExcel($sheet, $columnas[$col] . $row, 'Compra');
        setBgToCeldaExcel($sheet, 'A' . $row . ':' . $columnas[$col] . $row, '00B388');
        setColorTextToCeldaExcel($sheet, 'A' . $row . ':' . $columnas[$col] . $row, 'FFFFFF');

        foreach ($listado as $r) {
            $proveedor = Proveedor::find($r['producto']->id_proveedor);
            $row++;
            $col = 0;
            setValueToCeldaExcel($sheet, $columnas[$col] . $row, $proveedor != '' ? $proveedor->nombre : '');
            setBgToCeldaExcel($sheet, $columnas[$col] . $row, '5a7177');
            setColorTextToCeldaExcel($sheet, $columnas[$col] . $row, 'FFFFFF');
            $col++;
            setValueToCeldaExcel($sheet, $columnas[$col] . $row, $r['producto']->nombre);
            setBgToCeldaExcel($sheet, $columnas[$col] . $row, '5a7177');
            setColorTextToCeldaExcel($sheet, $columnas[$col] . $row, 'FFFFFF');
            $col++;
            setValueToCeldaExcel($sheet, $columnas[$col] . $row, getConfiguracionEmpresa($r['finca'])->nombre);
            setBgToCeldaExcel($sheet, $columnas[$col] . $row, '5a7177');
            setColorTextToCeldaExcel($sheet, $columnas[$col] . $row, 'FFFFFF');
            $col++;
            setValueToCeldaExcel($sheet, $columnas[$col] . $row, $r['producto']->disponibles);
            $col++;
            setValueToCeldaExcel($sheet, $columnas[$col] . $row, $r['cantidad']);
            $saldo = $r['producto']->disponibles - $r['cantidad'];
            $col++;
            setValueToCeldaExcel($sheet, $columnas[$col] . $row, $saldo < 0 ? abs($saldo) : 0);
        }

        setTextCenterToCeldaExcel($sheet, 'A1:' . $columnas[$col] . $row);
        setBorderToCeldaExcel($sheet, 'A1:' . $columnas[$col] . $row);

        for ($i = 0; $i <= $col; $i++)
            $sheet->getColumnDimension($columnas[$i])->setAutoSize(true);
    }

    public function imprimir_pedido(Request $request)
    {
        $barCode = new BarcodeGeneratorHTML();
        $pedido = PedidoBodega::find($request->pedido);
        $datos = [
            'pedido' => $pedido,
        ];
        return PDF::loadView('adminlte.gestion.bodega.pedido.partials.pdf_pedido', compact('datos', 'barCode'))
            ->setPaper(array(0, 0, 500, 195), 'landscape')->stream();
    }

    public function imprimir_pedidos_all(Request $request)
    {
        $barCode = new BarcodeGeneratorHTML();
        $query = PedidoBodega::where('estado', 1);
        if ($request->finca != 'T')
            $query = $query->where('id_empresa', $request->finca);
        if (!in_array(session('id_usuario'), [1, 2]))
            $query = $query->where('id_usuario', session('id_usuario'));
        $query = $query->orderBy('fecha')
            ->orderBy('id_empresa')
            ->orderBy('id_usuario')
            ->get();
        $pedidos = [];
        foreach ($query as $q) {
            $fecha_entrega = $q->getFechaEntrega();
            if ($fecha_entrega == $request->entrega) {
                $pedidos[] = $q;
            }
        }
        $datos = [
            'pedidos' => $pedidos,
        ];
        return PDF::loadView('adminlte.gestion.bodega.pedido.partials.pdf_pedido_all', compact('datos', 'barCode'))
            ->setPaper(array(0, 0, 500, 195), 'landscape')->stream();
    }

    public function imprimir_entregas_all(Request $request)
    {
        $barCode = new BarcodeGeneratorHTML();
        $query = PedidoBodega::where('estado', 1);
        if ($request->finca != 'T')
            $query = $query->where('id_empresa', $request->finca);
        if (!in_array(session('id_usuario'), [1, 2]))
            $query = $query->where('id_usuario', session('id_usuario'));
        $query = $query->orderBy('fecha')
            ->orderBy('id_empresa')
            ->orderBy('id_usuario')
            ->get();
        $pedidos = [];
        foreach ($query as $q) {
            $fecha_entrega = $q->getFechaEntrega();
            if ($fecha_entrega == $request->entrega) {
                $pedidos[] = $q;
            }
        }
        $datos = [
            'pedidos' => $pedidos,
            'fecha' => $request->entrega,
            'finca' => getConfiguracionEmpresa($request->finca),
        ];
        return PDF::loadView('adminlte.gestion.bodega.pedido.partials.pdf_entregas_all', compact('datos', 'barCode'))
            ->setPaper(array(0, 0, 800, 560), 'landscape')->stream();
    }
}
