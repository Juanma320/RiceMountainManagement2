<?php
include('includes/includes.php');
include('includes/funciones.php');

// Verificar si el usuario tiene el rol de administrador
if ($_SESSION['RolID'] != 1) {
    // Si no es administrador, redirigir a la página de inicio
    header('Location: login.php');
    exit();
}

// Obtener el ID del proveedor de la URL
if (isset($_GET['id'])) {
    $proveedorID = $_GET['id'];
} else {
    echo "ID de proveedor no especificado.";
    exit();
}

// Lógica para agregar una marca si se recibe el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['nombre_marca'])) {
    $nombreMarca = $_POST['nombre_marca'];
    agregarMarcaProveedor($conexion, $proveedorID, $nombreMarca);
    echo "<script>
    if (confirm('¿Estás seguro de agregar esta marca?')) {
        window.location.href = 'procesar_marca.php?id={$proveedorID}';
    } else {
        window.location.href = 'marcasproveedor.php?id={$proveedorID}';
    }
</script>";

}

// Redirigir de vuelta a la página de marcas del proveedor
header('Location: marcasproveedor.php?id=' . $proveedorID);
exit();

?>
