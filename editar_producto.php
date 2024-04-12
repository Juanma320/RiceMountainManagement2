<?php
include('includes/includes.php');
include('includes/funciones.php');
$row = obtenerDatosUsuario($conexion, $_SESSION['NombreUsuario']);

if ($_SESSION['RolID'] != 1 && $_SESSION['RolID'] != 3) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['id'])) {
    $productoID = mysqli_real_escape_string($conexion, $_GET['id']);
    $query = "SELECT * FROM Productos WHERE ProductoID = $productoID";
    $resultado = mysqli_query($conexion, $query);
    $producto = mysqli_fetch_assoc($resultado);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $productoID = mysqli_real_escape_string($conexion, $_POST['productoID']);
    $nuevoBeneficio = mysqli_real_escape_string($conexion, $_POST['nuevo_beneficio']);
    $fechaFinBeneficio = mysqli_real_escape_string($conexion, $_POST['fecha_fin_beneficio']);

    // Validar que nuevoBeneficio sea un valor entre 0 y 100
    if ($nuevoBeneficio < 0 || $nuevoBeneficio > 1000) {
        echo "<p>El valor del nuevo beneficio no permitido</p>";
    } else {
        $query = "UPDATE precio_compras SET NuevoBeneficio = '$nuevoBeneficio', FechaFinBeneficio = '$fechaFinBeneficio' WHERE ProductoID = $productoID";
        $resultado = mysqli_query($conexion, $query);

        if ($resultado) {
            echo "<p>Cambios guardados con éxito.</p>";
            if ($_SESSION['RolID'] == 3) {
                header('Location: gestion_productos_financiero.php');
            } else {
                header('Location: gestionproductosA.php');
            }
            exit();
        
        } else {
            echo "<p>Error al guardar los cambios.</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Producto</title>
</head>
<body>

    <?php include('includes/navbar.php'); ?>

    <h1>Editar Producto</h1>
    <form method="POST" action="">
        <input type="hidden" name="productoID" value="<?php echo $producto['ProductoID']; ?>">
        <label for="nuevo_beneficio">Nuevo Beneficio:</label>
        <input type="number" name="nuevo_beneficio" step="any" min="0" max="100" required>
        <span>%</span>
        <br>
        <label for="fecha_fin_beneficio">Fecha Fin Beneficio:</label>
        <input type="date" name="fecha_fin_beneficio" required>
        <br>
        <input type="submit" value="Guardar Cambios">
        <a href="<?php echo ($_SESSION['RolID'] == 3) ? 'gestion_productos_financiero.php' : 'gestionproductosA.php'; ?>"><button type="button">Cancelar</button></a>
    </form>

</body>
</html>
