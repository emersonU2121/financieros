<?php

namespace App\Http\Controllers;

use App\Models\CuentaPorCobrar;
use App\Models\Pago;
use App\Models\BitacoraAccion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PagoController extends Controller
{
      public function __construct()
    {
        $this->middleware('auth');
    }

    // REGISTRAR PAGO
    public function store(Request $request)
    {
        $data = $request->validate([
            'cuenta_id'    => 'required|exists:cuentas_por_cobrar,id',
            'fecha_pago'   => 'required|date',
            'monto_total'  => 'required|numeric|min:0.01',
            'forma_pago'   => 'nullable|string|max:50',
            'observaciones'=> 'nullable|string|max:1000',
        ]);

        return DB::transaction(function () use ($data) {

            $cuenta = CuentaPorCobrar::lockForUpdate()->findOrFail($data['cuenta_id']);

            if (in_array($cuenta->estado, ['INCOBRABLE', 'REFINANCIADO'])) {
                return back()->with('error', 'No se puede registrar pago en una cuenta incobrable o refinanciada.');
            }

            // Actualizar intereses acumulados hasta la fecha de pago (versión simplificada)
            $this->actualizarInteresesCorrientes($cuenta, $data['fecha_pago']);

            $montoPago     = $data['monto_total'];
            $aplicaInteres = 0;
            $aplicaComision= 0;
            $aplicaCapital = 0;

            // 1. Intereses
            if ($cuenta->intereses_acumulados > 0 && $montoPago > 0) {
                $aplicaInteres = min($montoPago, $cuenta->intereses_acumulados);
                $montoPago -= $aplicaInteres;
                $cuenta->intereses_acumulados -= $aplicaInteres;
            }

            // 2. Comisiones
            if ($cuenta->comisiones_acumuladas > 0 && $montoPago > 0) {
                $aplicaComision = min($montoPago, $cuenta->comisiones_acumuladas);
                $montoPago -= $aplicaComision;
                $cuenta->comisiones_acumuladas -= $aplicaComision;
            }

            // 3. Capital
            if ($cuenta->monto_capital_actual > 0 && $montoPago > 0) {
                $aplicaCapital = min($montoPago, $cuenta->monto_capital_actual);
                $montoPago -= $aplicaCapital;
                $cuenta->monto_capital_actual -= $aplicaCapital;
            }

            // Estado final
            if (
                $cuenta->monto_capital_actual <= 0 &&
                $cuenta->intereses_acumulados <= 0 &&
                $cuenta->comisiones_acumuladas <= 0
            ) {
                $cuenta->estado = 'CANCELADO';
            }

            $cuenta->save();

            // Generar número de recibo simple
            $numeroRecibo = 'REC-' . Carbon::now()->format('YmdHis') . '-' . $cuenta->id;

            // ⬇⬇⬇ AQUÍ EL FAMOSO CONTEXTO ⬇⬇⬇
            $usuarioId = Auth::id()
                ?? $cuenta->usuario_responsable_id
                ?? 1; // fallback al usuario 1 (asegúrate que exista)

            $pago = Pago::create([
                'cuenta_id'      => $cuenta->id,
                'usuario_id'     => $usuarioId,
                'numero_recibo'  => $numeroRecibo,
                'fecha_pago'     => $data['fecha_pago'],
                'monto_total'    => $data['monto_total'],
                'monto_interes'  => $aplicaInteres,
                'monto_comision' => $aplicaComision,
                'monto_capital'  => $aplicaCapital,
                'forma_pago'     => $data['forma_pago'] ?? null,
                'observaciones'  => $data['observaciones'] ?? null,
            ]);

            BitacoraAccion::create([
                'user_id'    => $usuarioId,
                'accion'     => 'REGISTRAR_PAGO',
                'entidad'    => 'Pago',
                'entidad_id' => $pago->id,
                'detalle'    => 'Pago registrado para cuenta ID ' . $cuenta->id,
            ]);

            return redirect()->route('cuentas.show', $cuenta)
                ->with('success', 'Pago registrado correctamente.');
        });
    }

    // CALCULA INTERESES CORRIENTES (versión simple)
    protected function actualizarInteresesCorrientes(CuentaPorCobrar $cuenta, string $fechaPago): void
    {
        $politica  = $cuenta->politica;
        $inicio    = Carbon::parse($cuenta->fecha_inicio);
        $fechaPago = Carbon::parse($fechaPago);

        if ($fechaPago->lessThanOrEqualTo($inicio)) {
            return;
        }

        $diasTranscurridos = $inicio->diffInDays($fechaPago);

        $tasaDiaria = ($politica->tasa_interes_anual / 100) / 365;

        $interesGenerado = $cuenta->monto_capital_actual * $tasaDiaria * $diasTranscurridos;

        $cuenta->intereses_acumulados += round($interesGenerado, 2);
    }
}
