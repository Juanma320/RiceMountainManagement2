<?php
include('includes/includes.php');
include('includes/funciones.php');
$row = obtenerDatosUsuario($conexion, $_SESSION['NombreUsuario']);
$proveedorID = $_POST['proveedorID'];

// Verificar si el usuario tiene el rol de administrador
if ($_SESSION['RolID'] != 1) {
    // Si no es administrador, redirigir a la página de inicio
    header('Location: login.php');
    exit();
}

// Verificar si se recibió el ID de la marca y el estado
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['marcaID']) && isset($_POST['estado'])) {
    $marcaID = $_POST['marcaID'];
    $estado = $_POST['estado'];

    // Inactivar la marca si está activa, o activarla si está inactiva
    if ($estado == 1) {
        $query = "UPDATE marcas SET Estado = 0 WHERE MarcaID = ?";
    } else {
        $query = "UPDATE marcas SET Estado = 1 WHERE MarcaID = ?";
    }

    $statement = mysqli_prepare($conexion, $query);
    mysqli_stmt_bind_param($statement, "i", $marcaID);
    if (mysqli_stmt_execute($statement)) {
        // Redirigir a la página de marcas del proveedor
        header("Location: marcasproveedor.php?id={$proveedorID}");
        exit();
    } else {
        echo "Error al cambiar el estado de la marca.";
    }
} else {
    echo "Parámetros incorrectos.";
}
?>
