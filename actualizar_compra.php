<?php
include ('includes/includes.php');
include ('includes/funciones.php');
$row = obtenerDatosUsuario($conexion, $_SESSION['UsuarioID']);

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['compraID']) && isset($_GET['nuevoEstado'])) {
    $compraID = mysqli_real_escape_string($conexion, $_GET['compraID']);
    $nuevoEstado = mysqli_real_escape_string($conexion, $_GET['nuevoEstado']);

    // Verificar si el nuevo estado es válido
    if ($nuevoEstado == 1 || $nuevoEstado == 2 || $nuevoEstado == 3) {
        // Actualizar el estado de la compra
        $queryActualizar = "UPDATE compras SET EstadoCompraID = ? WHERE CompraID = ?";
        $stmtActualizar = mysqli_prepare($conexion, $queryActualizar);
        mysqli_stmt_bind_param($stmtActualizar, "ii", $nuevoEstado, $compraID);
        mysqli_stmt_execute($stmtActualizar);

        if (mysqli_stmt_affected_rows($stmtActualizar) > 0) {
            // Redirigir a la página anterior
            header("Location: {$_SERVER['HTTP_REFERER']}");
            exit();
        } else {
            echo "Error al actualizar el estado de la compra.";
        }
    } else {
        echo "Estado de compra inválido.";
    }
} else {
    echo "Solicitud inválida.";
}