// =====================================================
//  CRUD DE USUARIOS
//  Secciones: 
//      1. Registrar
//      2. Consultar
//      3. Actualizar
//      4. Eliminar
//      5. Validaciones
//      6. Funciones reutilizables
// =====================================================

document.addEventListener("DOMContentLoaded", function () {

    // ---- 1. Registrar ----
    configurarRegistroUsuarios();

    // ---- 2. Consultar (próximo) ----
    // configurarConsultaUsuarios();

    // ---- 3. Actualizar (próximo) ----
    // configurarActualizacionUsuarios();

    // ---- 4. Eliminar (próximo) ----
    // configurarEliminacionUsuarios();

});


/* =====================================================
   1. REGISTRAR (CREATE)
   ===================================================== */

function configurarRegistroUsuarios() {

    const formulario = document.getElementById("formRegistroUsuarios");
    if (!formulario) return; // Si no existe, no hacer nada

    formulario.addEventListener("submit", function (e) {
        e.preventDefault();

        // Validación global antes de enviar
        if (!validateForm()) return;

        confirmar("¿Registrar Usuario?", "¿Deseas continuar?")
            .then(r => {
                if (!r.isConfirmed) return;

                apiRequestUsuarios("registrar", formulario)
                    .then(r => r.text())
                    .then(resp => manejarRespuestaCRUD(
                        resp,
                        "Usuario registrado correctamente.",
                        "index.php"
                    ))
                    .catch(() => alerta("Error", "Ocurrió un error al registrar el usuario.", "error"));
            });
    });
}



/* =====================================================
   2. CONSULTAR (READ)
   ===================================================== */

// FUTURO — estructura lista para avanzar como personal.js
function configurarConsultaUsuarios() {
    // Similar a consultarPersonal()
}



/* =====================================================
   3. ACTUALIZAR (UPDATE)
   ===================================================== */

function configurarActualizacionUsuarios() {
    // Base lista, se llenará cuando implementes la vista
}



/* =====================================================
   4. ELIMINAR (DELETE)
   ===================================================== */

function configurarEliminacionUsuarios() {
    // Base lista, igual que en personal.js
}



/* =====================================================
   5. VALIDACIONES DE FORMULARIO (SE MANTIENEN)
   ===================================================== */

// Referencias
const inputs = {
    nombre_usuario: document.getElementById('nombre_usuario'),
    email: document.getElementById('email'),
    clave_personal: document.getElementById('clave_personal'),
    password: document.getElementById('password'),
    confirm_password: document.getElementById('confirm_password')
};

// Helpers
function showError(input, message) {
    let errorDiv = input.parentElement.querySelector('.invalid-feedback');

    if (!errorDiv) {
        errorDiv = document.createElement('div');
        errorDiv.classList.add('invalid-feedback');
        input.parentElement.appendChild(errorDiv);
    }

    errorDiv.textContent = message;
    errorDiv.style.display = "block";
    input.classList.add('is-invalid');
}

function clearError(input) {
    let errorDiv = input.parentElement.querySelector('.invalid-feedback');
    if (errorDiv) {
        errorDiv.textContent = "";
        errorDiv.style.display = "none";
    }
    input.classList.remove('is-invalid');
}

// Validaciones individuales
function checkNombreUsuario() {
    const val = inputs.nombre_usuario.value.trim();
    clearError(inputs.nombre_usuario);

    if (!val) return showError(inputs.nombre_usuario, "El nombre de usuario es obligatorio");

    if (!/^[A-Z][a-zA-Z0-9]{4,20}$/.test(val))
        return showError(inputs.nombre_usuario, "Solo letras o números (3-20 caracteres), primera letra mayúscula");
}

function checkEmail() {
    const val = inputs.email.value.trim();
    clearError(inputs.email);

    if (!val) return showError(inputs.email, "El correo es obligatorio");

    const reg = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!reg.test(val)) return showError(inputs.email, "Correo electrónico inválido");
}

function checkClavePersonal() {
    const val = inputs.clave_personal.value.trim();
    clearError(inputs.clave_personal);

    if (!val) return showError(inputs.clave_personal, "La clave personal es obligatoria");

    if (!/^[a-zA-Z0-9]{18,20}$/.test(val))
        return showError(inputs.clave_personal, "Debe contener letras o números (18-20 caracteres)");
}

function checkPassword() {
    const val = inputs.password.value;
    clearError(inputs.password);

    if (!val) return showError(inputs.password, "La contraseña es obligatoria");

    const rules = {
        min: val.length >= 8,
        upper: /[A-Z]/.test(val),
        lower: /[a-z]/.test(val),
        number: /[0-9]/.test(val),
        special: /[@$!%*?&]/.test(val)
    };

    const errors = [];
    if (!rules.min) errors.push("8 caracteres");
    if (!rules.upper) errors.push("una mayúscula");
    if (!rules.lower) errors.push("una minúscula");
    if (!rules.number) errors.push("un número");
    if (!rules.special) errors.push("un símbolo especial");

    if (errors.length)
        return showError(inputs.password, "Debe contener: " + errors.join(", "));
}

function checkConfirmPassword() {
    clearError(inputs.confirm_password);

    if (!inputs.confirm_password.value)
        return showError(inputs.confirm_password, "Debe confirmar la contraseña");

    if (inputs.confirm_password.value !== inputs.password.value)
        return showError(inputs.confirm_password, "Las contraseñas no coinciden");
}

// Validación global
function validateForm() {
    checkNombreUsuario();
    checkEmail();
    checkClavePersonal();
    checkPassword();
    checkConfirmPassword();

    return !document.querySelector('.is-invalid');
}

// Eventos en tiempo real
if (inputs.nombre_usuario) inputs.nombre_usuario.addEventListener("input", checkNombreUsuario);
if (inputs.email) inputs.email.addEventListener("input", checkEmail);
if (inputs.clave_personal) inputs.clave_personal.addEventListener("input", checkClavePersonal);
if (inputs.password) inputs.password.addEventListener("input", checkPassword);
if (inputs.confirm_password) inputs.confirm_password.addEventListener("input", checkConfirmPassword);



/* =====================================================
   6. FUNCIONES REUTILIZABLES
   ===================================================== */

function apiRequestUsuarios(accion, datos = null) {

    const formData = datos instanceof HTMLFormElement
        ? new FormData(datos)
        : new FormData();

    if (datos && !(datos instanceof HTMLFormElement)) {
        for (const clave in datos) {
            formData.append(clave, datos[clave]);
        }
    }

    formData.append("action", accion);

    return fetch('/ajax/usuarios-ajax.php', {
        method: "POST",
        body: formData
    });
}

/**
 * Maneja respuestas del backend para cualquier operación del CRUD.
 */
function manejarRespuestaCRUD(respuesta, mensajeExito, redireccion = null) {

    if (respuesta.trim() === "OK") {
        alerta("Éxito", mensajeExito, "success")
            .then(() => {
                if (redireccion) window.location.href = redireccion;
            });

    } else {
        alerta("Error", respuesta, "error");
    }
}
