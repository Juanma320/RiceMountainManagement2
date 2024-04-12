<?php
// Incluir el archivo de conexión a la base de datos
include('includes/includes.php');
include('includes/funciones.php');
$row = obtenerDatosUsuario($conexion, $_SESSION['NombreUsuario']);

// Verificar si se recibió un ID de proveedor
if (isset($_GET['proveedorID'])) {
    $proveedorID = mysqli_real_escape_string($conexion, $_GET['proveedorID']);

    // Obtener información del proveedor
    $queryProveedor = "SELECT * FROM proveedores WHERE ProveedorID = $proveedorID";
    $resultProveedor = mysqli_query($conexion, $queryProveedor);

    // Verificar si se encontró el proveedor
    if ($resultProveedor && mysqli_num_rows($resultProveedor) > 0) {
        $rowProveedor = mysqli_fetch_assoc($resultProveedor);

        // Mostrar la barra de navegación
        include('includes/navbar.php');

        echo "<h1>Agregar Compra</h1>";
        echo "<label for='proveedor'>Proveedor:</label>";
        echo "<input type='text' id='proveedor' name='proveedor' value='{$rowProveedor['NombreProveedor']}' readonly><br>";

        // Formulario para agregar compra
        echo "<form action='procesar_compra.php' method='post'>";
        // Campos ocultos para pasar el ID de proveedor y usuario
        echo "<input type='hidden' name='proveedorID' value='$proveedorID'>";
        echo "<input type='hidden' name='usuarioID' value='{$row['UsuarioID']}'>";

        // Fecha de la compra
        echo "<label for='fecha'>Fecha de la Compra:</label>";
        echo "<input type='date' id='fecha' name='fecha' required><br>";

        // Botón para agregar compra
        echo "<input type='submit' value='Agregar Compra'>";

        // Botón para cancelar compra
        echo "<button type='button' onclick='history.back()'>Cancelar Compra</button>";
        echo "</form>";
    } else {
        echo "<p>No se encontró información para el proveedor con ID $proveedorID.</p>";
    }
} else {
    echo "<p>No se proporcionó un ID de proveedor.</p>";
}
?>
