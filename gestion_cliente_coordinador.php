<?php
include('includes/includes.php');
include('includes/funciones.php');
$row = obtenerDatosUsuario($conexion, $_SESSION['NombreUsuario']);

if ($_SESSION['RolID'] != 2) {
    header("Location: login.php");
    exit();
}

$queryClientes = "SELECT C.*, U.NombreUsuario as NombreCoordinador
                  FROM Clientes C
                  LEFT JOIN Usuarios U ON C.CoordinadorID = U.UsuarioID
                  WHERE C.CoordinadorID = {$_SESSION['UsuarioID']}";
$resultClientes = mysqli_query($conexion, $queryClientes);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Clientes</title>
</head>
<body>
<?php include('includes/navbar.php'); ?>
    <h1>Clientes Asignados a <?php echo $row['NombreUsuario']; ?></h1>
    <table border="1">
        <tr>
            <th>Nombre Cliente</th>
            <th>Correo Electrónico</th>
            <th>Teléfono</th>
            <th>Coordinador</th>
            <th>Nombre Encargado</th>
            <th>Agregar Dirección</th>
        </tr>

        <?php
        while ($rowCliente = mysqli_fetch_assoc($resultClientes)) {
            echo "<tr>";
            echo "<td><a href='infocliente.php?clienteID={$rowCliente['ClienteID']}'>{$rowCliente['NombreCliente']}</a></td>";
            echo "<td>{$rowCliente['CorreoElectronico']}</td>";
            echo "<td>{$rowCliente['Telefono']}</td>";
            echo "<td>{$rowCliente['NombreCoordinador']}</td>";
            echo "<td>{$rowCliente['NombreEncargado']}</td>";
            echo "<td><a href='agregardireccion.php?clienteID={$rowCliente['ClienteID']}'>Agregar Dirección</a></td>";
            echo "</tr>";
        }
        ?>
    </table>
</body>
</html>
