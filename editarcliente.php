<?php
include('includes/includes.php');
include('includes/funciones.php');
$row = obtenerDatosUsuario($conexion, $_SESSION['NombreUsuario']);
// Verificar si se recibió un ID de cliente
if (isset($_GET['clienteID'])) {
    $clienteID = mysqli_real_escape_string($conexion, $_GET['clienteID']);

    // Obtener información del cliente
    $queryCliente = "SELECT c.ClienteID, c.NombreCliente, c.CorreoElectronico, c.Telefono, c.NIT, c.TelefonoEncargado, c.CoordinadorID, c.NombreEncargado, c.UltimaAsignacion, u.NombreUsuario AS NombreCoordinador
                     FROM Clientes c
                     LEFT JOIN Usuarios u ON c.CoordinadorID = u.UsuarioID
                     WHERE c.ClienteID = $clienteID";

    $resultCliente = mysqli_query($conexion, $queryCliente);

    // Obtener la lista de usuarios coordinadores, incluyendo la opción "Ninguno"
    $queryCoordinadores = "SELECT UsuarioID, NombreUsuario FROM Usuarios WHERE RolID = 2";
    $resultCoordinadores = mysqli_query($conexion, $queryCoordinadores);

    // Verificar si se encontró el cliente
    if ($resultCliente && mysqli_num_rows($resultCliente) > 0) {
        $rowCliente = mysqli_fetch_assoc($resultCliente);

        // Procesar el formulario de edición si se envió
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Recuperar los datos del formulario
            $nombreCliente = mysqli_real_escape_string($conexion, $_POST['nombreCliente']);
            $correoElectronico = mysqli_real_escape_string($conexion, $_POST['correoElectronico']);
            $telefono = mysqli_real_escape_string($conexion, $_POST['telefono']);
            $nit = mysqli_real_escape_string($conexion, $_POST['nit']);
            $telefonoEncargado = mysqli_real_escape_string($conexion, $_POST['telefonoEncargado']);
            $coordinadorID = ($_POST['coordinadorID'] !== 'null') ? mysqli_real_escape_string($conexion, $_POST['coordinadorID']) : 'NULL';
            $nombreEncargado = mysqli_real_escape_string($conexion, $_POST['nombreEncargado']);

            // Actualizar los datos del cliente en la base de datos
            $queryUpdate = "UPDATE Clientes SET
                            NombreCliente = '$nombreCliente',
                            CorreoElectronico = '$correoElectronico',
                            Telefono = '$telefono',
                            NIT = '$nit',
                            TelefonoEncargado = '$telefonoEncargado',
                            CoordinadorID = $coordinadorID,
                            NombreEncargado = '$nombreEncargado'
                            WHERE ClienteID = $clienteID";

            $resultadoUpdate = mysqli_query($conexion, $queryUpdate);

            if ($resultadoUpdate) {
                echo "<p>Cliente actualizado con éxito.</p>";
                // Redirigir a gestionclientes.php
                echo "<script>window.location.href = 'gestionclientes.php';</script>";
                exit();
            } else {
                echo "<p>Error al actualizar el cliente.</p>";
            }
        }
include('includes/navbar.php');
        // Mostrar formulario de edición
        echo "<h2>Editar Cliente</h2>";
        echo "<form method='post' action=''>
                <label>Nombre Cliente:</label>
                <input type='text' name='nombreCliente' value='{$rowCliente['NombreCliente']}' required>
                <br>
                <label>Correo Electrónico:</label>
                <input type='email' name='correoElectronico' value='{$rowCliente['CorreoElectronico']}' required>
                <br>
                <label>Teléfono:</label>
                <input type='text' name='telefono' value='{$rowCliente['Telefono']}' required>
                <br>
                <label>NIT:</label>
                <input type='text' name='nit' value='{$rowCliente['NIT']}' required>
                <br>
                <label>Teléfono Encargado:</label>
                <input type='text' name='telefonoEncargado' value='{$rowCliente['TelefonoEncargado']}' required>
                <br>
                <label>Coordinador:</label>
                <select name='coordinadorID'>
                    <option value='null'>Ninguno</option>";
        while ($rowCoordinador = mysqli_fetch_assoc($resultCoordinadores)) {
            $selected = ($rowCoordinador['UsuarioID'] == $rowCliente['CoordinadorID']) ? 'selected' : '';
            echo "<option value='{$rowCoordinador['UsuarioID']}' $selected>{$rowCoordinador['NombreUsuario']}</option>";
        }
        echo "</select>
        <br>
        <label>Nombre Encargado:</label>
        <input type='text' name='nombreEncargado' value='{$rowCliente['NombreEncargado']}' required>
        <br>
        <input type='submit' value='Actualizar'>
        <input type='button' value='Cancelar' onclick='history.go(-1);'>
      </form>";
} else {
echo "<p>No se encontró información para el cliente con ID $clienteID.</p>";
}
} else {
echo "<p>No se proporcionó un ID de cliente.</p>";
}
?>
