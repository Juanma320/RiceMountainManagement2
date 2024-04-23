<?php
include ('includes/includes.php');
include ('includes/funciones.php');
$row = obtenerDatosUsuario($conexion, $_SESSION['NombreUsuario']);

if ($_SESSION['RolID'] != 1) {
    header('Location: login.php');
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['direccion']) && isset($_POST['ciudad']) && isset($_POST['codigoPostal']) && isset($_POST['proveedor_id'])) {
    $proveedorID = $_POST['proveedor_id'];
    $direccion = $_POST['direccion'];
    $ciudad = $_POST['ciudad'];
    $codigoPostal = $_POST['codigoPostal'];

    // Verificar si la dirección ya existe
    $queryVerificarDireccion = "SELECT Direccion FROM proveedores_direcciones WHERE Direccion = '$direccion' AND Ciudad = '$ciudad' AND CodigoPostal = '$codigoPostal'";
    $resultadoVerificarDireccion = mysqli_query($conexion, $queryVerificarDireccion);

    if (mysqli_num_rows($resultadoVerificarDireccion) > 0) {
        echo "La dirección ya existe.";
    } else {
        // Agregar la nueva dirección
        agregarDireccion($conexion, $proveedorID, $direccion, $ciudad, $codigoPostal);
        echo "Dirección agregada correctamente.";
    }

    // Redireccionar a direccionesproveedor.php
    header('Location: direccionesproveedor.php?id=' . $proveedorID);
    exit();
} else {
    echo "ID de proveedor no especificado.";
    exit();
}