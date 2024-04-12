<?php
include('includes/includes.php');
include('includes/funciones.php');
$row = obtenerDatosUsuario($conexion, $_SESSION['NombreUsuario']);
// Verificar si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recuperar los datos del formulario
    $nombreCliente = mysqli_real_escape_string($conexion, $_POST['nombreCliente']);
    $correoElectronico = mysqli_real_escape_string($conexion, $_POST['correoElectronico']);
    $telefono = mysqli_real_escape_string($conexion, $_POST['telefono']);
    $nit = mysqli_real_escape_string($conexion, $_POST['nit']);
    $telefonoEncargado = mysqli_real_escape_string($conexion, $_POST['telefonoEncargado']);
    $coordinadorID = ($_POST['coordinadorID'] !== 'null') ? mysqli_real_escape_string($conexion, $_POST['coordinadorID']) : 'NULL';
    $nombreEncargado = mysqli_real_escape_string($conexion, $_POST['nombreEncargado']);

    // Llamar a la función agregarCliente
    if (agregarCliente($conexion, $nombreCliente, $correoElectronico, $telefono, $nit, $telefonoEncargado, $coordinadorID, $nombreEncargado)) {
        echo "<p>Cliente agregado con éxito.</p>";
    } else {
        echo "<p>Error al agregar el cliente.</p>";
    }
}

include('includes/navbar.php');
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Cliente</title>
</head>
<body>
    <h2>Agregar Cliente</h2>
    <form method="post" action="">
        <label>Nombre Cliente:</label>
        <input type="text" name="nombreCliente" required>
        <br>
        <label>Correo Electrónico:</label>
        <input type="email" name="correoElectronico" required>
        <br>
        <label>Teléfono:</label>
        <input type="text" name="telefono" required>
        <br>
        <label>NIT:</label>
        <input type="text" name="nit" required>
        <br>
        <label>Teléfono Encargado:</label>
        <input type="text" name="telefonoEncargado" required>
        <br>
        <label>Coordinador:</label>
        <select name="coordinadorID">
            <option value="null">Ninguno</option>
            <?php
            $queryCoordinadores = "SELECT UsuarioID, NombreUsuario FROM Usuarios WHERE RolID = 2";
            $resultCoordinadores = mysqli_query($conexion, $queryCoordinadores);
            while ($rowCoordinador = mysqli_fetch_assoc($resultCoordinadores)) {
                echo "<option value='{$rowCoordinador['UsuarioID']}'>{$rowCoordinador['NombreUsuario']}</option>";
            }
            ?>
        </select>
        <br>
        <label>Nombre Encargado:</label>
        <input type="text" name="nombreEncargado" required>
        <br>
        <input type="submit" value="Agregar Cliente">
    </form>
</body>
</html>
