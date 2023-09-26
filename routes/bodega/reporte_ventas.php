<?php

Route::get('reporte_ventas', 'Bodega\ReporteVentasController@inicio');
Route::get('reporte_ventas/listar_reporte', 'Bodega\ReporteVentasController@listar_reporte');
Route::get('reporte_ventas/exportar_reporte', 'Bodega\ReporteVentasController@exportar_reporte');
