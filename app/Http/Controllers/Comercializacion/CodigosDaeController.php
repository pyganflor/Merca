<?php

namespace yura\Http\Controllers\Comercializacion;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use yura\Http\Controllers\Controller;
use yura\Modelos\Pais;
use yura\Modelos\Submenu;
use Validator;
use Illuminate\Support\Facades\Storage as Almacenamiento;
use \PhpOffice\PhpSpreadsheet\IOFactory as IOFactory;
use yura\Modelos\CodigoDae;

class CodigosDaeController extends Controller
{
    public function inicio(Request $request)
    {
        return view('adminlte.gestion.comercializacion.codigo_dae.inicio', [
            'url' => $request->getRequestUri(),
            'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
        ]);
    }

    public function listar_reporte(Request $request)
    {
        $mes = str_pad($request->mes, 2, '0', STR_PAD_LEFT);
        $listado = CodigoDae::where('codigo_dae.anno', $request->anno)
            ->join('pais as p', 'p.codigo', '=', 'codigo_dae.codigo_pais')
            ->select('codigo_dae.*', 'p.nombre')->distinct()
            ->where('codigo_dae.estado', 1);
        if ($request->mes != '')
            $listado = $listado->where('codigo_dae.mes', $mes);
        $listado = $listado->orderBy('p.nombre')
            ->get();
        return view('adminlte.gestion.comercializacion.codigo_dae.partials.listado', [
            'listado' => $listado
        ]);
    }

    public function importar_codigos_dae(Request $request)
    {
        return view('adminlte.gestion.comercializacion.codigo_dae.forms.importar_codigos_dae', []);
    }

    public function importar_file_codigos_dae(Request $request)
    {
        ini_set('max_execution_time', env('MAX_EXECUTION_TIME'));
        $valida = Validator::make($request->all(), [
            'file_codigos_dae' => 'required',
        ]);
        $msg = '<div class="alert alert-success text-center">Se ha importado el archivo. Revise su contenido antes de procesarlo.</div>';
        $success = true;
        if (!$valida->fails()) {
            try {
                $archivo = $request->file_codigos_dae;
                $extension = $archivo->getClientOriginalExtension();
                $nombre_archivo = "upload_codigos_dae." . $extension;
                $r1 = Almacenamiento::disk('file_loads')->put($nombre_archivo, \File::get($archivo));
            } catch (\Exception $e) {
                if (strpos($e->getMessage(), 'DOMDocument::loadHTML(): Invalid char in CDATA') !== false)
                    $mensaje_error = 'Problema con el archivo excel';
                else
                    $mensaje_error = $e->getMessage();
                return [
                    'mensaje' => '<div class="alert alert-danger text-center">' .
                        '<p>¡Ha ocurrido un problema al subir el archivo, contacte al administrador del sistema!</p>' .
                        '<legend style="font-size: 0.9em; color: white; margin-bottom: 2px">mensaje de error</legend>' .
                        $mensaje_error .
                        '</div>',
                    'success' => false
                ];
            }
        } else {
            $errores = '';
            foreach ($valida->errors()->all() as $mi_error) {
                if ($errores == '') {
                    $errores = '<li>' . $mi_error . '</li>';
                } else {
                    $errores .= '<li>' . $mi_error . '</li>';
                }
            }
            $success = false;
            $msg = '<div class="alert alert-danger">' .
                '<p class="text-center">¡Por favor corrija los siguientes errores!</p>' .
                '<ul>' .
                $errores .
                '</ul>' .
                '</div>';
        }
        return [
            'mensaje' => $msg,
            'success' => $success,
            'extension' => $extension
        ];
    }

    public function get_importar_file_codigos_dae(Request $request)
    {
        $url = public_path('storage/file_loads/upload_codigos_dae.' . $request->extension);
        $document = IOFactory::load($url);
        $sheet = $document->getActiveSheet()->toArray(null, true, true, true);
        $listado = [];
        $fallos = false;
        foreach ($sheet as $pos => $row) {
            if ($pos > 2) {
                $pais = Pais::All()
                    ->where('codigo', $row['A'])
                    ->first();
                $listado[] = [
                    'pais' => $pais,
                    'row' => $row
                ];
                if ($pais == '')
                    $fallos = true;
            }
        }
        return view('adminlte.gestion.comercializacion.codigo_dae.forms.file_codigos_dae', [
            'listado' => $listado,
            'fallos' => $fallos,
        ]);
    }

    public function exportar_paises(Request $request)
    {
        $mis_paises = DB::table('cliente as c')
            ->join('detalle_cliente as dc', 'dc.id_cliente', '=', 'c.id_cliente')
            ->join('pais as p', 'p.codigo', '=', 'dc.codigo_pais')
            ->select('dc.codigo_pais as codigo', 'p.nombre')->distinct()
            ->where('c.estado', 1)
            ->where('dc.estado', 1)
            ->orderBy('p.nombre')
            ->get();
        $paises = Pais::orderBy('nombre')
            ->get();
        return view('adminlte.gestion.comercializacion.codigo_dae.forms.exportar_paises', [
            'mis_paises' => $mis_paises,
            'paises' => $paises,
        ]);
    }

    public function descargar_plantilla(Request $request)
    {
        $spread = new Spreadsheet();
        $this->excel_plantilla($spread, $request);

        $fileName = "CODIGOS_DAE.xlsx";
        $writer = new Xlsx($spread);

        //--------------------------- GUARDAR EL EXCEL -----------------------

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . urlencode($fileName) . '"');
        $writer->save('php://output');

        //$writer->save('/var/www/html/Dasalflor/storage/storage/excel/excel_prueba.xlsx');
    }

    public function excel_plantilla($spread, $request)
    {
        $columnas = getColumnasExcel();
        $sheet = $spread->getActiveSheet();
        $sheet->setTitle('Codigos Dae');

        $row = 1;
        $col = 0;
        setValueToCeldaExcel($sheet, $columnas[$col] . $row, 'Código DAE por país');
        setBgToCeldaExcel($sheet, $columnas[$col] . $row, '00b388');
        setColorTextToCeldaExcel($sheet, $columnas[$col] . $row, 'ffffff');
        $sheet->mergeCells($columnas[$col] . $row . ':' . $columnas[$col + 4] . $row);

        $row++;
        $col = 0;
        setValueToCeldaExcel($sheet, $columnas[$col] . $row, 'CODIGO PAIS');
        $col++;
        setValueToCeldaExcel($sheet, $columnas[$col] . $row, 'NOMBRE PAIS');
        $col++;
        setValueToCeldaExcel($sheet, $columnas[$col] . $row, 'CODIGO DAE');
        $col++;
        setValueToCeldaExcel($sheet, $columnas[$col] . $row, 'MES');
        $col++;
        setValueToCeldaExcel($sheet, $columnas[$col] . $row, 'AÑO');
        setBgToCeldaExcel($sheet, 'A1:' . $columnas[$col] . $row, '00b388');
        setColorTextToCeldaExcel($sheet, 'A1:' . $columnas[$col] . $row, 'ffffff');

        foreach (json_decode($request->data) as $item) {
            $pais = Pais::All()
                ->where('codigo', $item)
                ->first();
            $row++;
            $col = 0;
            setValueToCeldaExcel($sheet, $columnas[$col] . $row, $pais->codigo);
            $col++;
            setValueToCeldaExcel($sheet, $columnas[$col] . $row, $pais->nombre);
            $col += 3;
        }

        setTextCenterToCeldaExcel($sheet, 'A1:' . $columnas[$col] . $row);

        setBorderToCeldaExcel($sheet, 'A1:' . $columnas[$col] . $row);

        for ($i = 0; $i <= $col; $i++)
            $sheet->getColumnDimension($columnas[$i])->setAutoSize(true);
    }

    public function store_codigos_dae(Request $request)
    {
        try {
            foreach (json_decode($request->data) as $d) {
                DB::beginTransaction();
                $mes = str_pad($d->mes, 2, '0', STR_PAD_LEFT);
                $model = CodigoDae::All()
                    ->where('codigo_pais', $d->codigo_pais)
                    ->where('mes', $mes)
                    ->where('anno', $d->anno)
                    ->where('estado', 1)
                    ->first();
                if ($model == '') {
                    $model = new CodigoDae();
                    $model->codigo_pais = $d->codigo_pais;
                    $model->mes = $mes;
                    $model->anno = $d->anno;
                }
                $model->codigo_dae = $d->dae;
                $model->save();
                DB::commit();
            }

            $success = true;
            $msg = 'Se han <b>IMPORTADO</b> los codigos DAE correctamente';
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

    public function cambiar_estado(Request $request)
    {
        try {
            DB::beginTransaction();
            $model = CodigoDae::find($request->id);
            $model->estado = !$model->estado;
            $model->save();
            DB::commit();

            $success = true;
            $msg = 'Se ha <b>DESACTIVADO</b> el codigo DAE correctamente';
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
}
