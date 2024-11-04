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
    </table>
            </div>
        </div>
    
</div>
@endsection
@section('scripts')
<!-- CSS de Select2 -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<!-- JavaScript de Select2 -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    // Inicializar Select2 en los selectores
    $('#agente').select2({
        placeholder: "Todos los agentes",
        allowClear: true
    });

    $('#anio').select2({
        placeholder: "Año actual",
        allowClear: true
    });

    $('#mes').select2({
        placeholder: "Todos los meses",
        allowClear: true
    });

    $('#cliente').select2({
        placeholder: "Todos los clientes",
        allowClear: true
    });

    // Llamada AJAX para obtener los datos de los selects
    $.ajax({
        url: '/obtener-opciones-select',
        method: 'GET',
        success: function(response) {
            // Llenar el select de agentes
            $.each(response.agentes, function(index, agente) {
                $('#agente').append(new Option(agente.Nombre, agente.Agente));
            });

            // Llenar el select de años
            $.each(response.anios, function(index, anio) {
                $('#anio').append(new Option(anio.anio, anio.anio));
            });

            // Llenar el select de meses
            $.each(response.meses, function(index, mes) {
                $('#mes').append(new Option(mes.nombre, mes.id));
            });

            // Llenar el select de clientes
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
        { data: 'Nombre', name: 'Agente.Nombre', title: 'Nombre Agente', searchable: true },
        { data: 'NombreCorto', name: 'Cte.NombreCorto', title: 'Nombre Cliente', searchable: true },
        { data: 'Importe', name: 'Importe', title: 'Importe', searchable: false },
        { data: 'Costo', name: 'Costo', title: 'Costo', searchable: false },
        { data: 'Utilidad', name: 'Utilidad', title: 'Utilidad', searchable: false },
        { data: 'Rentabilidad', name: 'Rentabilidad', title: 'Rentabilidad (%)', searchable: false }
    ],
    language: {
            url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json'
        },
        order: [[0, 'asc']],
        responsive: true
    });

     // Recargar DataTable cuando se cambia algún filtro
     $('#agente, #anio, #mes, #cliente').on('change', function() {
        table.draw();
    });
});
</script>
@endsection