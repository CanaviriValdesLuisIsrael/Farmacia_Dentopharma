<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recuperar Contraseña - Dentopharma</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    
</head>
<body>
<div class="container-fluid vh-100 d-flex align-items-center justify-content-center">
    <div class="contenido_login">
        <form action="/forgot-password" method="POST" class="p-4 shadow-lg bg-white rounded-4">
            @csrf
            <img src="{{ asset('img/logofar.png') }}" class="mb-3" alt="Logo">
            <h2>Recuperar Contraseña</h2>
            <p class="text-muted" style="font-size:0.9rem;">
                Ingresa tu correo y te enviaremos un enlace para restablecer tu contraseña.
            </p>

            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            @if ($errors->has('email'))
                <div class="alert alert-danger">{{ $errors->first('email') }}</div>
            @endif

            <div class="input-div dni">
                <div class="i"><i class="fas fa-envelope"></i></div>
                <div class="divi">
                    <h5>Correo electrónico</h5>
                    <input type="email" name="email" class="input" required>
                </div>
            </div>

            <button type="submit" class="btn">Enviar enlace</button>
            <p class="text-center mt-3" style="font-size:0.85rem;">
                <a href="/">← Volver al login</a>
            </p>
        </form>
    </div>
</div>
<script src="{{ asset('js/login.js') }}"></script>
</body>
</html>