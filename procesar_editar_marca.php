<?php
include ('includes/includes.php');
include ('includes/funciones.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['marcaID']) && isset($_POST['proveedorID']) && isset($_POST['nuevo_nombre_marca'])) {
    $marcaID = $_POST['marcaID'];
    $nuevoNombreMarca = $_POST['nuevo_nombre_marca'];

    // Consultar el nombre actual de la marca
    $query = "SELECT NombreMarca FROM marcas WHERE MarcaID = ?";
    $statement = mysqli_prepare($conexion, $query);
    mysqli_stmt_bind_param($statement, "i", $marcaID);
    mysqli_stmt_execute($statement);
    $result = mysqli_stmt_get_result($statement);
    $row = mysqli_fetch_assoc($result);
    $nombreActualMarca = $row['NombreMarca'];

    if ($nuevoNombreMarca == $nombreActualMarca) {
        echo "<script>alert(\"El nombre ingresado es el mismo que el nombre actual de la marca.\"); window.location.href='marcasproveedor.php?id=" . $_POST['proveedorID'] . "';</script>";
        exit();
    }

    // Actualizar el nombre de la marca en la base de datos
    $query = "UPDATE marcas SET NombreMarca = ? WHERE MarcaID = ?";
    $statement = mysqli_prepare($conexion, $query);
    mysqli_stmt_bind_param($statement, "si", $nuevoNombreMarca, $marcaID);
    mysqli_stmt_execute($statement);

    if (mysqli_stmt_affected_rows($statement) > 0) {
        // Redireccionar a la página de marcas del proveedor
        header('Location: marcasproveedor.php?id=' . $_POST['proveedorID']);
        exit();
    } else {
        echo "<script>alert(\"Error al actualizar el nombre de la marca.\"); window.location.href='marcasproveedor.php?id=" . $_POST['proveedorID'] . "';</script>";
        exit();
    }
} else {
    echo "<script>alert(\"Error al procesar la solicitud.\"); window.location.href='marcasproveedor.php?id=" . $_POST['proveedorID'] . "';</script>";
    exit();
}