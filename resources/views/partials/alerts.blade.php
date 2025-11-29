@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="alert alert-error">
        {{ session('error') }}
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-error">
        <strong>Se encontraron errores:</strong>
        <ul style="margin-top: 0.25rem; padding-left: 1.1rem;">
            @foreach ($errors->all() as $error)
                <li style="font-size: 0.8rem;">{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
