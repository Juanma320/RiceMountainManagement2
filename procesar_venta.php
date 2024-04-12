<?php
include('includes/includes.php');
include('includes/funciones.php');

// Verificar si el usuario está autenticado y obtener su UsuarioID
$usuarioID = null;
if (isset($_SESSION['UsuarioID'])) {
    $usuarioID = $_SESSION['UsuarioID'];
} else {
    // Si no está autenticado, redirigir a la página de inicio de sesión
    header("Location: iniciar_sesion.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $clienteID = $_POST['clienteID'];
    $direccionID = $_POST['direccion'];
    $fechaVenta = $_POST['fecha'];

    // Insertar la venta en la tabla Ventas
    $queryInsertVenta = "INSERT INTO Ventas (ClienteID, DireccionID, FechaVenta, UsuarioID) VALUES ($clienteID, $direccionID, '$fechaVenta', $usuarioID)";
    $resultInsertVenta = mysqli_query($conexion, $queryInsertVenta);
    if ($resultInsertVenta) {
        $ventaID = mysqli_insert_id($conexion); // Obtener el ID de la venta recién creada

        // Redirigir a la página de agregar detalles de venta con el ID de la venta
        header("Location: agregar_detalle_venta.php?ventaID=$ventaID&clienteID=$clienteID");
        exit();
    } else {
        echo "Error al crear la venta: " . mysqli_error($conexion);
    }
}

?>
