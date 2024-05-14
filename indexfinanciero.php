<?php
// Archivo: indexfinanciero.php

// Incluir configuraciones y funciones comunes
include ('includes/includes.php');
include ('includes/funciones.php');
// Verificar si el usuario tiene el rol de financiero
if ($_SESSION['RolID'] != 3) {
    // Si no es financiero, redirigir a la página de inicio
    header('Location: login.php');
    exit();
}

// Obtener datos del usuario
$row = obtenerDatosUsuario($conexion, $_SESSION['UsuarioID']);

// Consulta para obtener las compras con los estados 1 y 5 (Pedido Enviado y Pedido Creado)
$queryCompras = "SELECT c.*, CONCAT(u.NombreUsuario) as NombreUsuario, p.NombreProveedor, r.NombreRol
                 FROM compras c
                 JOIN usuarios u ON c.UsuarioID = u.UsuarioID
                 JOIN proveedores p ON c.ProveedorID = p.ProveedorID
                 JOIN roles r ON u.RolID = r.RolID
                 WHERE c.EstadoCompraID IN (1, 5)"; // EstadoCompraID 1 y 5 corresponden a "Pedido Enviado" y "Pedido Creado" respectivamente
$resultCompras = mysqli_query($conexion, $queryCompras);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Financiero</title>

    <link rel="icon" href="img/mdb-favicon.ico" type="image/x-icon" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" />
    <link rel="stylesheet" href="css/mdb.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.22/css/dataTables.bootstrap4.min.css">
</head>

<body class="gradient-custom-1">

    <?php include ('includes/navbar.php'); ?>

    <style>
        .gradient-custom-1 {
            height: 100vh;

            /* fallback for old browsers */
            background: #EEEEEE;
        }

        .mask-custom {
            background: rgba(24, 24, 16, .2);
            border-radius: 2em;
            backdrop-filter: blur(25px);
            border: 2px solid rgba(255, 255, 255, 0.05);
            background-clip: padding-box;
            box-shadow: 10px 10px 10px rgba(46, 54, 68, 0.03);
        }
    </style>

    <div class="container mt-5">
        <div class="row">
            <div class="col-md-12">
                <h1>Bienvenido, <?php echo $row['NombreUsuario']; ?> (Financiero)</h1>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-12">
                <ul class="nav nav-pills flex-column flex-md-row">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="gestion_productos_financiero.php">Gestión
                            De Productos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="gestion_proveedores_financiero.php">Gestión
                            De Proveedores</a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-12">
                <h2>Compras pendientes</h2>
                <table class="table table-striped table-responsive rounded-9 overflow-hidden table-hover"
                    id="sortTable">
                    <thead class="table-dark">
                        <tr>
                            <th>Proveedor</th>
                            <th>Realizado por</th>
                            <th>Valor de la compra</th>
                            <th>Fecha de la compra</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Mostrar cada compra pendiente en la tabla
                        while ($compra = mysqli_fetch_assoc($resultCompras)) {
                            echo "<tr>";
                            echo "<td>{$compra['NombreProveedor']}</td>";
                            echo "<td>{$compra['NombreUsuario']}</td>";
                            echo "<td>{$compra['ValorCompra']}</td>";
                            echo "<td>{$compra['FechaCompra']}</td>";
                            echo "<td>Pendiente</td>"; // Estado fijo como "Pendiente"
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</body>

</html>

<script type="text/javascript" src="js/mdb.umd.min.js"></script>
<script type="text/javascript"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.22/js/dataTables.bootstrap4.min.js"></script>
<script>$('#sortTable').DataTable({ order: [[3, 'des']] })</script>