<?php

Route::get('recepcion', 'RecepcionController@inicio');
Route::get('recepcion/buscar_listado_recepcion', 'RecepcionController@buscar_listado_recepcion');
Route::get('recepcion/add_recepcion', 'RecepcionController@add_recepcion');
Route::get('recepcion/select_variedad_recepcion', 'RecepcionController@select_variedad_recepcion');
Route::post('recepcion/store_recepcion', 'RecepcionController@store_recepcion');
Route::post('recepcion/update_desglose', 'RecepcionController@update_desglose');
Route::post('recepcion/delete_desglose', 'RecepcionController@delete_desglose');
