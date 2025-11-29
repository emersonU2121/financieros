@php
    $p = $politica ?? null;
@endphp

<div class="form-grid-2col">

    {{-- Nombre --}}
    <div class="form-group">
        <label for="nombre">Nombre de la política</label>
        <input
            id="nombre"
            type="text"
            name="nombre"
            class="input"
            placeholder="Ej.: Crédito 30 días, Crédito 60 días"
            value="{{ old('nombre', $p->nombre ?? '') }}"
            required
        >
        @error('nombre')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>

    {{-- Plazo días --}}
    <div class="form-group">
        <label for="plazo_dias">Plazo (días)</label>
        <input
            id="plazo_dias"
            type="number"
            name="plazo_dias"
            class="input"
            placeholder="Ej.: 30, 60, 90"
            value="{{ old('plazo_dias', $p->plazo_dias ?? '') }}"
            required
        >
        @error('plazo_dias')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>

    {{-- Interés anual --}}
    <div class="form-group">
        <label for="tasa_interes_anual">Tasa de interés anual (%)</label>
        <input
            id="tasa_interes_anual"
            type="number"
            step="0.01"
            name="tasa_interes_anual"
            class="input"
            placeholder="Ej.: 10"
            value="{{ old('tasa_interes_anual', $p->tasa_interes_anual ?? '') }}"
            required
        >
        @error('tasa_interes_anual')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>

    {{-- Mora anual --}}
    <div class="form-group">
        <label for="tasa_mora_anual">Tasa de mora anual (%)</label>
        <input
            id="tasa_mora_anual"
            type="number"
            step="0.01"
            name="tasa_mora_anual"
            class="input"
            placeholder="Ej.: 12"
            value="{{ old('tasa_mora_anual', $p->tasa_mora_anual ?? 0) }}"
        >
        @error('tasa_mora_anual')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>

    {{-- Comisión inicial --}}
    <div class="form-group">
        <label for="comision_inicial">Comisión sobre el crédito (%)</label>
        <input
            id="comision_inicial"
            type="number"
            step="0.01"
            name="comision_inicial"
            class="input"
            placeholder="Ej.: 2.5"
            value="{{ old('comision_inicial', $p->comision_inicial ?? 0) }}"
        >
        @error('comision_inicial')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>

    {{-- Días de gracia --}}
    <div class="form-group">
        <label for="dias_gracia">Días de gracia</label>
        <input
            id="dias_gracia"
            type="number"
            name="dias_gracia"
            class="input"
            placeholder="Ej.: 5"
            value="{{ old('dias_gracia', $p->dias_gracia ?? 0) }}"
        >
        @error('dias_gracia')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>

    {{-- Días para mora --}}
    <div class="form-group">
        <label for="dias_para_mora">Días para considerar mora</label>
        <input
            id="dias_para_mora"
            type="number"
            name="dias_para_mora"
            class="input"
            placeholder="Ej.: 30"
            value="{{ old('dias_para_mora', $p->dias_para_mora ?? 0) }}"
        >
        @error('dias_para_mora')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>

    {{-- Días para incobrable --}}
    <div class="form-group">
        <label for="dias_para_incobrable">Días para considerar incobrable</label>
        <input
            id="dias_para_incobrable"
            type="number"
            name="dias_para_incobrable"
            class="input"
            placeholder="Ej.: 90"
            value="{{ old('dias_para_incobrable', $p->dias_para_incobrable ?? 90) }}"
        >
        @error('dias_para_incobrable')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>

    {{-- Fiador --}}
    <div class="form-group form-group-full">
        <label class="checkbox-label" style="font-size:0.86rem; color:var(--text-main); display:flex; align-items:center; gap:0.45rem;">
            <input
                type="checkbox"
                name="requiere_fiador"
                value="1"
                {{ old('requiere_fiador', $p->requiere_fiador ?? false) ? 'checked' : '' }}
                style="width:16px;height:16px;"
            >
            Requiere fiador para otorgar este crédito
        </label>
    </div>

</div>

<div class="form-actions">
    <button type="submit" class="btn btn-primary">
        {{ $p ? 'Guardar cambios' : 'Guardar política' }}
    </button>
    <a href="{{ route('politicas.index') }}" class="btn btn-light">Cancelar</a>
</div>
