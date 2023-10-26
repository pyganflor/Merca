<?php

Route::get('descuentos_usuario', 'Bodega\DescuentosUsuarioController@inicio');
Route::get('descuentos_usuario/listar_reporte', 'Bodega\DescuentosUsuarioController@listar_reporte');
Route::post('descuentos_usuario/seleccionar_finca_filtro', 'Bodega\DescuentosUsuarioController@seleccionar_finca_filtro');
Route::get('descuentos_usuario/exportar_reporte', 'Bodega\DescuentosUsuarioController@exportar_reporte');
