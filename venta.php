<?php
// Incluir el archivo de conexión a la base de datos
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

        // Mostrar la barra de navegación
        include('includes/navbar.php');

        echo "<h1>Generar Venta</h1>";
        echo "<label for='cliente'>Cliente:</label>";
        echo "<input type='text' id='cliente' name='cliente' value='{$rowCliente['NombreCliente']}' readonly><br>";

        // Formulario para generar la venta
        echo "<form action='procesar_venta.php' method='post'>";
        // Campos ocultos para pasar el ID de cliente y usuario
        echo "<input type='hidden' name='clienteID' value='$clienteID'>";
        echo "<input type='hidden' name='usuarioID' value='{$row['UsuarioID']}'>";

        // Selección de dirección de envío
        echo "<label for='direccion'>Dirección de Envío:</label>";
        echo "<select name='direccion' id='direccion'>";
        $queryDirecciones = "SELECT * FROM Direcciones_Clientes WHERE ClienteID = $clienteID";
        $resultDirecciones = mysqli_query($conexion, $queryDirecciones);
        while ($rowDireccion = mysqli_fetch_assoc($resultDirecciones)) {
            echo "<option value='{$rowDireccion['DireccionID']}'>{$rowDireccion['Direccion']}</option>";
        }
        echo "</select><br>";

        // Fecha de la venta
        echo "<label for='fecha'>Fecha de la Venta:</label>";
        echo "<input type='date' id='fecha' name='fecha' required><br>";

        // Botón para agregar detalles de venta
        echo "<input type='submit' value='Crear Venta'>";

        // Botón para cancelar venta
        echo "<button type='button' onclick='history.back()'>Cancelar Venta</button>";
        echo "</form>";
    } else {
        echo "<p>No se encontró información para el cliente con ID $clienteID.</p>";
    }
} else {
    echo "<p>No se proporcionó un ID de cliente.</p>";
}
?>
