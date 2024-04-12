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
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil</title>
</head>
<body>

    <?php include('includes/navbar.php'); ?>

    <h1>Perfil de Usuario</h1>
    <p><strong>Nombre de Usuario:</strong> <?php echo $usuario['NombreUsuario']; ?></p>
    <p><strong>Documento de Identidad:</strong> <?php echo $usuario['DocumentoIdentidad']; ?></p>
    <p><strong>Rol:</strong> <?php echo obtenerNombreRol($conexion, $usuario['RolID']); ?></p>
    <p><strong>Correo Electrónico:</strong> <?php echo $usuario['Correo']; ?></p>
    <a href="editar_perfil.php">Cambiar contraseña</a>
    <a href="editar_user.php">Editar Perfil</a>
    <?php
// Verificar si el usuario tiene permiso de administrador
if ($_SESSION['RolID'] == 1) {
    echo '<p><a href="crear_admin.php"><button>Crear Administrador</button></a></p>';
}
?>

</body>
</html>
