<?php
// config-seguridad.php

// Verificar si la sesión ya está activa
if (!isset($_SESSION)) {
    // Desactivar la caché en el lado del cliente
    header('Cache-Control: no cache');
    session_cache_limiter('private_no_expire');
    
    // Configurar el tiempo de vida de la cookie de sesión en segundos (un día en este caso)
    ini_set('session.cookie_lifetime', 86400);
    
    // Iniciar o reanudar una sesión
    session_start();
}

// Resto del código...

// Aquí podrías agregar más configuraciones de seguridad o lógica adicional

// Por ejemplo, podrías definir funciones útiles
function validarUsuario() {
    // Lógica de validación de usuario
}

// También podrías realizar otras configuraciones necesarias para tu aplicación
// ...

?>
