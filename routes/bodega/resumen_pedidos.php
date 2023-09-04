<?php

Route::get('resumen_pedidos', 'Bodega\ResumenPedidosController@inicio');
Route::get('resumen_pedidos/listar_reporte', 'Bodega\ResumenPedidosController@listar_reporte');
Route::get('resumen_pedidos/exportar_reporte', 'Bodega\ResumenPedidosController@exportar_reporte');
