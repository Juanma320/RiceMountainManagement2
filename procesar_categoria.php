<?php
include('includes/includes.php');
include('includes/funciones.php');
$row = obtenerDatosUsuario($conexion, $_SESSION['NombreUsuario']);

if ($_SESSION['RolID'] != 1) {
    header('Location: login.php');
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombreCategoria = $_POST['nombreCategoria'];

    // Verificar si la categoría ya existe
    $queryVerificarCategoria = "SELECT CategoriaID FROM categorias WHERE NombreCategoria = '$nombreCategoria'";
    $resultadoVerificarCategoria = mysqli_query($conexion, $queryVerificarCategoria);

    if (mysqli_num_rows($resultadoVerificarCategoria) > 0) {
        echo "La categoría ya existe.";
    } else {
        // Insertar la nueva categoría
        $queryInsertarCategoria = "INSERT INTO categorias (NombreCategoria) VALUES ('$nombreCategoria')";
        if (mysqli_query($conexion, $queryInsertarCategoria)) {
            echo "Categoría agregada correctamente.";
        } else {
            echo "Error al agregar la categoría: " . mysqli_error($conexion);
        }
    }

    // Redireccionar a gestion_categorias.php
    header('Location: gestion_categorias.php');
    exit();
}
?>
