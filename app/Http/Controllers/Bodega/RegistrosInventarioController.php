<?php

namespace yura\Http\Controllers\Bodega;

use Illuminate\Http\Request;
use Validator;
use yura\Http\Controllers\Controller;
use yura\Modelos\IngresoBodega;
use yura\Modelos\InventarioBodega;
use yura\Modelos\SalidaBodega;
use yura\Modelos\Submenu;

class RegistrosInventarioController extends Controller
{
    public function inicio(Request $request)
    {
        return view('adminlte.gestion.bodega.inventario_bodega.inicio', [
            'url' => $request->getRequestUri(),
            'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
        ]);
    }

    public function listar_reporte(Request $request)
    {
        $inventarios = InventarioBodega::join('ingreso_bodega as i', 'i.id_ingreso_bodega', '=', 'inventario_bodega.id_ingreso_bodega')
            ->select('inventario_bodega.*')->distinct()
            ->where('inventario_bodega.fecha_ingreso', '>=', $request->desde)
            ->where('inventario_bodega.fecha_ingreso', '<=', $request->hasta)
            ->orderBy('inventario_bodega.fecha_registro', 'desc')
            ->get();
        $ingresos = IngresoBodega::where('fecha', '>=', $request->desde)
            ->where('fecha', '<=', $request->hasta)
            ->orderBy('fecha_registro', 'desc')
            ->get();
        $salidas = SalidaBodega::where('fecha', '>=', $request->desde)
            ->where('fecha', '<=', $request->hasta)
            ->orderBy('fecha_registro', 'desc')
            ->get();
        return view('adminlte.gestion.bodega.inventario_bodega.partials.listado', [
            'inventarios' => $inventarios,
            'ingresos' => $ingresos,
            'salidas' => $salidas,
        ]);
    }

    public function update_inventario(Request $request)
    {
        $valida = Validator::make($request->all(), [
            'cantidad' => 'required',
            'disponibles' => 'required',
            'precio' => 'required',
        ], [
            'cantidad.required' => 'La cantidad ingresada es obligatoria',
            'disponibles.required' => 'La cantidad disponible es obligatoria',
            'precio.required' => 'El precio es obligatorio',
        ]);
        if (!$valida->fails()) {
            $model = InventarioBodega::find($request->id);
            $model->cantidad = $request->cantidad;
            $model->disponibles = $request->disponibles;
            $model->precio = $request->precio;
            $model->save();

            $producto = $model->producto;
            $producto->disponibles = $producto->getDisponibles();
            $producto->save();

            $ingreso = $model->ingreso_bodega;
            $ingreso->cantidad = $request->cantidad;
            $ingreso->precio = $request->precio;
            $ingreso->save();

            $success = true;
            $msg = 'Se ha <strong>MODIFICADO</strong> el inventario satisfactoriamente';
            bitacora('inventario_bodega', $model->id_inventario_bodega, 'U', 'Modifico el inventario');
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

    public function delete_inventario(Request $request)
    {
        $valida = Validator::make($request->all(), [
            'id' => 'required',
        ], [
            'id.required' => 'El inventario es obligatorio',
        ]);
        if (!$valida->fails()) {
            $inventario = InventarioBodega::find($request->id);
            $producto = $inventario->producto;
            $producto->disponibles -= $inventario->disponibles;
            $producto->save();
            $id = $inventario->id_inventario_bodega;
            $cant = $inventario->disponibles;
            $fecha = $inventario->fecha_ingreso;
            $inventario->ingreso_bodega->delete();
            $inventario->delete();

            $success = true;
            $msg = 'Se ha <strong>ELIMINADO</strong> el inventario satisfactoriamente';
            bitacora('inventario_bodega', $id, 'D', 'ELIMINO del inventario: ' . $cant . ' ' . $producto->nombre . ', fecha: ' . $fecha);
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

    public function exportar_reporte(Request $request)
    {
        $spread = new Spreadsheet();
        $this->excel_reporte($spread, $request);

        $fileName = "Registros de Inventario.xlsx";
        $writer = new Xlsx($spread);

        //--------------------------- GUARDAR EL EXCEL -----------------------

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . urlencode($fileName) . '"');
        $writer->save('php://output');

        //$writer->save('/var/www/html/Dasalflor/storage/storage/excel/excel_prueba.xlsx');
    }

    public function excel_reporte($spread, $request)
    {
        $finca = getFincaActiva();
        $inventarios = InventarioBodega::join('ingreso_bodega as i', 'i.id_ingreso_bodega', '=', 'inventario_bodega.id_ingreso_bodega')
            ->select('inventario_bodega.*')->distinct()
            ->where('inventario_bodega.fecha_ingreso', '>=', $request->desde)
            ->where('inventario_bodega.fecha_ingreso', '<=', $request->hasta)
            ->where('i.id_empresa', $finca)
            ->orderBy('inventario_bodega.fecha_registro', 'desc')
            ->get();
        $ingresos = IngresoBodega::where('fecha', '>=', $request->desde)
            ->where('fecha', '<=', $request->hasta)
            ->where('id_empresa', $finca)
            ->orderBy('fecha_registro', 'desc')
            ->get();
        $salidas = SalidaBodega::where('fecha', '>=', $request->desde)
            ->where('fecha', '<=', $request->hasta)
            ->where('id_empresa', $finca)
            ->orderBy('fecha_registro', 'desc')
            ->get();

        $columnas = getColumnasExcel();

        /* HOJA DE INVENTARIO ACTUAL */
        $sheet = $spread->getActiveSheet();
        $sheet->setTitle('Inventario Actual');
        $row = 1;
        $col = 0;
        setValueToCeldaExcel($sheet, $columnas[$col] . $row, 'FECHA y HORA');
        $col++;
        setValueToCeldaExcel($sheet, $columnas[$col] . $row, 'PRODUCTO');
        $col++;
        setValueToCeldaExcel($sheet, $columnas[$col] . $row, 'INGRESO');
        $col++;
        setValueToCeldaExcel($sheet, $columnas[$col] . $row, 'DISPONIBLES');
        $col++;
        setValueToCeldaExcel($sheet, $columnas[$col] . $row, 'PRECIO');
        $col++;
        setValueToCeldaExcel($sheet, $columnas[$col] . $row, 'VALOR');
        setBgToCeldaExcel($sheet, 'A' . $row . ':' . $columnas[$col] . $row, '00B388');
        setColorTextToCeldaExcel($sheet, 'A' . $row . ':' . $columnas[$col] . $row, 'FFFFFF');

        foreach ($inventarios as $item) {
            $producto = $item->producto;

            $row++;
            $col = 0;
            setValueToCeldaExcel($sheet, $columnas[$col] . $row, convertDateTimeToText($item->fecha_registro));
            $col++;
            setValueToCeldaExcel($sheet, $columnas[$col] . $row, $producto->nombre);
            $col++;
            setValueToCeldaExcel($sheet, $columnas[$col] . $row, $item->cantidad);
            $col++;
            setValueToCeldaExcel($sheet, $columnas[$col] . $row, $item->disponibles);
            $col++;
            setValueToCeldaExcel($sheet, $columnas[$col] . $row, '$' . $item->precio);
            $col++;
            setValueToCeldaExcel($sheet, $columnas[$col] . $row, '$' . round($item->precio * $item->disponibles, 2));
        }

        setTextCenterToCeldaExcel($sheet, 'A1:' . $columnas[$col] . $row);
        setBorderToCeldaExcel($sheet, 'A1:' . $columnas[$col] . $row);

        for ($i = 0; $i <= $col; $i++)
            $sheet->getColumnDimension($columnas[$i])->setAutoSize(true);

        /* HOJA DE INGRESOS */
        $sheet = $spread->createSheet();
        $sheet->setTitle('Ingresos');
        $row = 1;
        $col = 0;
        setValueToCeldaExcel($sheet, $columnas[$col] . $row, 'FECHA y HORA');
        $col++;
        setValueToCeldaExcel($sheet, $columnas[$col] . $row, 'PRODUCTO');
        $col++;
        setValueToCeldaExcel($sheet, $columnas[$col] . $row, 'CANTIDAD');
        $col++;
        setValueToCeldaExcel($sheet, $columnas[$col] . $row, 'PRECIO');
        $col++;
        setValueToCeldaExcel($sheet, $columnas[$col] . $row, 'VALOR');
        setBgToCeldaExcel($sheet, 'A' . $row . ':' . $columnas[$col] . $row, '00B388');
        setColorTextToCeldaExcel($sheet, 'A' . $row . ':' . $columnas[$col] . $row, 'FFFFFF');

        foreach ($ingresos as $item) {
            $producto = $item->producto;

            $row++;
            $col = 0;
            setValueToCeldaExcel($sheet, $columnas[$col] . $row, convertDateTimeToText($item->fecha_registro));
            $col++;
            setValueToCeldaExcel($sheet, $columnas[$col] . $row, $producto->nombre);
            $col++;
            setValueToCeldaExcel($sheet, $columnas[$col] . $row, $item->cantidad);
            $col++;
            setValueToCeldaExcel($sheet, $columnas[$col] . $row, '$' . $item->precio);
            $col++;
            setValueToCeldaExcel($sheet, $columnas[$col] . $row, '$' . round($item->precio * $item->cantidad, 2));
        }

        setTextCenterToCeldaExcel($sheet, 'A1:' . $columnas[$col] . $row);
        setBorderToCeldaExcel($sheet, 'A1:' . $columnas[$col] . $row);

        for ($i = 0; $i <= $col; $i++)
            $sheet->getColumnDimension($columnas[$i])->setAutoSize(true);

        /* HOJA DE SALIDAS */
        $sheet = $spread->createSheet();
        $sheet->setTitle('Salidas');
        $row = 1;
        $col = 0;
        setValueToCeldaExcel($sheet, $columnas[$col] . $row, 'FECHA y HORA');
        $col++;
        setValueToCeldaExcel($sheet, $columnas[$col] . $row, 'PRODUCTO');
        $col++;
        setValueToCeldaExcel($sheet, $columnas[$col] . $row, 'FINCA');
        $col++;
        setValueToCeldaExcel($sheet, $columnas[$col] . $row, 'SECTOR');
        $col++;
        setValueToCeldaExcel($sheet, $columnas[$col] . $row, 'CANTIDAD');
        $col++;
        setValueToCeldaExcel($sheet, $columnas[$col] . $row, 'VALOR');
        setBgToCeldaExcel($sheet, 'A' . $row . ':' . $columnas[$col] . $row, '00B388');
        setColorTextToCeldaExcel($sheet, 'A' . $row . ':' . $columnas[$col] . $row, 'FFFFFF');

        foreach ($salidas as $item) {
            $producto = $item->producto;
            $sector = $item->sector;

            $row++;
            $col = 0;
            setValueToCeldaExcel($sheet, $columnas[$col] . $row, convertDateTimeToText($item->fecha_registro));
            $col++;
            setValueToCeldaExcel($sheet, $columnas[$col] . $row, $producto->nombre);
            $col++;
            setValueToCeldaExcel($sheet, $columnas[$col] . $row, $sector->empresa->nombre);
            $col++;
            setValueToCeldaExcel($sheet, $columnas[$col] . $row, $sector->nombre);
            $col++;
            setValueToCeldaExcel($sheet, $columnas[$col] . $row, $item->cantidad);
            $col++;
            setValueToCeldaExcel($sheet, $columnas[$col] . $row, '$' . round($item->getCostoTotal(), 2));
        }

        setTextCenterToCeldaExcel($sheet, 'A1:' . $columnas[$col] . $row);
        setBorderToCeldaExcel($sheet, 'A1:' . $columnas[$col] . $row);

        for ($i = 0; $i <= $col; $i++)
            $sheet->getColumnDimension($columnas[$i])->setAutoSize(true);
    }
}
