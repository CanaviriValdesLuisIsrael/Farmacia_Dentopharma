<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nueva Contraseña - Dentopharma</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body>
<div class="container-fluid vh-100 d-flex align-items-center justify-content-center">
    <div class="contenido_login">
        <form action="/reset-password" method="POST" class="p-4 shadow-lg bg-white rounded-4">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <img src="{{ asset('img/logofar.png') }}" class="mb-3" alt="Logo">
            <h2>Nueva Contraseña</h2>

            @if ($errors->any())
                <div class="alert alert-danger">{{ $errors->first() }}</div>
            @endif

            <div class="input-div dni">
                <div class="i"><i class="fas fa-envelope"></i></div>
                <div class="divi">
                    <h5>Tu correo electrónico</h5>
                    <input type="email" name="email" class="input" required>
                </div>
            </div>

            <div class="input-div pass">
                <div class="i"><i class="fas fa-lock"></i></div>
                <div class="div">
                    <h5>Nueva contraseña</h5>
                    <input type="password" name="password" class="input" required>
                </div>
            </div>

            <div class="input-div pass">
                <div class="i"><i class="fas fa-lock"></i></div>
                <div class="div">
                    <h5>Confirmar contraseña</h5>
                    <input type="password" name="password_confirmation" class="input" required>
                </div>
            </div>

            <button type="submit" class="btn">Cambiar contraseña</button>
        </form>
    </div>
</div>
<script src="{{ asset('js/login.js') }}"></script>
</body>
</html>