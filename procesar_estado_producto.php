<?php
include('includes/includes.php');
include('includes/funciones.php');

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['productoID']) && isset($_GET['estado'])) {
    $productoID = $_GET['productoID'];
    $estado = $_GET['estado'];

    // Actualizar el estado del producto en la base de datos
    $query = "UPDATE Productos SET Activo = $estado WHERE ProductoID = $productoID";
    $resultado = mysqli_query($conexion, $query);

    if ($resultado) {
        // Redirigir de vuelta a la página de gestión de productos
        header('Location: gestionproductosA.php');
        exit();
    } else {
        // Manejar el error, por ejemplo, mostrando un mensaje al usuario
        echo "Hubo un error al cambiar el estado del producto.";
    }
} else {
    // Si la solicitud no es válida, redirigir a alguna página de error o a la página de gestión de productos
    header('Location: gestionproductosA.php');
    exit();
}
?>
