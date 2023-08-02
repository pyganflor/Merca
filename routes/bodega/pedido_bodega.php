<?php

Route::get('pedido_bodega', 'Bodega\PedidoBodegaController@inicio');
Route::get('pedido_bodega/listar_reporte', 'Bodega\PedidoBodegaController@listar_reporte');
Route::get('pedido_bodega/add_pedido', 'Bodega\PedidoBodegaController@add_pedido');
Route::get('pedido_bodega/listar_catalogo', 'Bodega\PedidoBodegaController@listar_catalogo');
Route::post('pedido_bodega/seleccionar_finca', 'Bodega\PedidoBodegaController@seleccionar_finca');
