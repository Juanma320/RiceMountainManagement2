<?php
include ('includes/includes.php');
include ('includes/funciones.php');
$row = obtenerDatosUsuario($conexion, $_SESSION['NombreUsuario']);
// Verificar si el usuario tiene el rol de coordinador
if ($_SESSION['RolID'] != 2) {
    header("Location: login.php");
    exit();
}

$queryClientes = "SELECT C.*, U.NombreUsuario as NombreCoordinador
                  FROM Clientes C
                  LEFT JOIN Usuarios U ON C.CoordinadorID = U.UsuarioID
                  WHERE C.CoordinadorID = {$_SESSION['UsuarioID']}";
$resultClientes = mysqli_query($conexion, $queryClientes);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Clientes</title>

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
            role="button" href="indexcoordinador.php">
            <i class="fas fa-angle-left"></i>
        </a>

        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <h1>Clientes Asignados a <?php echo $row['NombreUsuario']; ?></h1>
                    </div>
                </div>
            </div>


            <div class="row mt-4">
                <div class="col-md-12">
                    <table class="table table-striped table-responsive rounded-9 overflow-hidden table-hover"
                        id="sortTable">
                        <thead class="table-dark">
                            <tr>
                            <tr>
                                <th>Nombre Cliente</th>
                                <th>Correo Electrónico</th>
                                <th>Teléfono</th>
                                <th>Coordinador</th>
                                <th>Nombre Encargado</th>
                                <th class="text-center">Agregar Dirección</th>
                            </tr>
                        </thead>
                        <?php
                        while ($rowCliente = mysqli_fetch_assoc($resultClientes)) {
                            echo "<tr>";
                            echo "<td><a data-mdb-ripple-init data-mdb-ripple-color='dark' class='btn btn-outline-dark' href='infocliente.php?clienteID={$rowCliente['ClienteID']}'>{$rowCliente['NombreCliente']}</a></td>";
                            echo "<td>{$rowCliente['CorreoElectronico']}</td>";
                            echo "<td>{$rowCliente['Telefono']}</td>";
                            echo "<td>{$rowCliente['NombreCoordinador']}</td>";
                            echo "<td>{$rowCliente['NombreEncargado']}</td>";
                            echo "<td class='text-center'><a><a data-mdb-ripple-init class='btn btn-primary' href='direccionescliente.php?clienteID={$rowCliente['ClienteID']}'>Agregar</a></td>";
                            echo "</tr>";
                        }
                        ?>
                    </table>
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
    // Initialization for ES Users
    import { Ripple, initMDB } from "mdb-ui-kit";

    initMDB({ Ripple });
</script>