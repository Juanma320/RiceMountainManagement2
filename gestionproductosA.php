<?php
include('includes/includes.php');
include('includes/funciones.php');
$row = obtenerDatosUsuario($conexion, $_SESSION['NombreUsuario']);

if ($_SESSION['RolID'] != 1) {
    header('Location: login.php');
    exit();
}

// Consulta para obtener los productos
$queryProductos = 'SELECT P.ProductoID, P.NombreProducto, C.NombreCategoria, PR.NombrePresentacion, PR.Medida, M.NombreMarca, 
          (IP.CantidadInicial + IP.CantidadComprada - IP.CantidadVendida) AS CantidadFinal,
          PC.PorcentajeBeneficio,
          P.Activo
          FROM Productos P
          INNER JOIN Categorias C ON P.CategoriaID = C.CategoriaID
          INNER JOIN Presentaciones PR ON P.PresentacionID = PR.PresentacionID
          INNER JOIN Marcas M ON P.MarcaID = M.MarcaID
          INNER JOIN Inventario_Producto IP ON P.ProductoID = IP.ProductoID
          INNER JOIN precio_compras PC ON P.ProductoID = PC.ProductoID';

$resultadoProductos = mysqli_query($conexion, $queryProductos);
// Consulta para obtener los cambios de precios programados
$queryCambiosPrecio = 'SELECT PC.ProductoID, PC.NuevoPrecio, PC.FechaFin, P.NombreProducto, M.NombreMarca, C.NombreCategoria, PR.Medida
                       FROM precio_compras PC
                       JOIN Productos P ON PC.ProductoID = P.ProductoID
                       JOIN Marcas M ON P.MarcaID = M.MarcaID
                       JOIN Categorias C ON P.CategoriaID = C.CategoriaID
                       JOIN Presentaciones PR ON P.PresentacionID = PR.PresentacionID
                       WHERE PC.FechaFin IS NOT NULL';

$resultadoCambiosPrecio = mysqli_query($conexion, $queryCambiosPrecio);


// Consulta para obtener los cambios de porcentaje programados
$queryCambiosPorcentaje = 'SELECT PC.ProductoID, PC.NuevoBeneficio, PC.FechaFinBeneficio, P.NombreProducto, M.NombreMarca, C.NombreCategoria, PR.Medida
                           FROM precio_compras PC
                           JOIN Productos P ON PC.ProductoID = P.ProductoID
                           JOIN Marcas M ON P.MarcaID = M.MarcaID
                           JOIN Categorias C ON P.CategoriaID = C.CategoriaID
                           JOIN Presentaciones PR ON P.PresentacionID = PR.PresentacionID
                           WHERE PC.FechaFinBeneficio IS NOT NULL';



$resultadoCambiosPorcentaje = mysqli_query($conexion, $queryCambiosPorcentaje);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Productos</title>
    <link rel="stylesheet" href="styles.css"> <!-- Estilos CSS para la ventana modal -->
</head>
<body>
<?php include('includes/navbar.php'); ?>

<h1>Gestión de Productos</h1>

<!-- Tabla de productos -->
<h2>Productos</h2>
<p><a href="gestion_categorias.php">Gestionar Categorías</a></p>
<p><a href="gestion_presentaciones.php">Gestionar Presentaciones</a></p>
<p><a href="agregar_producto.php">Agregar Producto</a></p>
<table border="1">
<tr>
<tr>
        <th>Nombre Producto</th>
        <th>Categoría</th>
        <th>Presentación</th>
        <th>Marca</th>
        <th>Medida</th>
        <th>Cantidad Final</th>
        <th>Precio Unitario</th>
        <th>% de Beneficio</th>
        <th>Precio Final Unitario</th>
        <th>Modificar Precio</th>
        <th>Modificar % Beneficio</th>
        <th>Estado</th>
    </tr>
    <?php
    while ($row = mysqli_fetch_assoc($resultadoProductos)) {
        $precioUnitario = obtenerPrecioUnitario($conexion, $row['ProductoID']); // Pasar la conexión y el ID del producto
        $precioFinalUnitario = $precioUnitario * (1 + $row['PorcentajeBeneficio'] / 100);
        echo "<tr>";
        echo "<td>{$row['NombreProducto']}</td>";
        echo "<td>{$row['NombreCategoria']}</td>";
        echo "<td>{$row['NombrePresentacion']}</td>";
        echo "<td>{$row['NombreMarca']}</td>";
        echo "<td>{$row['Medida']}</td>";
        echo "<td>{$row['CantidadFinal']}</td>";
        echo "<td>{$precioUnitario} Cop</td>";
        echo "<td>{$row['PorcentajeBeneficio']}%</td>";
        echo "<td>{$precioFinalUnitario} Cop</td>";
        echo "<td><a href='modificar_precio.php?productoID={$row['ProductoID']}'>Modificar Precio</a></td>";
        echo "<td><a href='editar_producto.php?id={$row['ProductoID']}'>Modificar %</a></td>";
        echo "<td>";
        if ($row['Activo'] == 1) {
            echo "<button onclick='cambiarEstado({$row['ProductoID']}, 0)'>Inactivar</button>";
        } else {
            echo "<button onclick='cambiarEstado({$row['ProductoID']}, 1)'>Activar</button>";
        }
        echo "</td>";
        echo "</tr>";
    }
    ?>
</table>

<!-- Tabla de cambios de precios programados -->
<h2>Cambios de Precios Programados</h2>
<table border="1">
    <tr>
        <th>Producto</th>
        <th>Marca</th>
        <th>Categoría</th>
        <th>Medida</th>
        <th>Nuevo Precio</th>
        <th>Fecha de Cambio</th>
        <th>Cancelar Cambio</th>
    </tr>
    <?php
    while ($row = mysqli_fetch_assoc($resultadoCambiosPrecio)) {
        echo "<tr>";
        echo "<td>{$row['NombreProducto']}</td>";
        echo "<td>{$row['NombreMarca']}</td>";
        echo "<td>{$row['NombreCategoria']}</td>";
        echo "<td>{$row['Medida']}</td>";
        echo "<td>{$row['NuevoPrecio']} Cop</td>";
        echo "<td>{$row['FechaFin']}</td>";
        echo "<td><a href='cancelar_programacion.php?productoID={$row['ProductoID']}&tipo=precio'>Cancelar Programación</a></td>";
        echo "</tr>";
    }
    ?>
</table>

<!-- Tabla de cambios de porcentaje programados -->
<h2>Cambios de Porcentaje Programados</h2>
<table border="1">
    <tr>
        <th>Producto</th>
        <th>Marca</th>
        <th>Categoría</th>
        <th>Medida</th>
        <th>Nuevo Porcentaje</th>
        <th>Fecha de Cambio</th>
        <th>Cancelar Cambio</th>
    </tr>
    <?php
    while ($row = mysqli_fetch_assoc($resultadoCambiosPorcentaje)) {
        echo "<tr>";
        echo "<td>{$row['NombreProducto']}</td>";
        echo "<td>{$row['NombreMarca']}</td>";
        echo "<td>{$row['NombreCategoria']}</td>";
        echo "<td>{$row['Medida']}</td>";
        echo "<td>{$row['NuevoBeneficio']}%</td>";
        echo "<td>{$row['FechaFinBeneficio']}</td>";
        echo "<td><a href='cancelar_programacion.php?productoID={$row['ProductoID']}&tipo=porcentaje'>Cancelar Programación</a></td>";
        echo "</tr>";
    }
    ?>
</table>
<script>
    function cambiarEstado(productoID, estado) {
        if (confirm("¿Estás seguro de cambiar el estado del producto?")) {
            // Si el usuario confirma, redirigir a procesar_estado_producto.php para cambiar el estado
            window.location.href = `procesar_estado_producto.php?productoID=${productoID}&estado=${estado}`;
        }
    }
</script>
</body>
</html>
