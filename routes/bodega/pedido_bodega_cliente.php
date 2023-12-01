<?php

Route::get('pedido_bodega_cliente', 'Bodega\PedidoClienteController@inicio');
Route::post('pedido_bodega_cliente/store_pedido', 'Bodega\PedidoClienteController@store_pedido');