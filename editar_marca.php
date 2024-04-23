<?php
include ('includes/includes.php');
include ('includes/funciones.php');

// Obtener el ID de la marca y el ID del proveedor de la URL
if (isset($_GET['marcaID']) && isset($_GET['proveedorID'])) {
    $marcaID = $_GET['marcaID'];
    $proveedorID = $_GET['proveedorID'];

    // Consulta SQL para obtener el nombre de la marca
    $query = "SELECT NombreMarca FROM marcas WHERE MarcaID = ?";
    $statement = mysqli_prepare($conexion, $query);
    mysqli_stmt_bind_param($statement, "i", $marcaID);
    mysqli_stmt_execute($statement);
    $resultado = mysqli_stmt_get_result($statement);

    // Verificar si se encontró la marca
    if (mysqli_num_rows($resultado) > 0) {
        $row = mysqli_fetch_assoc($resultado);
        $nombreMarcaActual = $row['NombreMarca'];
    } else {
        echo "No se encontró la marca.";
        exit();
    }
} else {
    echo "ID de marca o de proveedor no especificado.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Marca</title>
</head>

<body>

    <h1>Editar Marca</h1>

    <form method="post" action="procesar_editar_marca.php"
        onsubmit="return confirm('¿Estás seguro de que quieres cambiar el nombre de la marca?');">
        <input type='hidden' name='marcaID' value='<?php echo $marcaID; ?>'>
        <input type='hidden' name='proveedorID' value='<?php echo $proveedorID; ?>'>
        <label for='nuevo_nombre_marca'>Nuevo Nombre de la Marca:</label>
        <input type='text' id='nuevo_nombre_marca' name='nuevo_nombre_marca' value='<?php echo $nombreMarcaActual; ?>'
            required>
        <button type='submit'>Guardar</button>
    </form>

</body>

</html>