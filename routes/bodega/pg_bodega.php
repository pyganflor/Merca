<?php

Route::get('pg_bodega', 'Bodega\PyGController@inicio');
Route::get('pg_bodega/listar_reporte', 'Bodega\PyGController@listar_reporte');
Route::get('pg_bodega/exportar_reporte', 'Bodega\PyGController@exportar_reporte');
