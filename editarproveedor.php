<?php
// Archivo: editarproveedor.php

// Incluir configuraciones y funciones comunes
include('includes/includes.php');
include('includes/funciones.php');
$row = obtenerDatosUsuario($conexion, $_SESSION['NombreUsuario']);
// Verificar si el usuario tiene el rol de administrador
if ($_SESSION['RolID'] != 1) {
    // Si no es administrador, redirigir a la página de inicio
    header('Location: login.php');
    exit();
}

// Verificar si se proporciona un ID de proveedor válido en la URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    // Redirigir a la página de gestión de proveedores si no hay ID válido
    header('Location: gestionproveedores.php');
    exit();
}

$proveedorID = $_GET['id'];

// Obtener información del proveedor a editar
$proveedor = obtenerProveedorPorID($conexion, $proveedorID);

// Verificar si el proveedor existe
if (!$proveedor) {
    // Redirigir a la página de gestión de proveedores si el proveedor no existe
    header('Location: gestionproveedores.php');
    exit();
}

// Procesar el formulario de edición si se envía
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener los datos del formulario
    $nombre = mysqli_real_escape_string($conexion, $_POST['nombre']);

    $telefono = mysqli_real_escape_string($conexion, $_POST['telefono']);
    $correo = mysqli_real_escape_string($conexion, $_POST['correo']);
    $contacto = mysqli_real_escape_string($conexion, $_POST['contacto']);
    $telefonoContacto = mysqli_real_escape_string($conexion, $_POST['telefono_contacto']);
    $nit = mysqli_real_escape_string($conexion, $_POST['nit']);

    // Validar y actualizar la información del proveedor en la base de datos
    if (editarProveedor($conexion, $proveedorID, $nombre, $telefono, $correo, $contacto, $telefonoContacto, $nit)) {
        // Redirigir a la página de gestión de proveedores después de la edición
        header('Location: gestionproveedores.php');
        exit();
    } else {
        // Mostrar un mensaje de error si la edición falla
        $mensajeError = "Error al editar el proveedor.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Proveedor</title>
</head>
<body>

    <?php include('includes/navbar.php'); ?>

    <h1>Editar Proveedor</h1>

    <?php
    // Mostrar mensaje de error si existe
    if (isset($mensajeError)) {
        echo "<p>{$mensajeError}</p>";
    }
    ?>

    <form method="post">
        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($proveedor['NombreProveedor']); ?>" required>
        <br>

        <label for="telefono">Teléfono:</label>
        <input type="text" id="telefono" name="telefono" value="<?php echo htmlspecialchars($proveedor['Telefono']); ?>" required>
        <br>

        <label for="correo">Correo Electrónico:</label>
        <input type="email" id="correo" name="correo" value="<?php echo htmlspecialchars($proveedor['CorreoElectronico']); ?>" required>
        <br>

        <label for="contacto">Contacto:</label>
        <input type="text" id="contacto" name="contacto" value="<?php echo htmlspecialchars($proveedor['Contacto']); ?>" required>
        <br>

        <label for="telefono_contacto">Teléfono de Contacto:</label>
        <input type="text" id="telefono_contacto" name="telefono_contacto" value="<?php echo htmlspecialchars($proveedor['TelefonoContacto']); ?>" required>
        <br>

        <label for="nit">NIT:</label>
        <input type="text" id="nit" name="nit" value="<?php echo htmlspecialchars($proveedor['NIT']); ?>" required>
        <br>

        <input type="submit" value="Guardar Cambios">
        <a href="gestionproveedores.php">Cancelar</a>
    </form>

</body>
</html>
