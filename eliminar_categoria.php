<?php
include('includes/includes.php');
include('includes/funciones.php');
$row = obtenerDatosUsuario($conexion, $_SESSION['NombreUsuario']);

if ($_SESSION['RolID'] != 1) {
    header('Location: login.php');
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['categoriaID']) && !empty($_GET['categoriaID'])) {
    $categoriaID = $_GET['categoriaID'];

    // Verificar si la categoría está siendo usada en algún producto
    $queryVerificarUso = "SELECT COUNT(*) AS total FROM productos WHERE CategoriaID = $categoriaID";
    $resultadoVerificarUso = mysqli_query($conexion, $queryVerificarUso);
    $filaVerificarUso = mysqli_fetch_assoc($resultadoVerificarUso);
    $totalProductos = $filaVerificarUso['total'];

    if ($totalProductos > 0) {
        echo "La categoría aún está siendo usada en $totalProductos producto(s) y no se puede eliminar.";
    } else {
        // Mostrar cuadro de diálogo de confirmación solo si no se ha confirmado antes
        if (!isset($_GET['confirm']) || $_GET['confirm'] !== 'true') {
            echo "<script>
                if (confirm('¿Estás seguro de eliminar esta categoría?')) {
                    window.location.href = 'eliminar_categoria.php?categoriaID=$categoriaID&confirm=true';
                } else {
                    window.location.href = 'gestion_categorias.php';
                }
                </script>";
        } else {
            // Eliminar la categoría si se confirma
            $queryEliminarCategoria = "DELETE FROM categorias WHERE CategoriaID = $categoriaID";
            if (mysqli_query($conexion, $queryEliminarCategoria)) {
                echo "<script>window.location.href = 'gestion_categorias.php'; </script>";
            } else {
                echo "Error al eliminar la categoría: " . mysqli_error($conexion);
            }
        }
    }
} else {
    echo "No se proporcionó un ID de categoría válido.";
}
?>
