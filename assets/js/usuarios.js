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

    // Solo activar registro si existe el formulario
    if (document.getElementById("formRegistroUsuarios")) {
        configurarRegistroUsuarios();
    }

    // Solo activar consulta si existe el formulario
    if (document.getElementById("formConsultaUsuarios")) {
        configurarConsultaUsuarios();
    }
        if (document.querySelector('.form-control-custom')) {
        configurarEliminacionUsuarios();
    }


});



/* =====================================================
   1. REGISTRAR (CREATE)
   ===================================================== */

function configurarRegistroUsuarios() {

    const formulario = document.getElementById("formRegistroUsuarios");
    if (!formulario) return;

    formulario.addEventListener("submit", function (e) {
        e.preventDefault();

        // Validación global
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

function configurarConsultaUsuarios() {

    const formConsulta = document.getElementById("formConsultaUsuarios");
    const inputCriterio = document.getElementById("clave_usuario");
    const tabla = document.getElementById("tablaUsuarios");
    const btnVolver = document.getElementById("btnVolver");

    const tdUsuario = document.getElementById("td_usuario");
    const tdNombre = document.getElementById("td_nombre");
    const tdCorreo = document.getElementById("td_correo");
    const tdClave = document.getElementById("td_clave");

    if (!formConsulta) return;

    formConsulta.addEventListener("submit", function (e) {
        e.preventDefault();

        const criterio = inputCriterio.value.trim();

        if (criterio === "") {
            alerta("Consulta", "Debes ingresar una clave de personal (CURP).", "warning");
            return;
        }

        apiRequestUsuarios("consultar-usuario", { criterio })
            /*.then(r => r.text())
            .then(texto => {
                console.log("RESPUESTA CRUDA DEL SERVIDOR:");
                console.log(texto);
            })*/
        .then(r => r.json())
            .then(data => {

                if (!data || data.error) {
                    alerta("Sin resultados", "No se encontró ningún usuario con esa clave de personal.", "warning");
                    return;
                }

                tdUsuario.textContent = data.usuario;
                tdNombre.textContent = data.nombre_completo;
                tdCorreo.textContent = data.correo;
                tdClave.textContent = data.clave_personal;

                // Mostrar tabla
                tabla.style.display = "block";

                // Ocultar formulario
                formConsulta.parentElement.style.display = "none";
            });
    });

    if (btnVolver) {
        btnVolver.addEventListener("click", function () {
            tabla.style.display = "none";
            formConsulta.parentElement.style.display = "block";
            inputCriterio.value = "";
        });
    }
}



/* =====================================================
   5. VALIDACIONES DE FORMULARIO
   ===================================================== */

const inputs = {
    nombre_usuario: document.getElementById('nombre_usuario'),
    email: document.getElementById('email'),
    clave_personal: document.getElementById('clave_personal'),
    password: document.getElementById('password'),
    confirm_password: document.getElementById('confirm_password')
};

// Helpers
function showError(input, message) {
    if (!input) return;

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
    if (!input) return;

    let errorDiv = input.parentElement.querySelector('.invalid-feedback');
    if (errorDiv) {
        errorDiv.textContent = "";
        errorDiv.style.display = "none";
    }
    input.classList.remove('is-invalid');
}

// Validaciones individuales
function checkNombreUsuario() {
    if (!inputs.nombre_usuario) return;

    const val = inputs.nombre_usuario.value.trim();
    clearError(inputs.nombre_usuario);

    if (!val) return showError(inputs.nombre_usuario, "El nombre de usuario es obligatorio");

    if (!/^[A-Z][a-zA-Z0-9]{4,20}$/.test(val))
        return showError(inputs.nombre_usuario, "Debe iniciar con mayúscula y tener 5-20 caracteres");
}

function checkEmail() {
    if (!inputs.email) return;

    const val = inputs.email.value.trim();
    clearError(inputs.email);

    if (!val) return showError(inputs.email, "El correo es obligatorio");

    const reg = /^[^\\s@]+@[^\\s@]+\\.[^\\s@]+$/;
    if (!reg.test(val)) return showError(inputs.email, "Correo inválido");
}

function checkClavePersonal() {
    if (!inputs.clave_personal) return;

    const val = inputs.clave_personal.value.trim();
    clearError(inputs.clave_personal);

    if (!val) return showError(inputs.clave_personal, "La clave personal (CURP) es obligatoria");

    if (!/^[A-Za-z0-9]{18}$/.test(val))
        return showError(inputs.clave_personal, "La CURP debe tener exactamente 18 caracteres");
}

function checkPassword() {
    if (!inputs.password) return;

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

    const fails = [];
    for (const rule in rules) {
        if (!rules[rule]) fails.push(rule);
    }

    if (fails.length)
        return showError(inputs.password, "Debe contener: mayúscula, minúscula, número, símbolo y mínimo 8 caracteres");
}

function checkConfirmPassword() {
    if (!inputs.confirm_password) return;

    clearError(inputs.confirm_password);

    if (!inputs.confirm_password.value)
        return showError(inputs.confirm_password, "Debes confirmar la contraseña");

    if (inputs.confirm_password.value !== inputs.password.value)
        return showError(inputs.confirm_password, "Las contraseñas no coinciden");
}

// Validación global
function validateForm() {

    if (inputs.nombre_usuario) checkNombreUsuario();
    if (inputs.email) checkEmail();
    if (inputs.clave_personal) checkClavePersonal();
    if (inputs.password) checkPassword();
    if (inputs.confirm_password) checkConfirmPassword();

    return !document.querySelector('.is-invalid');
}

// Eventos con protección
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
function configurarEliminacionUsuarios() {
    const inputCurp = document.querySelector('.form-control-custom');
    const btnBuscar = document.querySelector('.btn-custom');
    
    if (!btnBuscar) return;
    
    btnBuscar.addEventListener("click", function () {
        const curp = inputCurp.value.trim();
        
        if (curp === "") {
            alerta("Eliminación", "Debes ingresar una CURP.", "warning");
            return;
        }
        
        confirmar("¿Eliminar Usuario?", "Esta acción no se puede deshacer. ¿Deseas continuar?")
            .then(r => {
                if (!r.isConfirmed) return;
                
                apiRequestUsuarios("eliminar", { curp })
                    .then(r => r.text())
                    .then(resp => manejarRespuestaCRUD(
                        resp,
                        "Usuario eliminado correctamente.",
                        "index.php"
                    ))
                    .catch(() => alerta("Error", "Ocurrió un error al eliminar el usuario.", "error"));
            });
    });
}