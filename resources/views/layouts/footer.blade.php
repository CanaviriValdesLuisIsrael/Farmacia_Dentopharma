  <footer class="main-footer">
    <div class="float-right d-none d-sm-block">
      <b>2026</b>
    </div>
    <strong> <a href="#">Farmacia Dentopharma</a>.</strong>
  </footer>

  <aside class="control-sidebar control-sidebar-dark"></aside>
</div>
<!-- ./wrapper -->

<!-- jQuery -->
<script src="{{ asset('js/jquery.min.js') }}"></script>
<!-- Bootstrap 4 -->
<script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
<!-- AdminLTE App -->
<script src="{{ asset('js/adminlte.min.js') }}"></script>
<script src="{{ asset('js/demo.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

{{-- ============================================================ --}}
{{-- CONTROL DE SESIÓN: alerta de expiración                     --}}
{{-- SESSION_LIFETIME está configurado en .env (minutos)          --}}
{{-- ============================================================ --}}
<script>
(function () {
    // Tiempo total de sesión en minutos (debe coincidir con SESSION_LIFETIME en .env)
    var SESSION_MINUTES = {{ config('session.lifetime', 120) }};
    // Avisar al usuario X minutos ANTES de que expire
    var WARN_BEFORE_MINUTES = 2;

    var warnMs  = (SESSION_MINUTES - WARN_BEFORE_MINUTES) * 60 * 1000;
    var expireMs = SESSION_MINUTES * 60 * 1000;

    var lastActivity = Date.now();
    var warnTimer, expireTimer;

    function resetTimers() {
        lastActivity = Date.now();
        clearTimeout(warnTimer);
        clearTimeout(expireTimer);

        warnTimer = setTimeout(function () {
            Swal.fire({
                title: '⚠️ Sesión por expirar',
                html: 'Tu sesión expirará en <b>' + WARN_BEFORE_MINUTES + ' minutos</b>.<br>¿Deseas mantenerla activa?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, mantener sesión',
                cancelButtonText: 'Cerrar sesión ahora',
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                timer: WARN_BEFORE_MINUTES * 60 * 1000,
                timerProgressBar: true,
            }).then(function (result) {
                if (result.isConfirmed) {
                    // Ping para refrescar la sesión en el servidor
                    $.get('/ventas/estadisticas').always(function () {
                        resetTimers();
                    });
                } else if (result.isDismissed && result.dismiss === Swal.DismissReason.timer) {
                    // Se acabó el tiempo de la alerta: cerrar sesión
                    cerrarSesion();
                } else {
                    cerrarSesion();
                }
            });
        }, warnMs);

        expireTimer = setTimeout(function () {
            cerrarSesion();
        }, expireMs);
    }

    function cerrarSesion() {
        Swal.fire({
            title: 'Sesión expirada',
            text: 'Tu sesión ha expirado por inactividad. Serás redirigido al inicio de sesión.',
            icon: 'info',
            allowOutsideClick: false,
            showConfirmButton: false,
            timer: 3000,
        }).then(function () {
            // POST logout
            $('<form method="POST" action="/logout">' +
              '<input type="hidden" name="_token" value="{{ csrf_token() }}">' +
              '</form>').appendTo('body').submit();
        });
    }

    // Reiniciar temporizador con cualquier interacción del usuario
    $(document).on('mousemove keydown click touchstart', function () {
        // Evitar reiniciar demasiado seguido (throttle a 30s)
        if (Date.now() - lastActivity > 30000) {
            resetTimers();
        }
    });

    // Iniciar al cargar
    resetTimers();
})();
</script>

</body>
</html>
