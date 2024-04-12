<?php
include('includes/includes.php');
include('includes/funciones.php');
$row = obtenerDatosUsuario($conexion, $_SESSION['NombreUsuario']);
include('includes/navbar.php');

// Obtener el ID de la venta de la URL
$ventaID = $_GET['ventaID'];
$clienteID = $_GET['clienteID'];

// Verificar si se recibió un ID de venta
if ($ventaID) {
    // Obtener información de la venta
    $queryVenta = "SELECT v.*, c.NombreCliente, d.Direccion FROM Ventas v
                   JOIN Clientes c ON v.ClienteID = c.ClienteID
                   JOIN Direcciones_Clientes d ON v.DireccionID = d.DireccionID
                   WHERE v.VentaID = $ventaID";
    $resultVenta = mysqli_query($conexion, $queryVenta);
    $rowVenta = mysqli_fetch_assoc($resultVenta);

    // Mostrar información de la venta
    echo "<h2>Información de la venta:</h2>";
    echo "<p><strong>Cliente:</strong> {$rowVenta['NombreCliente']}</p>";
    echo "<p><strong>Dirección de envío:</strong> {$rowVenta['Direccion']}</p>";
    echo "<p><strong>Fecha de la venta:</strong> {$rowVenta['FechaVenta']}</p>";
   // Obtener el valor total de la venta
$queryValorTotal = "SELECT IFNULL(ROUND(SUM(dv.Cantidad * (pc.PrecioUnitario + (pc.PrecioUnitario * pc.PorcentajeBeneficio / 100))), 2), 0) AS ValorTotal
FROM detalles_venta dv
JOIN precio_compras pc ON dv.ProductoID = pc.ProductoID
WHERE dv.VentaID = $ventaID";

$resultValorTotal = mysqli_query($conexion, $queryValorTotal);
$rowValorTotal = mysqli_fetch_assoc($resultValorTotal);
$valorTotalVenta = $rowValorTotal['ValorTotal'];

echo "<p><strong>Valor Total de la Venta:</strong> $valorTotalVenta  Cop</p>";

    // Mostrar la tabla de detalles de la venta
    $queryActualizarTotalVenta = "UPDATE Ventas SET TotalVenta = $valorTotalVenta WHERE VentaID = $ventaID";
    $resultActualizarTotalVenta = mysqli_query($conexion, $queryActualizarTotalVenta);
    // Mostrar el formulario para agregar un producto a la venta
    echo "<h1>Agregar Producto a la Venta</h1>";
    echo "<form action='procesar_detalle_venta.php' method='post' onsubmit='return validarCantidad()'>";
    echo "<input type='hidden' name='ventaID' value='$ventaID'>";
    echo "<input type='hidden' name='clienteID' value='$clienteID'>";

    // Select de marcas
echo "<label for='marca'>Marca:</label>";
echo "<select name='marca' id='marca' onchange='actualizarProductos()'>";
echo "<option value=''>Seleccione una marca</option>";
$queryMarcas = "SELECT * FROM marcas";
$resultMarcas = mysqli_query($conexion, $queryMarcas);
while ($rowMarca = mysqli_fetch_assoc($resultMarcas)) {
    echo "<option value='{$rowMarca['MarcaID']}'>{$rowMarca['NombreMarca']}</option>";
}
echo "</select><br>";

// Select de categorías
echo "<label for='categoria'>Categoría:</label>";
echo "<select name='categoria' id='categoria' onchange='actualizarProductos()'>";
echo "<option value=''>Seleccione una categoría</option>";
$queryCategorias = "SELECT * FROM categorias";
$resultCategorias = mysqli_query($conexion, $queryCategorias);
while ($rowCategoria = mysqli_fetch_assoc($resultCategorias)) {
    echo "<option value='{$rowCategoria['CategoriaID']}'>{$rowCategoria['NombreCategoria']}</option>";
}
echo "</select><br>";

// Select de presentaciones
echo "<label for='presentacion'>Presentación:</label>";
echo "<select name='presentacion' id='presentacion' onchange='actualizarProductos()'>";
echo "<option value=''>Seleccione una presentación</option>";
$queryPresentaciones = "SELECT * FROM presentaciones";
$resultPresentaciones = mysqli_query($conexion, $queryPresentaciones);
while ($rowPresentacion = mysqli_fetch_assoc($resultPresentaciones)) {
    echo "<option value='{$rowPresentacion['PresentacionID']}'>{$rowPresentacion['NombrePresentacion']}</option>";
}
echo "</select><br>";

// Select de productos (se actualiza dinámicamente)
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

    // Botón para cancelar la venta
    echo "<form id='formCancelarVenta' action='cancelar_venta.php' method='post'>";
    echo "<input type='hidden' name='ventaID' value='$ventaID'>";
    echo "<input type='hidden' name='clienteID' value='$clienteID'>";
    echo "<input type='submit' value='Cancelar Venta' onclick='return confirmarCancelacion();'>";
    echo "</form>";

    // Función para mostrar el cuadro de diálogo de confirmación
    echo "<script>";
    echo "function confirmarCancelacion() {";
    echo "  return confirm('¿Estás seguro de que deseas cancelar la venta? Esta acción no se puede deshacer.');";
    echo "}";
    echo "</script>";

    // Botón para terminar la venta
    echo "<form action='infocliente.php' method='get'>";
    echo "<input type='hidden' name='clienteID' value='$clienteID'>";
    echo "<input type='submit' value='Terminar Venta'>";
    echo "</form>";
} else {
    echo "<p>No se proporcionó un ID de venta.</p>";
}
echo "<h1>Detalles de la Venta</h1>";
    echo "<table border='1'>";
    echo "<thead>";
    echo "<tr>";
    echo "<th>Nombre del Producto</th>";
    echo "<th>Marca</th>";
    echo "<th>Presentación</th>";
    echo "<th>Cantidad</th>";
    echo "<th>Valor Unitario</th>";
    echo "<th>Valor Total</th>";
    echo "<th>Retirar Producto</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";

    $queryDetalles = "SELECT dv.*, p.NombreProducto, m.NombreMarca, pre.NombrePresentacion, pc.PrecioUnitario, pc.PorcentajeBeneficio
                  FROM detalles_venta dv
                  JOIN productos p ON dv.ProductoID = p.ProductoID
                  JOIN marcas m ON p.MarcaID = m.MarcaID
                  JOIN presentaciones pre ON p.PresentacionID = pre.PresentacionID
                  JOIN precio_compras pc ON p.ProductoID = pc.ProductoID
                  WHERE dv.VentaID = $ventaID";
$resultDetalles = mysqli_query($conexion, $queryDetalles);

if ($resultDetalles && mysqli_num_rows($resultDetalles) > 0) {
    while ($rowDetalle = mysqli_fetch_assoc($resultDetalles)) {
        $precioUnitario = $rowDetalle['PrecioUnitario'] + ($rowDetalle['PrecioUnitario'] * $rowDetalle['PorcentajeBeneficio'] / 100);
        $valorTotal = $rowDetalle['Cantidad'] * $precioUnitario;
        echo "<tr>";
        echo "<td>{$rowDetalle['NombreProducto']}</td>";
        echo "<td>{$rowDetalle['NombreMarca']}</td>";
        echo "<td>{$rowDetalle['NombrePresentacion']}</td>";
        echo "<td>{$rowDetalle['Cantidad']}</td>";
        echo "<td>$precioUnitario</td>";
        echo "<td>$valorTotal</td>";
        echo "<td><a href='eliminar_producto_venta.php?detalleVentaID={$rowDetalle['DetalleVentaID']}&ventaID=$ventaID&clienteID=$clienteID'>Eliminar</a></td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='6'>No hay detalles de venta para mostrar.</td></tr>";
}
echo "</tbody>";
echo "</table>";
?>

<script>
function validarCantidad() {
    var cantidad = document.getElementById('cantidad').value;
    var productoID = document.getElementById('producto').value;

    // Realizar una solicitud AJAX para obtener la cantidad disponible del producto
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            var inventario = JSON.parse(this.responseText);
            var cantidadDisponible = inventario.CantidadInicial + inventario.CantidadComprada - inventario.CantidadVendida;
            if (cantidad > cantidadDisponible) {
                alert('La cantidad seleccionada supera el stock disponible.');
                return false; // Detener el envío del formulario
            }
            return true; // Continuar con el envío del formulario
        }
    };
    xhttp.open("GET", "obtener_inventario.php?productoID=" + productoID, false);
    xhttp.send();
    var inventario = JSON.parse(xhttp.responseText);
    var cantidadDisponible = inventario.CantidadInicial + inventario.CantidadComprada - inventario.CantidadVendida;
    if (cantidad > cantidadDisponible) {
        alert('La cantidad seleccionada supera el stock disponible.');
        return false; // Detener el envío del formulario
    }
    return true; // Continuar con el envío del formulario
}

function actualizarProductos() {
    var marcaID = document.getElementById('marca').value;
    var categoriaID = document.getElementById('categoria').value;
    var presentacionID = document.getElementById('presentacion').value;

    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById('producto').innerHTML = this.responseText;
        }
    };
    xhttp.open("GET", "obtener_productos.php?marca=" + marcaID + "&categoria=" + categoriaID + "&presentacion=" + presentacionID, true);
    xhttp.send();
}
</script>
