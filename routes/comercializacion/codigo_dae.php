<?php

Route::get('codigo_dae', 'Comercializacion\CodigosDaeController@inicio');
Route::get('codigo_dae/listar_reporte', 'Comercializacion\CodigosDaeController@listar_reporte');
Route::get('codigo_dae/exportar_paises', 'Comercializacion\CodigosDaeController@exportar_paises');
Route::get('codigo_dae/descargar_plantilla', 'Comercializacion\CodigosDaeController@descargar_plantilla');
Route::get('codigo_dae/importar_codigos_dae', 'Comercializacion\CodigosDaeController@importar_codigos_dae');
Route::post('codigo_dae/importar_file_codigos_dae', 'Comercializacion\CodigosDaeController@importar_file_codigos_dae');
Route::get('codigo_dae/get_importar_file_codigos_dae', 'Comercializacion\CodigosDaeController@get_importar_file_codigos_dae');
Route::post('codigo_dae/store_codigos_dae', 'Comercializacion\CodigosDaeController@store_codigos_dae');
Route::post('codigo_dae/cambiar_estado', 'Comercializacion\CodigosDaeController@cambiar_estado');
