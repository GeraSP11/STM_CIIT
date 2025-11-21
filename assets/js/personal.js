document.addEventListener("DOMContentLoaded", function () {
    const form = document.querySelector("form");
    const selectLocalidad = document.getElementById("afiliacion_laboral");

    if (!form) {
        console.error("No se encontró el formulario");
        return;
    }

    // --- Cargar localidades vía AJAX ---
    const formData = new FormData();
    formData.append('action', 'obtener-localidades');

    fetch('/ajax/personal-ajax.php', {
        method: "POST",
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            // Limpiar opciones existentes excepto la primera
            selectLocalidad.length = 1;

            data.forEach(loc => {
                const option = document.createElement("option");
                option.value = loc.id_localidad;      // FK
                option.textContent = loc.nombre_centro_trabajo; // nombre que aparece
                selectLocalidad.appendChild(option);
            });

        })
        .catch(err => console.error("Error al cargar localidades:", err));
    // --- Configurar submit del formulario ---
    form.addEventListener("submit", function (e) {
        e.preventDefault();

        confirmar("¿Registrar Personal?", "¿Deseas continuar con el registro?")
            .then(result => {
                if (!result.isConfirmed) return;

                const formData = new FormData(form);
                formData.append("action", "registrar");

                fetch('/ajax/personal-ajax.php', {
                    method: "POST",
                    body: formData
                })
                    .then(r => r.text())
                    .then(resp => {
                        if (resp.trim() === "OK") {
                            alerta("Éxito", "Personal registrado correctamente.", "success")
                                .then(() => window.location.href = "dashboard.php");
                        } else {
                            alerta("Error", resp, "error");
                        }
                    })
                    .catch(() => {
                        alerta("Error", "Ocurrió un problema en la petición.", "error");
                    });
            });
    });
});
