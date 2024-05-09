<?php
include ('includes/includes.php');
include ('includes/funciones.php');

// Verificar si se recibió un ID de venta y el nuevo estado
if (isset($_GET['ventaID'], $_GET['nuevoEstado'])) {
    $ventaID = mysqli_real_escape_string($conexion, $_GET['ventaID']);
    $estadoVentaID = mysqli_real_escape_string($conexion, $_GET['nuevoEstado']);

    // Actualizar el estado de la venta en la base de datos
    $queryActualizar = "UPDATE Ventas SET EstadoVentaID = $estadoVentaID WHERE VentaID = $ventaID";
    $resultado = mysqli_query($conexion, $queryActualizar);

    // Verificar si la actualización fue exitosa
    if ($resultado) {
        echo "Estado de venta actualizado correctamente.";
    } else {
        echo "Error al actualizar el estado de la venta.";
    }
} else {
    echo "Error: No se recibió el ID de venta o el nuevo estado.";
}