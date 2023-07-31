<?php

Route::get('ingreso_clasificacion', 'Postcosecha\IngresoClasificacionController@inicio');
Route::get('ingreso_clasificacion/add_verde', 'Postcosecha\VerdeController@add_verde');
Route::post('ingreso_clasificacion/store_monitoreo', 'Postcosecha\VerdeController@store_monitoreo');
Route::get('ingreso_clasificacion/listar_verde', 'Postcosecha\VerdeController@listar_verde');
Route::get('ingreso_clasificacion/listar_blanco', 'Postcosecha\BlancoController@listar_blanco');
Route::post('ingreso_clasificacion/store_blanco', 'Postcosecha\BlancoController@store_blanco');
Route::post('ingreso_clasificacion/buscar_inventario', 'Postcosecha\BlancoController@buscar_inventario');
Route::get('ingreso_clasificacion/inventario_frio', 'Postcosecha\BlancoController@inventario_frio');
Route::post('ingreso_clasificacion/update_inventario', 'Postcosecha\BlancoController@update_inventario');
Route::post('ingreso_clasificacion/botar_inventario', 'Postcosecha\BlancoController@botar_inventario');
Route::post('ingreso_clasificacion/buscar_modulos', 'Postcosecha\BlancoController@buscar_modulos');
Route::get('ingreso_clasificacion/ver_pdf_etiquetas', 'Postcosecha\BlancoController@ver_pdf_etiquetas');
Route::post('ingreso_clasificacion/store_all_blanco', 'Postcosecha\BlancoController@store_all_blanco');
Route::get('ingreso_clasificacion/ver_all_pdf_etiquetas', 'Postcosecha\BlancoController@ver_all_pdf_etiquetas');
Route::get('ingreso_clasificacion/view_pdf_inventario', 'Postcosecha\BlancoController@view_pdf_inventario');
