/**
 * utilidades.js
 * Funciones compartidas entre pedidos.js y programacion.js.
 */

function escaparHtml(valor) {
    if (valor === null || valor === undefined) return '';
    return String(valor)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function formatearFecha(valor) {
    if (!valor) return '—';
    const fecha = new Date(valor);
    if (Number.isNaN(fecha.getTime())) return escaparHtml(valor);
    return fecha.toLocaleDateString('es-MX', { year: 'numeric', month: 'short', day: '2-digit' });
}

function formatearNumero(valor, decimales = 0) {
    const numero = Number(valor) || 0;
    return numero.toLocaleString('es-MX', { maximumFractionDigits: decimales });
}
