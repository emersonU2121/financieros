<?php

namespace App\Http\Controllers;

use App\Models\CuentaPorCobrar;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class ReporteCarteraController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // =========================
    //  CARTERA GENERAL
    // =========================
    public function carteraGeneral(Request $request)
    {
        $query = CuentaPorCobrar::with('cliente');

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $cuentas = $query->get();
        $totales = $this->calcularTotalesCartera($cuentas);

        return view('reportes.cartera_general', compact('cuentas', 'totales'));
    }

    // =========================
    //  REPORTE DE MORA
    // =========================
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

    // =========================
    //  INCOBRABLES
    // =========================
    public function incobrables()
    {
        $cuentas = CuentaPorCobrar::with('cliente')
            ->where('estado', 'INCOBRABLE')
            ->get();

        return view('reportes.incobrables', compact('cuentas'));
    }

    // =========================
    //  POR ZONA
    // =========================
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

    // =========================
    //  POR TIPO/CATEGORÍA
    // =========================
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

    // ============================================================
    //                    EXPORTACIONES
    // ============================================================

    public function exportarCarteraGeneral(string $formato)
    {
        $formato = strtolower($formato);

        $cuentas = CuentaPorCobrar::with('cliente')->get();
        $totales = $this->calcularTotalesCartera($cuentas);

        $headings = [
            'N° Factura',
            'Cliente',
            'Fecha inicio',
            'Vencimiento',
            'Capital inicial',
            'Capital actual',
            'Estado',
        ];

        $rows = [];
        foreach ($cuentas as $cuenta) {
            $rows[] = [
                $cuenta->numero_factura,
                $cuenta->cliente->nombre_completo ?? $cuenta->cliente->nombre,
                optional($cuenta->fecha_inicio)->format('d/m/Y'),
                optional($cuenta->fecha_vencimiento)->format('d/m/Y'),
                number_format($cuenta->monto_capital_inicial, 2, '.', ''),
                number_format($cuenta->monto_capital_actual, 2, '.', ''),
                $cuenta->estado,
            ];
        }

        if ($formato === 'csv') {
            return $this->exportCsv('reporte_cartera_general.csv', $headings, $rows);
        }

        if (in_array($formato, ['excel', 'xls'])) {
            return $this->exportExcel('reporte_cartera_general.xls', $headings, $rows);
        }

        if ($formato === 'pdf') {
            // Vista específica para PDF
            return view('reportes.pdf.cartera_general', compact('cuentas', 'totales'));
        }

        return redirect()->route('reportes.cartera')
            ->with('error', 'Formato de exportación no válido.');
    }

    public function exportarMora(string $formato)
    {
        $formato = strtolower($formato);
        $hoy = Carbon::today();

        $cuentas = CuentaPorCobrar::with('cliente')
            ->whereIn('estado', ['VIGENTE', 'EN_MORA'])
            ->get()
            ->filter(function ($cuenta) use ($hoy) {
                $venc = Carbon::parse($cuenta->fecha_vencimiento);
                return $hoy->greaterThan($venc);
            });

        $headings = [
            'N° Factura',
            'Cliente',
            'Fecha inicio',
            'Vencimiento',
            'Días de atraso',
            'Capital actual',
            'Estado',
        ];

        $rows = [];
        foreach ($cuentas as $cuenta) {
            $venc = Carbon::parse($cuenta->fecha_vencimiento);
            $diasAtraso = $hoy->diffInDays($venc, false);
            $diasAtraso = $diasAtraso < 0 ? abs($diasAtraso) : 0;

            $rows[] = [
                $cuenta->numero_factura,
                $cuenta->cliente->nombre_completo ?? $cuenta->cliente->nombre,
                optional($cuenta->fecha_inicio)->format('d/m/Y'),
                $venc->format('d/m/Y'),
                $diasAtraso,
                number_format($cuenta->monto_capital_actual, 2, '.', ''),
                $cuenta->estado,
            ];
        }

        if ($formato === 'csv') {
            return $this->exportCsv('reporte_cuentas_mora.csv', $headings, $rows);
        }

        if (in_array($formato, ['excel', 'xls'])) {
            return $this->exportExcel('reporte_cuentas_mora.xls', $headings, $rows);
        }

        if ($formato === 'pdf') {
            return view('reportes.pdf.mora', compact('cuentas'));
        }

        return redirect()->route('reportes.mora')
            ->with('error', 'Formato de exportación no válido.');
    }

    public function exportarIncobrables(string $formato)
    {
        $formato = strtolower($formato);

        $cuentas = CuentaPorCobrar::with('cliente')
            ->where('estado', 'INCOBRABLE')
            ->get();

        $headings = [
            'N° Factura',
            'Cliente',
            'Fecha inicio',
            'Vencimiento',
            'Capital inicial',
            'Capital actual',
        ];

        $rows = [];
        foreach ($cuentas as $cuenta) {
            $rows[] = [
                $cuenta->numero_factura,
                $cuenta->cliente->nombre_completo ?? $cuenta->cliente->nombre,
                optional($cuenta->fecha_inicio)->format('d/m/Y'),
                optional($cuenta->fecha_vencimiento)->format('d/m/Y'),
                number_format($cuenta->monto_capital_inicial, 2, '.', ''),
                number_format($cuenta->monto_capital_actual, 2, '.', ''),
            ];
        }

        if ($formato === 'csv') {
            return $this->exportCsv('reporte_cuentas_incobrables.csv', $headings, $rows);
        }

        if (in_array($formato, ['excel', 'xls'])) {
            return $this->exportExcel('reporte_cuentas_incobrables.xls', $headings, $rows);
        }

        if ($formato === 'pdf') {
            return view('reportes.pdf.incobrables', compact('cuentas'));
        }

        return redirect()->route('reportes.incobrables')
            ->with('error', 'Formato de exportación no válido.');
    }

    public function exportarPorZona(string $formato)
    {
        $formato = strtolower($formato);

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

        $headings = [
            'Zona',
            'Cantidad de cuentas',
            'Monto total',
        ];

        $rows = [];
        foreach ($porZona as $zona => $data) {
            $rows[] = [
                $zona,
                $data['cantidad_cuentas'],
                number_format($data['monto_total'], 2, '.', ''),
            ];
        }

        if ($formato === 'csv') {
            return $this->exportCsv('reporte_cartera_por_zona.csv', $headings, $rows);
        }

        if (in_array($formato, ['excel', 'xls'])) {
            return $this->exportExcel('reporte_cartera_por_zona.xls', $headings, $rows);
        }

        if ($formato === 'pdf') {
            return view('reportes.pdf.por_zona', compact('porZona'));
        }

        return redirect()->route('reportes.por_zona')
            ->with('error', 'Formato de exportación no válido.');
    }

    public function exportarPorTipoCliente(string $formato)
    {
        $formato = strtolower($formato);

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

        $headings = [
            'Tipo de cliente',
            'Categoría',
            'Saldo total',
        ];

        $rows = [];
        foreach ($estadistica as $tipo => $categorias) {
            foreach ($categorias as $cat => $saldo) {
                $rows[] = [
                    $tipo,
                    $cat,
                    number_format($saldo, 2, '.', ''),
                ];
            }
        }

        if ($formato === 'csv') {
            return $this->exportCsv('reporte_cartera_por_tipo_cliente.csv', $headings, $rows);
        }

        if (in_array($formato, ['excel', 'xls'])) {
            return $this->exportExcel('reporte_cartera_por_tipo_cliente.xls', $headings, $rows);
        }

        if ($formato === 'pdf') {
            return view('reportes.pdf.por_tipo_cliente', compact('estadistica'));
        }

        return redirect()->route('reportes.por_tipo_cliente')
            ->with('error', 'Formato de exportación no válido.');
    }

    // ============================================================
    //                    HELPERS PRIVADOS
    // ============================================================

    protected function calcularTotalesCartera($cuentas): array
    {
        return [
            'vigente'      => $cuentas->where('estado', 'VIGENTE')->sum('monto_capital_actual'),
            'mora'         => $cuentas->where('estado', 'EN_MORA')->sum('monto_capital_actual'),
            'refinanciado' => $cuentas->where('estado', 'REFINANCIADO')->sum('monto_capital_actual'),
            'incobrable'   => $cuentas->where('estado', 'INCOBRABLE')->sum('monto_capital_actual'),
            'embargo'      => $cuentas->where('estado', 'EMBARGO')->sum('monto_capital_actual'),
            'cancelado'    => $cuentas->where('estado', 'CANCELADO')->sum('monto_capital_inicial'),
            'total'        => $cuentas->sum('monto_capital_actual'),
        ];
    }

    protected function exportCsv(string $filename, array $headings, iterable $rows)
    {
        $handle = fopen('php://temp', 'r+');

        fputcsv($handle, $headings, ';');

        foreach ($rows as $row) {
            fputcsv($handle, $row, ';');
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        $csv = "\xEF\xBB\xBF" . $csv;

        return response($csv, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    protected function exportExcel(string $filename, array $headings, iterable $rows)
    {
        $html  = '<html><head><meta charset="UTF-8"></head><body>';
        $html .= '<table border="1"><thead><tr>';

        foreach ($headings as $heading) {
            $html .= '<th>' . htmlspecialchars($heading, ENT_QUOTES, 'UTF-8') . '</th>';
        }

        $html .= '</tr></thead><tbody>';

        foreach ($rows as $row) {
            $html .= '<tr>';
            foreach ($row as $value) {
                $html .= '<td>' . htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8') . '</td>';
            }
            $html .= '</tr>';
        }

        $html .= '</tbody></table></body></html>';

        return response($html, 200, [
            'Content-Type'        => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
