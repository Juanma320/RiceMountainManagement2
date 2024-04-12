<?php
include('includes/includes.php');
include('includes/funciones.php');
$row = obtenerDatosUsuario($conexion, $_SESSION['NombreUsuario']);

// Verificar si el usuario tiene el rol de financiero
if ($_SESSION['RolID'] != 3) {
    // Si no es financiero, redirigir a la página de inicio
    header('Location: login.php');
    exit();
}

// Consulta SQL para obtener los datos de los proveedores
$query = "SELECT ProveedorID, NombreProveedor, CorreoElectronico, Telefono, Contacto FROM proveedores";
$resultado = mysqli_query($conexion, $query);

// Verificar si hay resultados
if (mysqli_num_rows($resultado) > 0) {
    echo "<!DOCTYPE html>
    <html lang='es'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Lista de Proveedores</title>
    </head>
    <body>";

    include('includes/navbar.php');

    echo "<h1>Lista de Proveedores</h1>
    <table border='1'>
        <tr>
            <th>NombreProveedor</th>
            <th>CorreoElectronico</th>
            <th>Telefono</th>
            <th>Contacto</th>
        </tr>";
    
    // Recorrer los resultados y mostrarlos en la tabla
    while ($row = mysqli_fetch_assoc($resultado)) {
        echo "<tr>
        <td><a href='infoproveedor.php?proveedorID={$row['ProveedorID']}'>{$row['NombreProveedor']}</a></td>
        <td>{$row['CorreoElectronico']}</td>
        <td>{$row['Telefono']}</td>
        <td>{$row['Contacto']}</td>
    </tr>";
    }

    echo "</table>
    
    </body>
    </html>";
} else {
    echo "No se encontraron resultados.";
}
?>
