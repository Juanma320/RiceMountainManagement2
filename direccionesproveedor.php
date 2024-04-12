<?php
include('includes/includes.php');
include('includes/funciones.php');
$row = obtenerDatosUsuario($conexion, $_SESSION['NombreUsuario']);

// Verificar si el usuario tiene el rol de administrador
if ($_SESSION['RolID'] != 1) {
    // Si no es administrador, redirigir a la página de inicio
    header('Location: login.php');
    exit();
}

// Obtener el ID del proveedor de la URL
if (isset($_GET['id'])) {
    $proveedorID = $_GET['id'];
} else {
    echo "ID de proveedor no especificado.";
    exit();
}

// Lógica para eliminar una dirección si se recibe el ID por parámetro
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['eliminar_id'])) {
    $eliminarID = $_POST['eliminar_id'];
    eliminarDireccion($conexion, $eliminarID);
}

// Lógica para agregar una dirección si se recibe el formulario por POST
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['direccion']) && isset($_POST['ciudad']) && isset($_POST['codigoPostal'])) {
    $direccion = $_POST['direccion'];
    $ciudad = $_POST['ciudad'];
    $codigoPostal = $_POST['codigoPostal'];
    agregarDireccion($conexion, $proveedorID, $direccion, $ciudad, $codigoPostal);
}

// Consulta SQL para obtener las direcciones del proveedor
$query = "SELECT ID, Direccion, Ciudad, codigoPostal FROM proveedores_direcciones WHERE ProveedorID = ?";
$statement = mysqli_prepare($conexion, $query);
mysqli_stmt_bind_param($statement, "i", $proveedorID);
mysqli_stmt_execute($statement);
$resultado = mysqli_stmt_get_result($statement);

// Verificar si hay resultados
if (mysqli_num_rows($resultado) > 0) {
    echo "<!DOCTYPE html>
    <html lang='es'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Direcciones del Proveedor</title>
    </head>
    <body>";

    include('includes/navbar.php');

    echo "<h1>Direcciones del Proveedor</h1>
    <table border='1'>
        <tr>
            <th>Dirección</th>
            <th>Ciudad</th>
            <th>Código Postal</th>
            <th>Acciones</th>
        </tr>";
    
    // Recorrer los resultados y mostrarlos en la tabla
    while ($row = mysqli_fetch_assoc($resultado)) {
        echo "<tr>
                <td>{$row['Direccion']}</td>
                <td>{$row['Ciudad']}</td>
                <td>{$row['codigoPostal']}</td>
                <td>
                    <form method='post' action=''>
                        <input type='hidden' name='eliminar_id' value='{$row['ID']}'>
                        <button type='submit' onclick=\"return confirm('¿Estás seguro de eliminar esta dirección?');\">Eliminar</button>
                    </form>
                </td>
            </tr>";
    }

    echo "</table>
    
    <h2>Agregar Dirección</h2>
    <form method='post' action=''>
        <input type='hidden' name='proveedor_id' value='$proveedorID'>
        <label for='direccion'>Dirección:</label>
        <input type='text' id='direccion' name='direccion' required>
        <label for='ciudad'>Ciudad:</label>
        <input type='text' id='ciudad' name='ciudad' required>
        <label for='codigoPostal'>Código Postal:</label>
        <input type='text' id='codigoPostal' name='codigoPostal' required>
        <button type='submit'>Agregar Dirección</button>
    </form>";

    echo "</body>
    </html>";
} else {
    echo "No se encontraron direcciones para este proveedor.";
}

?>
