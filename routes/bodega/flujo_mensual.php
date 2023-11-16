<?php

Route::get('flujo_mensual', 'Bodega\FlujoMensualController@inicio');
Route::get('flujo_mensual/listar_reporte', 'Bodega\FlujoMensualController@listar_reporte');
Route::get('flujo_mensual/exportar_reporte', 'Bodega\FlujoMensualController@exportar_reporte');
Route::post('flujo_mensual/update_ga', 'Bodega\FlujoMensualController@update_ga');
