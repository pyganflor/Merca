<?php

namespace yura\Http\Controllers\Bodega;

use Illuminate\Http\Request;
use yura\Http\Controllers\Controller;
use yura\Modelos\Producto;
use yura\Modelos\Submenu;
use Validator;
use yura\Modelos\CategoriaProducto;
use yura\Modelos\DetalleCombo;
use yura\Modelos\Proveedor;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ProductosController extends Controller
{
    public function inicio(Request $request)
    {
        $categorias = CategoriaProducto::where('estado', 1)
            ->orderBy('nombre')
            ->get();
        return view('adminlte.gestion.bodega.productos.inicio', [
            'url' => $request->getRequestUri(),
            'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
            'categorias' => $categorias
        ]);
    }

    public function listar_reporte(Request $request)
    {
        if ($request->tipo == 'N') { //productos normales
            $listado = Producto::Where(function ($q) use ($request) {
                $q->Where('nombre', 'like', '%' . mb_strtoupper($request->busqueda) . '%')
                    ->orWhere('codigo', 'like', '%' . mb_strtoupper($request->busqueda) . '%');
            })
                ->where('combo', 0)
                ->where('peso', 0);
            if ($request->categoria != 'T')
                $listado = $listado->where('id_categoria_producto', $request->categoria);
            $listado = $listado->orderBy('orden')
                ->get();

            $categorias = CategoriaProducto::where('estado', 1)
                ->orderBy('nombre')
                ->get();
            $proveedores = Proveedor::where('estado', 1)
                ->orderBy('nombre')
                ->get();
            return view('adminlte.gestion.bodega.productos.partials.listado', [
                'listado' => $listado,
                'categorias' => $categorias,
                'proveedores' => $proveedores,
            ]);
        }
        if ($request->tipo == 'C') { //productos combos
            $listado = Producto::Where(function ($q) use ($request) {
                $q->Where('nombre', 'like', '%' . mb_strtoupper($request->busqueda) . '%')
                    ->orWhere('codigo', 'like', '%' . mb_strtoupper($request->busqueda) . '%');
            })
                ->where('combo', 1)
                ->where('peso', 0)
                ->orderBy('orden')
                ->get();
            $categorias = CategoriaProducto::where('estado', 1)
                ->orderBy('nombre')
                ->get();
            return view('adminlte.gestion.bodega.productos.partials.listado_combos', [
                'listado' => $listado,
                'categorias' => $categorias,
            ]);
        }
        if ($request->tipo == 'P') { //productos de peso
            $listado = Producto::Where(function ($q) use ($request) {
                $q->Where('nombre', 'like', '%' . mb_strtoupper($request->busqueda) . '%')
                    ->orWhere('codigo', 'like', '%' . mb_strtoupper($request->busqueda) . '%');
            })
                ->where('combo', 0)
                ->where('peso', 1);
            if ($request->categoria != 'T')
                $listado = $listado->where('id_categoria_producto', $request->categoria);
            $listado = $listado->orderBy('orden')
                ->get();

            $categorias = CategoriaProducto::where('estado', 1)
                ->orderBy('nombre')
                ->get();
            $proveedores = Proveedor::where('estado', 1)
                ->orderBy('nombre')
                ->get();
            return view('adminlte.gestion.bodega.productos.partials.listado_peso', [
                'listado' => $listado,
                'categorias' => $categorias,
                'proveedores' => $proveedores,
            ]);
        }
    }

    public function store_producto(Request $request)
    {
        $valida = Validator::make($request->all(), [
            'nombre' => 'required|max:500|unique:producto',
            'codigo' => 'required|max:500|unique:producto',
            'unidad_medida' => 'required',
            'stock_minimo' => 'required',
            'stock_maximo' => 'required',
            'disponibles' => 'required',
            'conversion' => 'required',
            'precio_compra' => 'required',
            'precio_venta' => 'required',
        ], [
            'nombre.required' => 'El nombre es obligatorio',
            'nombre.unique' => 'El nombre ya existe',
            'unidad_medida.required' => 'La unidad de medida es obligatoria',
            'nombre.max' => 'El nombre es muy grande',
            'codigo.required' => 'El codigo es obligatorio',
            'codigo.unique' => 'El codigo ya existe',
            'codigo.max' => 'El codigo es muy grande',
            'stock_minimo.required' => 'El stock minimo es obligatorio',
            'stock_maximo.required' => 'El stock maximo es obligatorio',
            'disponibles.required' => 'Los disponibles son obligatorios',
            'conversion.required' => 'La conversion es obligatoria',
            'precio_compra.required' => 'El precio de compra es obligatorio',
            'precio_venta.required' => 'El precio de venta es obligatorio',
        ]);
        if (!$valida->fails()) {
            $model = new Producto();
            $model->id_categoria_producto = $request->categoria;
            $model->id_proveedor = $request->proveedor;
            $model->codigo = espacios(mb_strtoupper($request->codigo));
            $model->nombre = espacios(mb_strtoupper($request->nombre));
            $model->unidad_medida = mb_strtoupper($request->unidad_medida);
            $model->stock_minimo = $request->stock_minimo;
            $model->stock_maximo = $request->stock_maximo;
            $model->disponibles = 0;
            $model->conversion = $request->conversion;
            $model->precio = $request->precio_compra;
            $model->precio_venta = $request->precio_venta;
            $model->orden = $request->orden;
            $model->peso = isset($request->peso) ? $request->peso : 0;
            $model->save();
            $model = Producto::All()->last();

            //------------------------------  GRABAR LA IMAGEN DEL PRODUCTO  -----------------------------------------
            try {
                if ($request->hasFile('imagen_new')) {
                    $archivo = $request->file('imagen_new');
                    $input = array('image' => $archivo);
                    $reglas = array('image' => 'required|image|mimes:jpeg,png,jpg|max:2000');
                    $validacion = Validator::make($input, $reglas);

                    if ($validacion->fails()) {
                        return [
                            'mensaje' => '<div class="alert alert-danger text-center">' .
                                '<p>¡Imagen no válida!</p>' .
                                '</div>',
                            'success' => false
                        ];
                    } else {
                        $nombre_original = $archivo->getClientOriginalName();
                        $extension = $archivo->getClientOriginalExtension();
                        $imagen = "prod_" . $model->id_producto . "." . $extension;
                        $path = \public_path('images/productos');
                        $r1 = $request->file('imagen_new')->move($path, $imagen);
                        //$r1 = Almacenamiento::disk('images/productos')->put($imagen, \File::get($archivo));
                        if (!$r1) {
                            return [
                                'mensaje' => '<div class="alert alert-danger text-center">' .
                                    '<p>¡No se pudo subir la imagen!</p>' .
                                    '</div>',
                                'success' => false
                            ];
                        } else {
                            $model->imagen = $imagen;
                            $model->save();
                        }
                    }
                }
            } catch (\Exception $e) {
                return [
                    'mensaje' => '<div class="alert alert-danger text-center">' .
                        '<p>¡Ha ocurrido un problema al guardar la imagen en el sistema!</p>' .
                        $e->getMessage() .
                        '</div>',
                    'success' => false
                ];
            }
            bitacora('producto', $model->id_producto, 'I', 'Creacion del producto');
            $success = true;
            $msg = 'Se ha <strong>CREADO</strong> el producto satisfactoriamente';
        } else {
            $success = false;
            $errores = '';
            foreach ($valida->errors()->all() as $mi_error) {
                if ($errores == '') {
                    $errores = '<li>' . $mi_error . '</li>';
                } else {
                    $errores .= '<li>' . $mi_error . '</li>';
                }
            }
            $msg = '<div class="alert alert-danger">' .
                '<p class="text-center">¡Por favor corrija los siguientes errores!</p>' .
                '<ul>' .
                $errores .
                '</ul>' .
                '</div>';
        }
        return [
            'mensaje' => $msg,
            'success' => $success
        ];
    }

    public function store_combo(Request $request)
    {
        $valida = Validator::make($request->all(), [
            'nombre' => 'required|max:500|unique:producto',
            'codigo' => 'required|max:500|unique:producto',
            'precio_venta' => 'required',
        ], [
            'nombre.required' => 'El nombre es obligatorio',
            'nombre.unique' => 'El nombre ya existe',
            'nombre.max' => 'El nombre es muy grande',
            'codigo.required' => 'El codigo es obligatorio',
            'codigo.unique' => 'El codigo ya existe',
            'codigo.max' => 'El codigo es muy grande',
            'precio_venta.required' => 'El precio de venta es obligatorio',
        ]);
        if (!$valida->fails()) {
            $model = new Producto();
            $model->id_categoria_producto = $request->categoria;
            $model->codigo = espacios(mb_strtoupper($request->codigo));
            $model->nombre = espacios(mb_strtoupper($request->nombre));
            $model->unidad_medida = mb_strtoupper($request->unidad_medida);
            $model->combo = 1;
            $model->conversion = 1;
            $model->stock_minimo = 0;
            $model->disponibles = 0;
            $model->precio = 0;
            $model->precio_venta = $request->precio_venta;
            $model->orden = $request->orden;
            $model->save();
            $model = Producto::All()->last();

            //------------------------------  GRABAR LA IMAGEN DEL PRODUCTO  -----------------------------------------
            try {
                if ($request->hasFile('imagen_new')) {
                    $archivo = $request->file('imagen_new');
                    $input = array('image' => $archivo);
                    $reglas = array('image' => 'required|image|mimes:jpeg,png,jpg|max:2000');
                    $validacion = Validator::make($input, $reglas);

                    if ($validacion->fails()) {
                        return [
                            'mensaje' => '<div class="alert alert-danger text-center">' .
                                '<p>¡Imagen no válida!</p>' .
                                '</div>',
                            'success' => false
                        ];
                    } else {
                        $nombre_original = $archivo->getClientOriginalName();
                        $extension = $archivo->getClientOriginalExtension();
                        $imagen = "prod_" . $model->id_producto . "." . $extension;
                        $path = \public_path('images/productos');
                        $r1 = $request->file('imagen_new')->move($path, $imagen);
                        //$r1 = Almacenamiento::disk('images/productos')->put($imagen, \File::get($archivo));
                        if (!$r1) {
                            return [
                                'mensaje' => '<div class="alert alert-danger text-center">' .
                                    '<p>¡No se pudo subir la imagen!</p>' .
                                    '</div>',
                                'success' => false
                            ];
                        } else {
                            $model->imagen = $imagen;
                            $model->save();
                        }
                    }
                }
            } catch (\Exception $e) {
                return [
                    'mensaje' => '<div class="alert alert-danger text-center">' .
                        '<p>¡Ha ocurrido un problema al guardar la imagen en el sistema!</p>' .
                        $e->getMessage() .
                        '</div>',
                    'success' => false
                ];
            }
            bitacora('producto', $model->id_producto, 'I', 'Creacion del combo');
            $success = true;
            $msg = 'Se ha <strong>CREADO</strong> el combo satisfactoriamente';
        } else {
            $success = false;
            $errores = '';
            foreach ($valida->errors()->all() as $mi_error) {
                if ($errores == '') {
                    $errores = '<li>' . $mi_error . '</li>';
                } else {
                    $errores .= '<li>' . $mi_error . '</li>';
                }
            }
            $msg = '<div class="alert alert-danger">' .
                '<p class="text-center">¡Por favor corrija los siguientes errores!</p>' .
                '<ul>' .
                $errores .
                '</ul>' .
                '</div>';
        }
        return [
            'mensaje' => $msg,
            'success' => $success
        ];
    }

    public function update_producto(Request $request)
    {
        $valida = Validator::make($request->all(), [
            'nombre' => 'required|max:500',
            'codigo' => 'required|max:500',
            'unidad_medida' => 'required',
            'stock_minimo' => 'required',
            'stock_maximo' => 'required',
            'conversion' => 'required',
            'precio_compra' => 'required',
            'precio_venta' => 'required',
        ], [
            'nombre.required' => 'El nombre es obligatorio',
            'nombre.max' => 'El nombre es muy grande',
            'codigo.required' => 'El codigo es obligatorio',
            'codigo.max' => 'El codigo es muy grande',
            'unidad_medida.required' => 'La unidad de medida es obligatoria',
            'stock_minimo.required' => 'El stock minimo es obligatorio',
            'stock_maximo.required' => 'El stock maximo es obligatorio',
            'conversion.required' => 'La conversion es obligatoria',
            'precio_compra.required' => 'El precio de compra es obligatorio',
            'precio_venta.required' => 'El precio de venta es obligatorio',
        ]);
        if (!$valida->fails()) {
            $existe_nombre = Producto::All()
                ->where('id_producto', '!=', $request->id)
                ->where('nombre', espacios(mb_strtoupper($request->nombre)))
                ->where('combo', 0)
                ->first();
            if ($existe_nombre != '') {
                $success = false;
                $msg = '<div class="alert alert-warning text-center">' .
                    '<p>El nombre del producto ya existe</p>'
                    . '</div>';
            } else {
                $existe_codigo = Producto::All()
                    ->where('id_producto', '!=', $request->id)
                    ->where('codigo', espacios(mb_strtoupper($request->codigo)))
                    ->where('combo', 0)
                    ->first();
                if ($existe_codigo != '') {
                    $success = false;
                    $msg = '<div class="alert alert-warning text-center">' .
                        '<p>El codigo del producto y existe</p>'
                        . '</div>';
                } else {
                    $model = Producto::find($request->id);
                    $model->id_categoria_producto = $request->categoria;
                    $model->id_proveedor = $request->proveedor;
                    $model->codigo = $request->codigo;
                    $model->nombre = espacios(mb_strtoupper($request->nombre));
                    $model->stock_minimo = $request->stock_minimo;
                    $model->stock_maximo = $request->stock_maximo;
                    $model->unidad_medida = $request->unidad_medida;
                    $model->conversion = $request->conversion;
                    $model->precio = $request->precio_compra;
                    $model->precio_venta = $request->precio_venta;
                    $model->tiene_iva = $request->tiene_iva == 'true' ? 1 : 0;
                    $model->orden = $request->orden;

                    //------------------------------  GRABAR LA IMAGEN DEL PRODUCTO  -----------------------------------------
                    try {
                        if ($request->hasFile('imagen_' . $model->id_producto)) {
                            $archivo = $request->file('imagen_' . $model->id_producto);
                            $input = array('image' => $archivo);
                            $reglas = array('image' => 'required|max:2000');
                            $validacion = Validator::make($input, $reglas);

                            if ($validacion->fails()) {
                                return [
                                    'mensaje' => '<div class="alert alert-danger text-center">' .
                                        '<p>¡Imagen no válida!</p>' .
                                        '</div>',
                                    'success' => false
                                ];
                            } else {
                                $nombre_original = $archivo->getClientOriginalName();
                                $extension = $archivo->getClientOriginalExtension();
                                $imagen = "prod_" . $model->id_producto . "." . $extension;
                                $path = \public_path('images/productos');
                                $r1 = $request->file('imagen_' . $model->id_producto)->move($path, $imagen);
                                //$r1 = Almacenamiento::disk('images/productos')->put($imagen, \File::get($archivo));
                                if (!$r1) {
                                    return [
                                        'mensaje' => '<div class="alert alert-danger text-center">' .
                                            '<p>¡No se pudo subir la imagen!</p>' .
                                            '</div>',
                                        'success' => false
                                    ];
                                } else {
                                    $model->imagen = $imagen;
                                }
                            }
                        }
                    } catch (\Exception $e) {
                        return [
                            'mensaje' => '<div class="alert alert-danger text-center">' .
                                '<p>¡Ha ocurrido un problema al guardar la imagen en el sistema!</p>' .
                                $e->getMessage() .
                                '</div>',
                            'success' => false
                        ];
                    }

                    if ($model->save()) {
                        $success = true;
                        $msg = 'Se ha <strong>MODIFICADO</strong> el producto satisfactoriamente';
                        bitacora('producto', $model->id_producto, 'U', 'Modifico el producto');
                    } else {
                        $success = false;
                        $msg = '<div class="alert alert-warning text-center">' .
                            '<p> Ha ocurrido un problema al guardar la información al sistema</p>'
                            . '</div>';
                    }
                }
            }
        } else {
            $success = false;
            $errores = '';
            foreach ($valida->errors()->all() as $mi_error) {
                if ($errores == '') {
                    $errores = '<li>' . $mi_error . '</li>';
                } else {
                    $errores .= '<li>' . $mi_error . '</li>';
                }
            }
            $msg = '<div class="alert alert-danger">' .
                '<p class="text-center">¡Por favor corrija los siguientes errores!</p>' .
                '<ul>' .
                $errores .
                '</ul>' .
                '</div>';
        }
        return [
            'mensaje' => $msg,
            'success' => $success
        ];
    }

    public function update_combo(Request $request)
    {
        $valida = Validator::make($request->all(), [
            'nombre' => 'required|max:500',
            'codigo' => 'required|max:500',
            'precio_venta' => 'required',
        ], [
            'nombre.required' => 'El nombre es obligatorio',
            'nombre.max' => 'El nombre es muy grande',
            'codigo.required' => 'El codigo es obligatorio',
            'codigo.max' => 'El codigo es muy grande',
            'precio_venta.required' => 'El precio de venta es obligatorio',
        ]);
        if (!$valida->fails()) {
            $existe_nombre = Producto::All()
                ->where('id_producto', '!=', $request->id)
                ->where('nombre', espacios(mb_strtoupper($request->nombre)))
                ->where('combo', 1)
                ->first();
            if ($existe_nombre != '') {
                $success = false;
                $msg = '<div class="alert alert-warning text-center">' .
                    '<p>El nombre del combo ya existe</p>'
                    . '</div>';
            } else {
                $existe_codigo = Producto::All()
                    ->where('id_producto', '!=', $request->id)
                    ->where('codigo', espacios(mb_strtoupper($request->codigo)))
                    ->where('combo', 1)
                    ->first();
                if ($existe_codigo != '') {
                    $success = false;
                    $msg = '<div class="alert alert-warning text-center">' .
                        '<p>El codigo del combo y existe</p>'
                        . '</div>';
                } else {
                    $model = Producto::find($request->id);
                    $model->id_categoria_producto = $request->categoria;
                    $model->codigo = $request->codigo;
                    $model->nombre = espacios(mb_strtoupper($request->nombre));
                    $model->precio_venta = $request->precio_venta;
                    $model->tiene_iva = $request->tiene_iva == 'true' ? 1 : 0;
                    $model->orden = $request->orden;

                    //------------------------------  GRABAR LA IMAGEN DEL PRODUCTO  -----------------------------------------
                    try {
                        if ($request->hasFile('imagen_' . $model->id_producto)) {
                            $archivo = $request->file('imagen_' . $model->id_producto);
                            $input = array('image' => $archivo);
                            $reglas = array('image' => 'required|max:2000');
                            $validacion = Validator::make($input, $reglas);

                            if ($validacion->fails()) {
                                return [
                                    'mensaje' => '<div class="alert alert-danger text-center">' .
                                        '<p>¡Imagen no válida!</p>' .
                                        '</div>',
                                    'success' => false
                                ];
                            } else {
                                $nombre_original = $archivo->getClientOriginalName();
                                $extension = $archivo->getClientOriginalExtension();
                                $imagen = "prod_" . $model->id_producto . "." . $extension;
                                $path = \public_path('images/productos');
                                $r1 = $request->file('imagen_' . $model->id_producto)->move($path, $imagen);
                                //$r1 = Almacenamiento::disk('images/productos')->put($imagen, \File::get($archivo));
                                if (!$r1) {
                                    return [
                                        'mensaje' => '<div class="alert alert-danger text-center">' .
                                            '<p>¡No se pudo subir la imagen!</p>' .
                                            '</div>',
                                        'success' => false
                                    ];
                                } else {
                                    $model->imagen = $imagen;
                                }
                            }
                        }
                    } catch (\Exception $e) {
                        return [
                            'mensaje' => '<div class="alert alert-danger text-center">' .
                                '<p>¡Ha ocurrido un problema al guardar la imagen en el sistema!</p>' .
                                $e->getMessage() .
                                '</div>',
                            'success' => false
                        ];
                    }

                    if ($model->save()) {
                        $success = true;
                        $msg = 'Se ha <strong>MODIFICADO</strong> el combo satisfactoriamente';
                        bitacora('producto', $model->id_producto, 'U', 'Modifico el combo');
                    } else {
                        $success = false;
                        $msg = '<div class="alert alert-warning text-center">' .
                            '<p> Ha ocurrido un problema al guardar la información al sistema</p>'
                            . '</div>';
                    }
                }
            }
        } else {
            $success = false;
            $errores = '';
            foreach ($valida->errors()->all() as $mi_error) {
                if ($errores == '') {
                    $errores = '<li>' . $mi_error . '</li>';
                } else {
                    $errores .= '<li>' . $mi_error . '</li>';
                }
            }
            $msg = '<div class="alert alert-danger">' .
                '<p class="text-center">¡Por favor corrija los siguientes errores!</p>' .
                '<ul>' .
                $errores .
                '</ul>' .
                '</div>';
        }
        return [
            'mensaje' => $msg,
            'success' => $success
        ];
    }

    public function cambiar_estado_producto(Request $request)
    {
        $model = Producto::find($request->id);
        $model->estado = $model->estado == 1 ? 0 : 1;
        $model->save();

        $success = true;
        $msg = 'Se ha <strong>MODIFICADO</strong> el producto satisfactoriamente';
        return [
            'mensaje' => $msg,
            'success' => $success
        ];
    }

    public function store_categoria(Request $request)
    {
        $valida = Validator::make($request->all(), [
            'nombre' => 'required|max:500|unique:categoria_producto',
        ], [
            'nombre.required' => 'La categoria es obligatorio',
            'nombre.unique' => 'La categoria ya existe',
            'nombre.max' => 'El nombre es muy grande',
        ]);
        if (!$valida->fails()) {
            $model = new CategoriaProducto();
            $model->nombre = espacios(mb_strtoupper($request->nombre));
            $model->save();
            $model = CategoriaProducto::All()->last();

            bitacora('categoria_producto', $model->id_categoria_producto, 'I', 'Creacion de la categoria');
            $success = true;
            $msg = 'Se ha <strong>CREADO</strong> la categoria satisfactoriamente';
        } else {
            $success = false;
            $errores = '';
            foreach ($valida->errors()->all() as $mi_error) {
                if ($errores == '') {
                    $errores = '<li>' . $mi_error . '</li>';
                } else {
                    $errores .= '<li>' . $mi_error . '</li>';
                }
            }
            $msg = '<div class="alert alert-danger">' .
                '<p class="text-center">¡Por favor corrija los siguientes errores!</p>' .
                '<ul>' .
                $errores .
                '</ul>' .
                '</div>';
        }
        return [
            'mensaje' => $msg,
            'success' => $success
        ];
    }

    public function admin_combo(Request $request)
    {
        $combo = Producto::find($request->id);

        return view('adminlte.gestion.bodega.productos.forms.admin_combo', [
            'combo' => $combo,
            'categorias' => CategoriaProducto::All()->where('estado', '=', 1),
        ]);
    }

    public function buscar_productos(Request $request)
    {
        $listado = Producto::where('combo', 0)
            ->where('estado', 1);
        if ($request->categoria != 'T')
            $listado = $listado->where('id_categoria_producto', $request->categoria);
        $listado = $listado->orderBy('orden')
            ->get();

        return view('adminlte.gestion.bodega.productos.forms.buscar_productos', [
            'listado' => $listado,
        ]);
    }

    public function store_agregar_productos(Request $request)
    {
        $delete = DetalleCombo::All()
            ->where('id_producto', $request->id_combo);
        foreach ($delete as $del)
            $del->delete();

        foreach (json_decode($request->data) as $d) {
            $model = new DetalleCombo();
            $model->id_producto = $request->id_combo;
            $model->id_item = $d->id_item;
            $model->unidades = $d->unidades;
            $model->save();
        }

        return [
            'success' => true,
            'mensaje' => 'Se han <strong>ASIGNADO</strong> los productos al combo correctamente',
        ];
    }

    public function exportar_reporte(Request $request)
    {
        $spread = new Spreadsheet();
        $this->excel_reporte($spread, $request);

        $fileName = "PRODUCTOS.xlsx";
        $writer = new Xlsx($spread);

        //--------------------------- GUARDAR EL EXCEL -----------------------

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . urlencode($fileName) . '"');
        $writer->save('php://output');

        //$writer->save('/var/www/html/Dasalflor/storage/storage/excel/excel_prueba.xlsx');
    }

    public function excel_reporte($spread, $request)
    {
        $listado = Producto::where('estado', 1)
            ->orderBy('orden')
            ->get();

        $columnas = getColumnasExcel();
        $sheet = $spread->getActiveSheet();
        $sheet->setTitle('PRODUCTOS');

        $row = 1;
        $col = 0;
        setValueToCeldaExcel($sheet, $columnas[$col] . $row, 'CATEGORIA');
        $col++;
        setValueToCeldaExcel($sheet, $columnas[$col] . $row, 'PROVEEDOR');
        $col++;
        setValueToCeldaExcel($sheet, $columnas[$col] . $row, 'CODIGO');
        $col++;
        setValueToCeldaExcel($sheet, $columnas[$col] . $row, 'NOMBRE');
        $col++;
        setValueToCeldaExcel($sheet, $columnas[$col] . $row, 'UM');
        $col++;
        setValueToCeldaExcel($sheet, $columnas[$col] . $row, 'STOCK MINIMO');
        $col++;
        setValueToCeldaExcel($sheet, $columnas[$col] . $row, 'CONVERSION');
        $col++;
        setValueToCeldaExcel($sheet, $columnas[$col] . $row, 'COSTO');
        $col++;
        setValueToCeldaExcel($sheet, $columnas[$col] . $row, 'VENTA');
        $col++;
        setValueToCeldaExcel($sheet, $columnas[$col] . $row, 'IVA');
        $col++;
        setValueToCeldaExcel($sheet, $columnas[$col] . $row, 'MARGEN');
        $col++;
        setValueToCeldaExcel($sheet, $columnas[$col] . $row, '% UTILIDAD');
        setBgToCeldaExcel($sheet, 'A' . $row . ':' . $columnas[$col] . $row, '00B388');
        setColorTextToCeldaExcel($sheet, 'A' . $row . ':' . $columnas[$col] . $row, 'FFFFFF');

        foreach ($listado as $item) {
            if ($item->combo == 0)
                $precio_costo = $item->precio;
            else
                $precio_costo = $item->getCostoCombo();

            if ($item->tiene_iva) {
                $temp = $item->precio_venta - porcentaje(12, $item->precio_venta, 2);
                $margen = $temp - $precio_costo;
            } else {
                $margen = $item->precio_venta - $precio_costo;
            }

            $row++;
            $col = 0;
            if ($item->combo == 0)
                setValueToCeldaExcel($sheet, $columnas[$col] . $row, $item->categoria_producto != '' ? $item->categoria_producto->nombre : '');
            $col++;
            if ($item->combo == 0)
                setValueToCeldaExcel($sheet, $columnas[$col] . $row, $item->proveedor != '' ? $item->proveedor->nombre : '');
            $col++;
            setValueToCeldaExcel($sheet, $columnas[$col] . $row, $item->codigo);
            $col++;
            setValueToCeldaExcel($sheet, $columnas[$col] . $row, $item->nombre);
            $col++;
            if ($item->combo == 0)
                setValueToCeldaExcel($sheet, $columnas[$col] . $row, $item->unidad_medida);
            $col++;
            if ($item->combo == 0)
                setValueToCeldaExcel($sheet, $columnas[$col] . $row, $item->stock_minimo);
            $col++;
            if ($item->combo == 0)
                setValueToCeldaExcel($sheet, $columnas[$col] . $row, $item->conversion);
            $col++;
            setValueToCeldaExcel($sheet, $columnas[$col] . $row, $precio_costo);
            $col++;
            setValueToCeldaExcel($sheet, $columnas[$col] . $row, $item->precio_venta);
            $col++;
            setValueToCeldaExcel($sheet, $columnas[$col] . $row, $item->tiene_iva == 1 ? 'SI' : 'NO');
            $col++;
            setValueToCeldaExcel($sheet, $columnas[$col] . $row, $margen);
            $col++;
            setValueToCeldaExcel($sheet, $columnas[$col] . $row, porcentaje($margen, $precio_costo, 1) . '%');
        }

        setTextCenterToCeldaExcel($sheet, 'A1:' . $columnas[$col] . $row);
        setBorderToCeldaExcel($sheet, 'A1:' . $columnas[$col] . $row);

        for ($i = 0; $i <= $col; $i++)
            $sheet->getColumnDimension($columnas[$i])->setAutoSize(true);
    }
}
