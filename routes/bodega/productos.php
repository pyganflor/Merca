<?php

Route::get('bodega_productos', 'Bodega\ProductosController@inicio');
Route::get('bodega_productos/listar_reporte', 'Bodega\ProductosController@listar_reporte');
Route::post('bodega_productos/update_producto', 'Bodega\ProductosController@update_producto');
Route::post('bodega_productos/update_combo', 'Bodega\ProductosController@update_combo');
Route::post('bodega_productos/cambiar_estado_producto', 'Bodega\ProductosController@cambiar_estado_producto');
Route::post('bodega_productos/store_producto', 'Bodega\ProductosController@store_producto');
Route::post('bodega_productos/store_combo', 'Bodega\ProductosController@store_combo');
Route::post('bodega_productos/store_categoria', 'Bodega\ProductosController@store_categoria');
Route::get('bodega_productos/admin_combo', 'Bodega\ProductosController@admin_combo');
Route::get('bodega_productos/buscar_productos', 'Bodega\ProductosController@buscar_productos');
Route::post('bodega_productos/store_agregar_productos', 'Bodega\ProductosController@store_agregar_productos');
