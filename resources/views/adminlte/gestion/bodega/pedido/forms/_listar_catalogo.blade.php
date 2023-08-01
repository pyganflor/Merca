<div class="container">
    <div class="row justify-content-center">
        @foreach ($listado as $item)
            @php
                $url_imagen = 'images\productos\*' . $item->imagen;
                $url_imagen = str_replace('*', '', $url_imagen);
            @endphp
            <div class="col-md-2 mouse-hand text-center" style="position: relative">
                <span style="position: absolute; top: 50%; left: 50%">
                    <b>{{ $item->nombre }}</b>
                </span>
                <img src="{{ url($url_imagen) }}" alt="..."
                    class="img-fluid img-thumbnail" style="border-radius: 16px"
                    onclick="$('.imagen_{{ $item->id_producto }}').toggleClass('hidden')">
            </div>
        @endforeach
    </div>
</div>
