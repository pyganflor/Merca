<?php

Route::get('ingreso_guias', 'Comercializacion\IngresoGuiasController@inicio');
Route::get('ingreso_guias/listar_reporte', 'Comercializacion\IngresoGuiasController@listar_reporte');
Route::post('ingreso_guias/store_guias', 'Comercializacion\IngresoGuiasController@store_guias');
