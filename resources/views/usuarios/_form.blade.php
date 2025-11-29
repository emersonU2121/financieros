@csrf

<div class="form-grid">
    {{-- Nombre --}}
    <div class="form-group">
        <label for="name">Nombre completo</label>
        <input type="text" name="name" id="name" class="input"
               value="{{ old('name', $usuario->name ?? '') }}" required>
    </div>

    {{-- Email --}}
    <div class="form-group">
        <label for="email">Correo electrónico</label>
        <input type="email" name="email" id="email" class="input"
               value="{{ old('email', $usuario->email ?? '') }}" required>
    </div>

    {{-- Rol --}}
    <div class="form-group">
        <label for="role_id">Rol</label>
        <select name="role_id" id="role_id" class="input">
            <option value="">Sin rol asignado</option>
            @foreach($roles as $rol)
                <option value="{{ $rol->id }}"
                    {{ old('role_id', $usuario->role_id ?? null) == $rol->id ? 'selected' : '' }}>
                    {{ $rol->nombre }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- Activo --}}
  <div class="form-group" style="align-self: center;">
    <label for="activo">
        <input type="hidden" name="activo" value="0">
        <input type="checkbox" id="activo" name="activo" value="1"
               {{ old('activo', $usuario->activo ?? true) ? 'checked' : '' }}>
        Usuario activo
    </label>
</div>


    {{-- Password --}}
    <div class="form-group">
        <label for="password">
            @if(isset($usuario))
                Nueva contraseña (opcional)
            @else
                Contraseña
            @endif
        </label>
        <input type="password" name="password" id="password" class="input">
    </div>

    {{-- Confirmación --}}
    <div class="form-group">
        <label for="password_confirmation">Confirmar contraseña</label>
        <input type="password" name="password_confirmation"
               id="password_confirmation" class="input">
    </div>
</div>

<div class="form-actions" style="margin-top: 1.2rem;">
    <a href="{{ route('usuarios.index') }}" class="btn btn-light">Cancelar</a>
    <button type="submit" class="btn btn-primary">
        {{ $textoBoton ?? 'Guardar' }}
    </button>
</div>
