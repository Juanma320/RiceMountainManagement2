<?php
include('includes/includes.php');
include('includes/funciones.php');
$row = obtenerDatosUsuario($conexion, $_SESSION['NombreUsuario']);
include('includes/navbar.php');

// Obtener el ID del proveedor de la URL
$proveedorID = $_GET['proveedorID'];
$compraID = $_GET['compraID'];

// Verificar si se recibió un ID de proveedor
if ($proveedorID) {
    // Obtener información del proveedor
    $queryProveedor = "SELECT * FROM proveedores WHERE ProveedorID = $proveedorID";
    $resultProveedor = mysqli_query($conexion, $queryProveedor);
    $rowProveedor = mysqli_fetch_assoc($resultProveedor);

    // Mostrar información del proveedor
    echo "<h2>Información de la Compra:</h2>";
    echo "<p><strong>Nombre:</strong> {$rowProveedor['NombreProveedor']}</p>";
    echo "<p><strong>Correo Electrónico:</strong> {$rowProveedor['CorreoElectronico']}</p>";
    echo "<p><strong>Teléfono:</strong> {$rowProveedor['Telefono']}</p>";

    // Obtener valor total de la compra
    $queryValorTotalCompra = "SELECT IFNULL(SUM(dc.Cantidad * pc.PrecioUnitario), 0) AS ValorTotal FROM detalle_compra dc
    INNER JOIN precio_compras pc ON dc.ProductoID = pc.ProductoID
    WHERE dc.CompraID = $compraID";
    $resultValorTotalCompra = mysqli_query($conexion, $queryValorTotalCompra);
    $rowValorTotalCompra = mysqli_fetch_assoc($resultValorTotalCompra);
    $valorTotalCompra = $rowValorTotalCompra['ValorTotal'];

    // Obtener información de la compra
    $queryCompra = "SELECT * FROM compras WHERE CompraID = $compraID";
    $resultCompra = mysqli_query($conexion, $queryCompra);
    $rowCompra = mysqli_fetch_assoc($resultCompra);

    // Mostrar información de la compra
    echo "<p><strong>Fecha de Entrega:</strong> {$rowCompra['FechaCompra']}</p>";
    echo "<p><strong>Valor Total de la Compra:</strong> $valorTotalCompra</p>";

    // Formulario para agregar detalles de compra
    echo "<h2>Agregar Detalles de Compra</h2>";
    echo "<form action='procesar_detalle_compra.php' method='post'>";
    // Campo oculto para pasar el ID del proveedor
    echo "<input type='hidden' name='proveedorID' value='$proveedorID'>";
    echo "<input type='hidden' name='compraID' value='$compraID'>"; // Añadido para recopilar el ID de la compra

    // Filtros
    echo "<label for='marca'>Marca:</label>";
    echo "<select name='marca' id='marca' onchange='actualizarProductos()'>";
    echo "<option value=''>Todas las Marcas</option>";
    $queryMarcasProveedor = "SELECT m.* FROM marcas m WHERE m.ProveedorID = $proveedorID";
    $resultMarcasProveedor = mysqli_query($conexion, $queryMarcasProveedor);
    while ($rowMarca = mysqli_fetch_assoc($resultMarcasProveedor)) {
        echo "<option value='{$rowMarca['MarcaID']}'>{$rowMarca['NombreMarca']}</option>";
    }
    echo "</select><br>";

    echo "<label for='categoria'>Categoría:</label>";
    echo "<select name='categoria' id='categoria' onchange='actualizarProductos()'>";
    echo "<option value=''>Todas las Categorías</option>";
    $queryCategorias = "SELECT * FROM categorias";
    $resultCategorias = mysqli_query($conexion, $queryCategorias);
    while ($rowCategoria = mysqli_fetch_assoc($resultCategorias)) {
        echo "<option value='{$rowCategoria['CategoriaID']}'>{$rowCategoria['NombreCategoria']}</option>";
    }
    echo "</select><br>";

    echo "<label for='presentacion'>Presentación:</label>";
    echo "<select name='presentacion' id='presentacion' onchange='actualizarProductos()'>";
    echo "<option value=''>Todas las Presentaciones</option>";
    $queryPresentaciones = "SELECT * FROM presentaciones";
    $resultPresentaciones = mysqli_query($conexion, $queryPresentaciones);
    while ($rowPresentacion = mysqli_fetch_assoc($resultPresentaciones)) {
        echo "<option value='{$rowPresentacion['PresentacionID']}'>{$rowPresentacion['NombrePresentacion']}</option>";
    }
    echo "</select><br>";

    // Select de productos
    echo "<label for='producto'>Producto:</label>";
    echo "<select name='producto' id='producto'>";
    echo "<option value=''>Seleccione un producto</option>";
    echo "</select><br>";

    // Cantidad
    echo "<label for='cantidad'>Cantidad:</label>";
    echo "<input type='number' id='cantidad' name='cantidad' min='1' required><br>";

    // Botón para agregar
    echo "<input type='submit' value='Agregar'>";
    echo "</form>";

    // Botón para terminar la compra
    echo "<form action='infoproveedor.php' method='get'>";
    echo "<input type='hidden' name='proveedorID' value='$proveedorID'>";
    echo "<input type='submit' value='Terminar Compra'>";
    echo "</form>";

    echo "<form action='cancelar_compra.php' method='post'>";
    echo "<input type='hidden' name='proveedorID' value='$proveedorID'>";
    echo "<input type='hidden' name='compraID' value='$compraID'>"; // Añadido para recopilar el ID de la compra
    echo "<input type='submit' value='Cancelar Compra'>";
    echo "</form>";

    // Tabla de productos agregados
    echo "<h2>Productos Agregados</h2>";
    echo "<table border='1'>";
    echo "<tr><th>Nombre Producto</th><th>Marca</th><th>Presentación</th><th>Cantidad</th><th>Precio Unitario</th><th>Valor Total</th><th>Acciones</th></tr>";

    // Obtener detalles de compra
    $queryDetallesCompra = "SELECT dc.*, p.NombreProducto, m.NombreMarca, pr.NombrePresentacion, pc.PrecioUnitario FROM detalle_compra dc
                            INNER JOIN productos p ON dc.ProductoID = p.ProductoID
                            INNER JOIN marcas m ON p.MarcaID = m.MarcaID
                            INNER JOIN presentaciones pr ON p.PresentacionID = pr.PresentacionID
                            INNER JOIN precio_compras pc ON dc.ProductoID = pc.ProductoID
                            WHERE dc.CompraID = $compraID";
    $resultDetallesCompra = mysqli_query($conexion, $queryDetallesCompra);

    while ($rowDetalleCompra = mysqli_fetch_assoc($resultDetallesCompra)) {
        $valorTotal = $rowDetalleCompra['Cantidad'] * $rowDetalleCompra['PrecioUnitario'];
        echo "<tr><td>{$rowDetalleCompra['NombreProducto']}</td><td>{$rowDetalleCompra['NombreMarca']}</td><td>{$rowDetalleCompra['NombrePresentacion']}</td><td>{$rowDetalleCompra['Cantidad']}</td><td>{$rowDetalleCompra['PrecioUnitario']}</td><td>$valorTotal</td><td><a href='eliminar_detalle_compra.php?detalleID={$rowDetalleCompra['DetalleCompraID']}&compraID=$compraID&proveedorID=$proveedorID'>Eliminar</a></td></tr>";
    }

    echo "</table>";

    // Script para actualizar productos
    echo <<<EOD
    <script>
    function actualizarProductos() {
        // Obtiene los valores seleccionados de los filtros
        var marca = document.getElementById('marca').value;
        var categoria = document.getElementById('categoria').value;
        var presentacion = document.getElementById('presentacion').value;
        
        // Hace una solicitud AJAX para obtener los productos actualizados
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById('producto').innerHTML = this.responseText;
            }
        };
        xmlhttp.open("GET", "obtener_productos.php?marca=" + marca + "&categoria=" + categoria + "&presentacion=" + presentacion, true);
        xmlhttp.send();
    }
    </script>
    EOD;

} else {
    echo "<p>No se proporcionó un ID de proveedor.</p>";
}
?>
