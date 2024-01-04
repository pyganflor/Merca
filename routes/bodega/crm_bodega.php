<?php

Route::get('crm_bodega', 'Bodega\crmBodegaController@inicio');
Route::get('crm_bodega/listar_graficas', 'Bodega\crmBodegaController@listar_graficas');
Route::get('crm_bodega/listar_ranking', 'Bodega\crmBodegaController@listar_ranking');
