@extends('layouts.app')

@section('content')
    @include('partials.navbar')
    <div class="container">
        <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-3">
                    <label for="agente">Agente</label>
                    <select id="agente" name="agente" class="form-control select2">
                        <option value="">Todos los agentes</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="anio">Año</label>
                    <select id="anio" name="anio" class="form-control select2">
                        <option value="">Año actual</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="mes">Mes</label>
                    <select id="mes" name="mes" class="form-control select2">
                        <option value="">Todos los meses</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="cliente">Cliente</label>
                    <select id="cliente" name="cliente" class="form-control select2">
                        <option value="">Todos los clientes</option>
                    </select>
                </div>
            </div>
        </div>

            <div class="card-body">
            <table id="cuadernillo-table" class="table table-striped">
        <thead>
            <tr>
                <th>Agente</th>
                <th>Cliente</th>
                <th>Importe</th>
                <th>Costo</th>
                <th>Utilidad</th>
                <th>Rentabilidad (%)</th>
            </tr>
        </thead>
        <tfoot>
        <tr>
            <th colspan="2" style="text-align:right">Totales:</th>
            <th id="totalImporte"></th>
            <th id="totalCosto"></th>
            <th id="totalUtilidad"></th>
            <th id="totalRentabilidad"></th>
        </tr>
    </tfoot>
    </table>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-6"><!-- Contenedores para las gráficas -->
                        <center><h3>Utilidad</h3></center>
                    <canvas id="utilidadChart" ></canvas></div>
                    <div class="col-6"><center><h3>Rentabilidad</h3></center><canvas id="rentabilidadChart" ></canvas></div>
                </div>
            </div>
        </div>
    
</div>
@endsection
@section('scripts')
<!-- CSS de Select2 -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<!-- Incluir Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- JavaScript de Select2 -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    // Inicializar Select2 en los selectores
    $('#agente, #anio, #mes, #cliente').select2({
        allowClear: true
    });

    // Llamada AJAX para obtener los datos de los selects
    $.ajax({
        url: '/obtener-opciones-select',
        method: 'GET',
        success: function(response) {
            // Llenar los selects
            $.each(response.agentes, function(index, agente) {
                $('#agente').append(new Option(agente.Nombre, agente.Agente));
            });
            $.each(response.anios, function(index, anio) {
                $('#anio').append(new Option(anio.anio, anio.anio));
            });
            $.each(response.meses, function(index, mes) {
                $('#mes').append(new Option(mes.nombre, mes.id));
            });
            $.each(response.clientes, function(index, cliente) {
                $('#cliente').append(new Option(cliente.NombreCorto, cliente.Cliente));
            });
        },
        error: function(xhr) {
            console.log("Error al obtener datos de selects: " + xhr.responseText);
        }
    });

    var table = $('#cuadernillo-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("cuadernillo.data") }}',
            data: function(d) {
                d.agente = $('#agente').val();
                d.anio = $('#anio').val();
                d.mes = $('#mes').val();
                d.cliente = $('#cliente').val();
            }
        },
        columns: [
            { data: 'Nombre', name: 'Agente.Nombre', title: 'Nombre Agente' },
            { data: 'NombreCliente', name: 'Cte.NombreCorto', title: 'Nombre Cliente' },
            { data: 'Importe', name: 'Importe', title: 'Importe' },
            { data: 'Costo', name: 'Costo', title: 'Costo' },
            { data: 'Utilidad', name: 'Utilidad', title: 'Utilidad' },
            { data: 'Rentabilidad', name: 'Rentabilidad', title: 'Rentabilidad (%)' }
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json'
        },
        responsive: true,
        order: [[0, 'asc']],
        footerCallback: function(row, data, start, end, display) {
            var api = this.api();

            // Función para convertir valores a float sin comas
            var parseValue = function(value) {
                return parseFloat(String(value).replace(/,/g, '')) || 0;
            };

            // Sumar cada columna con valores parseados
            var totalImporte = api.column(2, { page: 'current' }).data().reduce(function(a, b) {
                return parseValue(a) + parseValue(b);
            }, 0);
            var totalCosto = api.column(3, { page: 'current' }).data().reduce(function(a, b) {
                return parseValue(a) + parseValue(b);
            }, 0);
            var totalUtilidad = api.column(4, { page: 'current' }).data().reduce(function(a, b) {
                return parseValue(a) + parseValue(b);
            }, 0);

            // Rentabilidad promedio
            var totalRentabilidad = (
                api.column(5, { page: 'current' }).data().reduce(function(a, b) {
                    return parseValue(a) + parseValue(b);
                }, 0) / api.column(5, { page: 'current' }).data().length
            );

            // Mostrar valores en el footer
            $(api.column(2).footer()).html('$' + totalImporte.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
            $(api.column(3).footer()).html('$' + totalCosto.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
            $(api.column(4).footer()).html('$' + totalUtilidad.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
            $(api.column(5).footer()).html(totalRentabilidad.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + '%');
        },
        drawCallback: function() {
            updateCharts(table); // Llamar a la función para actualizar las gráficas
        }
    });

    // Variables de gráficas y configuración de Chart.js
    var ctxUtilidad = document.getElementById('utilidadChart').getContext('2d');
    var ctxRentabilidad = document.getElementById('rentabilidadChart').getContext('2d');
    var utilidadChart = new Chart(ctxUtilidad, {
        type: 'doughnut',
        data: { labels: [], datasets: [{ label: 'Utilidad', backgroundColor: [], data: [] }] },
        options: { responsive: true }
    });
    var rentabilidadChart = new Chart(ctxRentabilidad, {
        type: 'doughnut',
        data: { labels: [], datasets: [{ label: 'Rentabilidad (%)', backgroundColor: [], data: [] }] },
        options: { responsive: true }
    });

    // Función para actualizar los datos de las gráficas
    function updateCharts(table) {
        var labels = [], utilidadData = [], rentabilidadData = [], colors = [];

        // Colores para cada segmento
        const colorPalette = ['#ff6384', '#36a2eb', '#ffce56', '#4bc0c0', '#9966ff', '#ff9f40'];

        // Agrupar datos por cliente
        var dataByCliente = {};
        table.rows({ page: 'current' }).data().each(function(row) {
            var cliente = row.NombreCliente;
            if (!dataByCliente[cliente]) {
                dataByCliente[cliente] = { utilidad: 0, rentabilidad: 0, count: 0 };
            }
            dataByCliente[cliente].utilidad += parseFloat(row.Utilidad.replace(/,/g, ''));
            dataByCliente[cliente].rentabilidad += parseFloat(row.Rentabilidad.replace(/,/g, ''));
            dataByCliente[cliente].count += 1;
        });

        // Asignar datos a las gráficas
        Object.keys(dataByCliente).forEach((cliente, index) => {
            labels.push(cliente);
            utilidadData.push(dataByCliente[cliente].utilidad);
            rentabilidadData.push(dataByCliente[cliente].rentabilidad / dataByCliente[cliente].count); // Promedio de rentabilidad
            colors.push(colorPalette[index % colorPalette.length]);
        });

        // Actualizar datos en las gráficas
        utilidadChart.data.labels = labels;
        utilidadChart.data.datasets[0].data = utilidadData;
        utilidadChart.data.datasets[0].backgroundColor = colors;
        utilidadChart.update();

        rentabilidadChart.data.labels = labels;
        rentabilidadChart.data.datasets[0].data = rentabilidadData;
        rentabilidadChart.data.datasets[0].backgroundColor = colors;
        rentabilidadChart.update();
    }

    // Recargar DataTable cuando se cambia algún filtro
    $('#agente, #anio, #mes, #cliente').on('change', function() {
        table.draw();
    });
});
</script>
@endsection