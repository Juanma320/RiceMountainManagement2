<?php
include('includes/includes.php');
include('includes/funciones.php');
$row = obtenerDatosUsuario($conexion, $_SESSION['NombreUsuario']);

// Verificar si se recibió un ID de cliente
if (isset($_GET['clienteID'])) {
    $clienteID = mysqli_real_escape_string($conexion, $_GET['clienteID']);

    // Obtener información del cliente
    $queryCliente = "SELECT NombreCliente FROM clientes WHERE ClienteID = $clienteID";
    $resultCliente = mysqli_query($conexion, $queryCliente);
    $rowCliente = mysqli_fetch_assoc($resultCliente);

    // Verificar si se envió el formulario para agregar una dirección
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $direccion = mysqli_real_escape_string($conexion, $_POST['direccion']);
        $codigoPostal = mysqli_real_escape_string($conexion, $_POST['codigoPostal']);
        $ciudad = mysqli_real_escape_string($conexion, $_POST['ciudad']);

        // Insertar la nueva dirección en la base de datos
        $queryInsert = "INSERT INTO direcciones_clientes (ClienteID, Direccion, CodigoPostal, Ciudad) VALUES ($clienteID, '$direccion', $codigoPostal, '$ciudad')";
        $resultadoInsert = mysqli_query($conexion, $queryInsert);

        if ($resultadoInsert) {
            echo "<p>Dirección agregada con éxito.</p>";
        } else {
            echo "<p>Error al agregar la dirección.</p>";
        }
    }

    // Obtener las direcciones actuales del cliente
    $queryDirecciones = "SELECT DireccionID, Direccion, CodigoPostal, Ciudad FROM direcciones_clientes WHERE ClienteID = $clienteID";
    $resultDirecciones = mysqli_query($conexion, $queryDirecciones);

    // Mostrar la barra de navegación
    include('includes/navbar.php');

    echo "<h1>Direcciones de {$rowCliente['NombreCliente']}</h1>";

    // Mostrar las direcciones actuales
    if (mysqli_num_rows($resultDirecciones) > 0) {
        echo "<table border='1'>
                <tr>
                    <th>Dirección</th>
                    <th>Código Postal</th>
                    <th>Ciudad</th>
                    <th>Acciones</th>
                </tr>";
        while ($rowDireccion = mysqli_fetch_assoc($resultDirecciones)) {
            echo "<tr>";
            echo "<td>{$rowDireccion['Direccion']}</td>";
            echo "<td>{$rowDireccion['CodigoPostal']}</td>";
            echo "<td>{$rowDireccion['Ciudad']}</td>";
            echo "<td><a href='borrardireccion.php?direccionID={$rowDireccion['DireccionID']}'>Borrar</a></td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No hay direcciones registradas.</p>";
    }

    // Mostrar formulario para agregar una nueva dirección
    echo "<h2>Agregar Nueva Dirección</h2>";
    echo "<form method='post' action=''>
            <label>Dirección:</label>
            <input type='text' name='direccion' required>
            <br>
            <label>Código Postal:</label>
            <input type='text' name='codigoPostal' required>
            <br>
            <label>Ciudad:</label>
            <input type='text' name='ciudad' required>
            <br>
            <input type='submit' value='Agregar Dirección'>
          </form>";
} else {
    echo "<p>No se proporcionó un ID de cliente.</p>";
}
?>
