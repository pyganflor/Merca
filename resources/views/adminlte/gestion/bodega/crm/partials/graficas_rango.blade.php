<div class="nav-tabs-custom" style="cursor: move;">
    <!-- Tabs within a box -->
    <ul class="nav nav-pills nav-justified">
        <li class="active">
            <a href="#monto-chart" data-toggle="tab" aria-expanded="true">
                Ventas
            </a>
        </li>
        <li class="">
            <a href="#tallos-chart" data-toggle="tab" aria-expanded="false">
                Tallos
            </a>
        </li>
        <li class="">
            <a href="#ramos-chart" data-toggle="tab" aria-expanded="true">
                Ramos
            </a>
        </li>
    </ul>
    <div class="tab-content no-padding">
        <div class="chart tab-pane active" id="monto-chart" style="position: relative">
            <canvas id="chart_monto" width="100%" height="40" style="margin-top: 5px"></canvas>
        </div>
        <div class="chart tab-pane" id="tallos-chart" style="position: relative">
            <canvas id="chart_tallos" width="100%" height="40" style="margin-top: 5px"></canvas>
        </div>
        <div class="chart tab-pane" id="ramos-chart" style="position: relative">
            <canvas id="chart_ramos" width="100%" height="40" style="margin-top: 5px"></canvas>
        </div>
    </div>
</div>

<script>
    construir_char('Tallos', 'chart_tallos');
    construir_char('Ramos', 'chart_ramos');
    construir_char('Monto', 'chart_monto');

    function construir_char(label, id) {
        labels = [];
        datasets = [];
        data_list = [];
        data_tallos = [];
        @for ($i = 0; $i < count($labels); $i++)
            @if ($rango == 'S')
                labels.push("{{ $labels[$i]->codigo }}");
            @else
                labels.push("{{ substr($labels[$i], 0, 10) }}");
            @endif

            if (label == 'Tallos')
                data_list.push("{{ $data[$i]->tallos }}");
            if (label == 'Ramos')
                data_list.push("{{ $data[$i]->ramos }}");
            if (label == 'Monto')
                data_list.push("{{ round($data[$i]->monto, 2) }}");
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
