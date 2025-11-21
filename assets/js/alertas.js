// Alerta simple
function alerta(titulo, mensaje, tipo = "info") {
    return Swal.fire({
        title: titulo,
        text: mensaje,
        icon: tipo,
        confirmButtonText: "Aceptar",
        confirmButtonColor: "#4a1026" // color vino
    });
}

// Confirmación con dos botones
function confirmar(titulo, mensaje, tipo = "question") {
    return Swal.fire({
        title: titulo,
        text: mensaje,
        icon: tipo,
        showCancelButton: true,
        confirmButtonText: "Confirmar",
        cancelButtonText: "Cancelar",
        confirmButtonColor: "#4a1026",
        cancelButtonColor: "#6c757d"
    });
}

/*
Tipo	Descripción
success	Éxito / acción realizada
error	Error / fallo
warning	Advertencia
info	Información
question	Pregunta / confirmación*/