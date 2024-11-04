@extends('layouts.app')
@section('title')
Graficas Por Cliente
@endsection
@section('content')
    @include('partials.navbar')
    <div class="container mt-5">
    <div class="row">
  <div class="col-md-2 col-sm-4 col-12">
    <div class="card mb-3" style="height: 100%;">
      <div class="card-body d-flex align-items-center">
        <div class="bg-info text-white rounded p-2 me-3" style="width: 40px; height: 40px;">
          <i class="fas fa-sort-amount-up fa-lg"></i>
        </div>
        <div class="flex-grow-1 text-end">
          <h6 class="card-title mb-1" style="font-size: 14px;">Mes de Alta</h6>
          <p class="card-text mb-0" style="font-size: 12px;" id="mes-alta"></p> <!-- ID agregado -->
        </div>
      </div>
    </div>
  </div>

  <div class="col-md-2 col-sm-4 col-12">
    <div class="card mb-3" style="height: 100%;">
      <div class="card-body d-flex align-items-center">
        <div class="bg-danger text-white rounded p-2 me-3" style="width: 40px; height: 40px;">
          <i class="fas fa-sort-amount-down fa-lg"></i>
        </div>
        <div class="flex-grow-1 text-end">
          <h6 class="card-title mb-1" style="font-size: 14px;">Mes de Baja</h6>
          <p class="card-text mb-0" style="font-size: 12px;" id="mes-baja"></p> <!-- ID agregado -->
        </div>
      </div>
    </div>
  </div>

  <div class="col-md-2 col-sm-4 col-12">
    <div class="card mb-3" style="height: 100%;">
      <div class="card-body d-flex align-items-center">
        <div class="bg-secondary text-white rounded p-2 me-3" style="width: 40px; height: 40px;">
          <i class="fas fa-money-bill fa-lg"></i>
        </div>
        <div class="flex-grow-1 text-end">
          <h6 class="card-title mb-1" style="font-size: 14px;">Importe Total</h6>
          <p class="card-text mb-0" style="font-size: 12px;" id="importe-total"></p> <!-- ID agregado -->
        </div>
      </div>
    </div>
  </div>

  <div class="col-md-2 col-sm-4 col-12">
  <div class="card mb-3" style="height: 100%;">
    <div class="card-body d-flex align-items-center">
      <div class="bg-warning text-white rounded p-2 me-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
        <i class="fas fa-coins" style="font-size: 16px;"></i>
        <i class="fas fa-long-arrow-alt-down" style="font-size: 16px; margin-left: 2px;"></i>
      </div>
      <div class="flex-grow-1 text-end">
        <h6 class="card-title mb-1" style="font-size: 14px;">Costo Total</h6>
        <p class="card-text mb-0" style="font-size: 12px;" id="costo-total"></p> <!-- ID agregado -->
      </div>
    </div>
  </div>
</div>


<div class="col-md-2 col-sm-4 col-12">
  <div class="card mb-3" style="height: 100%;">
    <div class="card-body d-flex align-items-center">
      <div class="bg-success text-white rounded p-2 me-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
        <i class="fas fa-coins" style="font-size: 16px;"></i>
        <i class="fas fa-long-arrow-alt-up" style="font-size: 16px; margin-left: 2px;"></i>
      </div>
      <div class="flex-grow-1 text-end">
        <h6 class="card-title mb-1" style="font-size: 14px;">Utilidad Total</h6>
        <p class="card-text mb-0" style="font-size: 12px;" id="utilidad-total"></p> <!-- ID agregado -->
      </div>
    </div>
  </div>
</div>


  <div class="col-md-2 col-sm-4 col-12">
    <div class="card mb-3" style="height: 100%;">
      <div class="card-body d-flex align-items-center">
        <div class="bg-primary text-white rounded p-2 me-3" style="width: 40px; height: 40px;">
          <i class="far fa-percentage fa-lg"></i>
        </div>
        <div class="flex-grow-1 text-end">
          <h6 class="card-title mb-1" style="font-size: 14px;">Margen de Contrib.</h6>
          <p class="card-text mb-0" style="font-size: 12px;" id="margen-contribucion"></p> <!-- ID agregado -->
        </div>
      </div>
    </div>
  </div>
</div><br>


        
        <div class="content-area">
            
            <h2 class="text-center">Graficas por Cliente</h2>
            <div class="card mt-4">
                <div class="card-body">
                <div class="container">

                    <!-- Filtros -->
                    <form id="filterForm">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="year">Año:</label>
                                <select id="year" name="year" class="form-control select2">
                                    <option value="">Año Actual</option>
                                    @foreach($years as $year)
                                        <option value="{{ $year }}">{{ $year }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="month">Mes:</label>
                                <select id="month" name="month" class="form-control select2">
                                    <option value="">Todos</option>
                                    @for($m = 1; $m <= 12; $m++)
                                        <option value="{{ $m }}">{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="cliente">Cliente:</label>
                                <select id="cliente" name="cliente" class="form-control select2">
                                    <option value="">Todos</option>
                                    @foreach($groupedClientes as $nombreCorto => $clientes)
                                        @if(!empty($clientes)) <!-- Filtrar registros sin nombre -->
                                            <option value="{{ $nombreCorto }}">{{ $nombreCorto }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <button type="button" onclick="applyFilter()" class="btn btn-primary mt-3">Filtrar</button>
                    </form>

                    <!-- Gráficas -->
                    <div class="mt-5" id="ImporteChartContainer"></div>
                    <div id="ImporteDonaChartContainer"></div>
                    <div class="mt-5" id="CostoChartContainer"></div>
                    <div id="CostoDonaChartContainer"></div>
                    <div class="mt-5" id="UtilidadChartContainer"></div>
                    <div id="UtilidadDonaChartContainer"></div>
                    <div class="mt-5" id="RentabilidadChartContainer"></div>
                    <div id="RentabilidadDonaChartContainer"></div>
                </div>
                </div>
            </div>
        </div>
    </div>
@endsection


                
    @section('scripts')
<!-- Incluye CSS y JS de Select2 -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>

<script>
    $(document).ready(function() {
        // Inicializar Select2
        $('.select2').select2();
    });

    function applyFilter() {
        let form = document.getElementById('filterForm');
        let formData = new FormData(form);

        fetch('{{ route("dashboard.filter") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            // Procesar datos para las gráficas
            let meses = data.data.map(item => item.NombreMes); // Usar NombreMes (en español) como categoría
            let costoData = data.data.map(item => parseFloat(item.TotalCosto) || 0); // Asegurar que TotalCosto sea un número
            let importeData = data.data.map(item => parseFloat(item.TotalImporte) || 0); // Asegurar que TotalImporte sea un número
            let utilidadData = data.data.map(item => parseFloat(item.Utilidad) || 0); // Asegurar que Utilidad sea un número
            let rentabilidadData = data.data.map(item => parseFloat(item.Rentabilidad) || 0); // Asegurar que Rentabilidad sea un número
            
            // Asegúrate de que UtilidadTotalAnual y utilidadTotal estén definidos y sean números válidos
            let margencontrib = parseFloat(data.utilidadTotalAnual) || 0;
            let utilidadTotal = parseFloat(data.utilidadTotal) || 0;

            if (margencontrib === 0) {
                console.log("La utilidad total anual es 0, no se puede calcular el margen de contribución.");
            } else {
                var rescontrib = (utilidadTotal / margencontrib) * 100;
                console.log(`El margen de contribución es: ${rescontrib}%`);
            }
            
            // Actualiza el contenido de las tarjetas
            document.getElementById('mes-alta').innerText = data.mesAlta ? data.mesAlta.mes : 'N/A';
            document.getElementById('mes-baja').innerText = data.mesBaja ? data.mesBaja.mes : 'N/A'; // Asegúrate de que este campo exista en tu respuesta
            document.getElementById('importe-total').innerText = data.importeTotal.toLocaleString(); // Formatea el número
            document.getElementById('costo-total').innerText = data.costoTotal.toLocaleString(); // Formatea el número
            document.getElementById('utilidad-total').innerText = data.utilidadTotal.toLocaleString(); // Formatea el número
            document.getElementById('margen-contribucion').innerText = rescontrib.toLocaleString(); // Formatea el número

            // Actualizar gráfica de costo (line chart) con color rojo
            updateHighChart('CostoChartContainer', 'Costo por Mes', meses, costoData, false, 'line', '#ff0000'); // Color rojo

            // Actualizar gráfica de importe (line chart)
            updateHighChart('ImporteChartContainer', 'Importe por Mes', meses, importeData, false, 'line');

            // Actualizar gráfica de utilidad (column chart)
            updateHighChart('UtilidadChartContainer', 'Utilidad por Mes', meses, utilidadData);

            // Actualizar gráfica de rentabilidad (column chart)
            updateHighChart('RentabilidadChartContainer', 'Rentabilidad (%) por Mes', meses, rentabilidadData, true); // Indicar que es un porcentaje

            // Actualizar gráficas de dona para importe, costo, utilidad y rentabilidad
            updateDoughnutChart('UtilidadDonaChartContainer', 'Porcentaje de Utilidad por Mes', meses, utilidadData);
            updateDoughnutChart('ImporteDonaChartContainer', 'Porcentaje de Importe por Mes', meses, importeData);
            updateDoughnutChart('CostoDonaChartContainer', 'Porcentaje de Costo por Mes', meses, costoData);
            updateDoughnutChart('RentabilidadDonaChartContainer', 'Porcentaje de Rentabilidad por Mes', meses, rentabilidadData);

        })
        .catch(error => console.error('Error al aplicar filtro:', error));
    }

    function updateHighChart(containerId, title, categories, data, isPercentage = false, chartType = 'column', color = null) {
        Highcharts.chart(containerId, {
            chart: {
                type: chartType // Cambiar tipo de gráfico
            },
            title: {
                text: title
            },
            xAxis: {
                categories: categories,
                title: {
                    text: 'Meses'
                }
            },
            yAxis: {
                title: {
                    text: isPercentage ? 'Rentabilidad (%)' : 'Valores'
                },
                labels: {
                    formatter: function () {
                        return isPercentage ? Highcharts.numberFormat(this.value, 0) + '%' : Highcharts.numberFormat(this.value, 2, '.', ',') + ' MXN';
                    }
                }
            },
            series: [{
                name: title,
                data: data.map(value => parseFloat(value)), // Asegurarse de que los valores son numéricos
                color: color, // Establecer color para la serie
                tooltip: {
                    pointFormat: isPercentage ? '<b>{point.y:.0f}%</b>' : '<b>{point.y:,.2f} MXN</b>' // Formato del tooltip
                }
            }],
            plotOptions: {
                line: { // Opciones específicas para el gráfico de líneas
                    dataLabels: {
                        enabled: true
                    },
                    enableMouseTracking: true
                },
                column: {
                    colorByPoint: true
                }
            },
            colors: ['#7cb5ec', '#434348', '#90ed7d', '#f7a35c', '#8085e9', '#f15c80', '#e4d354', '#2b908f', '#f45b5b', '#91e8e1']
        });
    }

    // Función para crear gráfica de dona
function updateDoughnutChart(containerId, title, categories, data) {
    const total = data.reduce((acc, value) => acc + parseFloat(value), 0);

    const seriesData = data.map(value => (parseFloat(value) / total) * 100 || 0);

    Highcharts.chart(containerId, {
        chart: {
            type: 'pie'
        },
        title: {
            text: title
        },
        tooltip: {
            pointFormat: '<b>{point.name}: {point.y:.2f}%</b>'
        },
        plotOptions: {
            pie: {
                innerSize: '50%',
                dataLabels: {
                    enabled: true,
                    format: '{point.name}: {point.y:.2f}%'
                }
            }
        },
        series: [{
            name: title,
            data: categories.map((name, index) => ({
                name: name,
                y: seriesData[index],
                sliced: index === 0,
                selected: index === 0
            }))
        }]
    });
}

</script>

@endsection
