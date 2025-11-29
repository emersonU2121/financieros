<?php

namespace App\Http\Controllers;

use App\Models\CuentaPorCobrar;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReporteCarteraController extends Controller
{
   
      public function __construct()
    {
        $this->middleware('auth');
    }

    // CARTERA GENERAL
    public function carteraGeneral(Request $request)
    {
        $query = CuentaPorCobrar::with('cliente');

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $cuentas = $query->get();

        $totales = [
            'vigente'      => $cuentas->where('estado', 'VIGENTE')->sum('monto_capital_actual'),
            'mora'         => $cuentas->where('estado', 'EN_MORA')->sum('monto_capital_actual'),
            'refinanciado' => $cuentas->where('estado', 'REFINANCIADO')->sum('monto_capital_actual'),
            'incobrable'   => $cuentas->where('estado', 'INCOBRABLE')->sum('monto_capital_actual'),
            'embargo'      => $cuentas->where('estado', 'EMBARGO')->sum('monto_capital_actual'),
            'cancelado'    => $cuentas->where('estado', 'CANCELADO')->sum('monto_capital_inicial'),
            'total'        => $cuentas->sum('monto_capital_actual'),
        ];

        return view('reportes.cartera_general', compact('cuentas', 'totales'));
    }

    // REPORTE DE MORA
    public function mora()
    {
        $hoy = Carbon::today();
        $cuentas = CuentaPorCobrar::with('cliente')
            ->whereIn('estado', ['VIGENTE', 'EN_MORA'])
            ->get()
            ->filter(function ($cuenta) use ($hoy) {
                $venc = Carbon::parse($cuenta->fecha_vencimiento);
                return $hoy->greaterThan($venc);
            });

        return view('reportes.mora', compact('cuentas'));
    }

    // INCORBRABLES
    public function incobrables()
    {
        $cuentas = CuentaPorCobrar::with('cliente')
            ->where('estado', 'INCOBRABLE')
            ->get();

        return view('reportes.incobrables', compact('cuentas'));
    }

    // POR ZONA
    public function porZona()
    {
        $cuentas = CuentaPorCobrar::with('cliente')
            ->whereNotIn('estado', ['CANCELADO'])
            ->get();

        $porZona = $cuentas->groupBy(function ($cuenta) {
            return $cuenta->cliente->zona ?? 'SIN_ZONA';
        })->map(function ($grupo) {
            return [
                'cantidad_cuentas' => $grupo->count(),
                'monto_total'      => $grupo->sum('monto_capital_actual'),
            ];
        });

        return view('reportes.por_zona', compact('porZona'));
    }

    // POR TIPO/CATEGORÍA
    public function porTipoCliente()
    {
        $clientes = Cliente::with('clasificacion', 'cuentas')->get();

        $estadistica = [
            'NATURAL'  => ['A' => 0, 'B' => 0, 'C' => 0, 'D' => 0],
            'JURIDICA' => ['A' => 0, 'B' => 0, 'C' => 0, 'D' => 0],
        ];

        foreach ($clientes as $cliente) {
            $tipo = $cliente->tipo;
            $cat  = $cliente->clasificacion->codigo ?? 'D';

            $saldo = $cliente->cuentas
                ->whereNotIn('estado', ['CANCELADO', 'INCOBRABLE'])
                ->sum('monto_capital_actual');

            if (isset($estadistica[$tipo][$cat])) {
                $estadistica[$tipo][$cat] += $saldo;
            }
        }

        return view('reportes.por_tipo_cliente', compact('estadistica'));
    }
}

