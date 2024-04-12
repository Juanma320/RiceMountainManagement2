<?php
include('includes/includes.php');
include('includes/funciones.php');

// Verificar si el usuario tiene el rol de administrador
if ($_SESSION['RolID'] != 1) {
    // Si no es administrador, redirigir a la página de inicio
    header('Location: login.php');
    exit();
}

// Verificar si se recibió el ID de la marca a eliminar
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['marcaID'])) {
    $marcaID = $_POST['marcaID'];
    
    // Verificar si la marca está siendo usada en algún producto
    $queryVerificarUso = "SELECT COUNT(*) AS total FROM productos WHERE MarcaID = $marcaID";
    $resultadoVerificarUso = mysqli_query($conexion, $queryVerificarUso);
    $filaVerificarUso = mysqli_fetch_assoc($resultadoVerificarUso);
    $totalProductos = $filaVerificarUso['total'];
    
    if ($totalProductos > 0) {
        echo "La marca aún está siendo usada en $totalProductos producto(s) y no se puede eliminar.";
    } else {
        // Eliminar la marca
        $queryEliminarMarca = "DELETE FROM marcas WHERE MarcaID = $marcaID";
        if (mysqli_query($conexion, $queryEliminarMarca)) {
            echo "Marca eliminada correctamente.";
        } else {
            echo "Error al eliminar la marca: " . mysqli_error($conexion);
        }
    }
} else {
    echo "ID de marca no especificado.";
}

// Redirigir de vuelta a la página de marcas del proveedor
header('Location: marcasproveedor.php?id=' . $_POST['proveedorID']);
exit();
?>
