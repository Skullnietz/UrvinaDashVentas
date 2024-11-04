<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class DashVentasController extends Controller
{
    public function RentaUtilView() {
        // Obtener los años únicos desde el campo Periodo en la tabla Cuadernillo
        $years = DB::table('Cuadernillo')
            ->selectRaw("DISTINCT YEAR(CONVERT(DATE, Periodo + '-01')) AS year")
            ->pluck('year');
    
        // Obtener los clientes con el campo NombreComun sin usar NombreComun en el GROUP BY
        $clientes = DB::table('Cte')
            ->select('Cliente', DB::raw("
                CASE 
                    WHEN Cte.Nombre LIKE '%MABE%' THEN 'MABE' 
                    WHEN Cte.Nombre LIKE '%FAURECIA%' THEN 'FAURECIA' 
                    WHEN Cte.Nombre LIKE 'FORD %' THEN 'FORD' 
                    WHEN Cte.Nombre LIKE '%DANONE%' THEN 'DANONE' 
                    WHEN Cte.Nombre LIKE '%EBERSPAECHER%' THEN 'EBERSPAECHER' 
                    WHEN Cte.Nombre LIKE '%EFC SYSTEMS%' THEN 'EFC SYSTEMS' 
                    WHEN Cte.Nombre LIKE '%FASTENAL%' THEN 'FASTENAL' 
                    WHEN Cte.Nombre LIKE '%FEMSA%' THEN 'FEMSA' 
                    WHEN Cte.Nombre LIKE '%FLEX N GAT%' THEN 'FLEXNGATE'
                    WHEN Cte.Nombre LIKE '%FLEX-N-GATE%' THEN 'FLEXNGATE'
                    WHEN Cte.Nombre LIKE '%FUGRA%' THEN 'FUGRA'
                    WHEN Cte.Nombre LIKE '%GESTAMP%' THEN 'GESTAMP'
                    WHEN Cte.Nombre LIKE '%IACNA%' THEN 'IACNA'
                    WHEN Cte.Nombre LIKE '%LOREAL%' THEN 'LOREAL'
                    WHEN Cte.Nombre LIKE '%MAGNA%' THEN 'MAGNA'
                    WHEN Cte.Nombre LIKE '%MARELLI%' THEN 'MARELLI'
                    WHEN Cte.Nombre LIKE '%NAVISTAR%' THEN 'NAVISTAR'
                    WHEN Cte.Nombre LIKE '%PEPSICO%' THEN 'PEPSICO'
                    WHEN Cte.Nombre LIKE '%PIRELLI%' THEN 'PIRELLI'
                    WHEN Cte.Nombre LIKE '%PLASTIC OMNIUM%' THEN 'PLASTICOMNIUM'
                    WHEN Cte.Nombre LIKE '%PRETTL%' THEN 'PRETTL'
                    WHEN Cte.Nombre LIKE '%SEGLO%' THEN 'SEGLO'
                    WHEN Cte.Nombre LIKE 'GM %' THEN 'GM'
                    WHEN Cte.Nombre LIKE 'VALEO %' THEN 'VALEO'
                    WHEN Cte.Nombre LIKE 'VRK %' THEN 'VRK'
                    WHEN Cte.Nombre LIKE 'TRW %' THEN 'TRW'
                    WHEN Cte.Nombre LIKE 'WESCO %' THEN 'WESCO'
                    WHEN Cte.Nombre LIKE '%ZF %' THEN 'ZF'
                    ELSE Cte.Nombre 
                END AS NombreComun
            "))
            ->groupBy('Cliente', 'Cte.Nombre') // Agrupar solo por Cliente y el campo original Nombre
            ->orderBy('NombreComun') // Ordenar por NombreComun
            ->get();
        // Subconsulta para obtener los nombres comunes de los clientes, sin llamar a get()
        $clientesConNombreComun = DB::table('Cte')
        ->select('Cliente', DB::raw("
            CASE 
                WHEN Cte.Nombre LIKE '%MABE%' THEN 'MABE' 
                WHEN Cte.Nombre LIKE '%FAURECIA%' THEN 'FAURECIA' 
                WHEN Cte.Nombre LIKE 'FORD %' THEN 'FORD' 
                WHEN Cte.Nombre LIKE '%DANONE%' THEN 'DANONE' 
                WHEN Cte.Nombre LIKE '%EBERSPAECHER%' THEN 'EBERSPAECHER' 
                WHEN Cte.Nombre LIKE '%EFC SYSTEMS%' THEN 'EFC SYSTEMS' 
                WHEN Cte.Nombre LIKE '%FASTENAL%' THEN 'FASTENAL' 
                WHEN Cte.Nombre LIKE '%FEMSA%' THEN 'FEMSA' 
                WHEN Cte.Nombre LIKE '%FLEX N GAT%' THEN 'FLEXNGATE'
                WHEN Cte.Nombre LIKE '%FLEX-N-GATE%' THEN 'FLEXNGATE'
                WHEN Cte.Nombre LIKE '%FUGRA%' THEN 'FUGRA'
                WHEN Cte.Nombre LIKE '%GESTAMP%' THEN 'GESTAMP'
                WHEN Cte.Nombre LIKE '%IACNA%' THEN 'IACNA'
                WHEN Cte.Nombre LIKE '%LOREAL%' THEN 'LOREAL'
                WHEN Cte.Nombre LIKE '%MAGNA%' THEN 'MAGNA'
                WHEN Cte.Nombre LIKE '%MARELLI%' THEN 'MARELLI'
                WHEN Cte.Nombre LIKE '%NAVISTAR%' THEN 'NAVISTAR'
                WHEN Cte.Nombre LIKE '%PEPSICO%' THEN 'PEPSICO'
                WHEN Cte.Nombre LIKE '%PIRELLI%' THEN 'PIRELLI'
                WHEN Cte.Nombre LIKE '%PLASTIC OMNIUM%' THEN 'PLASTICOMNIUM'
                WHEN Cte.Nombre LIKE '%PRETTL%' THEN 'PRETTL'
                WHEN Cte.Nombre LIKE '%SEGLO%' THEN 'SEGLO'
                WHEN Cte.Nombre LIKE 'GM %' THEN 'GM'
                WHEN Cte.Nombre LIKE 'VALEO %' THEN 'VALEO'
                WHEN Cte.Nombre LIKE 'VRK %' THEN 'VRK'
                WHEN Cte.Nombre LIKE 'TRW %' THEN 'TRW'
                WHEN Cte.Nombre LIKE 'WESCO %' THEN 'WESCO'
                WHEN Cte.Nombre LIKE '%ZF %' THEN 'ZF'
                ELSE Cte.Nombre 
            END AS NombreComun
        "))
        ->groupBy('Cliente', 'Cte.Nombre'); // No uses ->get() aquí

        // Consulta principal para el Top 9 clientes con mayor utilidad
        $topClientes = DB::table('Cuadernillo')
        ->joinSub($clientesConNombreComun, 'Cte', function ($join) {
            $join->on('Cuadernillo.Cliente', '=', 'Cte.Cliente');
        })
        ->select(
            'Cte.NombreComun as Cliente',
            DB::raw('SUM(Cantidad * Precio * TipoCambio) - SUM(Cantidad * CostoReal * TipoCambio) AS Utilidad')
        )
        ->groupBy('Cte.NombreComun')
        ->havingRaw('SUM(Cantidad * Precio * TipoCambio) - SUM(Cantidad * CostoReal * TipoCambio) > 0')
        ->orderByDesc('Utilidad')
        ->limit(9)
        ->get();

        // Acumulado para el apartado de "Otros"
        $otrosClientes = DB::table('Cuadernillo')
        ->joinSub($clientesConNombreComun, 'Cte', function ($join) {
            $join->on('Cuadernillo.Cliente', '=', 'Cte.Cliente');
        })
        ->select(
            DB::raw("'Otros' as Cliente"),
            DB::raw("SUM(Cantidad * Precio * TipoCambio) - SUM(Cantidad * CostoReal * TipoCambio) AS Utilidad")
        )
        ->whereNotIn('Cte.NombreComun', $topClientes->pluck('Cliente'))
        ->havingRaw('SUM(Cantidad * Precio * TipoCambio) - SUM(Cantidad * CostoReal * TipoCambio) > 0')
        ->first();

        // Unión de resultados para incluir "Otros"
        $resultadosTopConOtros = $topClientes->merge([$otrosClientes]);

        // Consulta principal para los 10 clientes con menor utilidad, omitiendo los que tienen 0
        $clientesMenorUtilidad = DB::table('Cuadernillo')
        ->joinSub($clientesConNombreComun, 'Cte', function ($join) {
            $join->on('Cuadernillo.Cliente', '=', 'Cte.Cliente');
        })
        ->select(
            'Cte.NombreComun as Cliente',
            DB::raw("SUM(Cantidad * Precio * TipoCambio) - SUM(Cantidad * CostoReal * TipoCambio) AS Utilidad")
        )
        ->groupBy('Cte.NombreComun')
        ->havingRaw('SUM(Cantidad * Precio * TipoCambio) - SUM(Cantidad * CostoReal * TipoCambio) > 0')
        ->orderBy('Utilidad')
        ->limit(10)
        ->get();
    
        // Agrupar por NombreComun después de obtener los resultados
        $groupedClientes = $clientes->groupBy('NombreComun');

       
    
        return view('dashventas.rentautil', compact('years', 'groupedClientes','resultadosTopConOtros','clientesMenorUtilidad'));
    }
    public function TopSupInfView(Request $request){
     // Subconsulta para obtener los nombres comunes de los clientes, sin llamar a get()
     $clientesConNombreComun = DB::table('Cte')
     ->select('Cliente', DB::raw("
         CASE 
             WHEN Cte.Nombre LIKE '%MABE%' THEN 'MABE' 
             WHEN Cte.Nombre LIKE '%FAURECIA%' THEN 'FAURECIA' 
             WHEN Cte.Nombre LIKE 'FORD %' THEN 'FORD' 
             WHEN Cte.Nombre LIKE '%DANONE%' THEN 'DANONE' 
             WHEN Cte.Nombre LIKE '%EBERSPAECHER%' THEN 'EBERSPAECHER' 
             WHEN Cte.Nombre LIKE '%EFC SYSTEMS%' THEN 'EFC SYSTEMS' 
             WHEN Cte.Nombre LIKE '%FASTENAL%' THEN 'FASTENAL' 
             WHEN Cte.Nombre LIKE '%FEMSA%' THEN 'FEMSA' 
             WHEN Cte.Nombre LIKE '%FLEX N GAT%' THEN 'FLEXNGATE'
             WHEN Cte.Nombre LIKE '%FLEX-N-GATE%' THEN 'FLEXNGATE'
             WHEN Cte.Nombre LIKE '%FUGRA%' THEN 'FUGRA'
             WHEN Cte.Nombre LIKE '%GESTAMP%' THEN 'GESTAMP'
             WHEN Cte.Nombre LIKE '%IACNA%' THEN 'IACNA'
             WHEN Cte.Nombre LIKE '%LOREAL%' THEN 'LOREAL'
             WHEN Cte.Nombre LIKE '%MAGNA%' THEN 'MAGNA'
             WHEN Cte.Nombre LIKE '%MARELLI%' THEN 'MARELLI'
             WHEN Cte.Nombre LIKE '%NAVISTAR%' THEN 'NAVISTAR'
             WHEN Cte.Nombre LIKE '%PEPSICO%' THEN 'PEPSICO'
             WHEN Cte.Nombre LIKE '%PIRELLI%' THEN 'PIRELLI'
             WHEN Cte.Nombre LIKE '%PLASTIC OMNIUM%' THEN 'PLASTICOMNIUM'
             WHEN Cte.Nombre LIKE '%PRETTL%' THEN 'PRETTL'
             WHEN Cte.Nombre LIKE '%SEGLO%' THEN 'SEGLO'
             WHEN Cte.Nombre LIKE 'GM %' THEN 'GM'
             WHEN Cte.Nombre LIKE 'VALEO %' THEN 'VALEO'
             WHEN Cte.Nombre LIKE 'VRK %' THEN 'VRK'
             WHEN Cte.Nombre LIKE 'TRW %' THEN 'TRW'
             WHEN Cte.Nombre LIKE 'WESCO %' THEN 'WESCO'
             WHEN Cte.Nombre LIKE '%ZF %' THEN 'ZF'
             ELSE Cte.Nombre 
         END AS NombreComun
     "))
     ->groupBy('Cliente', 'Cte.Nombre'); // No uses ->get() aquí

     // Obtener el año de la solicitud, usando el año actual si no se proporciona
     $anio = $request->input('anio', date('Y')); // Obtener el año actual si no se envía
     $mes = $request->input('mes'); // Puede ser nulo

     ///////////////////////////////// UTILIDAD /////////////////////////////////
 
     // Consulta para el Top 9 clientes con mayor utilidad, filtrando por año y opcionalmente por mes
     $topClientesQuery = DB::table('Cuadernillo')
         ->joinSub($clientesConNombreComun, 'Cte', function ($join) {
             $join->on('Cuadernillo.Cliente', '=', 'Cte.Cliente');
         })
         ->select(
             'Cte.NombreComun as Cliente',
             DB::raw('SUM(Cantidad * Precio * TipoCambio) - SUM(Cantidad * CostoReal * TipoCambio) AS Utilidad')
         )
         ->whereRaw("LEFT(Cuadernillo.Periodo, 4) = ?", [$anio]); // Filtrar solo por año
 
     // Filtrar por mes solo si se proporciona
     if ($mes) {
         $topClientesQuery->whereRaw("SUBSTRING(Cuadernillo.Periodo, 6, 2) = ?", [sprintf('%02d', $mes)]);
     }
 
     $topClientes = $topClientesQuery->groupBy('Cte.NombreComun')
         ->havingRaw('SUM(Cantidad * Precio * TipoCambio) - SUM(Cantidad * CostoReal * TipoCambio) > 0')
         ->orderByDesc('Utilidad')
         ->limit(9)
         ->get();
 
     // Acumulado para el apartado de "Otros"
     $otrosClientesQuery = DB::table('Cuadernillo')
         ->joinSub($clientesConNombreComun, 'Cte', function ($join) {
             $join->on('Cuadernillo.Cliente', '=', 'Cte.Cliente');
         })
         ->select(
             DB::raw("'Otros' as Cliente"),
             DB::raw("SUM(Cantidad * Precio * TipoCambio) - SUM(Cantidad * CostoReal * TipoCambio) AS Utilidad")
         )
         ->whereRaw("LEFT(Cuadernillo.Periodo, 4) = ?", [$anio]); // Filtrar solo por año
 
     // Filtrar por mes solo si se proporciona
     if ($mes) {
         $otrosClientesQuery->whereRaw("SUBSTRING(Cuadernillo.Periodo, 6, 2) = ?", [sprintf('%02d', $mes)]);
     }
 
     $otrosClientes = $otrosClientesQuery->whereNotIn('Cte.NombreComun', $topClientes->pluck('Cliente'))
         ->havingRaw('SUM(Cantidad * Precio * TipoCambio) - SUM(Cantidad * CostoReal * TipoCambio) > 0')
         ->first();
 
     // Unión de resultados para incluir "Otros"
     $resultadosTopConOtros = $topClientes->merge([$otrosClientes]);

     // Verificar si hay resultados
    if ($resultadosTopConOtros->isEmpty() && $clientesMenorUtilidad->isEmpty()) {
        // Retornar un mensaje indicando que no hay registros
        return response()->json(['message' => 'No hay registros para el año y mes seleccionados.'], 404);
    }
 
     // Consulta principal para los 10 clientes con menor utilidad, omitiendo los que tienen 0
     $clientesMenorUtilidadQuery = DB::table('Cuadernillo')
         ->joinSub($clientesConNombreComun, 'Cte', function ($join) {
             $join->on('Cuadernillo.Cliente', '=', 'Cte.Cliente');
         })
         ->select(
             'Cte.NombreComun as Cliente',
             DB::raw("SUM(Cantidad * Precio * TipoCambio) - SUM(Cantidad * CostoReal * TipoCambio) AS Utilidad")
         )
         ->whereRaw("LEFT(Cuadernillo.Periodo, 4) = ?", [$anio]); // Filtrar solo por año
 
     // Filtrar por mes solo si se proporciona
     if ($mes) {
         $clientesMenorUtilidadQuery->whereRaw("SUBSTRING(Cuadernillo.Periodo, 6, 2) = ?", [sprintf('%02d', $mes)]);
     }
 
     $clientesMenorUtilidad = $clientesMenorUtilidadQuery->groupBy('Cte.NombreComun')
         ->havingRaw('SUM(Cantidad * Precio * TipoCambio) - SUM(Cantidad * CostoReal * TipoCambio) > 0')
         ->orderBy('Utilidad')
         ->limit(10)
         ->get();
        /////////////////////////////////////////// IMPORTE ////////////////////////////////////////////////
        // Consulta para el Top 9 clientes con mayor utilidad, filtrando por año y opcionalmente por mes
     $topClientesQueryImporte = DB::table('Cuadernillo')
     ->joinSub($clientesConNombreComun, 'Cte', function ($join) {
         $join->on('Cuadernillo.Cliente', '=', 'Cte.Cliente');
     })
     ->select(
         'Cte.NombreComun as Cliente',
         DB::raw('SUM(Cantidad * Precio * TipoCambio) AS Importe')
     )
     ->whereRaw("LEFT(Cuadernillo.Periodo, 4) = ?", [$anio]); // Filtrar solo por año

 // Filtrar por mes solo si se proporciona
 if ($mes) {
     $topClientesQueryImporte->whereRaw("SUBSTRING(Cuadernillo.Periodo, 6, 2) = ?", [sprintf('%02d', $mes)]);
 }

 $topClientesImporte = $topClientesQueryImporte->groupBy('Cte.NombreComun')
     ->havingRaw('SUM(Cantidad * Precio * TipoCambio) > 0')
     ->orderByDesc('Importe')
     ->limit(9)
     ->get();

 // Acumulado para el apartado de "Otros"
 $otrosClientesQueryImporte = DB::table('Cuadernillo')
     ->joinSub($clientesConNombreComun, 'Cte', function ($join) {
         $join->on('Cuadernillo.Cliente', '=', 'Cte.Cliente');
     })
     ->select(
         DB::raw("'Otros' as Cliente"),
         DB::raw("SUM(Cantidad * Precio * TipoCambio) AS Importe")
     )
     ->whereRaw("LEFT(Cuadernillo.Periodo, 4) = ?", [$anio]); // Filtrar solo por año

 // Filtrar por mes solo si se proporciona
 if ($mes) {
     $otrosClientesQueryImporte->whereRaw("SUBSTRING(Cuadernillo.Periodo, 6, 2) = ?", [sprintf('%02d', $mes)]);
 }

 $otrosClientesImporte = $otrosClientesQueryImporte->whereNotIn('Cte.NombreComun', $topClientesImporte->pluck('Cliente'))
     ->havingRaw('SUM(Cantidad * Precio * TipoCambio) > 0')
     ->first();

 // Unión de resultados para incluir "Otros"
 $resultadosTopConOtrosImporte = $topClientesImporte->merge([$otrosClientesImporte]);

 // Verificar si hay resultados
if ($resultadosTopConOtrosImporte->isEmpty() && $clientesMenorImporte->isEmpty()) {
    // Retornar un mensaje indicando que no hay registros
    return response()->json(['message' => 'No hay registros para el año y mes seleccionados.'], 404);
}

 // Consulta principal para los 10 clientes con menor utilidad, omitiendo los que tienen 0
 $clientesMenorImporteQuery = DB::table('Cuadernillo')
     ->joinSub($clientesConNombreComun, 'Cte', function ($join) {
         $join->on('Cuadernillo.Cliente', '=', 'Cte.Cliente');
     })
     ->select(
         'Cte.NombreComun as Cliente',
         DB::raw("SUM(Cantidad * Precio * TipoCambio) AS Importe")
     )
     ->whereRaw("LEFT(Cuadernillo.Periodo, 4) = ?", [$anio]); // Filtrar solo por año

 // Filtrar por mes solo si se proporciona
 if ($mes) {
     $clientesMenorImporteQuery->whereRaw("SUBSTRING(Cuadernillo.Periodo, 6, 2) = ?", [sprintf('%02d', $mes)]);
 }

 $clientesMenorImporte = $clientesMenorImporteQuery->groupBy('Cte.NombreComun')
     ->havingRaw('SUM(Cantidad * Precio * TipoCambio) > 0')
     ->orderBy('Importe')
     ->limit(10)
     ->get();

     /////////////////////////////////////////////////// COSTO ////////////////////////////////////////////////////////////////

     $topClientesQueryCosto = DB::table('Cuadernillo')
     ->joinSub($clientesConNombreComun, 'Cte', function ($join) {
         $join->on('Cuadernillo.Cliente', '=', 'Cte.Cliente');
     })
     ->select(
         'Cte.NombreComun as Cliente',
         DB::raw('SUM(Cantidad * CostoReal * TipoCambio) AS Costo')
     )
     ->whereRaw("LEFT(Cuadernillo.Periodo, 4) = ?", [$anio]); // Filtrar solo por año

 // Filtrar por mes solo si se proporciona
 if ($mes) {
     $topClientesQueryCosto->whereRaw("SUBSTRING(Cuadernillo.Periodo, 6, 2) = ?", [sprintf('%02d', $mes)]);
 }

 $topClientesCosto = $topClientesQueryCosto->groupBy('Cte.NombreComun')
     ->havingRaw('SUM(Cantidad * CostoReal * TipoCambio) > 0')
     ->orderByDesc('Costo')
     ->limit(9)
     ->get();

 // Acumulado para el apartado de "Otros"
 $otrosClientesQueryCosto = DB::table('Cuadernillo')
     ->joinSub($clientesConNombreComun, 'Cte', function ($join) {
         $join->on('Cuadernillo.Cliente', '=', 'Cte.Cliente');
     })
     ->select(
         DB::raw("'Otros' as Cliente"),
         DB::raw("SUM(Cantidad * CostoReal * TipoCambio) AS Costo")
     )
     ->whereRaw("LEFT(Cuadernillo.Periodo, 4) = ?", [$anio]); // Filtrar solo por año

 // Filtrar por mes solo si se proporciona
 if ($mes) {
     $otrosClientesQueryCosto->whereRaw("SUBSTRING(Cuadernillo.Periodo, 6, 2) = ?", [sprintf('%02d', $mes)]);
 }

 $otrosClientesCosto = $otrosClientesQueryCosto->whereNotIn('Cte.NombreComun', $topClientesCosto->pluck('Cliente'))
     ->havingRaw('SUM(Cantidad * CostoReal * TipoCambio) > 0')
     ->first();

 // Unión de resultados para incluir "Otros"
 $resultadosTopConOtrosCosto = $topClientesCosto->merge([$otrosClientesCosto]);

 // Verificar si hay resultados
if ($resultadosTopConOtrosCosto->isEmpty() && $clientesMenorCosto->isEmpty()) {
    // Retornar un mensaje indicando que no hay registros
    return response()->json(['message' => 'No hay registros para el año y mes seleccionados.'], 404);
}

 // Consulta principal para los 10 clientes con menor utilidad, omitiendo los que tienen 0
 $clientesMenorCostoQuery = DB::table('Cuadernillo')
     ->joinSub($clientesConNombreComun, 'Cte', function ($join) {
         $join->on('Cuadernillo.Cliente', '=', 'Cte.Cliente');
     })
     ->select(
         'Cte.NombreComun as Cliente',
         DB::raw("SUM(Cantidad * CostoReal * TipoCambio) AS Costo")
     )
     ->whereRaw("LEFT(Cuadernillo.Periodo, 4) = ?", [$anio]); // Filtrar solo por año

 // Filtrar por mes solo si se proporciona
 if ($mes) {
     $clientesMenorCostoQuery->whereRaw("SUBSTRING(Cuadernillo.Periodo, 6, 2) = ?", [sprintf('%02d', $mes)]);
 }

 $clientesMenorCosto = $clientesMenorCostoQuery->groupBy('Cte.NombreComun')
     ->havingRaw('SUM(Cantidad * Precio * TipoCambio) > 0')
     ->orderBy('Costo')
     ->limit(10)
     ->get();

     ///////////////////////////////////////////////////////////// RENTABILIDAD ////////////////////////////////////////////////////////////////////
     $topClientesQueryRentabilidad = DB::table('Cuadernillo')
    ->joinSub($clientesConNombreComun, 'Cte', function ($join) {
        $join->on('Cuadernillo.Cliente', '=', 'Cte.Cliente');
    })
    ->select(
        'Cte.NombreComun as Cliente',
        DB::raw("CASE WHEN SUM(Cantidad * CostoReal * TipoCambio) = 0 THEN 0 ELSE (SUM(Cantidad * Precio * TipoCambio) - SUM(Cantidad * CostoReal * TipoCambio)) / SUM(Cantidad * CostoReal * TipoCambio) * 100 END AS Rentabilidad")
    )
    ->whereRaw("LEFT(Cuadernillo.Periodo, 4) = ?", [$anio]); // Filtrar solo por año

// Filtrar por mes solo si se proporciona
if ($mes) {
    $topClientesQueryRentabilidad->whereRaw("SUBSTRING(Cuadernillo.Periodo, 6, 2) = ?", [sprintf('%02d', $mes)]);
}

$topClientesRentabilidad = $topClientesQueryRentabilidad->groupBy('Cte.NombreComun')
    ->havingRaw("CASE WHEN SUM(Cantidad * CostoReal * TipoCambio) = 0 THEN 0 ELSE (SUM(Cantidad * Precio * TipoCambio) - SUM(Cantidad * CostoReal * TipoCambio)) / SUM(Cantidad * CostoReal * TipoCambio) * 100 END > 0")
    ->orderByDesc('Rentabilidad')
    ->limit(9)
    ->get();

// Acumulado para el apartado de "Otros"
$otrosClientesQueryRentabilidad = DB::table('Cuadernillo')
    ->joinSub($clientesConNombreComun, 'Cte', function ($join) {
        $join->on('Cuadernillo.Cliente', '=', 'Cte.Cliente');
    })
    ->select(
        DB::raw("'Otros' as Cliente"),
        DB::raw("CASE WHEN SUM(Cantidad * CostoReal * TipoCambio) = 0 THEN 0 ELSE (SUM(Cantidad * Precio * TipoCambio) - SUM(Cantidad * CostoReal * TipoCambio)) / SUM(Cantidad * CostoReal * TipoCambio) * 100 END AS Rentabilidad")
    )
    ->whereRaw("LEFT(Cuadernillo.Periodo, 4) = ?", [$anio]); // Filtrar solo por año

// Filtrar por mes solo si se proporciona
if ($mes) {
    $otrosClientesQueryRentabilidad->whereRaw("SUBSTRING(Cuadernillo.Periodo, 6, 2) = ?", [sprintf('%02d', $mes)]);
}

$otrosClientesRentabilidad = $otrosClientesQueryRentabilidad->whereNotIn('Cte.NombreComun', $topClientesRentabilidad->pluck('Cliente'))
    ->havingRaw("CASE WHEN SUM(Cantidad * CostoReal * TipoCambio) = 0 THEN 0 ELSE (SUM(Cantidad * Precio * TipoCambio) - SUM(Cantidad * CostoReal * TipoCambio)) / SUM(Cantidad * CostoReal * TipoCambio) * 100 END > 0")
    ->first();

// Unión de resultados para incluir "Otros"
$resultadosTopConOtrosRentabilidad = $topClientesRentabilidad->merge([$otrosClientesRentabilidad]);

// Verificar si hay resultados
if ($resultadosTopConOtrosRentabilidad->isEmpty() && $clientesMenorRentabilidad->isEmpty()) {
    // Retornar un mensaje indicando que no hay registros
    return response()->json(['message' => 'No hay registros para el año y mes seleccionados.'], 404);
}

// Consulta principal para los 10 clientes con menor utilidad, omitiendo los que tienen 0
$clientesMenorRentabilidadQuery = DB::table('Cuadernillo')
    ->joinSub($clientesConNombreComun, 'Cte', function ($join) {
        $join->on('Cuadernillo.Cliente', '=', 'Cte.Cliente');
    })
    ->select(
        'Cte.NombreComun as Cliente',
        DB::raw("CASE WHEN SUM(Cantidad * CostoReal * TipoCambio) = 0 THEN 0 ELSE (SUM(Cantidad * Precio * TipoCambio) - SUM(Cantidad * CostoReal * TipoCambio)) / SUM(Cantidad * CostoReal * TipoCambio) * 100 END AS Rentabilidad")
    )
    ->whereRaw("LEFT(Cuadernillo.Periodo, 4) = ?", [$anio]); // Filtrar solo por año

// Filtrar por mes solo si se proporciona
if ($mes) {
    $clientesMenorRentabilidadQuery->whereRaw("SUBSTRING(Cuadernillo.Periodo, 6, 2) = ?", [sprintf('%02d', $mes)]);
}

$clientesMenorRentabilidad = $clientesMenorRentabilidadQuery->groupBy('Cte.NombreComun')
    ->havingRaw("CASE WHEN SUM(Cantidad * CostoReal * TipoCambio) = 0 THEN 0 ELSE (SUM(Cantidad * Precio * TipoCambio) - SUM(Cantidad * CostoReal * TipoCambio)) / SUM(Cantidad * CostoReal * TipoCambio) * 100 END > 0")
    ->orderBy('Rentabilidad')
    ->limit(10)
    ->get();


     
     return view('dashventas.topsupinf', compact('resultadosTopConOtros','clientesMenorUtilidad','resultadosTopConOtrosImporte','clientesMenorImporte','resultadosTopConOtrosCosto','clientesMenorCosto','resultadosTopConOtrosRentabilidad','clientesMenorRentabilidad'));
        
    }
    public function VentasPerMesView(){
        return view("dashventas.ventaspermes");
    }
}
