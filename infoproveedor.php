<?php
include('includes/includes.php');
include('includes/funciones.php');
$row = obtenerDatosUsuario($conexion, $_SESSION['NombreUsuario']);

if (isset($_GET['proveedorID'])) {
    $proveedorID = mysqli_real_escape_string($conexion, $_GET['proveedorID']);

    $queryProveedor = "SELECT * FROM proveedores WHERE ProveedorID = ?";
    $stmtProveedor = mysqli_prepare($conexion, $queryProveedor);
    mysqli_stmt_bind_param($stmtProveedor, "i", $proveedorID);
    mysqli_stmt_execute($stmtProveedor);
    $resultProveedor = mysqli_stmt_get_result($stmtProveedor);

    if ($resultProveedor && mysqli_num_rows($resultProveedor) > 0) {
        $rowProveedor = mysqli_fetch_assoc($resultProveedor);

        include('includes/navbar.php');

        echo "<h1>Información del Proveedor</h1>";
        echo "<p><strong>Nombre Proveedor:</strong> {$rowProveedor['NombreProveedor']}</p>";
        echo "<p><strong>Correo Electrónico:</strong> {$rowProveedor['CorreoElectronico']}</p>";
        echo "<p><strong>Teléfono:</strong> {$rowProveedor['Telefono']}</p>";
        echo "<p><strong>Contacto:</strong> {$rowProveedor['Contacto']}</p>";
        echo "<p><strong>Estado:</strong> " . ($rowProveedor['Activo'] ? 'Activo' : 'Inactivo') . "</p>";

        echo "<a href='compra.php?proveedorID=$proveedorID'><button>Agregar Compra</button></a>";

        echo "<h2>Compras Realizadas</h2>";
        $queryCompras = "SELECT c.*, ec.NombreEstado FROM compras c
                         INNER JOIN estado_compra ec ON c.EstadoCompraID = ec.EstadoCompraID
                         WHERE c.ProveedorID = ?";
        $stmtCompras = mysqli_prepare($conexion, $queryCompras);
        mysqli_stmt_bind_param($stmtCompras, "i", $proveedorID);
        mysqli_stmt_execute($stmtCompras);
        $resultCompras = mysqli_stmt_get_result($stmtCompras);

        if ($resultCompras && mysqli_num_rows($resultCompras) > 0) {
            echo "<table border='1'>";
            echo "<tr><th>Fecha de Compra</th><th>Valor Compra</th><th>Estado</th><th>Editar</th><th>Cambiar Estado</th></tr>";
            while ($rowCompra = mysqli_fetch_assoc($resultCompras)) 
                if ($rowCompra['EstadoCompraID'] != 4) {
                echo "<tr>";
                echo "<td>{$rowCompra['FechaCompra']}</td>";
                echo "<td>{$rowCompra['ValorCompra']}</td>";
                echo "<td>{$rowCompra['NombreEstado']}</td>";
                echo "<td>";
                if (!in_array($rowCompra['EstadoCompraID'], [2, 3, 4, 1])) {
                    echo "<a href='agregar_detalle_compra.php?compraID={$rowCompra['CompraID']}&proveedorID={$rowCompra['ProveedorID']}'><button>Editar</button></a>";
                } else {
                    echo "No editable";
                }
                echo "</td>";       
                echo "<td>";
                if ($rowCompra['EstadoCompraID'] == 1) {
                    echo "<select name='estado' onchange='cambiarEstado(this.value, {$rowCompra['CompraID']})'>";
                    echo "<option value='1'>Pedido Enviado</option>";
                    echo "<option value='2'>Cancelar</option>";
                    echo "<option value='3'>Pedido Recibido</option>";
                    echo "</select>";
                } elseif ($rowCompra['EstadoCompraID'] == 2) {
                    echo "Cancelada";
                } elseif ($rowCompra['EstadoCompraID'] == 3) {
                    echo "En Proceso";
                } elseif ($rowCompra['EstadoCompraID'] == 4) {
                    echo "Archivado";
                } elseif ($rowCompra['EstadoCompraID'] == 5) {
                    echo "<select name='estado' onchange='cambiarEstado(this.value, {$rowCompra['CompraID']})'>";
                    echo "<option value='5'>Pedido Creado</option>";
                    echo "<option value='1'>Enviar pedido</option>";
                    echo "</select>";
                }
 
            }
            echo "</table>";
        } else {
            echo "<p>No se encontraron compras para este proveedor.</p>";
        }
    } else {
        echo "<p>No se encontró información para el proveedor con ID $proveedorID.</p>";
    }
} else {
    echo "<p>No se proporcionó un ID de proveedor.</p>";
}
?>
<script>
function cambiarEstado(nuevoEstado, compraID) {
    var confirmacion = confirm("¿Estás seguro de cambiar el estado?");
    if (confirmacion) {
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                location.reload();
            }
        };
        xmlhttp.open("GET", "actualizar_compra.php?compraID=" + compraID + "&nuevoEstado=" + nuevoEstado, true);
        xmlhttp.send();
    }
}


function actualizarCompra(compraID) {
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            location.reload();
        }
    };
    xmlhttp.open("GET", "actualizar_compra.php?compraID=" + compraID, true);
    xmlhttp.send();
}

</script>
