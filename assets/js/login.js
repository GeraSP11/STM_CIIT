// Archivo: assets/js/login.js

function configurarLogin() {
    const formulario = document.getElementById("formLogin");
    if (!formulario) return;

    formulario.addEventListener("submit", function (e) {
        e.preventDefault();

        if (!formulario.checkValidity()) {
            formulario.classList.add('was-validated');
            return;
        }

        const btnLogin = document.getElementById('btnLogin');
        
        btnLogin.disabled = true;
        btnLogin.textContent = 'Iniciando sesión...';

        apiRequestLogin("login", formulario)
            .then(r => r.text())
            .then(resp => {
                if (resp === 'OK') {
                    mostrarMensaje('¡Inicio de sesión exitoso! Redirigiendo...', 'success');
                    
                    // Redirige a dashboard.php (misma carpeta que index.php)
                    setTimeout(() => {
                        window.location.href = 'dashboard.php';
                    }, 1500);
                } else {
                    mostrarMensaje(resp, 'error');
                    btnLogin.disabled = false;
                    btnLogin.textContent = 'Entrar';
                }
            })
            .catch(() => {
                mostrarMensaje('Error al procesar la solicitud. Intente nuevamente.', 'error');
                btnLogin.disabled = false;
                btnLogin.textContent = 'Entrar';
            });
    });
}

function apiRequestLogin(action, form) {
    const formData = new FormData(form);
    formData.append("action", action);

    // Desde frontend/ hacia ajax/
    return fetch("../ajax/login-ajax.php", {
        method: "POST",
        body: formData
    });
}

function mostrarMensaje(mensaje, tipo) {
    const mensajeAlerta = document.getElementById('mensajeAlerta');
    
    if (!mensajeAlerta) return;
    
    const clases = {
        'success': 'alert alert-success',
        'error': 'alert alert-danger',
        'warning': 'alert alert-warning',
        'info': 'alert alert-info'
    };
    
    mensajeAlerta.className = clases[tipo] || 'alert alert-info';
    mensajeAlerta.textContent = mensaje;
    mensajeAlerta.style.display = 'block';
    
    mensajeAlerta.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

document.addEventListener("DOMContentLoaded", function() {
    configurarLogin();
});