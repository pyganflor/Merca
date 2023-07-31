<legend id="help_formulario">Formulario</legend>

<div class="row">
    <div class="col-md-8">
        <img src="{{url('images/helps/help_costos_importar/1.png')}}" alt="" style="width: 100%" class="sombra_pequeña">
    </div>
    <div class="col-md-4">
        <div style="overflow-y: scroll; height: 345px">
            <a data-toggle="collapse" data-parent="#accordion" href="#collapse1_1" aria-expanded="false"
               class="collapsed btn btn-yura_dark btn-block">
                1- Concepto
            </a>
            <div id="collapse1_1" class="panel-collapse collapse" aria-expanded="false"
                 style="height: 0px; border: 1px solid #9d9d9d; padding: 5px; border-radius: 18px; margin-top: 2px">
                <p>Seleccione en este campo el <strong>tipo de costos</strong> que desesa importar.</p>
                <p>Las opciones son:</p>
                <ul>
                    <li>Insumos</li>
                    <li>Mano de Obra</li>
                </ul>
            </div>

            <a data-toggle="collapse" data-parent="#accordion" href="#collapse1_2" aria-expanded="false"
               class="collapsed btn btn-yura_dark btn-block" style="margin-top: 5px">
                2- Archivo
            </a>
            <div id="collapse1_2" class="panel-collapse collapse" aria-expanded="true"
                 style="height: 0px; border: 1px solid #9d9d9d; padding: 5px; border-radius: 18px; margin-top: 2px">
                <p>Seleccione en este campo el <strong>archivo</strong> de tipo <i class="fa fa-fw fa-file-excel-o"></i><strong>.xlsx</strong> o
                    <strong>.csv</strong> que desea
                    importar.</p>
            </div>

            <a data-toggle="collapse" data-parent="#accordion" href="#collapse1_3" aria-expanded="false"
               class="collapsed btn btn-yura_dark btn-block" style="margin-top: 5px">
                3- Criterio
            </a>
            <div id="collapse1_3" class="panel-collapse collapse" aria-expanded="true"
                 style="height: 0px; border: 1px solid #9d9d9d; padding: 5px; border-radius: 18px; margin-top: 2px">
                <p>Seleccione en este campo el <strong>valor</strong> que desea importar.</p>
                <p>Las opciones son:</p>
                <ul>
                    <li>Dinero</li>
                    <li>Cantidad (personas o productos)</li>
                </ul>
            </div>

            <a data-toggle="collapse" data-parent="#accordion" href="#collapse1_4" aria-expanded="false"
               class="collapsed btn btn-yura_dark btn-block" style="margin-top: 5px">
                4- Acción
            </a>
            <div id="collapse1_4" class="panel-collapse collapse" aria-expanded="true"
                 style="height: 0px; border: 1px solid #9d9d9d; padding: 5px; border-radius: 18px; margin-top: 2px">
                <p>Seleccione en este campo la <strong>acción</strong> que desea realizar al importar el archivo.</p>
                <p>Las opciones son:</p>
                <ul>
                    <li>Sobreescribir</li>
                    <li>Sumar a lo anterior</li>
                </ul>
            </div>

            <a data-toggle="collapse" data-parent="#accordion" href="#collapse1_5" aria-expanded="false"
               class="collapsed btn btn-yura_dark btn-block" style="margin-top: 5px">
                5- Descargar plantilla
            </a>
            <div id="collapse1_5" class="panel-collapse collapse" aria-expanded="true"
                 style="height: 0px; border: 1px solid #9d9d9d; padding: 5px; border-radius: 18px; margin-top: 2px">
                <p>Haga click en este botón para descargar un archivo tipo <i class="fa fa-fw fa-file-excel-o"></i><strong>.xlsx</strong> con
                    una plantilla de <strong>ejemplo</strong>.</p>
                <a href="#help_descargar_plantilla">
                    <small><em>Ver más información...</em></small>
                </a>
            </div>

            <a data-toggle="collapse" data-parent="#accordion" href="#collapse1_6" aria-expanded="false"
               class="collapsed btn btn-yura_dark btn-block" style="margin-top: 5px">
                6- Importar archivo
            </a>
            <div id="collapse1_6" class="panel-collapse collapse" aria-expanded="true"
                 style="height: 0px; border: 1px solid #9d9d9d; padding: 5px; border-radius: 18px; margin-top: 2px">
                <p>Haga click en este botón para <strong>importar</strong> el archivo finalmente.</p>
            </div>
        </div>
    </div>
</div>

<legend id="help_descargar_plantilla" style="margin-top: 10px">Plantillas</legend>

<div class="row">
    <div class="col-md-8">
        <img src="{{url('images/helps/help_costos_importar/2.png')}}" alt="" style="width: 100%" class="sombra_pequeña">
    </div>
    <div class="col-md-4">
        <div style="overflow-y: scroll; height: 345px">
            <h4>Insumos</h4>
            <p style="margin-top: 5px">
                Descargue esta plantilla selecionando el <a href="#help_formulario"><strong>criterio</strong></a> Insumos y luego click en el
                botón <a href="#help_formulario"><strong>Descargar plantilla</strong></a>.
            </p>
            <p>Descripción de columnas:</p>
            <ul>
                <li><strong>A - FINCA</strong>: Nombre de la finca</li>
                <li><strong>B - FECHA</strong>: Fecha del registro a importar</li>
                <li><strong>C - ACTIVIDAD</strong>: Nombre de la actividad a importar</li>
                <li><strong>D - INSUMOS</strong>: Nombre del insumo a importar</li>
                <li><strong>E - VALOR</strong>: Número correspondiente al costo del insumo</li>
                <li><strong>F - CANTIDAD</strong>: Número correspondiente a la cantidad de especie usada</li>
            </ul>
            <p>Importante:</p>
            <p>
                <em style="color: red">Los nombres <strong>(FINCA, ACTIVIDAD, INSUMOS)</strong> deben coincidir con los nombres ingresados
                    anteriormente en el sistema.
                    <br>
                    En caso de usar esta plantilla para importar el archivo, <strong>NO INCLUIR LA FILA DE EJEMPLO</strong> que contiene dicha
                    plantilla.
                    <br>
                    Guiarse en el formato de la fecha como se muestra en la plantilla.</em>
            </p>
        </div>
    </div>
</div>

<div class="row" style="margin-top: 10px">
    <div class="col-md-8">
        <img src="{{url('images/helps/help_costos_importar/3.png')}}" alt="" style="width: 100%" class="sombra_pequeña">
    </div>
    <div class="col-md-4">
        <div style="overflow-y: scroll; height: 345px">
            <h4>Mano de Obra</h4>
            <p style="margin-top: 5px">
                Descargue esta plantilla selecionando el <a href="#help_formulario"><strong>criterio</strong></a> Mano de Obra y luego click en
                el botón <a href="#help_formulario"><strong>Descargar plantilla</strong></a>.
            </p>
            <p>Descripción de columnas:</p>
            <ul>
                <li><strong>A - FINCA</strong>: Nombre de la finca</li>
                <li><strong>B - CEDULA</strong>: Cédula del empleado correspondiente al registro</li>
                <li><strong>C - NOMBRE</strong>: Nombre del empleado correspondiente al registro</li>
                <li><strong>D - AREA</strong>: Nombre del área a importar</li>
                <li><strong>E - ACTIVIDAD</strong>: Nombre de la actividad a importar</li>
                <li><strong>F - LABOR (MO)</strong>: Número de la labor de mano de obra a importar</li>
                <li><strong>G - SUELDO</strong>: Número correspondiente al sueldo mensual del empleado</li>
                <li><strong>H - FECHA INGRESO</strong>: Fecha de ingreso del empleado a la empresa</li>
                <li><strong>I - FECHA REINGRESO</strong>: Fecha del último reingreso del empleado a la empresa</li>
                <li><strong>J - FECHA INICIAL</strong>: Fecha inicial correspondiente al rango del registro</li>
                <li><strong>K - FECHA FINAL</strong>: Fecha final correspondiente al rango del registro</li>
                <li><strong>L - HORA NORMAL</strong>: Número correspondiente a la cantidad de horas normales trabajadas por el empleado</li>
                <li><strong>M - HORA 50</strong>: Número correspondiente a la cantidad de horas extras del 50% trabajadas por el empleado</li>
                <li><strong>N - HORA 100</strong>: Número correspondiente a la cantidad de horas extras del 100% trabajadas por el empleado</li>
                <li><strong>O - AUSENTISMO</strong>: Número correspondiente a la cantidad de horas ausentes del empleado</li>
            </ul>
            <p>Importante:</p>
            <p>
                <em style="color: red">Los nombres <strong>(FINCA, AREA, ACTIVIDAD, LABOR MO)</strong> deben coincidir con los nombres ingresados
                    anteriormente en el sistema.
                    <br>
                    En caso de usar esta plantilla para importar el archivo, <strong>NO INCLUIR LA FILA DE EJEMPLO</strong> que contiene dicha
                    plantilla.
                    <br>
                    Guiarse en los formatos de las fechas como se muestran en la plantilla.</em>
            </p>
        </div>
    </div>
</div>