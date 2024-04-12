<?php
include('includes/includes.php');
include('includes/funciones.php');

// Verificar si se recibió un ID de producto proveedor
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['productoID']) && isset($_GET['proveedorID'])) {
    $productoID = mysqli_real_escape_string($conexion, $_GET['productoID']);
    $proveedorID = mysqli_real_escape_string($conexion, $_GET['proveedorID']);

    // Verificar permisos o cualquier otra validación necesaria

    if (borrarProveedores_Productos($conexion, $productoID)) {
        echo "<p>Producto desanclado con éxito.</p>";
    } else {
        echo "<p>Error al desanclar el producto.</p>";
    }
} else {
    echo "<p>No se proporcionaron ID de producto y proveedor válidos.</p>";
}
?>
