@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Dashboard de Rentabilidad y Utilidad</h1>

    <!-- Filtros -->
    <form id="filterForm">
        <div class="row">
            <div class="col-md-4">
                <label for="year">Año:</label>
                <select id="year" name="year" class="form-control select2">
                    <option value="">-</option>
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
                    @foreach($clientes as $cliente)
                        @if(!empty($cliente->NombreCorto)) <!-- Filtrar registros sin nombre -->
                            <option value="{{ $cliente->Cliente }}">{{ $cliente->NombreCorto }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
        </div>
        <button type="button" onclick="applyFilter()" class="btn btn-primary mt-3">Filtrar</button>
    </form>

    <!-- Gráficas -->
    <div class="mt-5" id="ImporteChartContainer"></div>
    <div class="mt-5" id="CostoChartContainer"></div>
    <div class="mt-5" id="UtilidadChartContainer"></div>
    <div class="mt-5" id="RentabilidadChartContainer"></div>
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

            // Actualizar gráfica de costo
            updateHighChart('CostoChartContainer', 'Costo por Mes', meses, costoData);

            // Actualizar gráfica de importe
            updateHighChart('ImporteChartContainer', 'Importe por Mes', meses, importeData);

            // Actualizar gráfica de utilidad
            updateHighChart('UtilidadChartContainer', 'Utilidad por Mes', meses, utilidadData);

            // Actualizar gráfica de rentabilidad
            updateHighChart('RentabilidadChartContainer', 'Rentabilidad (%) por Mes', meses, rentabilidadData, true); // Indicar que es un porcentaje
        })
        .catch(error => console.error('Error al aplicar filtro:', error));
    }

    function updateHighChart(containerId, title, categories, data, isPercentage = false) {
        Highcharts.chart(containerId, {
            chart: {
                type: 'column'
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
                tooltip: {
                    pointFormat: isPercentage ? '<b>{point.y:.0f}%</b>' : '<b>{point.y:,.2f} MXN</b>' // Formato del tooltip
                }
            }],
            plotOptions: {
                column: {
                    colorByPoint: true
                }
            },
            colors: ['#7cb5ec', '#434348', '#90ed7d', '#f7a35c', '#8085e9', '#f15c80', '#e4d354', '#2b908f', '#f45b5b', '#91e8e1']
        });
    }
</script>
@endsection
