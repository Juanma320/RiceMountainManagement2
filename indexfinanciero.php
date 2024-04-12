<?php
// Archivo: indexfinanciero.php

// Incluir configuraciones y funciones comunes
include('includes/includes.php');
include('includes/funciones.php');
// Verificar si el usuario tiene el rol de financiero
if ($_SESSION['RolID'] != 3) {
    // Si no es financiero, redirigir a la página de inicio
    header('Location: login.php');
    exit();
}

// Obtener datos del usuario
$row = obtenerDatosUsuario($conexion, $_SESSION['NombreUsuario']);

// Consulta para obtener las compras con los estados 1 y 5 (Pedido Enviado y Pedido Creado)
$queryCompras = "SELECT c.*, CONCAT(u.NombreUsuario) as NombreUsuario, p.NombreProveedor, r.NombreRol
                 FROM compras c
                 JOIN usuarios u ON c.UsuarioID = u.UsuarioID
                 JOIN proveedores p ON c.ProveedorID = p.ProveedorID
                 JOIN roles r ON u.RolID = r.RolID
                 WHERE c.EstadoCompraID IN (1, 5)"; // EstadoCompraID 1 y 5 corresponden a "Pedido Enviado" y "Pedido Creado" respectivamente
$resultCompras = mysqli_query($conexion, $queryCompras);
?>

<?php include('includes/navbar.php'); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Financiero</title>
</head>
<body>

<h1>Bienvenido, <?php echo $row['NombreUsuario']; ?></h1>


    <!-- Enlaces a gestión de productos y proveedores -->
    <p><a href="gestion_productos_financiero.php">Gestión de Productos</a></p>
    <p><a href="gestion_proveedores_financiero.php">Gestión de Proveedores</a></p>
    
    <!-- Tabla de compras pendientes -->
    <h2>Compras Pendientes</h2>
    <table border="1">
        <thead>
            <tr>
                <th>Proveedor</th>
                <th>Realizado por</th>
                <th>Valor de la Compra</th>
                <th>Fecha de la Compra</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Mostrar cada compra pendiente en la tabla
            while ($compra = mysqli_fetch_assoc($resultCompras)) {
                echo "<tr>";
                echo "<td>{$compra['NombreProveedor']}</td>";
                echo "<td>{$compra['NombreUsuario']}</td>";
                echo "<td>{$compra['ValorCompra']}</td>";
                echo "<td>{$compra['FechaCompra']}</td>";
                echo "<td>Pendiente</td>"; // Estado fijo como "Pendiente"
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>

</body>
</html>
