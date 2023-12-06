<?php

Route::get('inventario_bodega', 'Bodega\RegistrosInventarioController@inicio');
Route::get('inventario_bodega/listar_reporte', 'Bodega\RegistrosInventarioController@listar_reporte');
Route::post('inventario_bodega/update_inventario', 'Bodega\RegistrosInventarioController@update_inventario');
Route::post('inventario_bodega/delete_inventario', 'Bodega\RegistrosInventarioController@delete_inventario');
Route::get('inventario_bodega/exportar_reporte', 'Bodega\RegistrosInventarioController@exportar_reporte');
