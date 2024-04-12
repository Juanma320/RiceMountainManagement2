<?php
include('includes/includes.php');
include('includes/funciones.php');
$row = obtenerDatosUsuario($conexion, $_SESSION['NombreUsuario']);
include('includes/navbar.php');

// Verificar si se recibió un ID de presentación
if (isset($_GET['presentacionID']) && !empty($_GET['presentacionID'])) {
    $presentacionID = $_GET['presentacionID'];

    // Verificar si la presentación está siendo usada por algún producto
    $queryUsada = "SELECT COUNT(*) AS total FROM productos WHERE PresentacionID = $presentacionID";
    $resultUsada = mysqli_query($conexion, $queryUsada);
    $rowUsada = mysqli_fetch_assoc($resultUsada);

    if ($rowUsada['total'] > 0) {
        echo "<p>No se puede eliminar la presentación porque está siendo utilizada por uno o más productos.</p>";
    } else {
        // Mostrar confirmación antes de eliminar
        echo "<script>
        if (confirm('¿Está seguro de que desea eliminar esta presentación?')) {
            window.location.href = 'eliminar_presentacion.php?presentacionID={$presentacionID}';
        } else {
            window.location.href = 'gestion_presentaciones.php';
        }
        </script>";
    }
} else {
    echo "<p>No se proporcionó un ID de presentación.</p>";
}
?>
