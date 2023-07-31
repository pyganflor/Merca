<?php

Route::get('crm_ventas','CRM\crmVentasController@inicio');
Route::get('crm_ventas/filtrar_graficas','CRM\crmVentasController@filtrar_graficas');
Route::get('crm_ventas/desglose_indicador','CRM\crmVentasController@desglose_indicador');
Route::get('crm_ventas/listar_graficas','CRM\crmVentasController@listar_graficas');
Route::get('crm_ventas/listar_ranking','CRM\crmVentasController@listar_ranking');