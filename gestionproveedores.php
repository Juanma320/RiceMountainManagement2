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

// Lógica para cambiar el estado de activo/inactivo si se recibe la acción por parámetro
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['accion']) && isset($_POST['proveedor_id'])) {
    $accion = $_POST['accion'];
    $proveedorID = $_POST['proveedor_id'];

    // Llamar a la función correspondiente según la acción
    if ($accion == 'Inactivar') {
        inactivarProveedor($conexion, $proveedorID);
    } elseif ($accion == 'Activar') {
        activarProveedor($conexion, $proveedorID);
    }
}

// Consulta SQL para obtener los datos de los proveedores
$query = "SELECT ProveedorID, NombreProveedor, CorreoElectronico, Telefono, Contacto, Activo FROM proveedores";
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
    <a href='agregar_proveedor.php'>Agregar Proveedor</a>
    <table border='1'>
        <tr>
            <th>NombreProveedor</th>
            <th>CorreoElectronico</th>
            <th>Telefono</th>
            <th>Contacto</th>
            <th>Estado</th>
            <th>Acciones</th>
            <th>Agregar Dirección</th>
            <th>Agregar Marca</th>
        </tr>";
    
    // Recorrer los resultados y mostrarlos en la tabla
    while ($row = mysqli_fetch_assoc($resultado)) {
        echo "<tr>
        <td><a href='infoproveedor.php?proveedorID={$row['ProveedorID']}'>{$row['NombreProveedor']}</a></td>
        <td>{$row['CorreoElectronico']}</td>
        <td>{$row['Telefono']}</td>
        <td>{$row['Contacto']}</td>
        <td>
            <form method='post' action=''>
                <input type='hidden' name='proveedor_id' value='{$row['ProveedorID']}'>";

if ($row['Activo']) {
    // Si está activo, mostrar botón para inactivar
    echo "<input type='hidden' name='accion' value='Inactivar'>
          <button type='submit' onclick=\"return confirm('¿Estás seguro de inactivar este proveedor?');\">Inactivar</button>";
} else {
    // Si está inactivo, mostrar botón para activar
    echo "<input type='hidden' name='accion' value='Activar'>
          <button type='submit' onclick=\"return confirm('¿Estás seguro de activar este proveedor?');\">Activar</button>";
}
echo "</form>
      </td>
      <td><a href='editarproveedor.php?id={$row['ProveedorID']}'>Editar</a></td>
      <td><a href='direccionesproveedor.php?id={$row['ProveedorID']}'>Agregar Dirección</a></td>
      <td><a href='marcasproveedor.php?id={$row['ProveedorID']}'>Agregar Marca</a></td>
    </tr>";

    }

    echo "</table>
    
    </body>
    </html>";
} else {
    echo "No se encontraron resultados.";
}
?>
