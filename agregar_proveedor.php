<?php
include('includes/includes.php');
include('includes/funciones.php');
$row = obtenerDatosUsuario($conexion, $_SESSION['NombreUsuario']);

// Verificar si el usuario tiene el rol de administrador
if ($_SESSION['RolID'] != 1) {
    // Si no es administrador, redirigir a la página de inicio
    header('Location: login.php');
    exit();
}

// Verificar si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validar y obtener datos del formulario
    $nombre = $_POST['nombre'] ?? '';
    $correo = $_POST['correo'] ?? '';
    $telefono = $_POST['telefono'] ?? '';
    $contacto = $_POST['contacto'] ?? '';
    $telefonoContacto = $_POST['telefonoContacto'] ?? '';
    $nit = $_POST['nit'] ?? '';

    // Validar los campos según tus requisitos
    if (empty($nombre) || empty($correo) || empty($telefono) || empty($contacto) || empty($telefonoContacto) || empty($nit)) {
        $error = "Todos los campos son obligatorios.";
    } else {
        // Llamar a la función agregarProveedor
        if (agregarProveedor($conexion, $nombre, $correo, $telefono, $contacto, $telefonoContacto, $nit)) {
            $exito = "Proveedor agregado con éxito.";
        } else {
            $error = "Error al agregar el proveedor.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Proveedor</title>
</head>
<body>

    <?php include('includes/navbar.php'); ?>

    <h1>Agregar Nuevo Proveedor</h1>

    <?php
    // Mostrar mensajes de éxito o error
    if (isset($exito)) {
        echo "<p>$exito</p>";
    } elseif (isset($error)) {
        echo "<p>$error</p>";
    }
    ?>

    <form method="post" action="">
        <div>
            <label for="nombre">Nombre del Proveedor:</label>
            <input type="text" id="nombre" name="nombre" required>
        </div>

        <div>
            <label for="correo">Correo Electrónico:</label>
            <input type="email" id="correo" name="correo" required>
        </div>

        <div>
            <label for="telefono">Teléfono:</label>
            <input type="text" id="telefono" name="telefono" required>
        </div>

        <div>
            <label for="contacto">Contacto:</label>
            <input type="text" id="contacto" name="contacto" required>
        </div>

        <div>
            <label for="telefonoContacto">Teléfono de Contacto:</label>
            <input type="text" id="telefonoContacto" name="telefonoContacto" required>
        </div>

        <div>
            <label for="nit">NIT:</label>
            <input type="text" id="nit" name="nit" required>
        </div>

        <button type="submit">Agregar Proveedor</button>
        <a href="gestionproveedores.php">Cancelar</a>
    </form>

</body>
</html>
