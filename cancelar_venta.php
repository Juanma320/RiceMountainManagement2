<?php
include('includes/includes.php');
include('includes/funciones.php');
$row = obtenerDatosUsuario($conexion, $_SESSION['NombreUsuario']);

// Verificar si se recibió un ID de venta y un ID de cliente
if (isset($_POST['ventaID']) && isset($_POST['clienteID'])) {
    $ventaID = $_POST['ventaID'];
    $clienteID = $_POST['clienteID'];

    // Actualizar el estado de la venta a "Cancelada"
    $queryActualizarEstado = "UPDATE Ventas SET EstadoVentaID = 4 WHERE VentaID = $ventaID";
    $resultActualizarEstado = mysqli_query($conexion, $queryActualizarEstado);

    if ($resultActualizarEstado) {
        // Redirigir a infocliente.php con el ID del cliente
        header("Location: infocliente.php?clienteID=$clienteID");
        exit;
    } else {
        echo "Error al cancelar la venta.";
    }
} else {
    echo "No se proporcionó un ID de venta o un ID de cliente.";
}
?>
