/* productos-validaciones.js
   - Validaciones en tiempo real y validación global
   - crear spans de error automáticamente si faltan
   - cálculo de peso volumétrico (factor 167)
   - espera IDs: nombre_producto, ubicacion_producto, peso, altura, largo, ancho,
     peso_soportado, cajas_cama (o cajas_por_cama), cajas_por_cama, camas_por_pallet,
     peso_volumetrico, unidades_existencia, tipo_embalaje, tipo_mercancia
*/

/* UTIL: crea span de error si no existe */
function ensureErrorSpan(fieldId) {
    const existing = document.getElementById('error_' + fieldId);
    if (existing) return existing;

    const field = document.getElementById(fieldId);
    if (!field) return null;

    const span = document.createElement('span');
    span.id = 'error_' + fieldId;
    span.className = 'error-message';
    // insertar justo después del input/select
    if (field.parentElement) {
        field.parentElement.appendChild(span);
    } else {
        field.after(span);
    }
    return span;
}

/* Mostrar y limpiar errores */
function mostrarErrorCampo(fieldId, mensaje) {
    const input = document.getElementById(fieldId);
    if (!input) return;
    input.classList.add('error');
    const span = ensureErrorSpan(fieldId);
    if (span) {
        span.textContent = mensaje;
        span.classList.add('show');
    } else {
        // fallback
        console.warn('No se pudo mostrar span para', fieldId);
    }
}

function limpiarErrorCampo(fieldId) {
    const input = document.getElementById(fieldId);
    if (!input) return;
    input.classList.remove('error');
    const span = document.getElementById('error_' + fieldId);
    if (span) {
        span.textContent = '';
        span.classList.remove('show');
    }
}

/* Validadores reutilizables (adaptados del script antiguo) */
function validarNumeroPositivoDecimalEvento(evento, fieldId, mensajeError) {
    let valor = evento.target.value;
    let valorLimpio = valor.replace(/[^\d.]/g, '');

    // permitir un solo punto
    const partes = valorLimpio.split('.');
    if (partes.length > 2) {
        valorLimpio = partes[0] + '.' + partes.slice(1).join('');
    }
    // limitar a 2 decimales
    if (partes.length === 2 && partes[1].length > 2) {
        valorLimpio = partes[0] + '.' + partes[1].substring(0, 2);
    }
    if (valor !== valorLimpio) evento.target.value = valorLimpio;

    const numero = parseFloat(valorLimpio);
    if (valorLimpio === '' || isNaN(numero) || numero <= 0) {
        mostrarErrorCampo(fieldId, mensajeError);
    } else {
        limpiarErrorCampo(fieldId);
    }
}

function validarEnteroPositivoEvento(evento, fieldId, mensajeError) {
    let valor = evento.target.value;
    let valorLimpio = valor.replace(/[^\d]/g, '');
    if (valor !== valorLimpio) evento.target.value = valorLimpio;
    const numero = parseInt(valorLimpio);
    if (valorLimpio === '' || isNaN(numero) || numero <= 0) {
        mostrarErrorCampo(fieldId, mensajeError);
    } else {
        limpiarErrorCampo(fieldId);
    }
}

function validarEnteroCeroOPositivoEvento(evento, fieldId, mensajeError) {
    let valor = evento.target.value;
    let valorLimpio = valor.replace(/[^\d]/g, '');
    if (valor !== valorLimpio) evento.target.value = valorLimpio;
    const numero = parseInt(valorLimpio);
    if (valorLimpio === '' || isNaN(numero) || numero < 0) {
        mostrarErrorCampo(fieldId, mensajeError);
    } else {
        limpiarErrorCampo(fieldId);
    }
}

/* Calcular peso volumétrico usando factor 167 (kg/m3) */
function calcularPesoVolumetrico() {
    const largo = parseFloat(document.getElementById('largo')?.value) || 0;   // cm
    const ancho = parseFloat(document.getElementById('ancho')?.value) || 0;   // cm
    const altura = parseFloat(document.getElementById('altura')?.value) || 0; // cm

    const pvInput = document.getElementById('peso_volumetrico');
    if (!pvInput) return;

    if (largo > 0 && ancho > 0 && altura > 0) {
        // Convertir cm³ a m³ dividiendo entre 1,000,000
        const volumen_m3 = (largo * ancho * altura) / 1000000; // cm³ -> m³
        
        const pv = (volumen_m3 * 167).toFixed(2); // Factor 167 kg/m³
        pvInput.value = pv;
        limpiarErrorCampo('peso_volumetrico');
    } else {
        pvInput.value = '';
        if (largo > 0 || ancho > 0 || altura > 0) {
            mostrarErrorCampo('peso_volumetrico', 'Complete dimensiones para calcular el peso volumétrico.');
        }
    }
}


/* Validación global (al enviar) */
function validarFormularioProductos() {
    let esValido = true;

    // crear spans si no existen (lista de campos que validamos)
    const campos = [
        'nombre_producto','ubicacion_producto','peso','altura','largo','ancho',
        'cajas_por_cama','camas_por_pallet','peso_soportado',
        'peso_volumetrico','unidades_existencia','tipo_embalaje','tipo_mercancia'
    ];

    campos.forEach(c => ensureErrorSpan(c));

    // limpiar marcas previas
    campos.forEach(c => limpiarErrorCampo(c));

    // nombre_producto
    const nombre = (document.getElementById('nombre_producto')?.value || '').trim();
    if (nombre === '') {
        mostrarErrorCampo('nombre_producto', 'El nombre del producto es obligatorio.');
        esValido = false;
    } else if (nombre.length > 100) {
        mostrarErrorCampo('nombre_producto', 'El nombre no debe superar 100 caracteres.');
        esValido = false;
    } else if (!/^[a-zA-Z0-9\sáéíóúÁÉÍÓÚñÑ]+$/.test(nombre)) {
        mostrarErrorCampo('nombre_producto', 'Nombre contiene caracteres inválidos.');
        esValido = false;
    }

    // ubicacion_producto
    const ubic = document.getElementById('ubicacion_producto')?.value || '';
    if (ubic === '') {
        mostrarErrorCampo('ubicacion_producto', 'Seleccione una localidad.');
        esValido = false;
    }

    // peso
    const pesoVal = parseFloat(document.getElementById('peso')?.value);
    if (isNaN(pesoVal) || pesoVal <= 0) {
        mostrarErrorCampo('peso', 'El peso debe ser un número mayor a 0.');
        esValido = false;
    }

    // dimensiones
    const alturaVal = parseFloat(document.getElementById('altura')?.value);
    const largoVal = parseFloat(document.getElementById('largo')?.value);
    const anchoVal = parseFloat(document.getElementById('ancho')?.value);

    if (isNaN(alturaVal) || alturaVal <= 0) {
        mostrarErrorCampo('altura', 'La altura debe ser mayor a 0.');
        esValido = false;
    }
    if (isNaN(largoVal) || largoVal <= 0) {
        mostrarErrorCampo('largo', 'El largo debe ser mayor a 0.');
        esValido = false;
    }
    if (isNaN(anchoVal) || anchoVal <= 0) {
        mostrarErrorCampo('ancho', 'El ancho debe ser mayor a 0.');
        esValido = false;
    }

    // cajas_por_cama
    const cajas = parseInt(document.getElementById('cajas_por_cama')?.value);
    if (isNaN(cajas) || cajas <= 0) {
        const field = document.getElementById('cajas_por_cama');
        mostrarErrorCampo(field, 'Ingrese un número válido de cajas por cama.');
        esValido = false;
    }

    // camas_por_pallet
    const camas = parseInt(document.getElementById('camas_por_pallet')?.value);
    if (isNaN(camas) || camas <= 0) {
        mostrarErrorCampo('camas_por_pallet', 'Ingrese un número válido de camas por pallet.');
        esValido = false;
    }

    // peso_soportado
    const ps = parseFloat(document.getElementById('peso_soportado')?.value);
    if (isNaN(ps) || ps <= 0) {
        mostrarErrorCampo('peso_soportado', 'El peso soportado debe ser positivo.');
        esValido = false;
    }

    // peso_volumetrico
    const pv = parseFloat(document.getElementById('peso_volumetrico')?.value);
    if (isNaN(pv) || pv <= 0) {
        mostrarErrorCampo('peso_volumetrico', 'No se pudo calcular el peso volumétrico. Verifique dimensiones/peso.');
        esValido = false;
    }

    // unidades_existencia
    const ue = parseFloat(document.getElementById('unidades_existencia')?.value);
    if (isNaN(ue) || ue < 0) {
        mostrarErrorCampo('unidades_existencia', 'Ingrese un número válido para unidades en existencia (>=0).');
        esValido = false;
    }

    // tipo_embalaje & tipo_mercancia
    const te = document.getElementById('tipo_embalaje')?.value || '';
    if (te === '') {
        mostrarErrorCampo('tipo_embalaje', 'Seleccione un tipo de embalaje.');
        esValido = false;
    }
    const tm = document.getElementById('tipo_mercancia')?.value || '';
    if (tm === '') {
        mostrarErrorCampo('tipo_mercancia', 'Seleccione un tipo de mercancía.');
        esValido = false;
    }

    return esValido;
}

/* Registrar listeners en tiempo real (protecciones incluidas) */
function configurarValidacionesTiempoRealProductos() {

    // crear spans si no existen para campos claves
    const camposACrear = ['nombre_producto','ubicacion_producto','peso','altura','largo','ancho',
        'cajas_por_cama','camas_por_pallet','peso_soportado','peso_volumetrico',
        'unidades_existencia','tipo_embalaje','tipo_mercancia'];
    camposACrear.forEach(c => ensureErrorSpan(c));

    // nombre_producto
    const nombreProducto = document.getElementById('nombre_producto');
    if (nombreProducto) {
        nombreProducto.addEventListener('input', function(e) {
            const valor = e.target.value;
            const limpio = valor.replace(/[^a-zA-Z0-9\sáéíóúÁÉÍÓÚñÑ]/g, '');
            if (valor !== limpio) e.target.value = limpio;
            if (limpio.trim() === '') mostrarErrorCampo('nombre_producto', 'El nombre del producto es obligatorio.');
            else limpiarErrorCampo('nombre_producto');
        });
    }

    // ubicacion_producto
    const ubic = document.getElementById('ubicacion_producto');
    if (ubic) {
        ubic.addEventListener('change', function(e) {
            if (e.target.value === '') mostrarErrorCampo('ubicacion_producto', 'Seleccione una localidad.');
            else limpiarErrorCampo('ubicacion_producto');
        });
    }

    // campos numéricos decimales
    const campoPeso = document.getElementById('peso');
    if (campoPeso) campoPeso.addEventListener('input', (e) => validarNumeroPositivoDecimalEvento(e, 'peso', 'El peso debe ser un número válido y mayor a 0 kilogramos'));

    const campoAltura = document.getElementById('altura');
    if (campoAltura) campoAltura.addEventListener('input', (e) => validarNumeroPositivoDecimalEvento(e, 'altura', 'La altura debe ser mayor a 0 metros.'));

    const campoLargo = document.getElementById('largo');
    if (campoLargo) campoLargo.addEventListener('input', (e) => { validarNumeroPositivoDecimalEvento(e, 'largo', 'El largo debe ser mayor a 0 metros.'); calcularPesoVolumetrico(); });

    const campoAncho = document.getElementById('ancho');
    if (campoAncho) campoAncho.addEventListener('input', (e) => { validarNumeroPositivoDecimalEvento(e, 'ancho', 'El ancho debe ser mayor a 0 metros.'); calcularPesoVolumetrico(); });

    const campoPesoSoportado = document.getElementById('peso_soportado');
    if (campoPesoSoportado) campoPesoSoportado.addEventListener('input', (e) => validarNumeroPositivoDecimalEvento(e, 'peso_soportado', 'El peso soportado debe ser positivo.'));

    // campos enteros
    const campoCajas = document.getElementById('cajas_por_cama');
    if (campoCajas) campoCajas.addEventListener('input', (e) => validarEnteroPositivoEvento(e, campoCajas.id, 'Ingrese un número válido de cajas por cama.'));

    const campoCamas = document.getElementById('camas_por_pallet');
    if (campoCamas) campoCamas.addEventListener('input', (e) => validarEnteroPositivoEvento(e, 'camas_por_pallet', 'Ingrese un número válido de camas por pallet.'));

    const campoUnidades = document.getElementById('unidades_existencia');
    if (campoUnidades) campoUnidades.addEventListener('input', (e) => validarEnteroCeroOPositivoEvento(e, 'unidades_existencia', 'Ingrese un número válido para las unidades en existencia.'));

    // tipo_embalaje y tipo_mercancia (change)
    const selEm = document.getElementById('tipo_embalaje');
    if (selEm) selEm.addEventListener('change', (e) => {
        if (e.target.value === '') mostrarErrorCampo('tipo_embalaje', 'Seleccione un tipo de embalaje.');
        else limpiarErrorCampo('tipo_embalaje');
    });

    const selMe = document.getElementById('tipo_mercancia');
    if (selMe) selMe.addEventListener('change', (e) => {
        if (e.target.value === '') mostrarErrorCampo('tipo_mercancia', 'Seleccione un tipo de mercancía.');
        else limpiarErrorCampo('tipo_mercancia');
    });

    // escuchar cambios que afecten peso volumétrico
    ['largo','ancho','altura','peso'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.addEventListener('input', calcularPesoVolumetrico);
    });
}

/* Exponer una función para inicializar validaciones desde el main */
function initValidacionesProductos() {
    configurarValidacionesTiempoRealProductos();
}

/* Si quieres auto-inicializar aquí cuando esté el formulario uncomment:
document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('formRegistroProductos')) {
        initValidacionesProductos();
    }
});
*/
