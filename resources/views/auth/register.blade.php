
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

    <!-- Tu CSS personalizado -->
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>

<!-- ESTA VISTA  SERVIRA PARA CUANDO SE NECESITE REGISTRAR USUARIOS-->

<body>

    <div class="container-fluid vh-100 d-flex align-items-center justify-content-center">
        
            <!-- Columna imagen -->
            

            <!-- Columna formulario -->
            <div class="col-12 col-md-6 d-flex justify-content-center align-items-center">
                <div class="contenido_login">
                    @include('partials.message') 
             <form action="/register" method="POST" class="p-4 shadow-lg bg-white rounded-4">
                @csrf
                <h2>Farmacia Dentopharma</h2>
                <div class="input-div name">
                    <div class="i">
                        <i class="fas fa-lock"></i>
                    </div>
                    <div class="div">
                        <h5>Nombre</h5>
                        <input type="text" name="name" class="input" required>
                    </div>
                </div>
                <div class="input-div dni">
                    <div class="i">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="div">
                        <h5>Email</h5>
                        <input type="email" name="email" id="enail" class="input" required>
                    </div>
                </div>

                <div class="input-div pass">
                    <div class="i">
                        <i class="fas fa-lock"></i>
                    </div>
                    <div class="div">
                        <h5>Contrasena</h5>
                        <input type="password" name="password" class="input" required>

                    </div>
                </div>
                <div class="input-div pass_confirmar">
                    <div class="i">
                        <i class="fas fa-lock"></i>
                    </div>
                    <div class="div">
                        <h5>Confirmar Contrasena</h5>
                        <input type="password" name="password_confirmation" class="input" required>

                    </div>
                </div>

                
                <button type="submit" class="btn">crear cuenta</button>
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








 