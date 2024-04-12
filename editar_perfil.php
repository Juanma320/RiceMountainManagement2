<?php
include('includes/includes.php');
include('includes/funciones.php');

$row = obtenerDatosUsuario($conexion, $_SESSION['NombreUsuario']);

// Verificar si el usuario tiene permiso para acceder a esta página
if ($_SESSION['RolID'] != 1 && $_SESSION['RolID'] != 2 && $_SESSION['RolID'] != 3) {
    // Si no tiene un rol válido, redirigir a la página de inicio
    header('Location: login.php');
    exit();
}

// Obtener datos del usuario
$usuario = obtenerDatosUsuario($conexion, $_SESSION['NombreUsuario']);

// Verificar si $usuario es un array válido y si tiene el índice 'UsuarioID'
if (is_array($usuario) && isset($usuario['UsuarioID'])) {
    // Continuar con el código para editar el perfil
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Verificar si se envió el formulario para cambiar la contraseña
        if (isset($_POST['cambiar_contrasena'])) {
            $contrasenaActual = mysqli_real_escape_string($conexion, $_POST['contrasena_actual']);
            $nuevaContrasena = mysqli_real_escape_string($conexion, $_POST['nueva_contrasena']);
            $confirmarContrasena = mysqli_real_escape_string($conexion, $_POST['confirmar_contrasena']);

            // Verificar si la contraseña actual es correcta
            $contrasenaActualDB = obtenerContrasenaUsuario($conexion, $_SESSION['NombreUsuario']);
            if (password_verify($contrasenaActual, $contrasenaActualDB)) {
                // Las contraseñas coinciden, hashear la nueva contraseña y actualizar en la base de datos
                $hashedPassword = password_hash($nuevaContrasena, PASSWORD_DEFAULT);
                $query = "UPDATE Usuarios SET Contra = '$hashedPassword' WHERE UsuarioID = {$usuario['UsuarioID']}";
                $resultado = mysqli_query($conexion, $query);

                if ($resultado) {
                    header('Location: perfil.php');
                    exit();
                } else {
                    $mensajeError = "Error al actualizar la contraseña.";
                }
            } else {
                $mensajeError = "La contraseña actual es incorrecta.";
            }
        }
    }
} else {
    // Mostrar un mensaje de error y salir del script
    echo "Error: Usuario no válido.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil</title>
</head>
<body>

    <?php include('includes/navbar.php'); ?>

    <h1>Editar Perfil</h1>

    <?php if (isset($mensajeError)): ?>
        <p style="color: red;"><?php echo $mensajeError; ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <label for="contrasena_actual">Contraseña Actual:</label><br>
        <input type="password" id="contrasena_actual" name="contrasena_actual" required><br>

        <label for="nueva_contrasena">Nueva Contraseña:</label><br>
        <input type="password" id="nueva_contrasena" name="nueva_contrasena"><br>

        <label for="confirmar_contrasena">Confirmar Contraseña:</label><br>
        <input type="password" id="confirmar_contrasena" name="confirmar_contrasena"><br>

        <input type="submit" name="cambiar_contrasena" value="Cambiar Contraseña">
        <a href="perfil.php">Cancelar</a>
    </form>

</body>
</html>
