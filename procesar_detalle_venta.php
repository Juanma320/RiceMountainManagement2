
<?php
include('includes/includes.php');
include('includes/funciones.php');

$ventaID = $_POST['ventaID'];
$clienteID = $_POST['clienteID'];
$productoID = $_POST['producto'];
$cantidad = $_POST['cantidad'];

// Obtener información del producto
$queryProducto = "SELECT * FROM productos WHERE ProductoID = $productoID";
$resultProducto = mysqli_query($conexion, $queryProducto);
$rowProducto = mysqli_fetch_assoc($resultProducto);

// Obtener información del inventario
$queryInventario = "SELECT * FROM inventario_producto WHERE ProductoID = $productoID";
$resultInventario = mysqli_query($conexion, $queryInventario);
$rowInventario = mysqli_fetch_assoc($resultInventario);

// Verificar si hay suficiente stock disponible
$cantidadDisponible = $rowInventario['CantidadInicial'] + $rowInventario['CantidadComprada'] - $rowInventario['CantidadVendida'];
// Verificar si hay suficiente stock disponible
if ($cantidad <= $cantidadDisponible) {
    // Obtener el precio unitario y el porcentaje de beneficio del producto
    $queryPrecio = "SELECT PrecioUnitario, PorcentajeBeneficio FROM Precio_compras WHERE ProductoID = $productoID";
    $resultPrecio = mysqli_query($conexion, $queryPrecio);
    if ($resultPrecio && mysqli_num_rows($resultPrecio) > 0) {
        $rowPrecio = mysqli_fetch_assoc($resultPrecio);
        $precioUnitario = $rowPrecio['PrecioUnitario'];
        $porcentajeBeneficio = $rowPrecio['PorcentajeBeneficio'];

        // Calcular el valor
        $valor = $precioUnitario + ($precioUnitario * $porcentajeBeneficio / 100);

        // Insertar el detalle de venta
        $queryInsertDetalle = "INSERT INTO detalles_venta (VentaID, ProductoID, Cantidad, Valor) VALUES ($ventaID, $productoID, $cantidad, $valor)";
        $resultInsertDetalle = mysqli_query($conexion, $queryInsertDetalle);
        if ($resultInsertDetalle) {
            // Actualizar el inventario
            $nuevaCantidadVendida = $rowInventario['CantidadVendida'] + $cantidad;
            $queryActualizarInventario = "UPDATE inventario_producto SET CantidadVendida = $nuevaCantidadVendida WHERE ProductoID = $productoID";
            mysqli_query($conexion, $queryActualizarInventario);

            // Redirigir a la página de detalles de la venta
            header("Location: agregar_detalle_venta.php?ventaID=$ventaID&clienteID=$clienteID");
            exit();
        } else {
            // Mostrar un mensaje de error si la inserción falla
            echo "<script>alert('Error al insertar el detalle de venta.');</script>";
            echo "<script>window.history.back();</script>";
            exit();
        }
    } else {
        // Mostrar un mensaje de error si no se encuentra el precio del producto
        echo "<script>alert('No se encontró el precio del producto.');</script>";
        echo "<script>window.history.back();</script>";
        exit();
    }
} else {
    // Mostrar un mensaje de error si no hay suficiente stock disponible
    echo "<script>alert('La cantidad seleccionada supera el stock disponible.');</script>";
    echo "<script>window.history.back();</script>";
    exit();
}
