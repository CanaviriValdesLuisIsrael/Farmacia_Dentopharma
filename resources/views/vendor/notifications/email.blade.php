@component('mail::message')
# Hola,

Recibiste este correo porque solicitaste restablecer la contraseña de tu cuenta en **Farmacia Dentopharma**.

@component('mail::button', ['url' => $actionUrl])
Restablecer Contraseña
@endcomponent

Este enlace expirará en **60 minutos**.

Si no solicitaste restablecer tu contraseña, ignora este mensaje.

Saludos,
**Farmacia Dentopharma**
@endcomponent