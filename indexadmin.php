<?php
// Archivo: indexadmin.php

// Incluir configuraciones y funciones comunes
include ('includes/includes.php');
include ('includes/funciones.php');
$row = obtenerDatosUsuario($conexion, $_SESSION['UsuarioID']);
// Verificar si el usuario tiene el rol de administrador
if ($_SESSION['RolID'] != 1) {
    // Si no es administrador, redirigir a la página de inicio
    header('Location: login.php');
    exit();
}

// Obtener datos del usuario

// Obtener información de usuarios (excepto administradores)
$query = 'SELECT Nombre, NombreUsuario, Correo, DocumentoIdentidad, FechaUltimaActividad FROM Usuarios WHERE RolID != 1';
$resultado = mysqli_query($conexion, $query);

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administrador</title>

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
                <h1>Bienvenido, <?php echo $row['NombreUsuario']; ?> (Administrador)</h1>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-12">
                <ul class="nav nav-pills flex-column flex-md-row">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="gestioncoordinadores.php">Gestion
                            Coordinadores</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="gestionfinancieros.php">Gestion
                            Financiera</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="gestionproductosA.php">Gestion De
                            Productos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="gestionproveedores.php">Gestion De
                            Proveedores</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="gestionclientes.php">Gestion De
                            Clientes</a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-12">
                <h2>Fechas de actividad</h2>
                <table class="table table-striped table-responsive rounded-9 overflow-hidden table-hover"
                    id="sortTable">
                    <thead class="table-dark">
                        <tr>
                            <th>Nombre</th>
                            <th>Nombre de usuario</th>
                            <th>Correo</th>
                            <th>Documento Identidad</th>
                            <th>Fecha Última Actividad</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Iterar sobre los resultados y mostrar en la tabla
                        while ($row = mysqli_fetch_assoc($resultado)) {
                            echo "<tr>";
                            echo "<td>{$row['Nombre']}</td>";
                            echo "<td>{$row['NombreUsuario']}</td>";
                            echo "<td>{$row['Correo']}</td>";
                            echo "<td>{$row['DocumentoIdentidad']}</td>";
                            echo "<td>";
                            
                            if ($row['FechaUltimaActividad'] === NULL) {
                                echo "Usuario no ha iniciado sesión";
                            } else {
                                echo $row['FechaUltimaActividad'];
                            }
                        
                            echo "</td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script type="text/javascript" src="js/mdb.umd.min.js"></script>
    <script type="text/javascript"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.22/js/dataTables.bootstrap4.min.js"></script>
    <script>$('#sortTable').DataTable({ order: [[3, 'des']] })</script>

</body>

</html>