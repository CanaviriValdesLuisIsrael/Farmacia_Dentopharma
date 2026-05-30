<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Farmacia Dentopharma</title>

    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome (iconos) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!--  CSS personalizado -->
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>

<body>

    <div class="container-fluid vh-100 d-flex align-items-center justify-content-center">
        <div class="row w-100">

            <!-- Columna imagen -->
            <!--
            <div class="col-md-6 d-none d-md-flex justify-content-end align-items-center">
                <img src="{{ asset('img/undraw_doctors_djoj.svg') }}" class="img-fluid" alt="Ilustración">
            </div>

            ´´ Columna formulario
            <div class="col-12 col-md-6 d-flex justify-content-center align-items-center justify-content-center">
-->
            <div class="container-fluid vh-100 d-flex align-items-center justify-content-center">
                <div class="contenido_login">
                    <form action="/" method="POST" class="p-4 shadow-lg bg-white rounded-4">
                        @csrf
                        <img src="{{ asset('img/logofar.png') }}" class="mb-3" alt="Logo">
                        <h2>Farmacia Dentopharma</h2>

                        <div class="input-div dni">
                            <div class="i">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="divi">
                                <h5>Usuario</h5>
                                <input type="email" name="email" id="email" class="input" required>
                            </div>
                        </div>

                        <div class="input-div pass">
                            <div class="i">
                                <i class="fas fa-lock"></i>
                            </div>
                            <div class="div">
                                <h5>Contraseña</h5>
                                <input type="password" name="password" class="input " required>
                            </div>
                        </div>
                        <!-- PARA REGISTRAR CLIENTES,USUARIOS , PARA UN SIGUIENTE MODULO -->
                        <!--
                        <p class="text-end">¿No tienes cuenta? <a href="/register">Registro</a></p>
-->
                        <button type="submit" class="btn">Entrar</button>
                        <p class="text-center mt-2" style="font-size:0.85rem;">
                            <a href="/forgot-password">¿Olvidaste tu contraseña?</a>
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->


    <!-- Tu JS -->
    <script src="{{ asset('js/login.js') }}"></script>
</body>

</html>
