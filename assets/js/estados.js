// assets/js/estados.js

const estados = [
    "Aguascalientes", "Baja California", "Baja California Sur", "Campeche", "Chiapas",
    "Chihuahua", "Ciudad de México", "Coahuila", "Colima", "Durango", "Guanajuato",
    "Guerrero", "Hidalgo", "Jalisco", "México", "Michoacán", "Morelos", "Nayarit",
    "Nuevo León", "Oaxaca", "Puebla", "Querétaro", "Quintana Roo", "San Luis Potosí",
    "Sinaloa", "Sonora", "Tabasco", "Tamaulipas", "Tlaxcala", "Veracruz", "Yucatán", "Zacatecas"
];

function cargarEstados(selectId) {
    const select = document.getElementById(selectId);
    if (!select) return;

    estados.forEach(estado => {
        const option = document.createElement("option");
        option.value = estado;
        option.textContent = estado;
        select.appendChild(option);
    });
}

// Ejecuta automáticamente 
document.addEventListener("DOMContentLoaded", () => {
    cargarEstados("estados"); // reemplaza "estado" con el ID de tu select
});
