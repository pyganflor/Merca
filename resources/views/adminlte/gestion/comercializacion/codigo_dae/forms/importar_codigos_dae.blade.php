<form id="form-importar_file_codigos_dae" action="{{ url('codigo_dae/importar_file_codigos_dae') }}" method="POST">
    {!! csrf_field() !!}
    <div class="input-group">
        <span class="input-group-addon bg-yura_dark span-input-group-yura-fixed">
            Archivo
        </span>
        <input type="file" id="file_codigos_dae" name="file_codigos_dae" required class="form-control input-group-addon"
            accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel, .csv">
        <span class="input-group-btn">
            <button type="button" class="btn btn-yura_dark" onclick="importar_file_codigos_dae()">
                <i class="fa fa-fw fa-check"></i>
            </button>
        </span>
    </div>
</form>

<div id="div_importar_file_codigos_dae" style="margin-top: 5px"></div>

<script>
    function importar_file_codigos_dae() {
        $.LoadingOverlay('show');
        formulario = $('#form-importar_file_codigos_dae');
        var formData = new FormData(formulario[0]);
        //hacemos la petición ajax
        $.ajax({
            url: formulario.attr('action'),
            type: 'POST',
            data: formData,
            dataType: 'json',
            //necesario para subir archivos via ajax
            cache: false,
            contentType: false,
            processData: false,

            success: function(retorno2) {
                if (retorno2.success) {
                    $.LoadingOverlay('hide');
                    alerta_accion(retorno2.mensaje, function() {
                        //cerrar_modals();
                        datos = {
                            extension: retorno2.extension
                        }
                        get_jquery('{{ url('codigo_dae/get_importar_file_codigos_dae') }}',
                            datos,
                            function(retorno) {
                                $('#div_importar_file_codigos_dae').html(retorno);
                            });
                    });
                } else {
                    alerta(retorno2.mensaje);
                    $.LoadingOverlay('hide');
                }
            },
            //si ha ocurrido un error
            error: function(retorno2) {
                console.log(retorno2);
                alerta(retorno2.responseText);
                alert('Hubo un problema en el envío de la información');
                $.LoadingOverlay('hide');
            }
        });
    }
</script>
