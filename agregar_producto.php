<?php
include('includes/includes.php');
include('includes/funciones.php');
$row = obtenerDatosUsuario($conexion, $_SESSION['NombreUsuario']);

// Verificar si el usuario tiene el rol de administrador
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener los datos del formulario
    $nombreProducto = mysqli_real_escape_string($conexion, $_POST['nombre_producto']);
    $categoriaID = mysqli_real_escape_string($conexion, $_POST['categoria']);
    $presentacionID = mysqli_real_escape_string($conexion, $_POST['presentacion']);
    $marcaID = mysqli_real_escape_string($conexion, $_POST['marca']);
    $precioUnitario = mysqli_real_escape_string($conexion, $_POST['precio_unitario']);
    $porcentajeBeneficio = mysqli_real_escape_string($conexion, $_POST['porcentaje_beneficio']);

    // Validar que el porcentaje de beneficio esté entre 0 y 1000
    if ($porcentajeBeneficio < 0 || $porcentajeBeneficio > 1000) {
        echo "<p>El porcentaje de beneficio debe estar entre 0 y 1000.</p>";
    } else {
        // Obtener el ID del usuario que está creando el producto
        $creador = obtenerDatosUsuario($conexion, $_SESSION['NombreUsuario']);
        $creadorID = $creador['UsuarioID'];

        // Obtener la fecha y hora actual
        $fechaCreacion = date('Y-m-d H:i:s');

        // Insertar el producto en la base de datos
        $query = "INSERT INTO Productos (NombreProducto, FinancieroID, FechaCreacion, CategoriaID, PresentacionID, MarcaID) 
                  VALUES ('$nombreProducto', '$creadorID', '$fechaCreacion', '$categoriaID', '$presentacionID', '$marcaID')";
        $resultado = mysqli_query($conexion, $query);

        if ($resultado) {
            // Insertar dato en Inventario_Producto
            $productoID = mysqli_insert_id($conexion); // Obtener el ID del producto recién insertado
            $query_inventario = "INSERT INTO Inventario_Producto (ProductoID, FechaInicial, CantidadInicial, CantidadComprada, CantidadVendida) 
                                 VALUES ('$productoID', '$fechaCreacion', '0', '0', '0')";
            $resultado_inventario = mysqli_query($conexion, $query_inventario);

            if ($resultado_inventario) {
                // Insertar dato en precio_compras
                $query_precio = "INSERT INTO precio_compras (ProductoID, PrecioUnitario, PorcentajeBeneficio, FechaInicio) 
                                 VALUES ('$productoID', '$precioUnitario', '$porcentajeBeneficio', '$fechaCreacion')";
                $resultado_precio = mysqli_query($conexion, $query_precio);

                if ($resultado_precio) {
                    echo "<p>Producto agregado con éxito.</p>";
                } else {
                    echo "<p>Error al agregar el precio del producto.</p>";
                }
            } else {
                echo "<p>Error al agregar producto en el inventario.</p>";
            }
        } else {
            echo "<p>Error al agregar producto.</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Producto</title>
</head>
<body>

    <?php include('includes/navbar.php'); ?>

    <h1>Agregar Producto</h1>

    <form method="POST" action="">
        <label for="nombre_producto">Nombre del Producto:</label><br>
        <input type="text" id="nombre_producto" name="nombre_producto" required><br>

        <label for="categoria">Categoría:</label><br>
        <select id="categoria" name="categoria" required>
            <!-- Aquí debes cargar las opciones desde la base de datos -->
            <?php
            $query_categorias = "SELECT * FROM Categorias";
            $resultado_categorias = mysqli_query($conexion, $query_categorias);

            while ($row_categoria = mysqli_fetch_assoc($resultado_categorias)) {
                echo "<option value='{$row_categoria['CategoriaID']}'>{$row_categoria['NombreCategoria']}</option>";
            }
            ?>
        </select><br>

        <label for="presentacion">Presentación:</label><br>
        <select id="presentacion" name="presentacion" required>
            <!-- Aquí debes cargar las opciones desde la base de datos -->
            <?php
            $query_presentaciones = "SELECT * FROM Presentaciones";
            $resultado_presentaciones = mysqli_query($conexion, $query_presentaciones);

            while ($row_presentacion = mysqli_fetch_assoc($resultado_presentaciones)) {
                echo "<option value='{$row_presentacion['PresentacionID']}'>{$row_presentacion['NombrePresentacion']}</option>";
            }
            ?>
        </select><br>

        <label for="marca">Marca:</label><br>
    <select id="marca" name="marca" required>
    <!-- Aquí debes cargar las opciones solo de las marcas activas desde la base de datos -->
    <?php
    $query_marcas = "SELECT * FROM Marcas WHERE Estado = 1"; // Solo marcas activas
    $resultado_marcas = mysqli_query($conexion, $query_marcas);
    while ($row_marca = mysqli_fetch_assoc($resultado_marcas)) {
        echo "<option value='{$row_marca['MarcaID']}'>{$row_marca['NombreMarca']}</option>";
    }
    ?>
    </select><br>

        </select><br>

        <label for="precio_unitario">Precio Unitario:</label><br>
        <input type="number" id="precio_unitario" name="precio_unitario" min="0" step="0.01" required><br>

        <label for="porcentaje_beneficio">Porcentaje de Beneficio (0 - 1000):</label><br>
        <input type="number" id="porcentaje_beneficio" name="porcentaje_beneficio" min="0" max="1000" required><br>

        <input type="submit" value="Agregar Producto">
        <a href="gestionproductosA.php"><button type="button">Cancelar</button></a>
    </form>

</body>
</html>
