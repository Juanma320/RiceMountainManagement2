<?php
include ('includes/includes.php');
include ('includes/funciones.php');

$row = obtenerDatosUsuario($conexion, $_SESSION['UsuarioID']);

if ($_SESSION['RolID'] != 1) {
    header('Location: login.php');
    exit();
}

// Verificar si se recibió un ID de presentación
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['presentacionID']) && !empty($_GET['presentacionID'])) {
    $presentacionID = $_GET['presentacionID'];

    // Verificar si la presentación está siendo usada por algún producto
    $queryVerificarUso = "SELECT COUNT(*) AS total FROM productos WHERE PresentacionID = $presentacionID";
    $resultadoVerificarUso = mysqli_query($conexion, $queryVerificarUso);
    $filaVerificarUso = mysqli_fetch_assoc($resultadoVerificarUso);
    $totalProductos = $filaVerificarUso['total'];

    if ($totalProductos > 0) {
        echo "La presentación aún está siendo usada en $totalProductos producto(s) y no se puede eliminar.";
    } else {
        // Mostrar cuadro de diálogo de confirmación solo si no se ha confirmado antes
        if (!isset($_GET['confirm']) || $_GET['confirm'] !== 'true') {
            echo "<script>
            if (confirm('¿Está seguro de que desea eliminar esta presentación?')) {
                window.location.href = 'eliminar_presentacion.php?presentacionID={$presentacionID}&confirm=true';
            } else {
                window.location.href = 'gestion_presentaciones.php';
            }
        </script>";
        } else {
            // Eliminar la presentación si se confirma    
            $queryEliminarPresentacion = "DELETE FROM presentaciones WHERE PresentacionID = $presentacionID";
            if (mysqli_query($conexion, $queryEliminarPresentacion)) {
                echo "<script>window.location.href = 'gestion_presentaciones.php'; </script>";
            } else {
                echo "Error al eliminar la presentación: " . mysqli_error($conexion);
            }
        }
    }
} else {
    echo "No se proporcionó un ID de categoría válido.";
}