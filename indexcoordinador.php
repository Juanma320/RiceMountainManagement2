<?php
// Archivo: indexcoordinador.php

// Incluir configuraciones y funciones comunes
include('includes/includes.php');
include('includes/funciones.php');
// Verificar si el usuario tiene el rol de coordinador
if ($_SESSION['RolID'] != 2) {
    // Si no es coordinador, redirigir a la página de inicio
    header('Location: login.php');
    exit();
}

// Obtener datos del usuario
$row = obtenerDatosUsuario($conexion, $_SESSION['NombreUsuario']);

?>
 <?php include('includes/navbar.php'); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Coordinador</title>
</head>
<body>

    <h1>Bienvenido, <?php echo $row['NombreUsuario']; ?> (Coordinador)</h1>

    <!-- Enlace a gestion_cliente_coordinador.php -->
    <a href="gestion_cliente_coordinador.php">Gestión de Clientes</a>

    <!-- Tabla de ventas -->
    <h2>Ventas en Proceso</h2>
    <table border="1">
        <thead>
            <tr>
                <th>Nombre del Cliente</th>
                <th>Valor de la Venta</th>
                <th>Estado de la Venta</th>
                <th>Fecha de la Venta</th>
                <th>Dirección de la Venta</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Consulta para obtener las ventas en proceso, creadas y en retraso
            $query = "SELECT v.*, c.NombreCliente, d.Direccion, ev.NombreEstado
                      FROM ventas v
                      JOIN clientes c ON v.ClienteID = c.ClienteID
                      JOIN direcciones_clientes d ON v.DireccionID = d.DireccionID
                      JOIN estado_venta ev ON v.EstadoVentaID = ev.EstadoVentaID
                      WHERE v.EstadoVentaID IN (1, 2, 6)"; // EstadoVentaID 1 es "Creada", 2 es "En Proceso", 6 es "Retraso"
            $result = mysqli_query($conexion, $query);

            // Mostrar cada venta en la tabla
            while ($venta = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>{$venta['NombreCliente']}</td>";
                echo "<td>{$venta['TotalVenta']}</td>";
                echo "<td>{$venta['NombreEstado']}</td>";
                echo "<td>{$venta['FechaVenta']}</td>";
                echo "<td>{$venta['Direccion']}</td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>

</body>
</html>
