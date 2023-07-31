<?php

Route::get('inventario_cajas', 'InventarioCajasController@inicio');
Route::get('inventario_cajas/listar_reporte', 'InventarioCajasController@listar_reporte');
Route::post('inventario_cajas/eliminar_caja', 'InventarioCajasController@eliminar_caja');
Route::get('inventario_cajas/editar_caja', 'InventarioCajasController@editar_caja');
Route::post('inventario_cajas/eliminar_detalle', 'InventarioCajasController@eliminar_detalle');
Route::get('inventario_cajas/cambiar_caja', 'InventarioCajasController@cambiar_caja');
Route::post('inventario_cajas/store_cambiar_caja', 'InventarioCajasController@store_cambiar_caja');
Route::get('inventario_cajas/add_detalle', 'InventarioCajasController@add_detalle');
Route::get('inventario_cajas/escanear_codigo', 'InventarioCajasController@escanear_codigo');
Route::post('inventario_cajas/update_caja', 'InventarioCajasController@update_caja');
