<?php
include ('includes/includes.php');
include ('includes/funciones.php');
$row = obtenerDatosUsuario($conexion, $_SESSION['NombreUsuario']);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener los datos del formulario
    $productoID = mysqli_real_escape_string($conexion, $_POST['productoID']);
    $nuevoBeneficio = mysqli_real_escape_string($conexion, $_POST['nuevo_beneficio']);
    $fechaFinBeneficio = mysqli_real_escape_string($conexion, $_POST['fecha_fin_beneficio']);

    // Validar que el nuevo beneficio sea un valor válido
    if ($nuevoBeneficio < 0 || $nuevoBeneficio > 100) {
        echo "<p>El valor del nuevo beneficio debe estar entre 0 y 100.</p>";
    } else {
        // Actualizar el beneficio del producto en la base de datos
        $query = "UPDATE precio_compras SET NuevoBeneficio='$nuevoBeneficio', FechaFinBeneficio='$fechaFinBeneficio' WHERE ProductoID='$productoID'";
        $resultado = mysqli_query($conexion, $query);

        if ($resultado) {
            if ($_SESSION['RolID'] == 3) {
                header('Location: gestion_productos_financiero.php');
            } else {
                header('Location: gestionproductosA.php');
            }
            exit();
        } else {
            echo "<p>Error al guardar los cambios.</p>";
        }
    }
}