<?php
include('includes/includes.php');
include('includes/funciones.php');
$row = obtenerDatosUsuario($conexion, $_SESSION['NombreUsuario']);
include('includes/navbar.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['nombrePresentacion'], $_POST['medida'], $_POST['medicion']) && !empty($_POST['nombrePresentacion']) && !empty($_POST['medida']) && !empty($_POST['medicion'])) {
        $nombrePresentacion = $_POST['nombrePresentacion'];
        $medida = $_POST['medida'];
        $medicion = $_POST['medicion'];

        // Concatenar medida y medicion si no es unidad
        if ($medicion !== 'unidad') {
            $medida .= $medicion;
        }

        // Insertar la nueva presentación en la base de datos
        $queryInsertPresentacion = "INSERT INTO presentaciones (NombrePresentacion, Medida) VALUES ('$nombrePresentacion', '$medida')";
        if (mysqli_query($conexion, $queryInsertPresentacion)) {
            echo "<p>Presentación agregada correctamente.</p>";
            // Redireccionar a gestion_presentaciones.php después de mostrar el mensaje
            header("Location: gestion_presentaciones.php");
            exit();
        } else {
            echo "<p>Error al agregar la presentación.</p>";
        }
    } else {
        echo "<p>Por favor, complete todos los campos.</p>";
    }
}
?>
