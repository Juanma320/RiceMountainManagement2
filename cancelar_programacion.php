<?php
include('includes/includes.php');
include('includes/funciones.php');
$row = obtenerDatosUsuario($conexion, $_SESSION['NombreUsuario']);

if ($_SESSION['RolID'] != 1) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['productoID']) && isset($_GET['tipo'])) {
    $productoID = $_GET['productoID'];
    $tipo = $_GET['tipo'];

    if ($tipo === 'precio') {
        // Actualizar NuevoPrecio y FechaFin a NULL
        $query = "UPDATE precio_compras SET NuevoPrecio = NULL, FechaFin = NULL WHERE ProductoID = $productoID";
        $resultado = mysqli_query($conexion, $query);
    } elseif ($tipo === 'porcentaje') {
        // Actualizar NuevoBeneficio y FechaFinBeneficio a NULL
        $query = "UPDATE precio_compras SET NuevoBeneficio = NULL, FechaFinBeneficio = NULL WHERE ProductoID = $productoID";
        $resultado = mysqli_query($conexion, $query);
    }

    if ($resultado) {
        echo "Programación cancelada correctamente.";
    } else {
        echo "Error al cancelar la programación.";
    }
} else {
    echo "Parámetros incorrectos.";
}
?>
