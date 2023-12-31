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
Route::get('pedido_bodega/imprimir_pedidos_all', 'Bodega\PedidoBodegaController@imprimir_pedidos_all');
Route::get('pedido_bodega/imprimir_entregas_all', 'Bodega\PedidoBodegaController@imprimir_entregas_all');
Route::get('pedido_bodega/imprimir_entregas_peso_all', 'Bodega\PedidoBodegaController@imprimir_entregas_peso_all');
Route::get('pedido_bodega/get_armar_pedido', 'Bodega\PedidoBodegaController@get_armar_pedido');
Route::get('pedido_bodega/escanear_codigo_pedido', 'Bodega\PedidoBodegaController@escanear_codigo_pedido');
Route::post('pedido_bodega/store_armar_pedidos', 'Bodega\PedidoBodegaController@store_armar_pedidos');
Route::post('pedido_bodega/devolver_pedido', 'Bodega\PedidoBodegaController@devolver_pedido');
Route::get('pedido_bodega/modal_contabilidad', 'Bodega\PedidoBodegaController@modal_contabilidad');
Route::get('pedido_bodega/descargar_contabilidad', 'Bodega\PedidoBodegaController@descargar_contabilidad');
