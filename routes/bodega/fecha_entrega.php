<?php

Route::get('fecha_entrega', 'Bodega\FechaEntregaController@inicio');
Route::get('fecha_entrega/listar_reporte', 'Bodega\FechaEntregaController@listar_reporte');
Route::post('fecha_entrega/store_fecha', 'Bodega\FechaEntregaController@store_fecha');
Route::post('fecha_entrega/update_fecha', 'Bodega\FechaEntregaController@update_fecha');
Route::post('fecha_entrega/eliminar_fecha', 'Bodega\FechaEntregaController@eliminar_fecha');
