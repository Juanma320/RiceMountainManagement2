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
    $proveedorID = mysqli_real_escape_string($conexion, $_POST['proveedorID']);
    $fechaCompra = mysqli_real_escape_string($conexion, $_POST['fecha']);
    $estadoCompraID = 5; // Estado Pedido Creado
    $valorCompra = 0; // Valor inicial en 0
    $fechaCreacion = date('Y-m-d H:i:s');

    // Insertar la compra en la tabla Compras
    $queryInsertCompra = "INSERT INTO Compras (ProveedorID, UsuarioID, FechaCompra, EstadoCompraID, ValorCompra, FechaCreacion) VALUES ($proveedorID, $usuarioID, '$fechaCompra', $estadoCompraID, $valorCompra, '$fechaCreacion')";
    $resultInsertCompra = mysqli_query($conexion, $queryInsertCompra);

    // Obtener el ID de la compra recién creada
    $compraID = mysqli_insert_id($conexion);

    if ($resultInsertCompra) {
        // Redirigir a la página de agregar detalles de compra con el ID de la compra
        header("Location: agregar_detalle_compra.php?compraID=$compraID&proveedorID=$proveedorID");
        exit();
    } else {
        echo "Error al crear la compra: " . mysqli_error($conexion);
    }
}
?>
