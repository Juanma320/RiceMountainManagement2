<?php
include('includes/includes.php');
include('includes/funciones.php');

// Verificar si se recibió un ID de proveedor
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $proveedorID = $_POST['proveedorID'];
    $productoID = $_POST['producto'];
    $cantidad = $_POST['cantidad'];
    $compraID = $_POST['compraID'];

    // Obtener el PrecioUnitario del producto desde la tabla Precio_compra
    $queryPrecioUnitario = "SELECT PrecioUnitario FROM precio_compras WHERE ProductoID = $productoID ORDER BY FechaInicio DESC LIMIT 1";
    $resultPrecioUnitario = mysqli_query($conexion, $queryPrecioUnitario);
    $rowPrecioUnitario = mysqli_fetch_assoc($resultPrecioUnitario);
    $precioUnitario = $rowPrecioUnitario['PrecioUnitario'];

    // Calcular el valor total del detalle de compra
    $valorTotal = $cantidad * $precioUnitario;

    // Insertar el detalle de compra en la tabla Detalle_Compra con el valor calculado
    $queryInsertDetalleCompra = "INSERT INTO detalle_compra (CompraID, ProductoID, Cantidad, Valor) VALUES ($compraID, $productoID, $cantidad, $valorTotal)";
    $resultInsertDetalleCompra = mysqli_query($conexion, $queryInsertDetalleCompra);
    if ($resultInsertDetalleCompra) {
        // Actualizar el valor total de la compra en la tabla Compras
        $queryUpdateValorCompra = "UPDATE compras SET ValorCompra = ValorCompra + $valorTotal WHERE CompraID = $compraID";
        $resultUpdateValorCompra = mysqli_query($conexion, $queryUpdateValorCompra);
        if (!$resultUpdateValorCompra) {
            echo "Error al actualizar el valor total de la compra: " . mysqli_error($conexion);
        }

        // Redirigir a la página de agregar detalles de compra con el ID del proveedor
        header("Location: agregar_detalle_compra.php?compraID=$compraID&proveedorID=$proveedorID");
        exit();
    } else {
        echo "Error al agregar el detalle de compra: " . mysqli_error($conexion);
    }
} else {
    echo "<p>No se recibieron datos para procesar.</p>";
}
?>
