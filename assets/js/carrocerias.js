// =====================================================
//  CRUD DE CARROCERÍAS (Módulo 6.1)
//  Basado en la estructura de Localidades CIIT-TMS
// =====================================================

// Función para prevenir números negativos en inputs numéricos
function prevenirNumerosNegativos(inputs) {
    inputs.forEach(input => {
        input.addEventListener('keydown', function(e) {
            if (e.key === '-' || e.key === 'e' || e.key === 'E') {
                e.preventDefault();
            }
        });
        input.addEventListener('input', function() {
            if (parseFloat(this.value) < 0) {
                this.value = Math.abs(parseFloat(this.value));
            }
        });
    });
}

// Función para cambiar el placeholder de matrícula según la modalidad
function cambiarPlaceholderMatricula() {
    const inputMatricula = document.getElementById("matricula");
    const selectModalidad = document.getElementById("modalidad_carroceria");

    if (!inputMatricula || !selectModalidad) return;

    const placeholders = {
        Carretero: "Ej. 8M2R1P5B321K7Z9X0",
        Ferroviario: "Ej. 123456789012",
        Marítimo: "Ej. 1234567",
        Aéreo: "Ej. AB12345"
    };

    const actualizarPlaceholder = () => {
        const modalidad = selectModalidad.value;
        if (modalidad && placeholders[modalidad]) {
            inputMatricula.placeholder = placeholders[modalidad];
        } else {
            inputMatricula.placeholder = "Ej.";
        }
    };

    // Cambiar placeholder cuando se selecciona una modalidad
    selectModalidad.addEventListener("change", actualizarPlaceholder);
}

document.addEventListener("DOMContentLoaded", function () {
    // ---- 0. Carga de Catálogos (NUEVO) ----
    if (document.getElementById('localidad_pertenece')) cargarLocalidades();
    if (document.getElementById('responsable_carroceria')) cargarPersonal();

    // ---- 1. Registrar ----
    configurarRegistroCarroceria();
    gestionarCamposCondicionales(); 
    configurarValidacionMatriculaRealTime();
    configurarValidacionEjesYContenedores();
    cambiarPlaceholderMatricula();

    // ---- 2. Consultar ----
    configurarVistaConsultarCarrocerias();
    consultarCarrocerias();

    // ---- 3. Actualizar ----
    actualizarCarrocerias();
    if (document.getElementById('buscar_matricula')) {
        inicializarActualizador();
    }

    // ---- 4. Eliminar ----
    /* configurarVistaEliminarCarrocerias();
    */eliminarCarrocerias();  

    // Prevenir números negativos en inputs específicos
    const inputsParaValidar = ['peso_vehicular', 'numero_ejes_vehiculares', 'numero_contenedores']
        .map(id => document.getElementById(id))
        .filter(Boolean);
    prevenirNumerosNegativos(inputsParaValidar);

    const form = document.getElementById("formCarrocerias");
    form.addEventListener("submit", function(e) {
        e.preventDefault(); // <--- ESTO evita que la página se salga/recargue
        ejecutarRegistroFinal();
    });

    
});



/* =====================================================
    0. CARGA DE CATÁLOGOS (LOCALIDADES Y PERSONAL)
   ===================================================== */
function cargarLocalidades() {
    apiRequest("obtener-localidades")
        .then(res => res.json())
        .then(data => {
            const select = document.getElementById('localidad_pertenece');
            if (!select) return;

            if (data.length === 0) {
                select.innerHTML = '<option value="">Sin localidades registradas</option>';
                select.disabled = true; // Opcional: deshabilitar si no hay datos
            } else {
                select.disabled = false;
                select.innerHTML = '<option value="">Seleccione localidad</option>';
                data.forEach(loc => {
                    select.add(new Option(loc.nombre_display, loc.id_localidad));
                });
            }
        });
}

/* =====================================================
    0. CARGA DE CATÁLOGOS (PERSONAL FILTRADO)
   ===================================================== */
function cargarPersonal() {
    apiRequest("obtener-personal")
        .then(res => res.json())
        .then(data => {
            const select = document.getElementById('responsable_carroceria');
            if (!select) return;

            // Limpiamos el select y ponemos la opción por defecto
            select.innerHTML = '<option value="">Seleccione responsable</option>';

            if (!data || data.length === 0) {
                select.innerHTML = '<option value="">Sin Jefes de Almacén registrados</option>';
                select.disabled = true;
            } else {
                select.disabled = false;
                // Simplemente recorremos los datos que envía el PHP 
                // (el PHP ya hizo el trabajo de filtrar por 'Jefe de Almacén')
                data.forEach(p => {
                    select.add(new Option(p.nombre_completo, p.id_personal));
                });
            }
        })
        .catch(err => {
            console.error("Error al cargar personal:", err);
        });
}

/* =====================================================
    1. REGISTRAR (CREATE) CON VALIDACIONES TÉCNICAS
   ===================================================== */

const ValidadoresMatricula = {
    Carretero: (v) => {
        const regex = /^[A-HJ-NPR-Z0-9]{8}[0-9X]{1}[A-HJ-NPR]{1}[A-HJ-NPR-Z0-9]{7}$/;
        return regex.test(v);
        
    },
    Ferroviario: (v) => {
        if (!/^\d{12}$/.test(v)) return false;
        let sum = 0;
        for (let i = 0; i < 12; i++) {
            let n = parseInt(v[i]);
            if (i % 2 === 0) {
                n *= 2;
                if (n > 9) n -= 9;
            }
            sum += n;
        }
        return sum % 10 === 0;
    },
    Marítimo: (v) => {
        if (!/^\d{7}$/.test(v)) return false;
        let checkSum = 0;
        let pesos = [7, 6, 5, 4, 3, 2];
        for (let i = 0; i < 6; i++) {
            checkSum += parseInt(v[i]) * pesos[i];
        }
        return (checkSum % 10) === parseInt(v[6]);
    },
    Aéreo: (v) => {
        return /^[A-Z]{1,2}[A-Z0-9]{1,5}$/.test(v);
    }
};
// Validación de ejes, contenedores y peso vehicular
function configurarValidacionEjesYContenedores() {
    const inputEjes = document.getElementById("numero_ejes_vehiculares");
    const inputContenedores = document.getElementById("numero_contenedores");
    const inputPeso = document.getElementById("peso_vehicular");
    const selectModalidad = document.getElementById("modalidad_carroceria");
    const msjErrorEjes = document.getElementById("msj-error-ejes");
    const msjErrorContenedores = document.getElementById("msj-error-contenedores");
    const msjErrorPeso = document.getElementById("msj-error-peso");

    const mensajesAyudaLimites = {
        ejes: "El número máximo de ejes permitidos es 20.",
        contenedores: "El número máximo de contenedores permitidos es 10."
    };

    // Límites máximos de peso por modalidad (en kg)
    const limitesPesoMax = {
        Carretero: 75500,
        Ferroviario: 130000,
        Marítimo: 200000000,
        Aéreo: 400000
    };

    // Límites mínimos de peso por modalidad (en kg)
    const limitesPesoMin = {
        Carretero: 15000,
        Ferroviario: 20000,
        Marítimo: 30000000,
        Aéreo: 180000
    };

    // Mensajes personalizados por modalidad (máximo)
    const mensajesPesoMax = {
        Carretero: "El peso máximo permitido para modalidad Carretero es 75,500 kg.",
        Ferroviario: "El peso máximo permitido para modalidad Ferroviario es 130,000 kg.",
        Marítimo: "El peso máximo permitido para modalidad Marítimo es 200,000,000 kg.",
        Aéreo: "El peso máximo permitido para modalidad Aéreo es 400,000 kg."
    };

    // Mensajes personalizados por modalidad (mínimo)
    const mensajesPesoMin = {
        Carretero: "El peso mínimo permitido para modalidad Carretero es 15,000 kg.",
        Ferroviario: "El peso mínimo permitido para modalidad Ferroviario es 20,000 kg.",
        Marítimo: "El peso mínimo permitido para modalidad Marítimo es 30,000,000 kg.",
        Aéreo: "El peso mínimo permitido para modalidad Aéreo es 180,000 kg."
    };

    const validarCampo = (input, msjError, tipo, max) => {
        const valor = parseInt(input.value) || 0;
        const esRequerido = input.required;

        if (esRequerido && valor === 0) {
            input.classList.add("input-invalido");
            input.classList.remove("input-valido");
            msjError.textContent = `El ${tipo === 'ejes' ? 'número de ejes' : 'número de contenedores'} debe ser mayor a 0.`;
            input.title = `El ${tipo === 'ejes' ? 'número de ejes' : 'número de contenedores'} debe ser mayor a 0.`;
        } else if (valor > max) {
            input.classList.add("input-invalido");
            input.classList.remove("input-valido");
            msjError.textContent = mensajesAyudaLimites[tipo];
            input.title = mensajesAyudaLimites[tipo];
        } else if (valor > 0 || (valor === 0 && !esRequerido)) {
            input.classList.remove("input-invalido");
            input.classList.add("input-valido");
            msjError.textContent = "";
            input.title = "Valor correcto";
        } else {
            input.classList.remove("input-valido", "input-invalido");
            msjError.textContent = "";
        }
    };

    const validarPeso = () => {
        if (!inputPeso || !msjErrorPeso) return;
        
        const modalidad = selectModalidad.value;
        const valor = parseFloat(inputPeso.value) || 0;
        const maxPeso = limitesPesoMax[modalidad] || 75500;
        const minPeso = limitesPesoMin[modalidad] || 15000;

        if (!modalidad || valor === 0) {
            inputPeso.classList.remove("input-valido", "input-invalido");
            msjErrorPeso.textContent = "";
            return;
        }

        if (valor < minPeso) {
            inputPeso.classList.add("input-invalido");
            inputPeso.classList.remove("input-valido");
            msjErrorPeso.textContent = mensajesPesoMin[modalidad] || "Peso inferior al mínimo.";
            inputPeso.title = mensajesPesoMin[modalidad];
        } else if (valor > maxPeso) {
            inputPeso.classList.add("input-invalido");
            inputPeso.classList.remove("input-valido");
            msjErrorPeso.textContent = mensajesPesoMax[modalidad] || "Peso excedido.";
            inputPeso.title = mensajesPesoMax[modalidad];
        } else if (valor > 0) {
            inputPeso.classList.remove("input-invalido");
            inputPeso.classList.add("input-valido");
            msjErrorPeso.textContent = "";
            inputPeso.title = "Peso correcto";
        }
    };

    const actualizarMinMaxPeso = () => {
        if (!inputPeso || !selectModalidad) return;
        
        const modalidad = selectModalidad.value;
        const maxPeso = limitesPesoMax[modalidad] || 75500;
        const minPeso = limitesPesoMin[modalidad] || 15000;
        
        // Actualizar los atributos min y max del input
        inputPeso.setAttribute("max", maxPeso);
        inputPeso.setAttribute("min", minPeso);
        
        // Re-validar el peso actual después de cambiar modalidad
        validarPeso();
    };

    // Validación de ejes
    if (inputEjes && msjErrorEjes) {
        inputEjes.addEventListener("input", () => validarCampo(inputEjes, msjErrorEjes, "ejes", 20));
    }

    // Validación de contenedores
    if (inputContenedores && msjErrorContenedores) {
        inputContenedores.addEventListener("input", () => validarCampo(inputContenedores, msjErrorContenedores, "contenedores", 10));
    }

    // Validación de peso (se ejecuta cuando cambia modalidad o peso)
    if (inputPeso && selectModalidad && msjErrorPeso) {
        inputPeso.addEventListener("input", validarPeso);
        selectModalidad.addEventListener("change", actualizarMinMaxPeso);
        
        // Establecer el min y max inicial
        actualizarMinMaxPeso();
    }
}

function configurarValidacionMatriculaRealTime() {
    const inputMatricula = document.getElementById("matricula");
    const selectModalidad = document.getElementById("modalidad_carroceria");
    const msjError = document.getElementById("msj-error-matricula");

        // SOLUCIÓN: Si no existen estos elementos (como en la vista de consulta), salimos sin error
    if (!inputMatricula || !selectModalidad) return; 


    const mensajesAyuda = {
        Carretero: "“Ingresa un código de 17 caracteres usando solo letras mayúsculas y números. No se permiten las letras I, O, Q ni Ñ. Verifica que todo esté bien escrito antes de continuar.",
        Ferroviario: "Deben ser 12 dígitos numéricos exactos (el último es verificador).",
        Marítimo: "Deben ser 7 dígitos (el último es verificador).",
        Aéreo: "Prefijo de 1-2 letras seguido de 1-5 caracteres."
    };

    const validarAccion = () => {
        const modalidad = selectModalidad.value;
        const valor = inputMatricula.value.trim();

        // 1. Si está vacío o no hay modalidad, resetear estado
        if (!modalidad || valor === "") {
            inputMatricula.classList.remove("input-valido", "input-invalido");
            msjError.textContent = "";
            return;
        }

        // 2. Ejecutar validador técnico
        const validador = ValidadoresMatricula[modalidad];
        const esValido = validador ? validador(valor) : true;

        if (esValido) {
            // ESTADO VÁLIDO: Usamos tus clases CSS
            inputMatricula.classList.remove("input-invalido");
            inputMatricula.classList.add("input-valido");
            msjError.textContent = ""; // Limpiar mensaje
            inputMatricula.title = "Formato correcto";
        } else {
            // ESTADO INVÁLIDO: Usamos tus clases CSS
            inputMatricula.classList.remove("input-valido");
            inputMatricula.classList.add("input-invalido");
            
            // Mostrar el mensaje de ayuda específico en el <small> que creaste
            msjError.textContent = mensajesAyuda[modalidad] || "Formato no válido";
            
            // Tooltip nativo (al pasar el mouse)
            inputMatricula.title = mensajesAyuda[modalidad];
        }
    };

    // Escuchar cambios en ambos campos
    inputMatricula.addEventListener("input", validarAccion);
    selectModalidad.addEventListener("change", validarAccion);
}

/* =====================================================
    1. GESTIÓN DE CONDICIONALES (RESTRICCIÓN FERROVIARIA)
   ===================================================== */
function gestionarCamposCondicionales() {
    const selectModalidad = document.getElementById("modalidad_carroceria");
    const selectTipo = document.getElementById("tipo_carroceria");
    const inputEjes = document.getElementById("numero_ejes_vehiculares");
    const inputContenedores = document.getElementById("numero_contenedores");
    const inputMatricula = document.getElementById("matricula");

    const inputPeso = document.getElementById("peso_vehicular");
    const inputLocalidad = document.getElementById("localidad_pertenece");
    const inputResponsable = document.getElementById("responsable_carroceria");

    if (!selectModalidad || !selectTipo) return;

    const actualizarVisibilidad = () => {
        const mod = selectModalidad.value;
        const tipo = selectTipo.value;

        // REQUERIMIENTO: Ferroviario no permite "Mixta"
        if (mod === "Ferroviario" && tipo === "Mixta") {
            alerta("Validación", "La modalidad Ferroviaria no permite el tipo de carrocería Mixta.", "warning");
            selectTipo.value = ""; // Reseteamos la selección
            selectTipo.style.pointerEvents = "auto";
            selectTipo.style.backgroundColor = "";
            return;
        }

        if (["Marítimo", "Aéreo"].includes(mod)) {
            selectTipo.value = "Mixta";
            selectTipo.style.pointerEvents = "none";
            selectTipo.style.backgroundColor = "#e9ecef";
            inputEjes.disabled = true;
            inputEjes.required = false;
        } else {
            selectTipo.style.pointerEvents = "auto";
            selectTipo.style.backgroundColor = "";
            inputEjes.disabled = false;
            inputEjes.required = true;
        }
        // Habilitar siempre estos campos al cambiar modalidad
        inputMatricula.disabled = false;
        inputPeso.disabled = false;
        inputLocalidad.disabled = false;
        inputResponsable.disabled = false;        

        const requiereContenedores = ["Unidad de carga", "Mixta"].includes(selectTipo.value);
        inputContenedores.disabled = !requiereContenedores;
        inputContenedores.required = requiereContenedores;
    };

    selectModalidad.addEventListener("change", actualizarVisibilidad);
    selectTipo.addEventListener("change", actualizarVisibilidad);
}

/* =====================================================
    VALIDACIÓN DE ERRORES ESPECÍFICOS
   ===================================================== */
function validarFormularioCompleto() {
    const matricula = document.getElementById("matricula").value.trim();
    const modalidad = document.getElementById("modalidad_carroceria").value;
    const tipo = document.getElementById("tipo_carroceria").value;
    const peso = parseFloat(document.getElementById("peso_vehicular").value) || 0;
    const responsable = document.getElementById("responsable_carroceria").value;
    const localidad = document.getElementById("localidad_pertenece").value;
    const ejes = parseInt(document.getElementById("numero_ejes_vehiculares").value) || 0;
    const contenedores = parseInt(document.getElementById("numero_contenedores").value) || 0;

    // Límites de peso por modalidad
    const limitesPesoMax = {
        Carretero: 75500,
        Ferroviario: 130000,
        Marítimo: 200000000,
        Aéreo: 400000
    };

    const limitesPesoMin = {
        Carretero: 15000,
        Ferroviario: 20000,
        Marítimo: 30000000,
        Aéreo: 180000
    };

    if (!matricula) return "La Matrícula es obligatoria.";
    if (!modalidad) return "Debe seleccionar una Modalidad.";
    if (!tipo) return "Debe seleccionar el Tipo de Carrocería.";
    if (!peso || peso <= 0) return "El Peso Vehicular debe ser un número mayor a 0.";
    if (!responsable) return "Debe asignar un Responsable (Jefe de Almacén).";
    if (!localidad) return "Debe seleccionar la Localidad a la que pertenece.";
    
    // Validar contenedores solo si es Unidad de carga o Mixta
    const requiereContenedores = ["Unidad de carga", "Mixta"].includes(tipo);
    if (requiereContenedores && contenedores === 0) return "El número de contenedores debe ser mayor a 0.";
    if (requiereContenedores && contenedores > 10) return "El número de contenedores no puede ser mayor a 10.";

    const requireejes = ["Carretero", "Ferroviario"].includes(modalidad);
    if (requireejes && ejes === 0) return "El número de ejes es obligatorio para la modalidad seleccionada.";
    if (requireejes && ejes > 20) return "El número de ejes no puede ser mayor a 0.";
    
    // Validación de peso según modalidad (mínimo y máximo)
    const minPeso = limitesPesoMin[modalidad] || 15000;
    const maxPeso = limitesPesoMax[modalidad] || 75500;
    
    if (peso < minPeso) {
        return `El peso mínimo permitido para ${modalidad} es ${minPeso.toLocaleString()} kg.`;
    }
    
    if (peso > maxPeso) {
        return `El peso máximo permitido para ${modalidad} es ${maxPeso.toLocaleString()} kg.`;
    }
    
    // Validación específica Ferroviario vs Mixto (Doble check)
    if (modalidad === "Ferroviario" && tipo === "Mixta") {
        return "Combinación inválida: Ferroviario no puede ser de tipo Mixta.";
    }

    return null; // Todo correcto
}



/* =====================================================
    1. REGISTRAR (CORREGIDO)
   ===================================================== */

function configurarRegistroCarroceria() {
    const formPrincipal = document.querySelector("#formCarrocerias");
    const btnSiguiente = document.getElementById("btnSiguiente");
    const seccionDetalles = document.getElementById("seccionDetalles"); 

    if (!formPrincipal) return;

    // Validación en tiempo real para habilitar el botón
    formPrincipal.addEventListener("input", () => {
        const mod = document.getElementById("modalidad_carroceria").value;
        const mat = document.getElementById("matricula").value;
        const peso = parseFloat(document.getElementById("peso_vehicular").value);
        const inputPeso = document.getElementById("peso_vehicular");
        
        // Límites de peso por modalidad
        const limitesPesoMax = {
            Carretero: 75500,
            Ferroviario: 130000,
            Marítimo: 200000000,
            Aéreo: 400000
        };

        const limitesPesoMin = {
            Carretero: 15000,
            Ferroviario: 20000,
            Marítimo: 30000000,
            Aéreo: 180000
        };
        
        // Simplificamos la validación de matrícula para que no sea tan estricta 
        // y permita avanzar si hay texto (puedes volver a usar ValidadoresMatricula[mod] si lo prefieres)
        const matValida = mat.trim().length >= 3; 
        
        const pesoValido = !isNaN(peso) && peso > 0;
        const minPeso = limitesPesoMin[mod] || 15000;
        const maxPeso = limitesPesoMax[mod] || 75500;
        const pesoEnRango = !mod || (peso >= minPeso && peso <= maxPeso);
        const formValido = formPrincipal.checkValidity();

        btnSiguiente.disabled = !(matValida && pesoValido && pesoEnRango && formValido);
    });

    // Acción del botón Siguiente
    btnSiguiente?.addEventListener("click", () => {
        const tipo = document.getElementById("tipo_carroceria").value;
        console.log("Formulario principal válido, avanzando...");
        console.log("Tipo de carrocería seleccionado:", tipo);
        
        // ASEGURAMOS QUE LOS CAMPOS SEAN ENVIABLES
        // Usar readOnly garantiza que el valor sea visible y se incluya en el FormData
        const camposParaBloquear = ["matricula", "peso_vehicular", "numero_ejes_vehiculares", "numero_contenedores"];
        
        camposParaBloquear.forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.readOnly = true;
                el.style.backgroundColor = "#e9ecef"; // Color grisáceo de Bootstrap
            }
        });
        
        // Para los SELECTS, establecemos disabled=true para que se vean realmente deshabilitados
        const selects = ["modalidad_carroceria", "localidad_pertenece", "responsable_carroceria", "tipo_carroceria"];
        selects.forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.style.pointerEvents = "none";
                el.style.backgroundColor = "#e9ecef"; // Color grisáceo de Bootstrap
            }
        });

        if (tipo === "Unidad de arrastre") {
            ejecutarRegistroFinal();
        } else {
            generarFormularioDetalles();
            document.getElementById("seccionPrincipal").style.display = "none";
            document.getElementById("seccionDetalles").style.display = "block";
        }
    });

    document.getElementById("btnAnterior")?.addEventListener("click", () => {
        // 1. Mostrar sección principal y ocultar detalles
        document.getElementById("seccionPrincipal").style.display = "block";
        document.getElementById("seccionDetalles").style.display = "none";

        // 2. DESBLOQUEAR los campos para que el usuario pueda corregir
        const campos = ["matricula", "peso_vehicular", "numero_ejes_vehiculares", "numero_contenedores"];
        campos.forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.readOnly = false;
                el.classList.remove("bg-light");
            }
        });

        // 3. Habilitar de nuevo los Selects
        const selects = ["modalidad_carroceria", "localidad_pertenece", "responsable_carroceria", "tipo_carroceria"];
        selects.forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.style.pointerEvents = "auto";
                el.style.backgroundColor = "";
            }
        });
    });
}

function generarFormularioDetalles() {
    const numContenedores = document.getElementById('numero_contenedores').value;
    const contenedor = document.getElementById('contenedor-detalles'); // El ID corregido
    
    contenedor.innerHTML = ''; // Limpiar contenido previo

    for (let i = 1; i <= numContenedores; i++) {
        contenedor.innerHTML += `
            <div class="card-detalle shadow-sm mb-3">
                <h5>Contenedor #${i}</h5>
                <div class="row">
                    <div class="col-md-4">
                        <label>Longitud (m)</label>
                        <input type="number" name="longitud[]" class="form-control" step="1.00" min="0" required>
                    </div>
                    <div class="col-md-4">
                        <label>Anchura (m)</label>
                        <input type="number" name="anchura[]" class="form-control" step="1.00" min="0" required>
                    </div>
                    <div class="col-md-4">
                        <label>Altura (m)</label>
                        <input type="number" name="altura[]" class="form-control" step="1.0" min="0" required>
                    </div>
                </div>
            </div>`;
    }
    // Prevenir números negativos en los inputs generados
    const inputsNumericos = contenedor.querySelectorAll('input[type="number"]');
    prevenirNumerosNegativos(inputsNumericos);
}

/* =====================================================
    REGISTRO FINAL (CON MENSAJES DE ERROR CLAROS)
   ===================================================== */
function ejecutarRegistroFinal() {
    // REQUERIMIENTO: Mensajes de error claros y específicos
    const errorMsg = validarFormularioCompleto();
    if (errorMsg) {
        alerta("Campo Faltante o Inválido", errorMsg, "error");
        return;
    }

    confirmar("¿Registrar Carrocería?", "¿Deseas continuar con el registro?")
        .then(r => {
            if (!r.isConfirmed) return;

            const formulario = document.getElementById("formCarrocerias");
            
            // Habilitar campos temporalmente para que FormData los capture
            //const elementosBloqueados = formulario.querySelectorAll('[readonly], :disabled');
            //elementosBloqueados.forEach(el => el.disabled = false);
            const fd = new FormData(formulario);
            for (let [k, v] of fd.entries()) {
                console.log(k, v);
            }
            // Rehabilitar los campos bloqueados

            apiRequest("registrar-carroceria", formulario)
                .then(res => res.text())
                .then(resp => {
                    manejarRespuestaCRUD(resp, "Carrocería registrada correctamente.");
                })
                .catch(err => {
                    console.error("Error:", err);
                    alerta("Error", "No se pudo conectar con el servidor", "error");
                });
        });
}


// ASEGÚRATE DE QUE LOS SELECTS NO ESTÉN DISABLED
// En la función donde pasas a la sección de detalles, usa esto:
function bloquearCamposSeccion1() {
    // En lugar de .disabled = true, usa esto:
    document.getElementById("matricula").readOnly = true;
    document.getElementById("modalidad_carroceria").style.pointerEvents = "none";
    document.getElementById("tipo_carroceria").style.pointerEvents = "none";
    // Esto hace que no se puedan editar pero que SÍ se envíen.
}

/* =====================================================
   2. CONSULTAR (READ)
   ===================================================== */
/* =====================================================
   2. CONSULTAR (READ) - VERSIÓN INTEGRAL CON SELECTS
   ===================================================== */
function configurarVistaConsultarCarrocerias() {
    const selectFiltros = document.getElementById("selectFiltro");
    const botonAgregarFiltro = document.getElementById("btnAddFiltro");
    const contenedorFiltros = document.getElementById("filtrosContainer");
    const contenedorBotonConsultar = document.getElementById("contenedorConsultar");
    const btnVolver = document.getElementById("btnVolver");

    if (!selectFiltros || !botonAgregarFiltro || !contenedorFiltros) return;

    // EVENTO AGREGAR FILTRO (Con lógica de Selects)
    botonAgregarFiltro.addEventListener("click", () => {
        const valorFiltro = selectFiltros.value;
        const textoFiltro = selectFiltros.options[selectFiltros.selectedIndex].text;

        if (!valorFiltro) {
            alerta("Filtros", "Selecciona un criterio de búsqueda", "warning");
            return;
        }

        const fila = document.createElement("div");
        fila.classList.add("filter-row", "mb-2", "d-flex", "gap-2", "animated", "fadeIn");
        
        // REINCORPORACIÓN DE LISTAS DESPLEGABLES
        fila.innerHTML = `
            <input type="text" class="form-control" value="${textoFiltro}" readonly style="width: 40%;">
            ${valorFiltro === 'modalidad_carroceria' ? `
            <select name="modalidad_carroceria" class="form-select" required style="width: 50%;">
                <option value="">Seleccione</option>
                <option value="Carretero">Carretero</option>
                <option value="Ferroviario">Ferroviario</option>
                <option value="Marítimo">Marítimo</option>
                <option value="Aéreo">Aéreo</option>
            </select>` : valorFiltro === 'estatus_carroceria' ? `
            <select name="estatus_carroceria" class="form-select" required style="width: 50%;">
                <option value="">Seleccione</option>
                <option value="Disponible">Disponible</option>
                <option value="Ensamblada">Ensamblada</option>
                <option value="En mantenimiento">En mantenimiento</option>
                <option value="En reparación">En reparación</option>
            </select>` : `
            <input type="text" class="form-control" name="${valorFiltro}" placeholder="Valor a buscar..." required style="width: 50%;">
            `}
            <button type="button" class="btn btn-danger delete-btn"><i class="fas fa-trash"></i></button>
        `;

        contenedorFiltros.appendChild(fila);
        contenedorBotonConsultar.style.display = "flex";

        // Deshabilitar opción en el combo principal
        const opcionOriginal = selectFiltros.querySelector(`option[value="${valorFiltro}"]`);
        if (opcionOriginal) opcionOriginal.disabled = true;
        selectFiltros.selectedIndex = 0;

        // Botón eliminar del filtro individual
        fila.querySelector(".delete-btn").addEventListener("click", () => {
            fila.remove();
            if (opcionOriginal) opcionOriginal.disabled = false;
            contenedorBotonConsultar.style.display = contenedorFiltros.children.length ? "flex" : "none";
        });
    });

    // BOTÓN VOLVER (Comportamiento Localidades)
    if (btnVolver) {
        btnVolver.addEventListener("click", () => {
            // 1. Limpiar contenedor de filtros
            contenedorFiltros.innerHTML = "";
            
            // 2. Habilitar todas las opciones del select principal
            const opciones = selectFiltros.querySelectorAll("option");
            opciones.forEach(opt => opt.disabled = false);
            selectFiltros.selectedIndex = 0;

            // 3. Resetear formulario y tabla
            const form = document.getElementById("formConsultarCarrocerias");
            if (form) form.reset();
            document.querySelector("#tablaCarrocerias tbody").innerHTML = "";

            // 4. Alternar vistas
            document.getElementById("formContainer").style.display = "block";
            document.getElementById("tablaResultados").style.display = "none";
            contenedorBotonConsultar.style.display = "none";
        });
    }
}

function consultarCarrocerias() {
    const form = document.getElementById("formConsultarCarrocerias");
    const contenedorFiltros = document.getElementById("filtrosContainer");

    if (!form) return;

    form.addEventListener("submit", function (e) {
        e.preventDefault();

        // 1. Integridad conservada: Recolección dinámica de filtros
        const filtros = {};
        const elementos = contenedorFiltros.querySelectorAll('input:not([readonly]), select');
        
        // Verificación básica antes de enviar
        if (elementos.length === 0) {
            alerta("Filtros", "Por favor agrega al menos un filtro para la búsqueda.", "warning");
            return;
        }

        elementos.forEach(el => {
            if (el.value.trim() !== "") {
                filtros[el.name] = el.value.trim();
            }
        });

        // 2. Llamada a la API
        apiRequest("consultar-carrocerias", filtros)
            .then(res => res.json())
            .then(data => {
                const tbody = document.querySelector("#tablaCarrocerias tbody");
                tbody.innerHTML = "";

                // 3. Alerta de "Sin resultados" manteniendo tu flujo
                if (!data || data.length === 0) {
                    alerta("Sin resultados", "No se encontraron carrocerías con los criterios seleccionados.", "info");
                    return;
                }

                // 4. Integridad conservada: Construcción de filas con operadores OR para compatibilidad de nombres
                data.forEach(carro => {
                    const tr = document.createElement("tr");
                    tr.innerHTML = `
                        <td>${carro.matricula || ''}</td>
                        <td>${carro.modalidad_carroceria || carro.modalidad || ''}</td>
                        <td>${carro.tipo_carroceria || carro.tipo || ''}</td>
                        <td>
                            <span class="badge ${typeof getBadgeClass === 'function' ? getBadgeClass(carro.estatus_carroceria || carro.estatus) : 'bg-primary'}">
                                ${carro.estatus_carroceria || carro.estatus || ''}
                            </span>
                        </td>
                        <td>${carro.nombre_display_localidad || 'N/A'}</td> 
                        <td>${carro.nombre_completo_personal || 'N/A'}</td>
                    `;
                    tbody.appendChild(tr);
                });

                // Cambio de vista
                document.getElementById("formContainer").style.display = "none";
                document.getElementById("tablaResultados").style.display = "block";
            })
            .catch(err => {
                // 5. Alerta de error técnica
                console.error("Error:", err);
                alerta("Error de conexión", "No se pudo obtener respuesta del servidor. Intente más tarde.", "error");
            });
    });
}

// Función auxiliar para colores de estatus
function getBadgeClass(estatus) {
    switch(estatus) {
        case 'Disponible': return 'bg-success';
        case 'Ensamblada': return 'bg-primary';
        case 'En mantenimiento': return 'bg-warning text-dark';
        case 'En reparación': return 'bg-danger';
        default: return 'bg-secondary';
    }


}
/* =====================================================
   3. ACTUALIZAR (UPDATE)
   ===================================================== */
function actualizarCarrocerias() {
    const inputBusqueda = document.getElementById('inputBuscarCarroceria');
    const datalist = document.getElementById('carroceriasList');
    const formulario = document.getElementById('formActualizarCarroceria');

    if (!inputBusqueda) return;

    inputBusqueda.addEventListener('input', () => {
        const texto = inputBusqueda.value.trim();
        if (texto.length < 2) return;

        apiRequest('buscar-carrocerias', { busqueda: texto })
            .then(r => r.json())
            .then(data => {
                datalist.innerHTML = '';
                data.forEach(c => {
                    const opt = document.createElement('option');
                    opt.value = c.matricula;
                    Object.keys(c).forEach(key => opt.dataset[key] = c[key]);
                    datalist.appendChild(opt);
                });
            });
    });

    formulario?.addEventListener('submit', e => {
        e.preventDefault();
        confirmar("¿Actualizar Carrocería?", "Se verificará la integridad de los datos.")
            .then(r => {
                if (r.isConfirmed) {
                    apiRequest("actualizar-carroceria", formulario)
                        .then(r => r.text())
                        .then(resp => manejarRespuestaCRUD(resp, "Actualización exitosa.", "consultar-carrocerias.php"));
                }
            });
    });
}

function inicializarActualizador() {
    const inputBusqueda = document.getElementById('buscar_matricula');
    const btnCargar = document.getElementById('btnCargarCarroceria');
    const datalist = document.getElementById('lista_carrocerias');

    inputBusqueda.addEventListener('input', function() {
        const texto = this.value;
        if (texto.length > 2) {
            apiRequest('buscar-carrocerias', { busqueda: texto })
            .then(res => res.json())
            .then(data => {
                datalist.innerHTML = '';
                data.forEach(item => {
                    const option = document.createElement('option');
                    option.value = item.matricula;
                    datalist.appendChild(option);
                });
            });
        }
    });

    btnCargar.addEventListener('click', function() {
        const matricula = inputBusqueda.value;
        if (!matricula) return alerta("Atención", "Ingrese una matrícula", "warning");

        apiRequest('mostrar-eliminar-carroceria', { matricula: matricula })
        .then(res => res.json())
        .then(res => {
            if (res && res.length > 0) {
                const c = res[0];
                document.getElementById('id_carroceria').value = c.id_carroceria;
                document.getElementById('matricula').value = c.matricula;
                document.getElementById('modalidad_carroceria').value = c.modalidad_carroceria;
                document.getElementById('estatus_carroceria').value = c.estatus_carroceria;
                document.getElementById('peso_vehicular').value = c.peso_vehicular;
                document.getElementById('numero_ejes_vehiculares').value = c.numero_ejes_vehiculares;
                document.getElementById('tipo_carroceria').value = c.tipo_carroceria;
                document.getElementById('localidad_pertenece').value = c.id_localidad; // Setea el select
                document.getElementById('responsable_carroceria').value = c.id_personal; // Setea el select
                
                document.getElementById('btnActualizar').disabled = false;
                alerta("Éxito", "Datos cargados correctamente", "success");
            } else {
                alerta("No encontrado", "No existe esa matrícula", "error");
            }
        });
    });
}

/* =====================================================
   4. ELIMINAR (DELETE)
   ===================================================== */
function eliminarCarrocerias() {
    const formulario = document.getElementById('formConsultaEliminar');
    if (!formulario) return;

    formulario.addEventListener('submit', function (e) {
        e.preventDefault();
        const matricula = document.getElementById('inputMatriculaEliminar').value;

        apiRequest("mostrar-eliminar-carroceria", { matricula: matricula })
            .then(res => res.json())
            .then(data => {
                if (!data || data.length === 0) {
                    alerta("No encontrado", "No existe esa carrocería.", "warning");
                    return;
                }
                const carro = data[0];
                if (carro.estatus_carroceria === "Ensamblada") {
                    alerta("Acción Bloqueada", "No se puede eliminar una carrocería ensamblada.", "error");
                    return;
                }

                confirmar("¿Eliminar?", `¿Eliminar matrícula ${carro.matricula}?`, "warning")
                .then(res => {
                    if (res.isConfirmed) {
                        apiRequest("eliminar-carroceria", { id_carroceria: carro.id_carroceria })
                            .then(r => r.text())
                            .then(resp => manejarRespuestaCRUD(resp, "Eliminación exitosa."));
                    }
                });
            });
    });
}

/* =====================================================
   5. FUNCIONES REUTILIZABLES (ESTÁNDAR)
   ===================================================== */
function apiRequest(accion, datos = null) {
    const formData = datos instanceof HTMLFormElement ? new FormData(datos) : new FormData();
    if (datos && !(datos instanceof HTMLFormElement)) {
        for (const clave in datos) formData.append(clave, datos[clave]);
    }
    formData.append("action", accion);

    return fetch('/ajax/carroceria-ajax.php', {
        method: "POST",
        body: formData
    });
}

function manejarRespuestaCRUD(respuesta, mensajeExito, redireccion = null) {
    // Limpiamos la respuesta de espacios en blanco
    const res = respuesta.trim();

    if (res === "OK") {
        alerta("¡Éxito!", mensajeExito, "success").then(() => {
            if (redireccion) window.location.href = redireccion;
            else location.reload();
        });
    } else {
        // Si el controlador PHP mandó algo que no es "OK", es un error o advertencia
        // Ejemplo: "Error: La matrícula ya existe"
        const tipo = res.includes("Error") ? "error" : "warning";
        alerta("Atención", res, tipo);
    }
}