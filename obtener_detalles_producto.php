<?php
include('includes/includes.php');
include('includes/funciones.php');

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['id'])) {
    $productoID = mysqli_real_escape_string($conexion, $_GET['id']);
    $query = "SELECT P.*, U.NombreUsuario AS Creador
              FROM Productos P
              INNER JOIN Usuarios U ON P.FinancieroID = U.UsuarioID
              WHERE ProductoID = $productoID";
    $resultado = mysqli_query($conexion, $query);
    $producto = mysqli_fetch_assoc($resultado);

    if ($producto) {
        echo "<h2>Detalles del Producto</h2>";
        echo "<p>Nombre: {$producto['NombreProducto']}</p>";
        echo "<p>Descripción: {$producto['Descripcion']}</p>";
        echo "<p>Precio: {$producto['Precio']}</p>";
        echo "<p>Fecha de Creación: {$producto['FechaCreacion']}</p>";
        echo "<p>Creador: {$producto['Creador']}</p>";
    } else {
        echo "<p>No se encontró ningún producto con ese ID.</p>";
    }
}
?>
