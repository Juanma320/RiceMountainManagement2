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
        $nuevoCorreo = mysqli_real_escape_string($conexion, $_POST['nuevo_correo']);
        $query = "UPDATE Usuarios SET NombreUsuario = '$nuevoNombreUsuario', Correo = '$nuevoCorreo' WHERE UsuarioID = {$usuario['UsuarioID']}";
        $resultado = mysqli_query($conexion, $query);

        if ($resultado) {
            header('Location: perfil.php');
            exit();
        } else {
            $mensajeError = "Error al actualizar el perfil.";
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
    <title>Editar Usuario</title>
</head>
<body>

    <?php include('includes/navbar.php'); ?>

    <h1>Editar Usuario</h1>

    <?php if (isset($mensajeError)): ?>
        <p style="color: red;"><?php echo $mensajeError; ?></p>
    <?php endif; ?>

    <form method="POST" action="">
    
        <label for="nuevo_correo">Nuevo Correo Electrónico:</label><br>
        <input type="email" id="nuevo_correo" name="nuevo_correo" value="<?php echo $usuario['Correo']; ?>" required><br>

        <input type="submit" value="Guardar Cambios">
        <a href="perfil.php">Cancelar</a>
    </form>

</body>
</html>
