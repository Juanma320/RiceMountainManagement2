<?php
// Archivo: login.php

// Incluir configuraciones y funciones comunes
include('includes/includes.php');
include('includes/funciones.php');


// Verificar si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener datos del formulario
    $nombreUsuario = $_POST['nombreUsuario'];
    $contrasena = $_POST['contrasena'];

    // Consultar la base de datos para verificar las credenciales
    $usuario = verificarCredenciales($conexion, $nombreUsuario, $contrasena);

    // Verificar las credenciales del usuario
    if ($usuario !== false) {
        // Guardar información del usuario en la sesión
        $_SESSION['UsuarioID'] = $usuario['UsuarioID'];
        $_SESSION['NombreUsuario'] = $usuario['NombreUsuario'];

        // Verificar si la clave 'RolID' existe antes de acceder a ella
        if (isset($usuario['RolID'])) {
            $_SESSION['RolID'] = $usuario['RolID'];

            // Redireccionar según el rol
            switch ($usuario['RolID']) {
                case 1: // Admin
                    header('Location: indexadmin.php');
                    break;
                case 2: // Coordinador
                    header('Location: indexcoordinador.php');
                    break;
                case 3: // Financiero
                    header('Location: indexfinanciero.php');
                    break;
                default:
                    // En caso de un rol desconocido, podrías redirigir a una página por defecto o mostrar un mensaje de error.
                    break;
            }

            exit(); // Asegúrate de salir después de la redirección.
        } else {
            // La clave 'RolID' no está definida en los datos del usuario
            echo "watafa mi loco.";
        }
    } else {
        // Mostrar mensaje de usuario desconocido o contraseña incorrecta.
        echo "Error: Usuario desconocido o contraseña incorrecta. Rellene nuevamente los datos.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>

    <h1>Login</h1>

    <form method="post" action="">
        <label for="nombreUsuario">Nombre de Usuario:</label>
        <input type="text" name="nombreUsuario" required>

        <br>

        <label for="contrasena">Contraseña:</label>
        <input type="password" name="contrasena" required>

        <br>

        <input type="submit" value="Iniciar Sesión">
    </form>

</body>
</html>
