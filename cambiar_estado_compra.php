<?php
include('includes/includes.php');
include('includes/funciones.php');

if (isset($_GET['compraID']) && isset($_GET['nuevoEstado'])) {
    $compraID = mysqli_real_escape_string($conexion, $_GET['compraID']);
    $nuevoEstado = mysqli_real_escape_string($conexion, $_GET['nuevoEstado']);

    // Verificar si el nuevo estado es válido para la compra actual
    $validacionEstado = validarCambioEstado($conexion, $compraID, $nuevoEstado);

    if ($validacionEstado) {
        // Actualizar el estado de la compra
        $queryActualizarEstado = "UPDATE compras SET EstadoCompraID = $nuevoEstado WHERE CompraID = $compraID";
        $resultadoActualizarEstado = mysqli_query($conexion, $queryActualizarEstado);

        if ($resultadoActualizarEstado) {
            echo "El estado de la compra se ha actualizado correctamente.";
        } else {
            echo "Error al actualizar el estado de la compra.";
        }
    } else {
        echo "El cambio de estado no es válido para esta compra.";
    }
} else {
    echo "Parámetros incompletos.";
}

function validarCambioEstado($conexion, $compraID, $nuevoEstado) {
    // Obtener el estado actual de la compra
    $queryEstadoActual = "SELECT EstadoCompraID FROM compras WHERE CompraID = $compraID";
    $resultadoEstadoActual = mysqli_query($conexion, $queryEstadoActual);
    $rowEstadoActual = mysqli_fetch_assoc($resultadoEstadoActual);
    $estadoActual = $rowEstadoActual['EstadoCompraID'];

    // Verificar si el nuevo estado es válido según las restricciones
    switch ($estadoActual) {
        case 5:
            return $nuevoEstado == 1; // Solo puede cambiar a estado 1
        case 1:
            return $nuevoEstado == 2 || $nuevoEstado == 3; // Puede cambiar a estados 2 o 3
        default:
            return false; // Resto de casos no permitidos
    }
}
?>
