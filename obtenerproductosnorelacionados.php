<?php
include('includes/includes.php');
include('includes/funciones.php');

$proveedorID = obtenerProveedorID($conexion);

if ($proveedorID !== null) {
    // Obtener todos los productos que no están relacionados con el proveedor
    $queryProductos = "SELECT ProductoID, NombreProducto FROM Productos WHERE ProductoID NOT IN 
                        (SELECT ProductoID FROM Proveedores_Productos WHERE ProveedorID = $proveedorID)";
    $resultProductos = mysqli_query($conexion, $queryProductos);

    if ($resultProductos && mysqli_num_rows($resultProductos) > 0) {
        while ($rowProducto = mysqli_fetch_assoc($resultProductos)) {
            echo "<option value='{$rowProducto['ProductoID']}'>{$rowProducto['NombreProducto']}</option>";
        }
    } else {
        echo "<option value=''>No hay productos disponibles</option>";
    }
} else {
    echo "No se proporcionó un ID de proveedor.";
}
?>
