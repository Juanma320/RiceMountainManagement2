<?php
include('includes/includes.php');
include('includes/funciones.php');
$row = obtenerDatosUsuario($conexion, $_SESSION['NombreUsuario']);
include('includes/navbar.php');

if ($_SESSION['RolID'] != 1) {
    header('Location: login.php');
    exit();
}

// Consulta para obtener las categorías
$queryCategorias = 'SELECT * FROM categorias';
$resultadoCategorias = mysqli_query($conexion, $queryCategorias);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Categorías</title>
    <link rel="stylesheet" href="styles.css"> <!-- Estilos CSS -->
</head>
<body>
    <h1>Gestión de Categorías</h1>

    <!-- Tabla de categorías -->
    <table border="1">
        <tr>
            <th>Nombre Categoría</th>
            <th>Acciones</th>
        </tr>
        <?php
        while ($rowCategoria = mysqli_fetch_assoc($resultadoCategorias)) {
            echo "<tr>";
            echo "<td>{$rowCategoria['NombreCategoria']}</td>";
            // Verificar si la categoría está siendo usada en productos
            $queryProductosCategoria = "SELECT COUNT(*) AS total FROM productos WHERE CategoriaID = {$rowCategoria['CategoriaID']}";
            $resultProductosCategoria = mysqli_query($conexion, $queryProductosCategoria);
            $rowProductosCategoria = mysqli_fetch_assoc($resultProductosCategoria);
            $totalProductos = $rowProductosCategoria['total'];
            if ($totalProductos > 0) {
                echo "<td>La categoría aún está siendo usada</td>";
            } else {
                echo "<td><a href='eliminar_categoria.php?categoriaID={$rowCategoria['CategoriaID']}'>Eliminar</a></td>";
            }
            echo "</tr>";
        }
        ?>
    </table>

    <!-- Formulario para agregar categoría -->
    <h2>Agregar Categoría</h2>
    <form action="procesar_categoria.php" method="post">
        <label for="nombreCategoria">Nombre de la Categoría:</label>
        <input type="text" id="nombreCategoria" name="nombreCategoria" required><br>
        <input type="submit" value="Agregar Categoría">
    </form>

</body>
</html>
