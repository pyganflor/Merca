<?php

    Route::get('precio','PrecioController@inicio');
    Route::get('precio/seleccionar_cliente','PrecioController@seleccionar_cliente');
    Route::post('precio/store_precio','PrecioController@store_precio');
    Route::post('precio/update_precio','PrecioController@update_precio');
    Route::post('precio/delete_precio','PrecioController@delete_precio');
