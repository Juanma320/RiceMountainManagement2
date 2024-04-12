<?php
include('includes/includes.php');
include('includes/funciones.php');

// Verificar si se recibió un ID de compra
if (isset($_POST['compraID'])) {
    $compraID = mysqli_real_escape_string($conexion, $_POST['compraID']);

    // Actualizar el estado de la compra a "pedido cancelado"
    $queryActualizarCompra = "UPDATE Compras SET EstadoCompraID = 2 WHERE CompraID = $compraID";
    $resultActualizarCompra = mysqli_query($conexion, $queryActualizarCompra);

    if ($resultActualizarCompra) {
        // Redirigir a la página de información del proveedor
        if (isset($_POST['proveedorID'])) {
            $proveedorID = mysqli_real_escape_string($conexion, $_POST['proveedorID']);
            header("Location: infoproveedor.php?proveedorID=$proveedorID");
            exit();
        }
    } else {
        echo "Error al cancelar la compra: " . mysqli_error($conexion);
    }
} else {
    echo "No se recibieron datos para cancelar la compra.";
}
?>
