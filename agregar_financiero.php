<?php
include('includes/includes.php');
include('includes/funciones.php');
$row = obtenerDatosUsuario($conexion, $_SESSION['NombreUsuario']);
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener datos del formulario
    $nombreUsuario = mysqli_real_escape_string($conexion, $_POST['nombre_usuario']);
    $contra = password_hash($_POST['contra'], PASSWORD_DEFAULT);
    $documentoIdentidad = mysqli_real_escape_string($conexion, $_POST['documento_identidad']);
    $correo = mysqli_real_escape_string($conexion, $_POST['correo']);
    
    // Hashear la contraseña
    $hashedContra = password_hash($contra, PASSWORD_DEFAULT);


    // Insertar nuevo usuario financiero en la base de datos
    $query = "INSERT INTO Usuarios (NombreUsuario, Contra, DocumentoIdentidad, Correo, RolID, Activo, FechaUltimaActividad)
              VALUES ('$nombreUsuario', '$contra', '$documentoIdentidad', '$correo', 3, 1, NOW())";
    $resultado = mysqli_query($conexion, $query);

    if ($resultado) {
        echo "<p>Usuario financiero creado con éxito.</p>";
    } else {
        echo "<p>Error al crear usuario financiero.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Financiero</title>
</head>
<body>

    <?php include('includes/navbar.php'); ?>

    <h1>Crear Financiero</h1>

    <form method="POST" action="">
        <label for="nombre_usuario">Nombre de Usuario:</label><br>
        <input type="text" id="nombre_usuario" name="nombre_usuario" required><br>

        <label for="contra">Contraseña:</label><br>
        <input type="password" id="contra" name="contra" required><br>

        <label for="documento_identidad">Documento de Identidad:</label><br>
        <input type="text" id="documento_identidad" name="documento_identidad" required><br>

        <label for="correo">Correo Electrónico:</label><br>
        <input type="email" id="correo" name="correo" required><br>

        <input type="submit" value="Crear Financiero">
        <input type="button" value="Cancelar" onclick="history.go(-1);">
    </form>

</body>
</html>
