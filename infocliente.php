<?php
include('includes/includes.php');
include('includes/funciones.php');
$row = obtenerDatosUsuario($conexion, $_SESSION['NombreUsuario']);

// Verificar si se recibió un ID de cliente
if (isset($_GET['clienteID'])) {
    $clienteID = mysqli_real_escape_string($conexion, $_GET['clienteID']);

    // Obtener información del cliente
    $queryCliente = "SELECT * FROM Clientes WHERE ClienteID = $clienteID";
    $resultCliente = mysqli_query($conexion, $queryCliente);

    // Verificar si se encontró el cliente
    if ($resultCliente && mysqli_num_rows($resultCliente) > 0) {
        $rowCliente = mysqli_fetch_assoc($resultCliente);

        // Obtener el nombre del coordinador del cliente
        $coordinadorID = $rowCliente['CoordinadorID'];
        $queryCoordinador = "SELECT NombreUsuario FROM Usuarios WHERE UsuarioID = $coordinadorID";
        $resultCoordinador = mysqli_query($conexion, $queryCoordinador);
        $rowCoordinador = mysqli_fetch_assoc($resultCoordinador);

        // Mostrar la barra de navegación
        include('includes/navbar.php');

        echo "<h1>Información del Cliente</h1>";
        echo "<p><strong>Nombre Cliente:</strong> {$rowCliente['NombreCliente']}</p>";
        echo "<p><strong>Correo Electrónico:</strong> {$rowCliente['CorreoElectronico']}</p>";
        echo "<p><strong>Teléfono:</strong> {$rowCliente['Telefono']}</p>";
        echo "<p><strong>NIT:</strong> {$rowCliente['NIT']}</p>";
        echo "<p><strong>Teléfono Encargado:</strong> {$rowCliente['TelefonoEncargado']}</p>";
        echo "<p><strong>Coordinador:</strong> {$rowCoordinador['NombreUsuario']}</p>";

        // Mostrar direcciones del cliente
        echo "<h2>Direcciones</h2>";
        $queryDirecciones = "SELECT * FROM Direcciones_Clientes WHERE ClienteID = $clienteID";
        $resultDirecciones = mysqli_query($conexion, $queryDirecciones);
        if ($resultDirecciones && mysqli_num_rows($resultDirecciones) > 0) {
            echo "<ul>";
            while ($rowDireccion = mysqli_fetch_assoc($resultDirecciones)) {
                echo "<li>{$rowDireccion['Direccion']}</li>";
            }
            echo "</ul>";
        } else {
            echo "<p>No se encontraron direcciones para este cliente.</p>";
        }
        echo "<a href='venta.php?clienteID=$clienteID'><button>Agregar Venta</button></a>";
        // Mostrar ventas realizadas para el cliente
        echo "<h2>Ventas</h2>";
        $queryVentas = "SELECT v.*, d.Direccion, u.NombreUsuario, ev.NombreEstado
                        FROM Ventas v 
                        JOIN Direcciones_Clientes d ON v.DireccionID = d.DireccionID
                        JOIN Usuarios u ON v.UsuarioID = u.UsuarioID
                        JOIN estado_venta ev ON v.EstadoVentaID = ev.EstadoVentaID
                        WHERE v.ClienteID = $clienteID";
        $resultVentas = mysqli_query($conexion, $queryVentas);
        if ($resultVentas && mysqli_num_rows($resultVentas) > 0) {
            echo "<table border='1'>";
            echo "<tr><th>Fecha de Entrega</th><th>Dirección</th><th>Total</th><th>Usuario</th><th>Estado</th><th>Acciones</th></tr>";
            while ($rowVenta = mysqli_fetch_assoc($resultVentas)) {
                if ($rowVenta['EstadoVentaID'] != 5){
                echo "<tr>";
                echo "<td>{$rowVenta['FechaVenta']}</td>";
                echo "<td>{$rowVenta['Direccion']}</td>";
                echo "<td>{$rowVenta['TotalVenta']}</td>";
                echo "<td>{$rowVenta['NombreUsuario']}</td>";
                echo "<td>{$rowVenta['NombreEstado']}</td>";
                echo "<td>";
                echo "<form method='post' action='actualizar_estado_venta.php' onsubmit='return confirm(\"¿Estás seguro de actualizar el estado?\")'>";
                echo "<input type='hidden' name='clienteID' value='$clienteID'>";
                echo "<input type='hidden' name='ventaID' value='{$rowVenta['VentaID']}'>";
                echo "<select name='estado'>";
                switch ($rowVenta['EstadoVentaID']) {
                    case 1: // Creada
                        echo "<option value='2'>En Proceso</option>";
                        echo "<option value='4'>Cancelada</option>";
                        break;
                    case 2: // En Proceso
                        echo "<option value='3'>Realizada</option>";
                        echo "<option value='4'>Cancelada</option>";
                        break;
                    case 6: // Retraso
                        echo "<option value='3'>Realizada</option>";
                        echo "<option value='4'>Cancelada</option>";
                        break;
                    case 4: // Cancelada
                        echo "Venta Cancelada";
                        break;
                }
                echo "</select>";
                echo "<button type='submit'>Actualizar</button>";
                echo "</form>";                
                echo "</td>";
                echo "<td>";
                if (!in_array($rowVenta['EstadoVentaID'], [4, 3, 5])) {
                    echo "<a href='agregar_detalle_venta.php?ventaID={$rowVenta['VentaID']}&clienteID=$clienteID'>Editar</a>";
                } else {
                    echo "Venta no editable";
                }
                echo "</td>";
            }
         }
            echo "</table>";
        } else {
            echo "<p>No se encontraron ventas para este cliente.</p>";
        }

        // Botón para agregar venta
     
    } else {
        echo "<p>No se encontró información para el cliente con ID $clienteID.</p>";
    }
} else {
    echo "<p>No se proporcionó un ID de cliente.</p>";
}
?>
