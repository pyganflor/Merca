<?php

namespace yura\Http\Controllers\Bodega;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use yura\Http\Controllers\Controller;
use yura\Modelos\PedidoBodega;
use yura\Modelos\Submenu;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ReporteVentasController extends Controller
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

        return view('adminlte.gestion.bodega.reporte_ventas.inicio', [
            'url' => $request->getRequestUri(),
            'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
            'fincas' => $fincas
        ]);
    }

    public function listar_reporte(Request $request)
    {
        $fincas = DB::table('pedido_bodega as p')
            ->join('configuracion_empresa as f', 'f.id_configuracion_empresa', '=', 'p.id_empresa')
            ->select('p.id_empresa', 'f.nombre')->distinct();
        if ($request->finca != 'T')
            $fincas = $fincas->where('p.id_empresa', $request->finca);
        $fincas = $fincas->orderBy('f.nombre')
            ->get();
        $listado = [];
        foreach ($fincas as $f) {
            $pedidos = PedidoBodega::where('id_empresa', $f->id_empresa)
                ->where('fecha', '<=', $request->hasta)
                ->orderBy('fecha')
                ->get();
            $costos_armados = 0;
            $costos_pendientes = 0;
            $venta_armados = 0;
            $venta_pendientes = 0;
            $personas = [];
            foreach ($pedidos as $pedido) {
                $fecha_entrega = $pedido->getFechaEntrega();
                if ($fecha_entrega >= $request->desde && $fecha_entrega <= $request->hasta) {
                    $costo = $pedido->getCostos();
                    $venta = $pedido->getTotalMonto();
                    if ($pedido->armado == 1) {
                        $costos_armados += $costo;
                        $venta_armados += $venta;
                    } else {
                        $costos_pendientes += $costo;
                        $venta_pendientes += $venta;
                    }
                    if (!in_array($pedido->id_usuario, $personas))
                        $personas[] = $pedido->id_usuario;
                }
            }
            if (count($personas) > 0)
                $listado[] = [
                    'finca' => $f,
                    'costos_armados' => $costos_armados,
                    'costos_pendientes' => $costos_pendientes,
                    'venta_armados' => $venta_armados,
                    'venta_pendientes' => $venta_pendientes,
                    'personas' => $personas,
                ];
        }

        return view('adminlte.gestion.bodega.reporte_ventas.partials.listado', [
            'listado' => $listado
        ]);
    }

    public function exportar_reporte(Request $request)
    {
        $spread = new Spreadsheet();
        $this->excel_reporte($spread, $request);

        $fileName = "Resumen de Ventas.xlsx";
        $writer = new Xlsx($spread);

        //--------------------------- GUARDAR EL EXCEL -----------------------

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . urlencode($fileName) . '"');
        $writer->save('php://output');

        //$writer->save('/var/www/html/Dasalflor/storage/storage/excel/excel_prueba.xlsx');
    }

    public function excel_reporte($spread, $request)
    {
        $fincas = DB::table('pedido_bodega as p')
            ->join('configuracion_empresa as f', 'f.id_configuracion_empresa', '=', 'p.id_empresa')
            ->select('p.id_empresa', 'f.nombre')->distinct();
        if ($request->finca != 'T')
            $fincas = $fincas->where('p.id_empresa', $request->finca);
        $fincas = $fincas->orderBy('f.nombre')
            ->get();
        $listado = [];
        foreach ($fincas as $f) {
            $pedidos = PedidoBodega::where('id_empresa', $f->id_empresa)
                ->where('fecha', '<=', $request->hasta)
                ->orderBy('fecha')
                ->get();
            $costos_armados = 0;
            $costos_pendientes = 0;
            $venta_armados = 0;
            $venta_pendientes = 0;
            $personas = [];
            foreach ($pedidos as $pedido) {
                $fecha_entrega = $pedido->getFechaEntrega();
                if ($fecha_entrega >= $request->desde && $fecha_entrega <= $request->hasta) {
                    $costo = $pedido->getCostos();
                    $venta = $pedido->getTotalMonto();
                    if ($pedido->armado == 1) {
                        $costos_armados += $costo;
                        $venta_armados += $venta;
                    } else {
                        $costos_pendientes += $costo;
                        $venta_pendientes += $venta;
                    }
                    if (!in_array($pedido->id_usuario, $personas))
                        $personas[] = $pedido->id_usuario;
                }
            }
            if (count($personas) > 0)
                $listado[] = [
                    'finca' => $f,
                    'costos_armados' => $costos_armados,
                    'costos_pendientes' => $costos_pendientes,
                    'venta_armados' => $venta_armados,
                    'venta_pendientes' => $venta_pendientes,
                    'personas' => $personas,
                ];
        }

        $columnas = getColumnasExcel();
        $sheet = $spread->getActiveSheet();
        $sheet->setTitle('Resumen de Ventas');

        $row = 1;
        $col = 0;
        setValueToCeldaExcel($sheet, $columnas[$col] . $row, 'Finca');
        $col++;
        setValueToCeldaExcel($sheet, $columnas[$col] . $row, 'Venta Armados');
        $col++;
        setValueToCeldaExcel($sheet, $columnas[$col] . $row, 'Costos Armados');
        $col++;
        setValueToCeldaExcel($sheet, $columnas[$col] . $row, 'Margen Armados');
        $col++;
        setValueToCeldaExcel($sheet, $columnas[$col] . $row, '% Utilidad Armados');
        $col++;
        setValueToCeldaExcel($sheet, $columnas[$col] . $row, 'Venta Pendientes');
        $col++;
        setValueToCeldaExcel($sheet, $columnas[$col] . $row, 'Costos Pendientes');
        $col++;
        setValueToCeldaExcel($sheet, $columnas[$col] . $row, 'Margen Pendientes');
        $col++;
        setValueToCeldaExcel($sheet, $columnas[$col] . $row, '% Utilidad Pendientes');
        $col++;
        setValueToCeldaExcel($sheet, $columnas[$col] . $row, 'Venta TOTAL');
        $col++;
        setValueToCeldaExcel($sheet, $columnas[$col] . $row, 'Costos TOTAL');
        $col++;
        setValueToCeldaExcel($sheet, $columnas[$col] . $row, 'Margen TOTAL');
        $col++;
        setValueToCeldaExcel($sheet, $columnas[$col] . $row, '% Utilidad TOTAL');
        $col++;
        setValueToCeldaExcel($sheet, $columnas[$col] . $row, 'Personas');
        $col++;
        setValueToCeldaExcel($sheet, $columnas[$col] . $row, 'Venta x Persona');

        setBgToCeldaExcel($sheet, 'A' . $row . ':' . $columnas[$col] . $row, '00B388');
        setColorTextToCeldaExcel($sheet, 'A' . $row . ':' . $columnas[$col] . $row, 'FFFFFF');

        $total_costos_armados = 0;
        $total_venta_armados = 0;
        $total_costos_pendientes = 0;
        $total_venta_pendientes = 0;
        $total_personas = 0;
        foreach ($listado as $item) {
            $margen_armados = $item['venta_armados'] - $item['costos_armados'];
            $utilidad_armados = porcentaje($margen_armados, $item['costos_armados'], 1);
            $margen_pendientes = $item['venta_pendientes'] - $item['costos_pendientes'];
            $utilidad_pendientes = porcentaje($margen_pendientes, $item['costos_pendientes'], 1);
            $total_costos = $item['costos_armados'] + $item['costos_pendientes'];
            $total_venta = $item['venta_armados'] + $item['venta_pendientes'];
            $margen_total = $total_venta - $total_costos;
            $utilidad_total = porcentaje($margen_total, $total_costos, 1);
            $personas = count($item['personas']);

            $total_costos_armados += $item['costos_armados'];
            $total_venta_armados += $item['venta_armados'];
            $total_costos_pendientes += $item['costos_pendientes'];
            $total_venta_pendientes += $item['venta_pendientes'];
            $total_personas += $personas;

            $row++;
            $col = 0;
            setValueToCeldaExcel($sheet, $columnas[$col] . $row, $item['finca']->nombre);
            $col++;
            setValueToCeldaExcel($sheet, $columnas[$col] . $row, round($item['venta_armados'], 2));
            $col++;
            setValueToCeldaExcel($sheet, $columnas[$col] . $row, round($item['costos_armados'], 2));
            $col++;
            setValueToCeldaExcel($sheet, $columnas[$col] . $row, round($margen_armados, 2));
            $col++;
            setValueToCeldaExcel($sheet, $columnas[$col] . $row, round($utilidad_armados, 2) . '%');
            $col++;
            setValueToCeldaExcel($sheet, $columnas[$col] . $row, round($item['venta_pendientes'], 2));
            $col++;
            setValueToCeldaExcel($sheet, $columnas[$col] . $row, round($item['costos_pendientes'], 2));
            $col++;
            setValueToCeldaExcel($sheet, $columnas[$col] . $row, round($margen_pendientes, 2));
            $col++;
            setValueToCeldaExcel($sheet, $columnas[$col] . $row, round($utilidad_pendientes, 2) . '%');
            $col++;
            setValueToCeldaExcel($sheet, $columnas[$col] . $row, round($total_venta, 2));
            $col++;
            setValueToCeldaExcel($sheet, $columnas[$col] . $row, round($total_costos, 2));
            $col++;
            setValueToCeldaExcel($sheet, $columnas[$col] . $row, round($margen_total, 2));
            $col++;
            setValueToCeldaExcel($sheet, $columnas[$col] . $row, round($utilidad_total, 2) . '%');
            $col++;
            setValueToCeldaExcel($sheet, $columnas[$col] . $row, $personas);
            $col++;
            setValueToCeldaExcel($sheet, $columnas[$col] . $row, round($total_venta / $personas, 2));
        }

        $margen_armados = $total_venta_armados - $total_costos_armados;
        $utilidad_armados = porcentaje($margen_armados, $total_costos_armados, 1);
        $margen_pendientes = $total_venta_pendientes - $total_costos_pendientes;
        $utilidad_pendientes = porcentaje($margen_pendientes, $total_costos_pendientes, 1);
        $total_costos = $total_costos_armados + $total_costos_pendientes;
        $total_venta = $total_venta_armados + $total_venta_pendientes;
        $margen_total = $total_venta - $total_costos;
        $utilidad_total = porcentaje($margen_total, $total_costos, 1);

        $row++;
        $col = 0;
        setValueToCeldaExcel($sheet, $columnas[$col] . $row, 'TOTALES');
        $col++;
        setValueToCeldaExcel($sheet, $columnas[$col] . $row, round($total_venta_armados, 2));
        $col++;
        setValueToCeldaExcel($sheet, $columnas[$col] . $row, round($total_costos_armados, 2));
        $col++;
        setValueToCeldaExcel($sheet, $columnas[$col] . $row, round($margen_armados, 2));
        $col++;
        setValueToCeldaExcel($sheet, $columnas[$col] . $row, round($utilidad_armados, 2) . '%');
        $col++;
        setValueToCeldaExcel($sheet, $columnas[$col] . $row, round($total_venta_pendientes, 2));
        $col++;
        setValueToCeldaExcel($sheet, $columnas[$col] . $row, round($total_costos_pendientes, 2));
        $col++;
        setValueToCeldaExcel($sheet, $columnas[$col] . $row, round($margen_pendientes, 2));
        $col++;
        setValueToCeldaExcel($sheet, $columnas[$col] . $row, round($utilidad_pendientes, 2) . '%');
        $col++;
        setValueToCeldaExcel($sheet, $columnas[$col] . $row, round($total_venta, 2));
        $col++;
        setValueToCeldaExcel($sheet, $columnas[$col] . $row, round($total_costos, 2));
        $col++;
        setValueToCeldaExcel($sheet, $columnas[$col] . $row, round($margen_total, 2));
        $col++;
        setValueToCeldaExcel($sheet, $columnas[$col] . $row, round($utilidad_total, 2) . '%');
        $col++;
        setValueToCeldaExcel($sheet, $columnas[$col] . $row, $total_personas);
        $col++;
        setValueToCeldaExcel($sheet, $columnas[$col] . $row, round($total_venta / $total_personas, 2));

        setBgToCeldaExcel($sheet, 'A' . $row . ':' . $columnas[$col] . $row, '00B388');
        setColorTextToCeldaExcel($sheet, 'A' . $row . ':' . $columnas[$col] . $row, 'FFFFFF');

        setTextCenterToCeldaExcel($sheet, 'A1:' . $columnas[$col] . $row);
        setBorderToCeldaExcel($sheet, 'A1:' . $columnas[$col] . $row);

        for ($i = 0; $i <= $col; $i++)
            $sheet->getColumnDimension($columnas[$i])->setAutoSize(true);
    }
}
