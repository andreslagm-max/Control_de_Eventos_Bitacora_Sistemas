/* =====================================================================
   SysTrace · Validaciones del formulario y comportamiento del cliente.

   Reglas comprobadas antes de enviar el formulario de registro:
     - Campos vacíos.
     - Campos numéricos (severidad y tiempo de resolución).
     - Longitud de datos (sistema, responsable, descripción).
     - Valores incorrectos (opciones fuera de catálogo, rangos, fecha futura).
     - Correo electrónico del responsable.
   Si algo falla se bloquea el envío, se marca cada campo y se enfoca
   el primero con error. El servidor repite las mismas reglas en PHP.
   ===================================================================== */
(function () {
    'use strict';

    var TIPOS   = ['Incidente', 'Mantenimiento', 'Alerta', 'Cambio', 'Respaldo'];
    var ESTADOS = ['Abierto', 'En proceso', 'Resuelto', 'Cerrado'];
    var REGEX_CORREO = /^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/;

    /* ---------- utilidades ---------- */

    function campoDe(elemento) {
        return elemento.closest('.campo');
    }

    function mostrarError(elemento, mensaje) {
        var campo = campoDe(elemento);
        var salida = campo ? campo.querySelector('.error-campo') : null;
        if (campo) { campo.classList.add('invalido'); }
        if (salida) { salida.textContent = mensaje; }
    }

    function limpiarError(elemento) {
        var campo = campoDe(elemento);
        var salida = campo ? campo.querySelector('.error-campo') : null;
        if (campo) { campo.classList.remove('invalido'); }
        if (salida) { salida.textContent = ''; }
    }

    function esEnteroValido(texto) {
        return /^-?\d+$/.test(texto.trim());
    }

    function ahoraLocalISO() {
        // Formato yyyy-MM-ddTHH:mm en hora local, sin segundos.
        var d = new Date();
        d.setSeconds(0, 0);
        var pad = function (n) { return (n < 10 ? '0' : '') + n; };
        return d.getFullYear() + '-' + pad(d.getMonth() + 1) + '-' + pad(d.getDate()) +
               'T' + pad(d.getHours()) + ':' + pad(d.getMinutes());
    }

    /* ---------- reglas por campo (devuelven '' si el valor es válido) ---------- */

    var reglas = {
        sistema: function (v) {
            v = v.trim();
            if (v === '') { return 'El sistema afectado es obligatorio.'; }
            if (v.length < 3 || v.length > 60) { return 'Debe tener entre 3 y 60 caracteres.'; }
            return '';
        },
        tipo: function (v) {
            if (v === '') { return 'Selecciona el tipo de evento.'; }
            if (TIPOS.indexOf(v) === -1) { return 'El tipo seleccionado no es válido.'; }
            return '';
        },
        severidad: function (v) {
            v = v.trim();
            if (v === '') { return 'La severidad es obligatoria.'; }
            if (!esEnteroValido(v)) { return 'La severidad debe ser un número entero.'; }
            var n = parseInt(v, 10);
            if (n < 1 || n > 5) { return 'La severidad debe estar entre 1 y 5.'; }
            return '';
        },
        fecha_evento: function (v) {
            if (v === '') { return 'Indica la fecha y hora del evento.'; }
            var fecha = new Date(v);
            if (isNaN(fecha.getTime())) { return 'La fecha no tiene un formato válido.'; }
            if (fecha.getTime() > Date.now()) { return 'La fecha no puede ser futura.'; }
            return '';
        },
        responsable: function (v) {
            v = v.trim();
            if (v === '') { return 'El responsable es obligatorio.'; }
            if (v.length < 3 || v.length > 80) { return 'Debe tener entre 3 y 80 caracteres.'; }
            return '';
        },
        correo_responsable: function (v) {
            v = v.trim();
            if (v === '') { return 'El correo es obligatorio.'; }
            if (v.length > 120) { return 'El correo no puede superar 120 caracteres.'; }
            if (!REGEX_CORREO.test(v)) { return 'Escribe un correo válido (nombre@dominio.com).'; }
            return '';
        },
        tiempo_resolucion_min: function (v) {
            v = v.trim();
            if (v === '') { return 'Indica el tiempo de resolución (0 si no aplica).'; }
            if (!esEnteroValido(v)) { return 'Debe ser un número entero de minutos.'; }
            var n = parseInt(v, 10);
            if (n < 0) { return 'El tiempo no puede ser negativo.'; }
            if (n > 10080) { return 'El tiempo no puede superar 10080 minutos (una semana).'; }
            return '';
        },
        estado: function (v) {
            if (v === '') { return 'Selecciona el estado.'; }
            if (ESTADOS.indexOf(v) === -1) { return 'El estado seleccionado no es válido.'; }
            return '';
        },
        descripcion: function (v) {
            v = v.trim();
            if (v === '') { return 'La descripción es obligatoria.'; }
            if (v.length < 10) { return 'La descripción debe tener al menos 10 caracteres.'; }
            if (v.length > 500) { return 'La descripción no puede superar 500 caracteres.'; }
            return '';
        }
    };

    /* ---------- validación de un campo y del formulario completo ---------- */

    function validarCampo(elemento) {
        var regla = reglas[elemento.name];
        if (!regla) { return true; }
        var mensaje = regla(elemento.value);
        if (mensaje) { mostrarError(elemento, mensaje); return false; }
        limpiarError(elemento);
        return true;
    }

    function validarFormulario(form) {
        var primerError = null;
        Object.keys(reglas).forEach(function (nombre) {
            var elemento = form.elements[nombre];
            if (!elemento) { return; }
            if (!validarCampo(elemento) && !primerError) { primerError = elemento; }
        });
        if (primerError) { primerError.focus(); }
        return primerError === null;
    }

    /* ---------- inicialización ---------- */

    function iniciarFormularioEvento(form) {
        var descripcion = form.elements.descripcion;
        var contador = document.getElementById('contador-descripcion');
        var fecha = form.elements.fecha_evento;

        if (fecha) { fecha.max = ahoraLocalISO(); }

        if (descripcion && contador) {
            var actualizarContador = function () { contador.textContent = String(descripcion.value.length); };
            descripcion.addEventListener('input', actualizarContador);
            actualizarContador();
        }

        Object.keys(reglas).forEach(function (nombre) {
            var elemento = form.elements[nombre];
            if (!elemento) { return; }
            elemento.addEventListener('blur', function () { validarCampo(elemento); });
            elemento.addEventListener('input', function () {
                if (campoDe(elemento) && campoDe(elemento).classList.contains('invalido')) {
                    validarCampo(elemento);
                }
            });
        });

        form.addEventListener('submit', function (evento) {
            if (!validarFormulario(form)) {
                evento.preventDefault();
            }
        });

        form.addEventListener('reset', function () {
            Object.keys(reglas).forEach(function (nombre) {
                var elemento = form.elements[nombre];
                if (elemento) { limpiarError(elemento); }
            });
            if (contador) { setTimeout(function () { contador.textContent = '0'; }, 0); }
        });
    }

    function iniciarConfirmaciones() {
        var formularios = document.querySelectorAll('form[data-confirmar]');
        Array.prototype.forEach.call(formularios, function (form) {
            form.addEventListener('submit', function (evento) {
                if (!window.confirm(form.getAttribute('data-confirmar'))) {
                    evento.preventDefault();
                }
            });
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        var formEvento = document.getElementById('form-evento');
        if (formEvento) { iniciarFormularioEvento(formEvento); }
        iniciarConfirmaciones();
    });
}());
