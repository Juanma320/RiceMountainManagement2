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

// Lógica para agregar una marca si se recibe el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['nombre_marca'])) {
    $nombreMarca = $_POST['nombre_marca'];
    agregarMarcaProveedor($conexion, $proveedorID, $nombreMarca);
    header('Location: marcasproveedor.php?id=' . $proveedorID);
    exit();
}

// Consulta SQL para obtener las marcas del proveedor
$query = "SELECT MarcaID, NombreMarca, Estado FROM marcas WHERE ProveedorID = ?";
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
        <title>Marcas del Proveedor</title>
    </head>
    <body>";

    include('includes/navbar.php');

    echo "<h1>Marcas del Proveedor</h1>
    <table border='1'>
        <tr>
            <th>Nombre de la Marca</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>";
    
    // Recorrer los resultados y mostrarlos en la tabla
    while ($row = mysqli_fetch_assoc($resultado)) {
        echo "<tr>
        <td>{$row['NombreMarca']}</td>
        <td>" . ($row['Estado'] == 1 ? 'Activa' : 'Inactiva') . "</td>
        <td>
            <form method='post' action='eliminar_marca.php' onsubmit=\"return confirm('¿Estás seguro de eliminar esta marca?');\">
                <input type='hidden' name='marcaID' value='{$row['MarcaID']}'>
                <input type='hidden' name='proveedorID' value='{$proveedorID}'>
                <button type='submit'>Eliminar</button>
            </form>
            <form method='post' action='inactivar_marca.php' onsubmit=\"return confirm('¿Estás seguro de inactivar esta marca?');\">
                <input type='hidden' name='marcaID' value='{$row['MarcaID']}'>               
                <input type='hidden' name='estado' value='{$row['Estado']}'>
                <input type='hidden' name='proveedorID' value='{$proveedorID}'>
                <button type='submit'>".($row['Estado'] ? 'Inactivar' : 'Activar')."</button>
            </form>
        </td>
    </tr>";


    }

    echo "</table>
    
    <h2>Agregar Marca</h2>
    <form method='post' action='procesar_marca.php?id={$proveedorID}'>
        <label for='nombre_marca'>Nombre de la Marca:</label>
        <input type='text' id='nombre_marca' name='nombre_marca' required>
        <button type='submit'>Agregar Marca</button>
    </form>";

    echo "</body>
    </html>";
} else {
    echo "No se encontraron marcas para este proveedor.";
}

?>
