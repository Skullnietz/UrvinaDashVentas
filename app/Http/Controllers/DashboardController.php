<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Obtener los años únicos desde el campo Periodo en la tabla Cuadernillo
        $years = DB::table('Cuadernillo')
            ->selectRaw("DISTINCT YEAR(CONVERT(DATE, Periodo + '-01')) AS year")
            ->pluck('year');

        // Obtener los nombres de los clientes de la tabla Cte
        $clientes = DB::table('Cte')
            ->select('Cliente', 'Nombre')
            ->get();

        return view('dashboard.index', compact('years', 'clientes'));
    }

    public function filter(Request $request)
    {
        try {
            $year = $request->input('year') ?: date('Y');
            $month = $request->input('month');
            $clienteNombre = $request->input('cliente');
    
            $query = DB::table('Cuadernillo')
                ->join('Cte', 'Cuadernillo.Cliente', '=', 'Cte.Cliente')
                ->select(
                    DB::raw("SUBSTRING(Periodo, 1, 4) as Year"),
                    DB::raw("SUBSTRING(Periodo, 6, 2) as Month"),
                    DB::raw('SUM(Cantidad * Precio * TipoCambio) AS TotalImporte'),
                    DB::raw('SUM(Cantidad * CostoReal * TipoCambio) AS TotalCosto'),
                    DB::raw('SUM(Cantidad * Precio * TipoCambio) - SUM(Cantidad * CostoReal * TipoCambio) AS Utilidad'),
                    DB::raw('CASE WHEN SUM(Cantidad * CostoReal * TipoCambio) > 0 THEN 
                        (SUM(Cantidad * Precio * TipoCambio) - SUM(Cantidad * CostoReal * TipoCambio)) / SUM(Cantidad * CostoReal * TipoCambio) * 100 
                        ELSE 0 END AS Rentabilidad')
                );
    
            // Aplicar filtros si están presentes
            if ($year) {
                $query->whereRaw("SUBSTRING(Periodo, 1, 4) = ?", [$year]);
            }
            if ($month) {
                $query->whereRaw("SUBSTRING(Periodo, 6, 2) = ?", [str_pad($month, 2, '0', STR_PAD_LEFT)]);
            }
    
            if ($clienteNombre) {
                $clientes = DB::table('Cte')
                    ->where('Nombre', 'like', '%' . $clienteNombre . '%')
                    ->pluck('Cliente');
    
                if ($clientes->isNotEmpty()) {
                    $query->whereIn('Cuadernillo.Cliente', $clientes);
                } else {
                    return response()->json([
                        'data' => [],
                        'totalCosto' => 0,
                        'totalImporte' => 0,
                    ]);
                }
            }
    
            // Agrupar por Año y Mes
            $query->groupBy(DB::raw("SUBSTRING(Periodo, 1, 4)"), DB::raw("SUBSTRING(Periodo, 6, 2)"));
    
            // Obtener los datos
            $data = $query->orderBy('Year')->orderBy('Month')->get();
    
            // Crear un array para los nombres de los meses en español
            $meses = [
                '01' => 'Enero',
                '02' => 'Febrero',
                '03' => 'Marzo',
                '04' => 'Abril',
                '05' => 'Mayo',
                '06' => 'Junio',
                '07' => 'Julio',
                '08' => 'Agosto',
                '09' => 'Septiembre',
                '10' => 'Octubre',
                '11' => 'Noviembre',
                '12' => 'Diciembre'
            ];
    
            foreach ($data as $item) {
                $item->Periodo = $item->Year . '-' . str_pad($item->Month, 2, '0', STR_PAD_LEFT);
                $item->NombreMes = $meses[$item->Month];
            }

            $yearTotals = DB::table('Cuadernillo')
                ->whereRaw("SUBSTRING(Periodo, 1, 4) = ?", [$year])
                ->select(
                    DB::raw('SUM(Cantidad * Precio * TipoCambio) AS ImporteTotalAnual'),
                    DB::raw('SUM(Cantidad * CostoReal * TipoCambio) AS CostoTotalAnual'), // Agregado para debug
                    DB::raw('SUM(Cantidad * Precio * TipoCambio) - SUM(Cantidad * CostoReal * TipoCambio) AS UtilidadTotalAnual')
                )
                ->first();

    
            // Calcular totales
            $totalCosto = $data->sum('TotalCosto');
            $totalImporte = $data->sum('TotalImporte');
            $totalUtilidad = $data->sum('Utilidad');
    
            // Calcular indicadores
            $mesAlta = $data->sortByDesc('Utilidad')->first(); // Mes con más utilidad
            $mesBaja = $data->sortBy('Utilidad')->first(); // Mes con menos utilidad
            $margenContribucion = $totalCosto > 0 ? ($totalUtilidad / $totalCosto) * 100 : 0;
    
            return response()->json([
                'data' => $data,
                'costoTotal' => $totalCosto,
                'importeTotal' => $totalImporte,
                'utilidadTotal' => $totalUtilidad,
                'mesAlta' => $mesAlta ? ['mes' => $meses[$mesAlta->Month], 'utilidad' => $mesAlta->Utilidad] : null,
                'mesBaja' => $mesBaja ? ['mes' => $meses[$mesBaja->Month], 'utilidad' => $mesBaja->Utilidad] : null,
                'margenContribucion' => $margenContribucion,'importeTotalAnual' => $yearTotals->ImporteTotalAnual ?? 0,
                'utilidadTotalAnual' => $yearTotals->UtilidadTotalAnual ,
                'ImporteTotalAnual' => $yearTotals->ImporteTotalAnual ,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al filtrar los datos: ' . $e->getMessage()], 500);
        }
    }
    


}
