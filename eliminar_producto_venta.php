<?php
include('includes/includes.php');
include('includes/funciones.php');

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['detalleVentaID'])) {
    $detalleVentaID = $_GET['detalleVentaID'];

    // Obtener la cantidad vendida que se va a eliminar
    $queryCantidadVendida = "SELECT dv.Cantidad, dv.ProductoID
                             FROM detalles_venta dv
                             WHERE dv.DetalleVentaID = $detalleVentaID";
    $resultCantidadVendida = mysqli_query($conexion, $queryCantidadVendida);
    $rowCantidadVendida = mysqli_fetch_assoc($resultCantidadVendida);
    $cantidadVendida = $rowCantidadVendida['Cantidad'];
    $productoID = $rowCantidadVendida['ProductoID'];

    // Consulta para eliminar el detalle de venta
    $queryEliminar = "DELETE FROM detalles_venta WHERE DetalleVentaID = $detalleVentaID";
    $resultEliminar = mysqli_query($conexion, $queryEliminar);

    // Verificar si se pudo eliminar el detalle de venta
    if ($resultEliminar) {
        echo "Producto eliminado correctamente.";

        // Actualizar la cantidad vendida en la tabla inventario_producto
        $queryActualizarInventario = "UPDATE inventario_producto
                                      SET CantidadVendida = CantidadVendida - $cantidadVendida
                                      WHERE ProductoID = $productoID";
        mysqli_query($conexion, $queryActualizarInventario);
    } else {
        echo "Error al eliminar el producto: " . mysqli_error($conexion);
    }

    // Redirigir al usuario de vuelta a la página de detalles de venta con la misma ventaID
    $ventaID = $_GET['ventaID'];
    $clienteID = $_GET['clienteID'];
    header("Location: agregar_detalle_venta.php?ventaID=$ventaID&clienteID=$clienteID");
    exit();
} else {
    echo "Acceso no válido.";
}
?>
