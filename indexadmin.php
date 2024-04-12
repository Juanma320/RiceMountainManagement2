<?php
// Archivo: indexadmin.php

// Incluir configuraciones y funciones comunes
include('includes/includes.php');
include('includes/funciones.php');
$row = obtenerDatosUsuario($conexion, $_SESSION['NombreUsuario']);
// Verificar si el usuario tiene el rol de administrador
if ($_SESSION['RolID'] != 1) {
    // Si no es administrador, redirigir a la página de inicio
    header('Location: login.php');
    exit();
}

// Obtener datos del usuario

// Obtener información de usuarios (excepto administradores)
$query = 'SELECT NombreUsuario, Correo, DocumentoIdentidad, FechaUltimaActividad FROM Usuarios WHERE RolID != 1';
$resultado = mysqli_query($conexion, $query);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administrador</title>
</head>
<body>

    <?php include('includes/navbar.php'); ?>

    <h1>Bienvenido, <?php echo $row['NombreUsuario']; ?> (Administrador)</h1>
    
    <!-- Menú para el rol de administrador -->
    <aside>
        <ul>
            <li><a href="gestioncoordinadores.php">Gestion Coordinadores</a></li>
            <!-- Otras opciones específicas para administradores -->
        </ul>
        <ul>
            <li><a href="gestionfinancieros.php">Gestion Financiera</a></li>
            <!-- Otras opciones específicas para administradores -->
        </ul>
        <ul>
            <li><a href="gestionproductosA.php">Gestion De Productos</a></li>
            <!-- Otras opciones específicas para administradores -->
        </ul>
        <ul>
            <li><a href="gestionproveedores.php">Gestion De Proveedores</a></li>
            <!-- Otras opciones específicas para administradores -->
        </ul>
        <ul>
            <li><a href="gestionclientes.php">Gestion De Clientes</a></li>
            <!-- Otras opciones específicas para administradores -->
        </ul>
    </aside>


    <!-- Contenido específico para administrador -->

    <h2>Fachas de actividad</h2>

    <table border="1">
        <tr>
            <th>Nombre</th>
            <th>Correo</th>
            <th>DocumentoIdentidad</th>
            <th>Fecha Última Actividad</th>
        </tr>
        <?php
        // Iterar sobre los resultados y mostrar en la tabla
        while ($row = mysqli_fetch_assoc($resultado)) {
            echo "<tr>";
            echo "<td>{$row['NombreUsuario']}</td>";
            echo "<td>{$row['Correo']}</td>";
            echo "<td>{$row['DocumentoIdentidad']}</td>";
            echo "<td>{$row['FechaUltimaActividad']}</td>";
            echo "</tr>";
        }
        ?>
    </table>

</body>
</html>
