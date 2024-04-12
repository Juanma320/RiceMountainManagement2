<?php
include('includes/includes.php');
include('includes/funciones.php');

// Verificar si se recibió un ID de detalle de compra
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['detalleID']) && isset($_GET['compraID']) && isset($_GET['proveedorID'])) {
    $detalleID = $_GET['detalleID'];
    $compraID = $_GET['compraID'];
    $proveedorID = $_GET['proveedorID'];

    // Obtener el valor del detalle de compra a eliminar
    $queryDetalleCompra = "SELECT * FROM detalle_compra WHERE DetalleCompraID = $detalleID";
    $resultDetalleCompra = mysqli_query($conexion, $queryDetalleCompra);
    $rowDetalleCompra = mysqli_fetch_assoc($resultDetalleCompra);
    $valorDetalleCompra = $rowDetalleCompra['Valor'];

    // Eliminar el detalle de compra de la tabla Detalle_Compra
    $queryEliminarDetalleCompra = "DELETE FROM detalle_compra WHERE DetalleCompraID = $detalleID";
    $resultEliminarDetalleCompra = mysqli_query($conexion, $queryEliminarDetalleCompra);
    if ($resultEliminarDetalleCompra) {
        // Actualizar el valor total de la compra en la tabla Compras
        $queryActualizarValorCompra = "UPDATE compras SET ValorCompra = ValorCompra - $valorDetalleCompra WHERE CompraID = $compraID";
        $resultActualizarValorCompra = mysqli_query($conexion, $queryActualizarValorCompra);
        if (!$resultActualizarValorCompra) {
            echo "Error al actualizar el valor total de la compra: " . mysqli_error($conexion);
        }

        // Redirigir a la página de agregar detalles de compra con el ID del proveedor
        header("Location: agregar_detalle_compra.php?compraID=$compraID&proveedorID=$proveedorID");
        exit();
    } else {
        echo "Error al eliminar el detalle de compra: " . mysqli_error($conexion);
    }
} else {
    echo "<p>No se recibió un ID de detalle de compra válido.</p>";
}
?>
