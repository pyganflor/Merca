<?php

Route::get('ranking_productos', 'Bodega\RankingProductosController@inicio');
Route::get('ranking_productos/listar_reporte', 'Bodega\RankingProductosController@listar_reporte');
Route::get('ranking_productos/exportar_reporte', 'Bodega\RankingProductosController@exportar_reporte');
