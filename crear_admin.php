<?php
include('includes/includes.php');
include('includes/funciones.php');
$row = obtenerDatosUsuario($conexion, $_SESSION['NombreUsuario']);
// Verificar si el usuario tiene permiso para acceder a esta página
if ($_SESSION['RolID'] != 1) {
    // Si no tiene permiso de administrador, redirigir a la página de inicio
    header('Location: login.php');
    exit();
}

// Inicializar variables
$nombreUsuario = $correo = $contrasena = $documentoIdentidad = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener datos del formulario
    $nombreUsuario = mysqli_real_escape_string($conexion, $_POST['nombreUsuario']);
    $correo = mysqli_real_escape_string($conexion, $_POST['correo']);
    $contrasena = mysqli_real_escape_string($conexion, $_POST['contrasena']);
    $documentoIdentidad = mysqli_real_escape_string($conexion, $_POST['documentoIdentidad']);

    // Hashear la contraseña
    $hashedPassword = password_hash($contrasena, PASSWORD_DEFAULT);

    // Insertar nuevo administrador en la base de datos
    $query = "INSERT INTO Usuarios (NombreUsuario, Correo, Contra, DocumentoIdentidad, RolID, Activo) VALUES ('$nombreUsuario', '$correo', '$hashedPassword', '$documentoIdentidad', 1, 1)";
    $resultado = mysqli_query($conexion, $query);

    if ($resultado) {
        // Redirigir a una página de éxito o mostrar un mensaje
        header('Location: perfil.php');
        exit();
    } else {
        $mensajeError = "Error al crear el administrador. Por favor, inténtelo de nuevo.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Administrador</title>
</head>
<body>

    <?php include('includes/navbar.php'); ?>

    <h1>Crear Administrador</h1>

    <?php if (isset($mensajeError)): ?>
        <p style="color: red;"><?php echo $mensajeError; ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <label for="nombreUsuario">Nombre de Usuario:</label><br>
        <input type="text" id="nombreUsuario" name="nombreUsuario" required><br>

        <label for="correo">Correo Electrónico:</label><br>
        <input type="email" id="correo" name="correo" required><br>

        <label for="documentoIdentidad">Documento de Identidad:</label><br>
        <input type="text" id="documentoIdentidad" name="documentoIdentidad" required><br>

        <label for="contrasena">Contraseña:</label><br>
        <input type="password" id="contrasena" name="contrasena" required><br>

        <input type="submit" value="Crear Administrador">
        <a href="perfil.php">Cancelar</a>
    </form>

</body>
</html>
