<?php
define('URL_BASE', 'http://localhost/tu_proyecto/');
define('RUTA_IMAGENES', '/ruta/absoluta/al/directorio/imagenes/'); // Cambia esto con la ruta real

function redirigirSiNoAutenticado() {
    if (!isset($_SESSION['usuario_id'])) {
        header('Location: ' . URL_BASE . 'login.php');
        exit();
    }
}

function redirigirSegunRol($rol) {
    switch ($rol) {
        case 1:
            header('Location: ' . URL_BASE . 'administrador.php');
            exit();
        case 2:
            header('Location: ' . URL_BASE . 'coordinador.php');
            exit();
        case 3:
            header('Location: ' . URL_BASE . 'financiero.php');
            exit();
        default:
            // Puedes manejar otros roles aquí según sea necesario
    }
}
?>
