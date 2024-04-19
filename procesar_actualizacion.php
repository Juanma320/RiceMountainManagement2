<?php
session_start(); // Iniciar sesión si no está iniciada
include ('includes/includes.php');
include ('includes/funciones.php');

// Verificar si el usuario tiene el rol de administrador
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener los datos del formulario
    $productoID = mysqli_real_escape_string($conexion, $_POST['producto_id']);
    $nuevoPrecio = mysqli_real_escape_string($conexion, $_POST['nuevo_precio']);
    $fechaFin = mysqli_real_escape_string($conexion, $_POST['fecha_fin']);

    // Actualizar el precio del producto en la base de datos
    $query = "UPDATE precio_compras SET NuevoPrecio='$nuevoPrecio', FechaFin='$fechaFin' WHERE ProductoID='$productoID'";
    $resultado = mysqli_query($conexion, $query);

    if ($resultado) {
        echo "<p>Precio del producto actualizado con éxito.</p>";
    } else {
        echo "<p>Error al actualizar el precio del producto.</p>";
    }
}

// Redirigir según el rol del usuario
if ($_SESSION['RolID'] == 3) {
    // Redirigir a la página de gestión de productos financiero
    header("Location: gestion_productos_financiero.php");
} else {
    // Redirigir a la página de gestión de productos A
    header("Location: gestionproductosA.php");
}