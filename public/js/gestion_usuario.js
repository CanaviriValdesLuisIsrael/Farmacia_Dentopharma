$(document).ready(function () {
    buscar_empleado();

    $("#buscarusuario").keyup(function () {
        let valor = $(this).val();
        buscar_empleado(valor);
    });
});

// 🔥 CREAR USUARIO
$(document).on("submit", "#form-crear", function (e) {
    e.preventDefault();

    let form = $(this);

    $.ajax({
        url: form.attr("action"),
        method: "POST",
        data: form.serialize(),
        success: function (response) {
            mostrarMensaje(response.mensaje, "success");
            form[0].reset();
            buscar_empleado();
        },
        error: function (xhr) {
            let mensaje = xhr.responseJSON?.mensaje || "Error al crear usuario";
            mostrarMensaje(mensaje, "error");
        }
    });
});

//  ELIMINAR USUARIO
$(document).on("submit", "#eliminaempleado", function (e) {
    e.preventDefault();

    let form = $(this);

    $.ajax({
        url: form.attr("action"),
        method: "POST",
        data: form.serialize(),

        success: function (response) {
            mostrarMensajeModal(response.mensaje, "success");
            //  limpiar contraseña
            form[0].reset();
            //  recargar lista
            buscar_empleado();

            //  NO cerrar modal automáticamente
        },

        error: function (xhr) {
            let mensaje = xhr.responseJSON?.mensaje || "Error al eliminar";
            mostrarMensajeModal(mensaje, "error");
            //  limpiar contraseña SIEMPRE
            form[0].reset();
        }
    });
});

function mostrarMensajeModal(texto, tipo = "success") {
    let clase = tipo === "success" ? "alert-success" : "alert-danger";

    let mensaje = `
        <div class="alert ${clase} fade-custom show text-center">
            ${texto}
        </div>
    `;

    $("#mensaje-eliminar").html(mensaje);

    setTimeout(() => {
        $(".fade-custom").addClass("hide");

        setTimeout(() => {
            $("#mensaje-eliminar").html("");
        }, 800);
    }, 2000);
}

// 🔥 CAPTURAR ID
$(document).on("click", ".borrar_usuario", function () {
    let userId = $(this).data("id");
    $("#user_id_eliminar").val(userId);
});

// 🔥 BUSCAR EMPLEADOS
function buscar_empleado(valor = "") {
    $.get("/admin/buscar-empleado", { buscar: valor }, function (response) {
        let template = "";

        response.forEach((emp) => {
            let avatarHTML = emp.user && emp.user.avatar
                ? `<img src="/storage/avatars/${emp.user.avatar}" class="img-fluid rounded-circle mb-2" width="60">`
                : "";

            let rol = emp.user?.role?.name || "";
            let email = emp.user?.email || "";
            let telefono = emp.nro_contacto || "";

            let botonEliminar = "";

            if (usuarioAuth.role === "admin" && emp.user && emp.user.id != usuarioAuth.id) {
                botonEliminar = `
                    <button class="borrar_usuario btn btn-danger"
                        data-id="${emp.user.id}"
                        data-toggle="modal"
                        data-target="#eliminar_usuario">
                        <i class="fas fa-window-close mr-1"></i>Eliminar
                    </button>
                `;
            }

            template += `
                <div class="col-md-4">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <div class="card-header text-muted border-bottom-0">
                                ${rol}
                            </div>

                            ${avatarHTML}

                            <h5>${emp.nombre} ${emp.apellido}</h5>

                            <p class="text-muted">
                                ${email}<br>
                                ${telefono}
                            </p>

                            <p>CI: ${emp.ci}</p>

                            ${botonEliminar}
                        </div>
                    </div>
                </div>
            `;
        });

        $("#empleados").html(template);
    });
}

// 🔥 MENSAJES ELEGANTES
function mostrarMensaje(texto, tipo = "success") {
    let clase = tipo === "success" ? "alert-success" : "alert-danger";

    let mensaje = `
        <div class="alert ${clase} fade-custom show text-center">
            ${texto}
        </div>
    `;

    $("#mensaje-global").html(mensaje);

    setTimeout(() => {
        $(".fade-custom").addClass("hide");

        setTimeout(() => {
            $("#mensaje-global").html("");
        }, 900);
    }, 2000);
}