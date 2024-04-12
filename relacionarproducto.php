<?php
include('includes/includes.php');
include('includes/funciones.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $proveedorID = mysqli_real_escape_string($conexion, $_POST['proveedorID']);
    $productoID = mysqli_real_escape_string($conexion, $_POST['productoID']);

    // Insertar la nueva relación en la tabla Proveedores_Productos
    $queryInsert = "INSERT INTO Proveedores_Productos (ProveedorID, ProductoID) VALUES ($proveedorID, $productoID)";
    $resultInsert = mysqli_query($conexion, $queryInsert);

    if ($resultInsert) {
        echo "Producto agregado correctamente.";
    } else {
        echo "Error al agregar el producto.";
    }
}
?>
