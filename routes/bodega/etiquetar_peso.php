<?php

Route::get('etiquetar_peso', 'Bodega\EtiquetarPesoController@inicio');
Route::get('etiquetar_peso/listar_inventario', 'Bodega\EtiquetarPesoController@listar_inventario');
Route::get('etiquetar_peso/seleccionar_inventario', 'Bodega\EtiquetarPesoController@seleccionar_inventario');
Route::post('etiquetar_peso/store_etiqueta', 'Bodega\EtiquetarPesoController@store_etiqueta');
Route::get('etiquetar_peso/imprimir_etiqueta', 'Bodega\EtiquetarPesoController@imprimir_etiqueta');
Route::get('etiquetar_peso/ver_etiquetas', 'Bodega\EtiquetarPesoController@ver_etiquetas');
Route::post('etiquetar_peso/delete_etiqueta', 'Bodega\EtiquetarPesoController@delete_etiqueta');
