@extends('layouts.app')
@section('title')
Top Graficado
@endsection
@section('content')
    @include('partials.navbar')
    <div class="container mt-5">
        <div class="content-area">
            <h2 class="text-center">Graficas de Top Superior e Inferior</h2>
            <form id="filtroForm" method="GET" action="{{ route('topsupinf') }}">
    <div class="form-group">
        <label for="anio">Año:</label>
        <input type="number" id="anio" name="anio" class="form-control" value="{{ date('Y') }}">
    </div>
    <div class="form-group">
        <label for="mes">Mes:</label>
        <select id="mes" name="mes" class="form-control">
            <option value="">Todos los meses</option>
            @for ($i = 1; $i <= 12; $i++)
                <option value="{{ $i }}" {{ (request('mes') == $i) ? 'selected' : '' }}>
                    {{ DateTime::createFromFormat('!m', $i)->format('F') }}
                </option>
            @endfor
        </select>
    </div>
    <button type="submit" class="btn btn-primary">Filtrar</button>
</form>
            <div class="card mt-4">
                <div class="card-body">
                <div class="row">
            <div class="col-md-12 col-sm-12 col-12"><div class="card mt-5">
                <div class="card-header">
                    <h3>Top Clientes Utilidad</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                    <div class="col"><div id="TopClientesMayorUtilidadContainer" style="height: 400px;"></div></div>
                    <div class="col"><div id="TopClientesMenorUtilidadContainer" style="height: 400px;" class="mt-4"></div></div>
                    </div>  
                </div>
            </div>
            <div class="row">
            <div class="col-md-12 col-sm-12 col-12"><div class="card mt-5">
                <div class="card-header">
                    <h3>Top Clientes Importe</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                    <div class="col"><div id="TopClientesMayorImporteContainer" style="height: 400px;"></div></div>
                    <div class="col"><div id="TopClientesMenorImporteContainer" style="height: 400px;" class="mt-4"></div></div>
                    </div>  
                </div>
            </div>
            <div class="row">
            <div class="col-md-12 col-sm-12 col-12"><div class="card mt-5">
                <div class="card-header">
                    <h3>Top Clientes Costo</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                    <div class="col"><div id="TopClientesMayorCostoContainer" style="height: 400px;"></div></div>
                    <div class="col"><div id="TopClientesMenorCostoContainer" style="height: 400px;" class="mt-4"></div></div>
                    </div>  
                </div>
            </div>
            <div class="row">
            <div class="col-md-12 col-sm-12 col-12"><div class="card mt-5">
                <div class="card-header">
                    <h3>Top Clientes Rentabilidad</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                    <div class="col"><div id="TopClientesMayorRentabilidadContainer" style="height: 400px;"></div></div>
                    <div class="col"><div id="TopClientesMenorRentabilidadContainer" style="height: 400px;" class="mt-4"></div></div>
                    </div>  
                </div>
            </div>
        </div>
        </div>
        
        
        </div>
        
            
            
        </div>
        
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
    document.addEventListener('DOMContentLoaded', function () {
        // Datos para los Top Clientes con Mayor Utilidad
        const topClientesMayorUtilidad = @json($resultadosTopConOtros->map(function($cliente) {
            return ['name' => $cliente->Cliente, 'y' => (float) $cliente->Utilidad];
        }));

        // Gráfica de Top Clientes con Mayor Utilidad
        Highcharts.chart('TopClientesMayorUtilidadContainer', {
            chart: { type: 'pie' },
            title: { text: 'Top Clientes por Mayor Utilidad' },
            series: [{
                name: 'Utilidad',
                colorByPoint: true,
                data: topClientesMayorUtilidad
            }]
        });

        // Datos para los Top Clientes con Menor Utilidad
        const topClientesMenorUtilidad = @json($clientesMenorUtilidad->map(function($cliente) {
            return ['name' => $cliente->Cliente, 'y' => (float) $cliente->Utilidad];
        }));

        // Gráfica de Top Clientes con Menor Utilidad
        Highcharts.chart('TopClientesMenorUtilidadContainer', {
            chart: { type: 'pie' },
            title: { text: 'Top Clientes por Menor Utilidad' },
            series: [{
                name: 'Utilidad',
                colorByPoint: true,
                data: topClientesMenorUtilidad
            }]
        });

        // Datos para los Top Clientes con Mayor Importe
        const topClientesMayorImporte = @json($resultadosTopConOtrosImporte->map(function($cliente) {
            return ['name' => $cliente->Cliente, 'y' => (float) $cliente->Importe];
        }));

        // Gráfica de Top Clientes con Mayor Importe
        Highcharts.chart('TopClientesMayorImporteContainer', {
            chart: { type: 'pie' },
            title: { text: 'Top Clientes por Mayor Importe' },
            series: [{
                name: 'Importe',
                colorByPoint: true,
                data: topClientesMayorImporte
            }]
        });

        // Datos para los Top Clientes con Menor Importe
        const topClientesMenorImporte = @json($clientesMenorImporte->map(function($cliente) {
            return ['name' => $cliente->Cliente, 'y' => (float) $cliente->Importe];
        }));

        // Gráfica de Top Clientes con Menor Importe
        Highcharts.chart('TopClientesMenorImporteContainer', {
            chart: { type: 'pie' },
            title: { text: 'Top Clientes por Menor Importe' },
            series: [{
                name: 'Importe',
                colorByPoint: true,
                data: topClientesMenorImporte
            }]
        });

        // Datos para los Top Clientes con Mayor Costo
        const topClientesMayorCosto = @json($resultadosTopConOtrosCosto->map(function($cliente) {
            return ['name' => $cliente->Cliente, 'y' => (float) $cliente->Costo];
        }));

        // Gráfica de Top Clientes con Mayor Costo
        Highcharts.chart('TopClientesMayorCostoContainer', {
            chart: { type: 'pie' },
            title: { text: 'Top Clientes por Mayor Costo' },
            series: [{
                name: 'Costo',
                colorByPoint: true,
                data: topClientesMayorCosto
            }]
        });

        // Datos para los Top Clientes con Costo
        const topClientesMenorCosto = @json($clientesMenorCosto->map(function($cliente) {
            return ['name' => $cliente->Cliente, 'y' => (float) $cliente->Costo];
        }));

        // Gráfica de Top Clientes con Menor Costo
        Highcharts.chart('TopClientesMenorCostoContainer', {
            chart: { type: 'pie' },
            title: { text: 'Top Clientes por Menor Costo' },
            series: [{
                name: 'Costo',
                colorByPoint: true,
                data: topClientesMenorCosto
            }]
        });

        // Datos para los Top Clientes con Mayor Costo
        const topClientesMayorRentabilidad = @json($resultadosTopConOtrosRentabilidad->map(function($cliente) {
            return ['name' => $cliente->Cliente, 'y' => (float) $cliente->Rentabilidad];
        }));

        // Gráfica de Top Clientes con Mayor Costo
        Highcharts.chart('TopClientesMayorRentabilidadContainer', {
            chart: { type: 'pie' },
            title: { text: 'Top Clientes por Mayor Rentabilidad' },
            series: [{
                name: 'Rentabilidad',
                colorByPoint: true,
                data: topClientesMayorRentabilidad
            }]
        });

        // Datos para los Top Clientes con Costo
        const topClientesMenorRentabilidad = @json($clientesMenorRentabilidad->map(function($cliente) {
            return ['name' => $cliente->Cliente, 'y' => (float) $cliente->Rentabilidad];
        }));

        // Gráfica de Top Clientes con Menor Costo
        Highcharts.chart('TopClientesMenorRentabilidadContainer', {
            chart: { type: 'pie' },
            title: { text: 'Top Clientes por Menor Rentabilidad' },
            series: [{
                name: 'Rentabilidad',
                colorByPoint: true,
                data: topClientesMenorRentabilidad
            }]
        });

        
        
    });
</script>

@endsection
