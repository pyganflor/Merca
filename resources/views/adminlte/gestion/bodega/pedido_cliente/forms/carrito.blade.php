<div class="box-group" id="accordion_carritoxxx">
    <div class="panel box box-success" style="margin-bottom: 0">
        <div class="box-header with-border">
            <h4 class="box-title">
                <a data-toggle="collapse" data-parent="#accordion_carrito" href="#collapse_contenido_pedido"
                    aria-expanded="false" class="collapsed text-color_yura">
                    Contenido del Pedido
                </a>
            </h4>
        </div>
        <div id="collapse_contenido_pedido" class="panel-collapse collapse" aria-expanded="false"
            style="height: 0px; overflow-x: scroll">
            <table id="table_contenido_pedido">
                <tr></tr>
            </table>
        </div>
    </div>
    <div class="panel box box-success" style="margin-bottom: 0">
        <div class="box-header with-border">
            <h4 class="box-title">
                <a data-toggle="collapse" data-parent="#accordion_carrito" href="#collapse_forma_pago"
                    class="collapsed text-color_yura" aria-expanded="false">
                    Forma de pago: <b id="span_forma_pago"></b>
                </a>
            </h4>
            <div class="box-tools pull-right">
                <div class="checkbox" style="margin-top: 0">
                    <label class="btn btn-box-tool">
                        <input type="checkbox" id="check_mes_actual" checked disabled> Pagar a partir del mes actual
                    </label>
                </div>
            </div>
        </div>
        <div id="collapse_forma_pago" class="panel-collapse collapse" aria-expanded="false">
            <div class="box-body">
                <div class="row">
                    <div class="col-md-3 mouse-hand div_forma_pago" style="border-radius: 16px"
                        onmouseover="$(this).addClass('sombra_pequeña')"
                        onmouseleave="$(this).removeClass('sombra_pequeña')"
                        onclick="seleccionar_forma_pago('al_contado')" id="div_al_contado" data-nombre="Al Contado"
                        data-diferido="-1">
                        <span class="info-box-icon sombra_pequeña bg-yura_dark" style="border-radius: 50%">
                            <span class="info-box-text">Al Contado</span>
                        </span>

                        <div class="info-box-content">
                            <small>Subtotal: <b class="pull-right" id="html_subtotal_al_contado">$0</b></small>
                            <br>
                            <small>Total IVA: <b class="pull-right" id="html_iva_al_contado">$0</b></small>
                            <br>
                            <small>Monto Total: <b class="pull-right" id="html_monto_al_contado">$0</b></small>
                            <br>
                            <small>Diferido Total: <b class="pull-right" id="html_diferido_al_contado">$0</b></small>
                            <br>
                            <small>Restar saldo: <b class="pull-right" id="html_saldo_al_contado">$0</b></small>
                        </div>
                    </div>
                    <div class="col-md-3 mouse-hand div_forma_pago" style="border-radius: 16px"
                        onmouseover="$(this).addClass('sombra_pequeña')"
                        onmouseleave="$(this).removeClass('sombra_pequeña')"
                        onclick="seleccionar_forma_pago('una_cuota')" id="div_una_cuota" data-nombre="Una Cuota"
                        data-diferido="0">
                        <span class="info-box-icon sombra_pequeña bg-yura_primary" style="border-radius: 50%">
                            <span class="info-box-text">Una Cuota</span>
                        </span>

                        <div class="info-box-content">
                            <small>Subtotal: <b class="pull-right" id="html_subtotal_una_cuota">$0</b></small>
                            <br>
                            <small>Total IVA: <b class="pull-right" id="html_iva_una_cuota">$0</b></small>
                            <br>
                            <small>Monto Total: <b class="pull-right" id="html_monto_una_cuota">$0</b></small>
                            <br>
                            <small>Diferido Total: <b class="pull-right" id="html_diferido_una_cuota">$0</b></small>
                            <br>
                            <small>Restar saldo: <b class="pull-right" id="html_saldo_una_cuota">$0</b></small>
                        </div>
                    </div>
                    <div class="col-md-3 mouse-hand div_forma_pago" style="border-radius: 16px"
                        onmouseover="$(this).addClass('sombra_pequeña')"
                        onmouseleave="$(this).removeClass('sombra_pequeña')" onclick="seleccionar_forma_pago('2_meses')"
                        id="div_2_meses" data-nombre="Diferido a 2 Meses" data-diferido="2">
                        <span class="info-box-icon sombra_pequeña bg-yura_warning" style="border-radius: 50%">
                            <span class="info-box-text">2 Meses</span>
                        </span>

                        <div class="info-box-content">
                            <small>Subtotal: <b class="pull-right" id="html_subtotal_2_meses">$0</b></small>
                            <br>
                            <small>Total IVA: <b class="pull-right" id="html_iva_2_meses">$0</b></small>
                            <br>
                            <small>Monto Total: <b class="pull-right" id="html_monto_2_meses">$0</b></small>
                            <br>
                            <small>Diferido Total: <b class="pull-right" id="html_diferido_2_meses">$0</b></small>
                            <br>
                            <small>Restar saldo: <b class="pull-right" id="html_saldo_2_meses">$0</b></small>
                        </div>
                    </div>
                    <div class="col-md-3 mouse-hand div_forma_pago" style="border-radius: 16px"
                        onmouseover="$(this).addClass('sombra_pequeña')"
                        onmouseleave="$(this).removeClass('sombra_pequeña')"
                        onclick="seleccionar_forma_pago('3_meses')" id="div_3_meses" data-nombre="Diferido a 3 Meses"
                        data-diferido="3">
                        <span class="info-box-icon sombra_pequeña bg-yura_danger" style="border-radius: 50%">
                            <span class="info-box-text">3 Meses</span>
                        </span>

                        <div class="info-box-content">
                            <small>Subtotal: <b class="pull-right" id="html_subtotal_3_meses">$0</b></small>
                            <br>
                            <small>Total IVA: <b class="pull-right" id="html_iva_3_meses">$0</b></small>
                            <br>
                            <small>Monto Total: <b class="pull-right" id="html_monto_3_meses">$0</b></small>
                            <br>
                            <small>Diferido Total: <b class="pull-right" id="html_diferido_3_meses">$0</b></small>
                            <br>
                            <small>Restar saldo: <b class="pull-right" id="html_saldo_3_meses">$0</b></small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="text-center" style="margin-top: 5px">
        <button type="button" class="btn btn-yura_primary hidden" id="btn_grabar_pedido" onclick="store_pedido()">
            <i class="fa fa-fw fa-save"></i> FINALIZAR PEDIDO
        </button>
    </div>
</div>
<input type="hidden" id="input_forma_pago" value="">
<input type="hidden" id="input_empresa" value="{{ $finca->id_configuracion_empresa }}">
<input type="hidden" id="input_usuario" value="{{ $usuario->id_usuario }}">
<input type="hidden" id="input_saldo" value="{{ $usuario->saldo }}">
