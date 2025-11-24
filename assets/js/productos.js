/********************************************
 *      INICIALIZADOR PRINCIPAL
 ********************************************/

document.addEventListener("DOMContentLoaded", () => {

    const formRegistro = document.getElementById("formRegistroProductos");

    if (formRegistro) {
        cargarTiposEmbalaje();
        cargarTiposMercancia();
        cargarLocalidades();
        initValidacionesProductos()
        configurarRegistroProductos();
    }

    // PARA FUTURO CRUD
    // if (document.getElementById("formConsultaProductos")) configurarConsultaProductos();
    // if (document.getElementById("formActualizarProducto")) configurarActualizarProducto();
    // if (document.getElementById("btnEliminarProducto")) configurarEliminarProducto();
});

/********************************************
 *      LISTAS ESTÁTICAS (CATÁLOGOS)
 ********************************************/

const tiposEmbalaje = [
    "Envase simple", "Envase combinado", "Envase interior", "Envase exterior",
    "Envase intermedio", "Recipiente intermedio para granel (RIG / IBC)", "Gran embalaje (LP)",
    "Tambor metálico", "Tambor de plástico", "Bidón metálico", "Bidón de plástico", "Caja de madera",
    "Caja de cartón / fibra", "Caja de plástico", "Caja metálica",
    "Bolsa de plástico", "Bolsa de papel", "Frasco o botella de vidrio", "Frasco o botella de plástico",
    "Garrafa o contenedor de vidrio / gres", "Contenedor a presión (cilindro, tanque portátil)",
    "Embalaje compuesto (varios materiales)", "Embalaje con flejes o ligaduras",
    "Embalaje interior rígido", "Envase reutilizable o retornable", "Embalaje para cantidades limitadas",
    "Embalaje para residuos peligrosos", "Embalaje especial para líquidos corrosivos",
    "Embalaje especial para materiales explosivos"
];

const tiposMercancia = [
    "Mercancías peligrosas", "Sustancias peligrosas", "Materiales peligrosos", "Residuos peligrosos",
    "Mercancías en cantidades limitadas", "Mercancías en cantidades exceptuadas", "Mercancías no peligrosas",
    "Mercancías comunes / generales", "Mercancías para consumo final", "Mercancías a granel",
    "Mercancías en envases especiales", "Mercancías transportadas en tanque/autotanque",
    "Clase 1 - Explosivos", "Clase 2 - Gases", "Clase 3 - Líquidos inflamables",
    "Clase 4 - Sólidos inflamables / combustión espontánea / reacción con agua",
    "Clase 5 - Sustancias comburentes y peróxidos orgánicos", "Clase 6 - Sustancias tóxicas e infecciosas",
    "Clase 7 - Materiales radiactivos", "Clase 8 - Sustancias corrosivas",
    "Clase 9 - Sustancias peligrosas varias / misceláneas"
];


/********************************************
 *      CARGA DE SELECTS (EMBALAJE / MERCANCÍA / LOCALIDADES)
 ********************************************/

function cargarTiposEmbalaje() {
    const select = document.getElementById("tipo_embalaje");
    if (!select) return;

    tiposEmbalaje.forEach(tipo => {
        const opt = document.createElement("option");
        opt.value = tipo;
        opt.textContent = tipo;
        select.appendChild(opt);
    });
}

function cargarTiposMercancia() {
    const select = document.getElementById("tipo_mercancia");
    if (!select) return;

    tiposMercancia.forEach(tipo => {
        const opt = document.createElement("option");
        opt.value = tipo;
        opt.textContent = tipo;
        select.appendChild(opt);
    });
}

function cargarLocalidades() {
    const select = document.getElementById("ubicacion_producto");
    if (!select) return;

    apiRequestProductos("listar_localidades")
        /*.then(r => r.text())
                .then(texto => {
                    console.log("RESPUESTA CRUDA DEL SERVIDOR:");
                    console.log(texto);
                })*/
        .then(r => r.json())
        .then(data => {
            if (data.error) {
                alerta("Error", data.error, "error");
                return;
            }

            data.forEach(loc => {
                const opt = document.createElement("option");
                opt.value = loc.id_localidad;
                opt.textContent = `${loc.nombre_centro_trabajo} (${loc.estado})`;
                select.appendChild(opt);
            });
        })
        .catch(err => console.error("Error cargando localidades:", err));
}


/********************************************
 *     REGISTRO DE PRODUCTOS (CRUD)
 ********************************************/

function configurarRegistroProductos() {

    const formulario = document.getElementById("formRegistroProductos");
    if (!formulario) return;

    formulario.addEventListener("submit", function (e) {
        e.preventDefault();

        // Validación global
        if (!validarFormularioProductos()) return;

        confirmar("¿Registrar Producto?", "¿Deseas continuar?")
            .then(r => {
                if (!r.isConfirmed) return;

                apiRequestProductos("registrar", formulario)
                    .then(r => r.text())
                    .then(resp => {
                        manejarRespuestaCRUD(
                            resp,
                            "Producto registrado correctamente.",
                            null
                        );
                        // Limpia los campos
                        formulario.reset();

                        // Limpia errores visuales si existen
                        document.querySelectorAll('.error-message').forEach(e => e.classList.remove('show'));
                        document.querySelectorAll('.error').forEach(e => e.classList.remove('error'));

                        // Limpia el peso volumétrico
                        document.getElementById("peso_volumetrico").value = "";
                    })
                    .catch(() =>
                        alerta("Error", "Ocurrió un error al registrar el producto.", "error")
                    );
            });
    });
}


/********************************************
 *  UTILIDADES GENERALES PARA CRUD (API)
 ********************************************/

/**
 * Hacer peticiones AJAX al backend.
 * Igual que apiRequestUsuarios(), pero versión para productos.
 */
function apiRequestProductos(accion, datos = null) {

    const formData = datos instanceof HTMLFormElement
        ? new FormData(datos)
        : new FormData();

    if (datos && !(datos instanceof HTMLFormElement)) {
        for (const k in datos) {
            formData.append(k, datos[k]);
        }
    }

    formData.append("action", accion);

    return fetch('/ajax/productos-ajax.php', {
        method: "POST",
        body: formData
    });
}

/**
 * Manejo centralizado de respuestas del backend
 * Igual que manejarRespuestaCRUD() que ya usabas en usuarios/personal.
 */
function manejarRespuestaCRUD(respuesta, mensajeExito, redireccion = null) {

    if (respuesta.trim() === "OK") {
        alerta("Éxito", mensajeExito, "success").then(() => {
            if (redireccion) window.location.href = redireccion;
        });
    } else {
        alerta("Error", respuesta, "error");
    }
}