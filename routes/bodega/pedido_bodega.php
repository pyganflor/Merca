<?php

Route::get('pedido_bodega', 'Bodega\PedidoBodegaController@inicio');
Route::get('pedido_bodega/listar_reporte', 'Bodega\PedidoBodegaController@listar_reporte');
Route::get('pedido_bodega/add_pedido', 'Bodega\PedidoBodegaController@add_pedido');
Route::get('pedido_bodega/listar_catalogo', 'Bodega\PedidoBodegaController@listar_catalogo');
Route::post('pedido_bodega/seleccionar_finca', 'Bodega\PedidoBodegaController@seleccionar_finca');
Route::post('pedido_bodega/store_pedido', 'Bodega\PedidoBodegaController@store_pedido');
Route::post('pedido_bodega/delete_pedido', 'Bodega\PedidoBodegaController@delete_pedido');
Route::get('pedido_bodega/ver_pedido', 'Bodega\PedidoBodegaController@ver_pedido');
Route::post('pedido_bodega/update_pedido', 'Bodega\PedidoBodegaController@update_pedido');
Route::post('pedido_bodega/armar_pedido', 'Bodega\PedidoBodegaController@armar_pedido');
Route::post('pedido_bodega/seleccionar_finca_filtro', 'Bodega\PedidoBodegaController@seleccionar_finca_filtro');
Route::get('pedido_bodega/exportar_resumen_pedidos', 'Bodega\PedidoBodegaController@exportar_resumen_pedidos');
Route::get('pedido_bodega/imprimir_pedido', 'Bodega\PedidoBodegaController@imprimir_pedido');
