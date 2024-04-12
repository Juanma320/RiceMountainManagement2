<?php
include('includes/includes.php');
include('includes/funciones.php');

$marcaID = $_GET['marca'];
$categoriaID = $_GET['categoria'];
$presentacionID = $_GET['presentacion'];

// Modificar la consulta para excluir productos inactivos
$queryProductos = "SELECT * FROM productos WHERE MarcaID = $marcaID AND CategoriaID = $categoriaID AND PresentacionID = $presentacionID AND Activo = 1";

$resultProductos = mysqli_query($conexion, $queryProductos);

$options = "<option value=''>Seleccione un producto</option>";
while ($rowProducto = mysqli_fetch_assoc($resultProductos)) {
    $options .= "<option value='{$rowProducto['ProductoID']}'>{$rowProducto['NombreProducto']}</option>";
}
echo $options;
?>
