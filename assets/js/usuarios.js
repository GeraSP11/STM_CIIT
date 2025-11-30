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

    // Solo activar eliminación si existe el formulario específico
    if (document.getElementById("formEliminarUsuario")) {
        configurarEliminacionUsuarios();
    }

    // Solo activar actualización si existe el formulario específico
    if (document.getElementById("updateForm")) {
        configurarActualizacionUsuarios();
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
   3. ACTUALIZAR (UPDATE)
   ===================================================== */

function configurarActualizacionUsuarios() {
    const formActualizar = document.getElementById("updateForm");
    if (!formActualizar) return;
    
    const inputCurpBusqueda = document.getElementById("curp_busqueda");
    const btnBuscar = document.getElementById("btnBuscarUsuario");
    
    // Buscar con Enter
    if (inputCurpBusqueda) {
        inputCurpBusqueda.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                buscarUsuarioActualizar();
            }
        });
    }
    
    // Buscar con botón
    if (btnBuscar) {
        btnBuscar.addEventListener('click', function(e) {
            e.preventDefault();
            buscarUsuarioActualizar();
        });
    }
    
    // Submit del formulario
    formActualizar.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const password = document.getElementById('contrasena').value;
        const passwordConfirm = document.getElementById('contrasena_confirmar').value;
        
        // Validar contraseñas solo si se ingresó alguna
        if (password !== '' || passwordConfirm !== '') {
            if (password !== passwordConfirm) {
                alerta("Error", "Las contraseñas no coinciden", "error");
                return;
            }
            if (password.length < 6) {
                alerta("Error", "La contraseña debe tener al menos 6 caracteres", "error");
                return;
            }
        }
        
        // Pedir contraseña actual del usuario
        const result = await Swal.fire({
            title: 'Confirmar cambios',
            html: '<p>Para actualizar el usuario, ingrese la contraseña actual del usuario:</p>',
            input: 'password',
            inputLabel: 'Contraseña actual del usuario',
            inputPlaceholder: 'Ingrese la contraseña',
            inputAttributes: {
                autocapitalize: 'off',
                autocorrect: 'off'
            },
            showCancelButton: true,
            confirmButtonText: 'Actualizar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#5c2e3e',
            showLoaderOnConfirm: true,
            preConfirm: async (passwordActual) => {
                if (!passwordActual) {
                    Swal.showValidationMessage('Debe ingresar la contraseña actual');
                    return false;
                }
                
                try {
                    const response = await apiRequestUsuarios('verificar-password', {
                        id_usuario: document.getElementById('id_usuario').value,
                        password_actual: passwordActual
                    });
                    
                    const data = await response.json();
                    
                    if (data.error) {
                        Swal.showValidationMessage(data.message || 'Contraseña incorrecta');
                        return false;
                    }
                    
                    return true;
                } catch (error) {
                    console.error('Error:', error);
                    Swal.showValidationMessage('Error al verificar la contraseña');
                    return false;
                }
            },
            allowOutsideClick: () => !Swal.isLoading()
        });
        
        if (result.isConfirmed) {
            actualizarUsuarioFinal();
        }
    });
}

function actualizarUsuarioFinal() {
    // Crear FormData y agregar manualmente todos los campos necesarios
    const formData = new FormData();
    
    // Obtener valores y validar que existan
    const id_usuario = document.getElementById('id_usuario')?.value || '';
    const curp = document.getElementById('curp')?.value || '';
    const nombre_usuario = document.getElementById('nombre_usuario')?.value || '';
    const identificador_de_rh = document.getElementById('identificador_de_rh')?.value || '';
    const correo_electronico = document.getElementById('correo_electronico')?.value || '';
    const contrasena = document.getElementById('contrasena')?.value || '';
    
    // Debug: Verificar que los campos ocultos tengan valores
    console.log('Datos a enviar:', {
        id_usuario,
        curp,
        nombre_usuario,
        identificador_de_rh,
        correo_electronico,
        contrasena: contrasena ? '***' : '(vacío)'
    });
    
    // Validar campos obligatorios
    if (!id_usuario || !curp || !nombre_usuario || !identificador_de_rh || !correo_electronico) {
        alerta("Error", "Faltan datos obligatorios. Por favor, busque el usuario nuevamente.", "error");
        return;
    }
    
    // Agregar todos los campos al FormData
    formData.append('id_usuario', id_usuario);
    formData.append('curp', curp);
    formData.append('nombre_usuario', nombre_usuario);
    formData.append('identificador_de_rh', identificador_de_rh);
    formData.append('correo_electronico', correo_electronico);
    formData.append('contrasena', contrasena);
    formData.append('action', 'actualizar-usuario');
    
    // Hacer la petición con la ruta correcta
    fetch('/ajax/usuarios-ajax.php', {
        method: 'POST',
        body: formData
    })
    .then(r => r.text())
    .then(resp => {
        console.log('Respuesta del servidor:', resp); // Debug
        manejarRespuestaCRUD(
            resp,
            "Usuario actualizado correctamente.",
            "actualizar-usuarios.php"
        );
    })
    .catch(err => {
        console.error('Error en la petición:', err);
        alerta("Error", "Ocurrió un error al actualizar el usuario.", "error");
    });
}
function buscarUsuarioActualizar() {
    const curp = document.getElementById('curp_busqueda').value.trim().toUpperCase();
    const formUpdate = document.getElementById('updateForm');
    
    if (!curp) {
        mostrarAlerta('Ingrese una CURP para buscar', 'alert-info');
        return;
    }
    
    if (curp.length !== 18) {
        mostrarAlerta('La CURP debe tener 18 caracteres', 'alert-error');
        return;
    }
    
    apiRequestUsuarios('buscar-usuario', { curp })
        .then(r => r.json())
        .then(data => {
            if (data.error) {
                mostrarAlerta(data.message, 'alert-error');
                formUpdate.classList.remove('active');
                return;
            }
            
            llenarFormularioActualizar(data);
            formUpdate.classList.add('active');
            mostrarAlerta('Usuario encontrado. Puede editar los datos.', 'alert-success');
        })
        .catch(err => {
            console.error('Error:', err);
            mostrarAlerta('Error al buscar el usuario', 'alert-error');
        });
}

function llenarFormularioActualizar(data) {
    // Campos ocultos
    document.getElementById('id_usuario').value = data.id_usuario || '';
    document.getElementById('curp').value = data.curp_actual || '';
    document.getElementById('nombre_usuario').value = data.nombre_usuario || '';
    document.getElementById('identificador_de_rh').value = data.id_personal || '';
    
    // Campos de solo lectura
    const nombres = data.nombre_completo.split(' ');
    document.getElementById('nombre_personal').value = nombres[0] || '';
    document.getElementById('apellido_paterno').value = nombres[1] || '';
    document.getElementById('apellido_materno').value = nombres.slice(2).join(' ') || '';
    
    // Campo editable
    document.getElementById('correo_electronico').value = data.correo_electronico || '';
    
    // Limpiar contraseñas
    document.getElementById('contrasena').value = '';
    document.getElementById('contrasena_confirmar').value = '';
}

function mostrarAlerta(mensaje, tipo) {
    const alertDiv = document.getElementById('alertMessage');
    if (!alertDiv) return;
    
    alertDiv.className = `alert ${tipo} show`;
    alertDiv.textContent = mensaje;
    
    setTimeout(() => {
        alertDiv.classList.remove('show');
    }, 5000);
}
/* =====================================================
   4. ELIMINAR (DELETE)
   ===================================================== */

function configurarEliminacionUsuarios() {
    
    const formEliminar = document.getElementById("formEliminarUsuario");
    if (!formEliminar) return;
    
    const inputCurp = document.getElementById("input_curp_eliminar");
    const btnBuscar = formEliminar.querySelector('button[type="submit"]');
    
    if (!btnBuscar || !inputCurp) return;
    
    btnBuscar.addEventListener("click", function (e) {
        e.preventDefault(); 
        
        const curp = inputCurp.value.trim();
        
        if (curp === "") {
            alerta("Eliminación", "Debes ingresar una CURP.", "warning");
            return;
        }
        
        confirmar("¿Eliminar Usuario?", "Esta acción no se puede deshacer. ¿Deseas continuar?")
            .then(r => {
                if (!r.isConfirmed) return;
                
                apiRequestUsuarios("eliminar-usuario", { curp })
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



/* =====================================================
   FUNCIONES AUXILIARES PARA ACTUALIZACIÓN
   ===================================================== */

// Función para cargar el personal en el select
function cargarPersonalParaSelect() {
    apiRequestUsuarios("obtener-personal", {})
        .then(r => r.json())
        .then(data => {
            const select = document.getElementById('identificador_de_rh');
            if (!select) return;
            
            select.innerHTML = '<option value="">Seleccione una persona</option>';
            
            data.forEach(p => {
                const nombreCompleto = `${p.nombre_personal} ${p.apellido_paterno} ${p.apellido_materno} (${p.curp})`;
                const option = document.createElement('option');
                option.value = p.id_personal;
                option.textContent = nombreCompleto;
                select.appendChild(option);
            });
        })
        .catch(err => console.error('Error al cargar personal:', err));
}

// Función para buscar usuario por CURP
function buscarUsuarioActualizar() {
    const curp = document.getElementById('curp_busqueda').value.trim().toUpperCase();
    const formUpdate = document.getElementById('updateForm');
    
    if (!curp) {
        mostrarAlerta('Ingrese una CURP para buscar', 'alert-info');
        return;
    }
    
    if (curp.length !== 18) {
        mostrarAlerta('La CURP debe tener 18 caracteres', 'alert-error');
        return;
    }
    
    apiRequestUsuarios('buscar-usuario', { curp })
        .then(r => r.json())
        .then(data => {
            if (data.error) {
                mostrarAlerta(data.message, 'alert-error');
                formUpdate.classList.remove('active');
                return;
            }
            
            llenarFormularioActualizar(data);
            formUpdate.classList.add('active');
            mostrarAlerta('Usuario encontrado. Puede editar los datos.', 'alert-success');
        })
        .catch(err => {
            console.error('Error:', err);
            mostrarAlerta('Error al buscar el usuario', 'alert-error');
        });
}

// Función para llenar el formulario con los datos del usuario
function llenarFormularioActualizar(data) {
    // Campos ocultos
    document.getElementById('id_usuario').value = data.id_usuario;
    document.getElementById('curp').value = data.curp_actual;
    
    // Separar el nombre completo que viene del backend
    const partesNombre = data.nombre_completo.trim().split(' ');
    
    // Asignar las partes del nombre (si los campos existen en el HTML)
    const campoNombre = document.getElementById('nombre_personal');
    const campoPaterno = document.getElementById('apellido_paterno');
    const campoMaterno = document.getElementById('apellido_materno');
    
    if (campoNombre) campoNombre.value = partesNombre[0] || '';
    if (campoPaterno) campoPaterno.value = partesNombre[1] || '';
    if (campoMaterno) campoMaterno.value = partesNombre.slice(2).join(' ') || '';
    
    // Campo editable de correo
    const campoCorreo = document.getElementById('correo_electronico');
    if (campoCorreo) campoCorreo.value = data.correo_electronico;
    
    // Limpiar contraseñas
    const campoPassword = document.getElementById('contrasena');
    const campoPasswordConfirm = document.getElementById('contrasena_confirmar');
    
    if (campoPassword) campoPassword.value = '';
    if (campoPasswordConfirm) campoPasswordConfirm.value = '';
}
// Función para mostrar alertas personalizadas
function mostrarAlerta(mensaje, tipo) {
    const alertDiv = document.getElementById('alertMessage');
    if (!alertDiv) return;
    
    alertDiv.className = `alert ${tipo} show`;
    alertDiv.textContent = mensaje;
    
    setTimeout(() => {
        alertDiv.classList.remove('show');
    }, 5000);
}

// Función para limpiar el formulario
function limpiarFormulario() {
    document.getElementById('updateForm').reset();
    document.getElementById('updateForm').classList.remove('active');
    document.getElementById('curp_busqueda').value = '';
    
    const alertDiv = document.getElementById('alertMessage');
    if (alertDiv) alertDiv.classList.remove('show');
}

// Función para cancelar y volver
function cancelar() {
    if (confirm('¿Desea cancelar? Los cambios no guardados se perderán.')) {
        window.location.href = 'index.php';
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

    const reg = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
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

    let formData;

    // Si datos es un formulario HTML, usar sus datos directamente
    if (datos instanceof HTMLFormElement) {
        formData = new FormData(datos);
    }
    // Si datos es un objeto, crear FormData y agregar cada propiedad
    else {
        formData = new FormData();
        if (datos && typeof datos === 'object') {
            for (const clave in datos) {
                formData.append(clave, datos[clave]);
            }
        }
    }

    // Siempre agregar la acción
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
    
    const formEliminar = document.getElementById("formEliminarUsuario");
    if (!formEliminar) return;
    
    const inputCurp = document.getElementById("input_curp_eliminar");
    const btnBuscar = formEliminar.querySelector('button[type="submit"]');
    
    if (!btnBuscar || !inputCurp) return;
    
    btnBuscar.addEventListener("click", function (e) {
        e.preventDefault(); 
        
        const curp = inputCurp.value.trim();
        
        if (curp === "") {
            alerta("Eliminación", "Debes ingresar una CURP.", "warning");
            return;
        }
        
        confirmar("¿Eliminar Usuario?", "Esta acción no se puede deshacer. ¿Deseas continuar?")
            .then(r => {
                if (!r.isConfirmed) return;
                
                apiRequestUsuarios("eliminar-usuario", { curp })
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