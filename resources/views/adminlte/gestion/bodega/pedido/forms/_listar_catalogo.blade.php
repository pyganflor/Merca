<div style="padding-left: 15px; padding-right: 15px">
    <div class="row">
        @foreach ($listado as $item)
            @php
                $url_imagen = 'images\productos\*' . $item->imagen;
                $url_imagen = str_replace('*', '', $url_imagen);
            @endphp
            <div class="col-md-2 mouse-hand text-center padding_lateral_5" style="position: relative; margin-top: 10px"
                onmouseover="$('.span_descriptivo_{{ $item->id_producto }}').removeClass('hidden')"
                onmouseleave="$('.span_descriptivo_{{ $item->id_producto }}').addClass('hidden')">
                <span class="span_descriptivo_{{ $item->id_producto }} span_descriptivo sombra_pequeña hidden"
                    style="position: absolute; top: 50%; background-image: linear-gradient(to right, #00B388 ,#7effdf8a)">
                    <b>{{ $item->nombre }}</b>
                </span>
                <img src="{{ url($url_imagen) }}" alt="..." class="img-fluid img-thumbnail"
                    style="border-radius: 16px; width: 100%; height: auto;"
                    onclick="$('.imagen_{{ $item->id_producto }}').toggleClass('hidden')">
                <span class="span_nombre_producto_{{ $item->id_producto }}">
                    <b>{{ $item->nombre }}</b>
                </span>
            </div>
        @endforeach
    </div>
</div>

<style>
    .span_descriptivo {
        position: absolute;
        top: 0;
        padding: 5px;
        color: #242424;
        font-size: 0.9em;
        font-weight: bold;
    }
</style>
