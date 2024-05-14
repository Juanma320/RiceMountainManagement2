<?php
include ('includes/includes.php');
include ('includes/funciones.php');
$row = obtenerDatosUsuario($conexion, $_SESSION['UsuarioID']);

// Verificar si el usuario tiene el rol de financiero
if ($_SESSION['RolID'] != 3) {
    // Si no es financiero, redirigir a la página de inicio
    header('Location: login.php');
    exit();
}

// Consulta SQL para obtener los datos de los proveedores
$query = "SELECT ProveedorID, NombreProveedor, CorreoElectronico, Telefono, Contacto FROM proveedores";
$resultado = mysqli_query($conexion, $query);

// Recorrer los resultados y almacenarlos en un array
$proveedores = [];
while ($row2 = mysqli_fetch_assoc($resultado)) {
    $proveedores[] = $row2;
}
?>

<!DOCTYPE html>
<html lang='es'>

<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Lista de Proveedores</title>
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
                        <h1>Gestionar Proveedores</h1>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-md-12">
                    <table class="table table-striped table-responsive rounded-9 overflow-hidden table-hover"
                        id="sortTable">
                        <thead class="table-dark">
                            <tr>
                                <th>NombreProveedor</th>
                                <th>CorreoElectronico</th>
                                <th>Telefono</th>
                                <th>Contacto</th>
                            </tr>
                        </thead>
                        <?php foreach ($proveedores as $proveedor): ?>
                            <tr>
                                <td><a data-mdb-ripple-init data-mdb-ripple-color="dark" class="btn btn-outline-dark"
                                        href='infoproveedor.php?proveedorID=<?= $proveedor['ProveedorID'] ?>'>
                                        <?= $proveedor['NombreProveedor'] ?></a></td>
                                <td><?= $proveedor['CorreoElectronico'] ?></td>
                                <td><?= $proveedor['Telefono'] ?></td>
                                <td><?= $proveedor['Contacto'] ?></td>
                            </tr>
                        <?php endforeach; ?>
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