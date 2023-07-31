<?php

Route::get('pedidos', 'Comercializacion\PedidoController@inicio');
Route::get('pedidos/listar_reporte', 'Comercializacion\PedidoController@listar_reporte');
Route::get('pedidos/add_pedido', 'Comercializacion\PedidoController@add_pedido');
Route::get('pedidos/buscar_inventario', 'Comercializacion\PedidoController@buscar_inventario');
Route::post('pedidos/store_pedido', 'Comercializacion\PedidoController@store_pedido');
Route::post('pedidos/eliminar_pedido', 'Comercializacion\PedidoController@eliminar_pedido');
Route::post('pedidos/seleccionar_cliente', 'Comercializacion\PedidoController@seleccionar_cliente');
Route::get('pedidos/editar_pedido', 'Comercializacion\PedidoController@editar_pedido');
Route::get('pedidos/agregar_inventario', 'Comercializacion\PedidoController@agregar_inventario');
Route::post('pedidos/regresar_inventario', 'Comercializacion\PedidoController@regresar_inventario');
Route::post('pedidos/deshacer_pedido', 'Comercializacion\PedidoController@deshacer_pedido');
Route::post('pedidos/update_precio', 'Comercializacion\PedidoController@update_precio');
Route::get('pedidos/cambiar_caja', 'Comercializacion\PedidoController@cambiar_caja');
Route::post('pedidos/eliminar_detalle_pedido', 'Comercializacion\PedidoController@eliminar_detalle_pedido');
Route::get('pedidos/add_caja', 'Comercializacion\PedidoController@add_caja');
Route::post('pedidos/agregar_caja', 'Comercializacion\PedidoController@agregar_caja');
Route::post('pedidos/update_pedido', 'Comercializacion\PedidoController@update_pedido');
Route::get('pedidos/generar_packing', 'Comercializacion\PedidoController@generar_packing');
Route::get('pedidos/generar_factura', 'Comercializacion\PedidoController@generar_factura');
Route::get('pedidos/exportar_etiqueta', 'Comercializacion\PedidoController@exportar_etiqueta');
Route::get('pedidos/generar_prefactura', 'Comercializacion\PedidoController@generar_prefactura');
Route::get('pedidos/modal_exportar', 'Comercializacion\PedidoController@modal_exportar');
Route::get('pedidos/exportar_pedidos', 'Comercializacion\PedidoController@exportar_pedidos');
Route::get('pedidos/exportar_resumen_pedidos', 'Comercializacion\PedidoController@exportar_resumen_pedidos');
