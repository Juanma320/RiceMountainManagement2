<?php
include('includes/includes.php');
include('includes/funciones.php');
$row = obtenerDatosUsuario($conexion, $_SESSION['NombreUsuario']);
// Verificar si se ha enviado el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener la acción y el ID del usuario
    $accion = $_POST['accion'];
    $usuarioID = $_POST['usuario_id'];

    // Realizar la acción según el botón presionado
    switch ($accion) {
        case 'Inactivar':
            inactivarUsuario($conexion, $usuarioID);
            break;
        case 'Activar':
            activarUsuario($conexion, $usuarioID);
            break;
        // Otros casos según sea necesario
    }
}


// Obtener información de usuarios coordinadores
$query = 'SELECT U.UsuarioID, U.NombreUsuario, U.Correo, U.DocumentoIdentidad, U.FechaUltimaActividad, U.Activo, COUNT(C.ClienteID) AS CantidadEmpresas
          FROM Usuarios U
          LEFT JOIN Clientes C ON U.UsuarioID = C.CoordinadorID
          WHERE U.RolID = 2
          GROUP BY U.UsuarioID';
$resultado = mysqli_query($conexion, $query);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Coordinadores</title>
</head>
<body>

    <?php include('includes/navbar.php'); ?>

    <h1>Gestionar Coordinadores</h1>

    <a href="agregar_coordinador.php"><button>Agregar Coordinador</button></a>

    <table border="1">
        <tr>
            <th>Nombre Usuario</th>
            <th>Correo</th>
            <th>Documento Identidad</th>
            <th>Cantidad Empresas</th>
            <th>Estado</th>
        </tr>
        <?php
        // Iterar sobre los resultados y mostrar en la tabla
        while ($row = mysqli_fetch_assoc($resultado)) {
            echo "<tr>";
            echo "<td>{$row['NombreUsuario']}</td>";
            echo "<td>{$row['Correo']}</td>";
            echo "<td>{$row['DocumentoIdentidad']}</td>";
            echo "<td>{$row['CantidadEmpresas']}</td>";
            echo "<td>";
            // Agregar un formulario con un botón que envía la acción y el ID del usuario
            echo "<form method='post' action=''>
                    <input type='hidden' name='usuario_id' value='{$row['UsuarioID']}'>";
            if ($row['Activo']) {
                // Si el usuario está activo, muestra un botón para inactivar
                echo "<input type='submit' name='accion' value='Inactivar'>";
            } else {
                // Si el usuario está inactivo, muestra un botón para activar
                echo "<input type='submit' name='accion' value='Activar'>";
            }
            echo "</form>";
            echo "</td>";
            echo "</tr>";
        }
        ?>
    </table>

</body>
</html>
