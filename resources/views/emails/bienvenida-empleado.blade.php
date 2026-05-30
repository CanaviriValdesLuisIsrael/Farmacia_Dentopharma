@component('mail::message')
# Bienvenido, {{ $nombreEmpleado }}

Has sido registrado en el sistema de **Farmacia Dentopharma**.

Estas son tus credenciales para ingresar:

| Campo | Valor |
|-------|-------|
| **Correo** | {{ $emailEmpleado }} |
| **Contraseña** | {{ $passwordTemporal }} |

@component('mail::button', ['url' => config('app.url')])
Ingresar al sistema
@endcomponent

> Por seguridad, te recomendamos cambiar tu contraseña luego de iniciar sesión.

Gracias,
**Farmacia Dentopharma**
@endcomponent