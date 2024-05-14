<?php
include ('includes/includes.php');
include ('includes/funciones.php');
$row = obtenerDatosUsuario($conexion, $_SESSION['NombreUsuario']);
// Verificar si el usuario tiene el rol de administrador
if ($_SESSION['RolID'] != 1 && $_SESSION['RolID'] != 2) {
    // Si no es administrador o coordinador, redirigir a la página de inicio
    header('Location: login.php');
    exit();
}

// Verificar si se recibió un ID de cliente
if (isset($_GET['clienteID'])) {
    $clienteID = mysqli_real_escape_string($conexion, $_GET['clienteID']);

    // Obtener información del cliente
    $queryCliente = "SELECT * FROM Clientes WHERE ClienteID = $clienteID";
    $resultCliente = mysqli_query($conexion, $queryCliente);

    // Verificar si se encontró el cliente
    if ($resultCliente && mysqli_num_rows($resultCliente) > 0) {
        $rowCliente = mysqli_fetch_assoc($resultCliente);

        // Obtener el nombre del coordinador del cliente
        $coordinadorID = $rowCliente['CoordinadorID'];
        $queryCoordinador = "SELECT NombreUsuario FROM Usuarios WHERE UsuarioID = $coordinadorID";
        $resultCoordinador = mysqli_query($conexion, $queryCoordinador);
        $rowCoordinador = mysqli_fetch_assoc($resultCoordinador);

        $clienteInfo = [
            'nombre' => $rowCliente['NombreCliente'],
            'correo' => $rowCliente['CorreoElectronico'],
            'telefono' => $rowCliente['Telefono'],
            'nit' => $rowCliente['NIT'],
            'telefonoEncargado' => $rowCliente['TelefonoEncargado'],
            'coordinador' => $rowCoordinador['NombreUsuario']
        ];

        // Mostrar direcciones del cliente
        $queryDirecciones = "SELECT * FROM Direcciones_Clientes WHERE ClienteID = $clienteID";
        $resultDirecciones = mysqli_query($conexion, $queryDirecciones);
        $direcciones = [];
        if ($resultDirecciones && mysqli_num_rows($resultDirecciones) > 0) {
            while ($rowDireccion = mysqli_fetch_assoc($resultDirecciones)) {
                $direcciones[] = $rowDireccion['Direccion'];
            }
        }

        // Mostrar ventas realizadas para el cliente
        $queryVentas = "SELECT v.*, d.Direccion, u.NombreUsuario, ev.NombreEstado
                        FROM Ventas v 
                        JOIN Direcciones_Clientes d ON v.DireccionID = d.DireccionID
                        JOIN Usuarios u ON v.UsuarioID = u.UsuarioID
                        JOIN estado_venta ev ON v.EstadoVentaID = ev.EstadoVentaID
                        WHERE v.ClienteID = $clienteID";
        $resultVentas = mysqli_query($conexion, $queryVentas);

        $ventas = [];
        if ($resultVentas && mysqli_num_rows($resultVentas) > 0) {
            while ($rowVenta = mysqli_fetch_assoc($resultVentas)) {
                $ventas[] = $rowVenta;
            }
        }
    } else {
        $error = "No se encontró información para el cliente con ID $clienteID.";
    }
} else {
    $error = "No se proporcionó un ID de cliente.";
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Información del Cliente</title>

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

        .infoprov {
            font-size: 37px;
        }
    </style>

    <div class="mx-4 my-4">
        <a class="btn text-white btn-lg btn-floating" data-mdb-ripple-init style="background-color: #ac2bac;"
            role="button" onclick="return cancelarActualizacion()">
            <i class="fas fa-angle-left"></i>
        </a>

        <div class="container">
            <div class="row">
                <div class="col-md-3">
                    <div class="container">
                        <div class="card">
                            <div class="card-body">
                                <div class="pb-4">
                                    <h1 class="infoprov">Información del proveedor</p>
                                </div>
                                <p><strong>Nombre Cliente:</strong><br>
                                    <?= $clienteInfo['nombre'] ?></p>
                                <p><strong>Correo Electrónico:</strong><br>
                                    <?= $clienteInfo['correo'] ?></p>
                                <p><strong>Teléfono:</strong><br>
                                    <?= $clienteInfo['telefono'] ?></p>
                                <p><strong>NIT:</strong><br>
                                    <?= $clienteInfo['nit'] ?></p>
                                <p><strong>Teléfono Encargado:</strong><br>
                                    <?= $clienteInfo['telefonoEncargado'] ?></p>
                                <p><strong>Coordinador:</strong><br>
                                    <?= $clienteInfo['coordinador'] ?></p><br>

                                <h2>Direcciones</h2>
                                <?php if (!empty($direcciones)): ?>
                                    <ul class="list-group list-group-flush">
                                        <?php foreach ($direcciones as $direccion): ?>
                                            <li class="list-group-item"><?= $direccion ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                    <br>

                                <?php else: ?>
                                    <p>No se encontraron direcciones para este cliente.</p>
                                <?php endif; ?>

                                <ul class="nav nav-pills flex-column flex-md-row justify-content-center">
                                    <li class="nav-item">
                                        <a href='venta.php?clienteID=<?= $clienteID ?>' style="color: #F9F6EE">
                                            <button data-mdb-ripple-init type="button"
                                                class="btn btn-primary btn-rounded">
                                                <i class="fas fa-user pe-2"></i>Agregar Venta</button>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-9 ">
                    <div class="container">
                        <div class="card p-4">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h1>Ventas realizadas</h1>
                                    </div>
                                </div>
                            </div>

                            <?php if (!empty($ventas)): ?>
                                <div class="row mt-4">
                                    <div class="col-md-12">
                                        <table
                                            class="table table-striped table-responsive rounded-9 overflow-hidden table-hover"
                                            id="sortTable">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th style="width:15%">Fecha de Entrega</th>
                                                    <th>Dirección</th>
                                                    <th>Total</th>
                                                    <th>Usuario</th>
                                                    <th class="text-center">Estado</th>
                                                    <th class="text-center">Modificar</th>
                                                    <th class="text-center" style="width:20%">Cambiar Estado</th>
                                                </tr>
                                            </thead>
                                            <?php foreach ($ventas as $rowVenta): ?>
                                                <?php if ($rowVenta['EstadoVentaID'] != 5): ?>
                                                    <tr>
                                                        <td><?= $rowVenta['FechaVenta'] ?></td>
                                                        <td><?= $rowVenta['Direccion'] ?></td>
                                                        <td><?= $rowVenta['TotalVenta'] ?></td>
                                                        <td><?= $rowVenta['NombreUsuario'] ?></td>
                                                        <td><?= $rowVenta['NombreEstado'] ?></td>
                                                        <td class="text-center">
                                                            <?php if (!in_array($rowVenta['EstadoVentaID'], [4, 3, 5])): ?>
                                                                <a data-mdb-ripple-init class='btn btn-warning'
                                                                    href='agregar_detalle_venta.php?ventaID=<?= $rowVenta['VentaID'] ?>&clienteID=<?= $clienteID ?>'>Modificar</a>
                                                            <?php else: ?>
                                                                <?= "No editable" ?>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td class="text-center">
                                                            <?php switch ($rowVenta['EstadoVentaID']):
                                                                case 1: // Creada ?>
                                                                    <select name='estado' class="form-select bg-transparent"
                                                                        data-mdb-select-init
                                                                        onchange='cambiarEstadoVenta(this.value, <?= $rowVenta['VentaID'] ?>)'>
                                                                        <option value='2'>En Proceso</option>
                                                                        <option value='4'>Cancelar</option>
                                                                    </select>
                                                                    <?php break;
                                                                case 2: // En Proceso ?>
                                                                    <select name='estado' class="form-select bg-transparent"
                                                                        data-mdb-select-init
                                                                        onchange='cambiarEstadoVenta(this.value, <?= $rowVenta['VentaID'] ?>)'>
                                                                        <option value='3'>Venta Realizada</option>
                                                                        <option value='4'>Cancelar</option>
                                                                    </select>
                                                                    <?php break;
                                                                case 6: // Retraso ?>
                                                                    <select name='estado' class="form-select bg-transparent"
                                                                        data-mdb-select-init
                                                                        onchange='cambiarEstadoVenta(this.value, <?= $rowVenta['VentaID'] ?>)'>
                                                                        <option value='3'>Venta Realizada</option>
                                                                        <option value='4'>Cancelar</option>
                                                                    </select>
                                                                    <?php break;
                                                                case 3: // Realizada ?>
                                                                    <?= "Venta Realizada" ?>
                                                                    <?php break;
                                                                case 4: // Cancelada ?>
                                                                    <?= "Venta Cancelada" ?>
                                                                    <?php break;
                                                            endswitch; ?>
                                                        </td>
                                                    </tr>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </table>
                                    <?php else: ?>
                                        <p>No se encontraron ventas para este cliente.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div><br>

</body>

</html>

<script>
    function cambiarEstadoVenta(nuevoEstado, ventaID) {
        var confirmacion = confirm("¿Estás seguro de cambiar el estado?");
        if (confirmacion) {
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {
                    location.reload();
                }
            };
            xmlhttp.open("GET", "actualizar_estado_venta.php?ventaID=" + ventaID + "&nuevoEstado=" + nuevoEstado, true);
            xmlhttp.send();
        }
    }
</script>
<script type="text/javascript" src="js/mdb.umd.min.js"></script>
<script type="text/javascript"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.22/js/dataTables.bootstrap4.min.js"></script>
<script>$('#sortTable').DataTable({ order: [[0, 'des']] })</script>
<script>
    // Initialization for ES Users
    import { Ripple, initMDB } from "mdb-ui-kit";

    initMDB({ Ripple });
</script>
<script>
    function cancelarActualizacion() {
        var url = <?php echo ($_SESSION['RolID'] == 1) ? "'gestionclientes.php'" : "'gestion_cliente_coordinador.php'"; ?>;
        window.location.href = url;
    }
</script>