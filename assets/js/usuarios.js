// -------------------------
//  REFERENCIAS A LOS CAMPOS
// -------------------------
const form = document.querySelector("form");

const inputs = {
    nombre_usuario: document.getElementById('nombre_usuario'),
    email: document.getElementById('email'),
    clave_personal: document.getElementById('clave_personal'),
    password: document.getElementById('password'),
    confirm_password: document.getElementById('confirm_password')
};

// -------------------------
//  FUNCIONES AUXILIARES
// -------------------------
const showError = (input, message) => {
    let errorDiv = input.parentElement.querySelector('.invalid-feedback');

    // Si no existe el div, lo creamos
    if (!errorDiv) {
        errorDiv = document.createElement('div');
        errorDiv.classList.add('invalid-feedback');
        input.parentElement.appendChild(errorDiv);
    }

    errorDiv.textContent = message;
    errorDiv.style.display = "block";
    input.classList.add('is-invalid');
};

const clearError = (input) => {
    let errorDiv = input.parentElement.querySelector('.invalid-feedback');
    if (errorDiv) {
        errorDiv.textContent = "";
        errorDiv.style.display = "none";
    }
    input.classList.remove('is-invalid');
};

// -------------------------
//  VALIDACIONES INDIVIDUALES
// -------------------------

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
        return showError(inputs.clave_personal, "Debe contener letras o números (3-20 caracteres)");
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

// ---------------------------------
// VALIDACIÓN GLOBAL DEL FORMULARIO
// ---------------------------------
function validateForm() {
    checkNombreUsuario();
    checkEmail();
    checkClavePersonal();
    checkPassword();
    checkConfirmPassword();

    return !document.querySelector('.is-invalid');
}

// ---------------------------------
// VALIDACIONES EN TIEMPO REAL
// ---------------------------------
inputs.nombre_usuario.addEventListener("input", checkNombreUsuario);
inputs.email.addEventListener("input", checkEmail);
inputs.clave_personal.addEventListener("input", checkClavePersonal);
inputs.password.addEventListener("input", checkPassword);
inputs.confirm_password.addEventListener("input", checkConfirmPassword);

// ---------------------------------
// ENVÍO DEL FORMULARIO CON AJAX
// ---------------------------------
form.addEventListener("submit", function (e) {
    e.preventDefault();

    if (!validateForm()) return;

    const formData = new FormData(form);
    formData.append("action", "registrar");

    fetch('/ajax/usuarios-ajax.php', {
        method: "POST",
        body: formData
    })
    .then(response => response.text())
    .then(resp => {
        if (resp.trim() === "OK") {
            alert("Usuario registrado correctamente.");
            window.location.href = "index.php";
        } else {
            alert("Error: " + resp);
        }
    })
    .catch(err => console.error("Error en petición AJAX:", err));
});
