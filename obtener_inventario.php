<?php
include('includes/includes.php');

$productoID = $_GET['productoID'];

$queryInventario = "SELECT * FROM inventario_producto WHERE ProductoID = $productoID";
$resultInventario = mysqli_query($conexion, $queryInventario);
$rowInventario = mysqli_fetch_assoc($resultInventario);

echo json_encode($rowInventario);
?>
