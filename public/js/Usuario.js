document.addEventListener("DOMContentLoaded", function () {
    // Seleccionamos los elementos dentro del formulario de edición
    const form = document.getElementById("formGuardar"); // tu formulario
    const btnEditar = document.getElementById("btnEditar");// botón Editar
    const btnGuardar = document.getElementById("btnGuardardatos"); // botón Guardar
    const inputs = form.querySelectorAll("input, textarea"); // solo los campos dentro del formulario

    // Guardamos los valores originales para luego restaurarlos al editar
    const valoresOriginales = {};
    inputs.forEach(input => {
        valoresOriginales[input.name] = input.value;
        input.value = ""; // vaciamos al inicio
        input.disabled = true; // deshabilitamos
    });

    // Desactivamos el botón Guardar al cargar
    btnGuardar.disabled = true;
    btnGuardar.style.opacity = "0.5";
    btnGuardar.style.cursor = "not-allowed";

    // Evento para activar edición
    btnEditar.addEventListener("click", function () {
        // Habilitamos inputs
        inputs.forEach(input => {
            input.disabled = false;
            input.value = valoresOriginales[input.name]; // restauramos los valores reales
        });

        // Habilitamos botón Guardar
        btnGuardar.disabled = false;
        btnGuardar.style.opacity = "1";
        btnGuardar.style.cursor = "pointer";

        // Cambiamos el estilo o texto del botón Editar (opcional)
        btnEditar.textContent = "Editando...";
        btnEditar.classList.remove("bg-gradient-primary");
        btnEditar.classList.add("bg-gradient-warning");
    });

    //tiempo en el que aparece el mensaje en el modelo de cambiar contrasenia
    const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
        // Aplica la clase animada
        alert.classList.add('alert-slide');

        // Espera 3 segundos antes de comenzar el deslizamiento
        setTimeout(() => {
        alert.classList.add('hide');
        // Luego de la animación, elimina el elemento
        setTimeout(() => alert.remove(), 500);
        }, 3000);
        });

     

});
