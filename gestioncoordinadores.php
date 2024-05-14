<?php
include ('includes/includes.php');
include ('includes/funciones.php');
$row = obtenerDatosUsuario($conexion, $_SESSION['UsuarioID']);
// Verificar si el usuario tiene el rol de administrador
if ($_SESSION['RolID'] != 1) {
    // Si no es administrador, redirigir a la página de inicio
    header('Location: login.php');
    exit();
}

// Verificar si se ha enviado el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener la acción y el ID del usuario
    $accion = $_POST['accion'];
    $usuarioID = $_POST['usuario_id'];

    // Realizar la acción según el botón presionado
    switch ($accion) {
        case 'Inactivar':
            inactivarUsuario($conexion, $usuarioID);
            break;
        case 'Activar':
            activarUsuario($conexion, $usuarioID);
            break;
        // Otros casos según sea necesario
    }
}

// Obtener información de usuarios coordinadores
$query = 'SELECT U.UsuarioID, U.Nombre, U.NombreUsuario, U.Correo, U.DocumentoIdentidad, U.FechaUltimaActividad, U.Activo, COUNT(C.ClienteID) AS CantidadEmpresas
          FROM Usuarios U
          LEFT JOIN Clientes C ON U.UsuarioID = C.CoordinadorID
          WHERE U.RolID = 2
          GROUP BY U.UsuarioID';
$resultado = mysqli_query($conexion, $query);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Coordinadores</title>

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

        #tdcenter {
            display: flex;
            justify-content: center;
        }
    </style>

    <div class="mx-4 my-4">
        <a class="btn text-white btn-lg btn-floating" data-mdb-ripple-init style="background-color: #ac2bac;"
            role="button" href="indexadmin.php">
            <i class="fas fa-angle-left"></i>
        </a>

        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <h1>Gestionar Coordinadores</h1>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-md-12">
                    <ul class="nav nav-pills flex-column flex-md-row">
                        <li class="nav-item">
                            <a href="agregar_coordinador.php" style="color: #F9F6EE">
                                <button data-mdb-ripple-init type="button" class="btn btn-primary btn-rounded">
                                    <i class="fas fa-user pe-2"></i>Agregar coordinador</button>
                            </a>
                        </li>
                    </ul>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <table class="table table-striped table-responsive rounded-9 overflow-hidden table-hover"
                                id="sortTable">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Nombre Usuario</th>
                                        <th>Correo</th>
                                        <th>Documento Identidad</th>
                                        <th>Cantidad Empresas</th>
                                        <th id='tdcenter'>Estado</th>
                                    </tr>
                                </thead>
                                <?php
                                // Iterar sobre los resultados y mostrar en la tabla
                                while ($row = mysqli_fetch_assoc($resultado)) {
                                    echo "<tr>";
                                    echo "<td>{$row['Nombre']}</td>";
                                    echo "<td>{$row['NombreUsuario']}</td>";
                                    echo "<td>{$row['Correo']}</td>";
                                    echo "<td>{$row['DocumentoIdentidad']}</td>";
                                    echo "<td>{$row['CantidadEmpresas']}</td>";
                                    echo "<td id='tdcenter'>";
                                    // Mostrar el estado como texto y el botón de acción
                                    $estadoText = $row['Activo'] ? 'Inactivar' : 'Activar';
                                    echo "<span class='estado-text' style='display: none'>{$estadoText}</span>";

                                    // Agregar un botón con el estado actual y el ID del usuario
                                    echo "<form method='post' action=''>
                                    <input type='hidden' name='usuario_id' value='{$row['UsuarioID']}'>";
                                    if ($row['Activo']) {
                                        // Si el usuario está activo, muestra un botón para inactivar
                                        echo "<input type='submit' name='accion' class='btn btn-light' value='Inactivar'>";
                                    } else {
                                        // Si el usuario está inactivo, muestra un botón para activar
                                        echo "<input type='submit' name='accion' class='btn btn-dark' value='Activar'>";
                                    }
                                    echo "</form>";
                                    echo "</td>";
                                    echo "</tr>";
                                }
                                ?>
                            </table>
                        </div>
                    </div>
                </div>
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
<script>$('#sortTable').DataTable({ order: [[0, 'asc']] })</script>
<script>
    const loginButton = document.getElementById('loginButton');

    loginButton.addEventListener('click', () => {
        // Enviar el formulario aquí
        const form = document.querySelector('form'); // Selecciona el formulario
        form.submit(); // Envía el formulario
    });
</script>
<script>
    // Initialization for ES Users
    import { Ripple, initMDB } from "mdb-ui-kit";

    initMDB({ Ripple });
</script>