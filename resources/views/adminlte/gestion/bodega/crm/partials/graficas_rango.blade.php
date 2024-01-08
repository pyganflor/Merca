<div class="nav-tabs-custom" style="cursor: move;">
    <!-- Tabs within a box -->
    <ul class="nav nav-pills nav-justified">
        <li class="active">
            <a href="#venta-chart" data-toggle="tab" aria-expanded="true">
                Venta
            </a>
        </li>
        <li class="">
            <a href="#costo-chart" data-toggle="tab" aria-expanded="false">
                Costo
            </a>
        </li>
        <li class="">
            <a href="#margen-chart" data-toggle="tab" aria-expanded="true">
                Margen
            </a>
        </li>
        <li class="">
            <a href="#porcentaje_margen-chart" data-toggle="tab" aria-expanded="true">
                % Margen
            </a>
        </li>
    </ul>
    <div class="tab-content no-padding">
        <div class="chart tab-pane active" id="venta-chart" style="position: relative">
            <canvas id="chart_venta" width="100%" height="40" style="margin-top: 5px"></canvas>
        </div>
        <div class="chart tab-pane" id="costo-chart" style="position: relative">
            <canvas id="chart_costo" width="100%" height="40" style="margin-top: 5px"></canvas>
        </div>
        <div class="chart tab-pane" id="margen-chart" style="position: relative">
            <canvas id="chart_margen" width="100%" height="40" style="margin-top: 5px"></canvas>
        </div>
        <div class="chart tab-pane" id="porcentaje_margen-chart" style="position: relative">
            <canvas id="chart_porcentaje_margen" width="100%" height="40" style="margin-top: 5px"></canvas>
        </div>
    </div>
</div>

<script>
    construir_char('Costo', 'chart_costo');
    construir_char('Margen', 'chart_margen');
    construir_char('Venta', 'chart_venta');
    construir_char('% Margen', 'chart_porcentaje_margen');

    function construir_char(label, id) {
        labels = [];
        datasets = [];
        data_list = [];
        data_tallos = [];
        @for ($i = 0; $i < count($labels); $i++)
            @if ($rango == 'D')
                labels.push("{{ $labels[$i]->entrega }}");
            @else
                labels.push("{{ substr($labels[$i], 0, 10) }}");
            @endif

            if (label == 'Venta')
                data_list.push("{{ $data[$i]['venta'] }}");
            if (label == 'Costo')
                data_list.push("{{ $data[$i]['costo'] }}");
            if (label == 'Margen')
                data_list.push("{{ $data[$i]['margen'] }}");
            if (label == '% Margen')
                data_list.push("{{ $data[$i]['porcentaje_margen'] }}");
        @endfor

        datasets = [{
            label: label + ' ',
            data: data_list,
            //backgroundColor: '#8c99ff54',
            borderColor: 'black',
            borderWidth: 1,
            fill: {{ $fill_grafica }},
        }];

        ctx = document.getElementById(id).getContext('2d');
        myChart = new Chart(ctx, {
            type: '{{ $tipo_grafica }}',
            data: {
                labels: labels,
                datasets: datasets
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: false
                        }
                    }]
                },
                elements: {
                    line: {
                        tension: 0.2, // disables bezier curves
                    }
                },
                tooltips: {
                    mode: 'point' // nearest, point, index, dataset, x, y
                },
                legend: {
                    display: true,
                    position: 'bottom',
                    fullWidth: false,
                    onClick: function() {},
                    onHover: function() {},
                    reverse: true,
                },
                showLines: true, // for all datasets
                borderCapStyle: 'round', // "butt" || "round" || "square"
            }
        });
    }
</script>
