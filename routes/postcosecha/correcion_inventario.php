<?php

Route::get('correcion_inventario', 'CorrecionInventarioController@inicio');
Route::get('correcion_inventario/escanear_codigo', 'CorrecionInventarioController@escanear_codigo');
Route::post('correcion_inventario/corregir_all_inventario', 'CorrecionInventarioController@corregir_all_inventario');
Route::post('correcion_inventario/corregir_inventario_selected', 'CorrecionInventarioController@corregir_inventario_selected');
