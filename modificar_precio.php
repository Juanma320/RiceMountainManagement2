<?php
include('includes/includes.php');
include('includes/funciones.php');
$row = obtenerDatosUsuario($conexion, $_SESSION['NombreUsuario']);
// Verificar si el usuario tiene el rol de administrador
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener los datos del formulario
    $productoID = mysqli_real_escape_string($conexion, $_POST['producto_id']);
    $nuevoPrecio = mysqli_real_escape_string($conexion, $_POST['nuevo_precio']);
    $fechaFin = mysqli_real_escape_string($conexion, $_POST['fecha_fin']);

    // Actualizar el precio del producto en la base de datos
    $query = "UPDATE precio_compras SET NuevoPrecio='$nuevoPrecio', FechaFin='$fechaFin' WHERE ProductoID='$productoID'";
    $resultado = mysqli_query($conexion, $query);

    if ($resultado) {
        echo "<p>Precio del producto actualizado con éxito.</p>";
    } else {
        echo "<p>Error al actualizar el precio del producto.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar Precio</title>
</head>
<body>

    <?php include('includes/navbar.php'); ?>

    <h1>Modificar Precio</h1>

    <form method="POST" action="">
        <input type="hidden" name="producto_id" value="<?php echo $_GET['productoID']; ?>">

        <label for="nuevo_precio">Nuevo Precio:</label><br>
        <input type="number" id="nuevo_precio" name="nuevo_precio" min="0" step="0.01" required><br>

        <label for="fecha_fin">Fecha Fin:</label><br>
        <input type="date" id="fecha_fin" name="fecha_fin" required><br>

        <input type="submit" value="Actualizar Precio">
<a href="<?php echo ($_SESSION['RolID'] == 3) ? 'gestion_productos_financiero.php' : 'gestionproductosA.php'; ?>"><button type="button">Cancelar</button></a>

    </form>

</body>
</html>
