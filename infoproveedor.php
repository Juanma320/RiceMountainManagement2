<?php
include ('includes/includes.php');
include ('includes/funciones.php');
$row = obtenerDatosUsuario($conexion, $_SESSION['UsuarioID']);
// Verificar si el usuario tiene el rol de administrador
if ($_SESSION['RolID'] != 1 && $_SESSION['RolID'] != 3) {
    // Si no es administrador o financiero, redirigir a la página de inicio
    header('Location: login.php');
    exit();
}

if (isset($_GET['proveedorID'])) {
    $proveedorID = mysqli_real_escape_string($conexion, $_GET['proveedorID']);

    $queryProveedor = "SELECT * FROM proveedores WHERE ProveedorID = ?";
    $stmtProveedor = mysqli_prepare($conexion, $queryProveedor);
    mysqli_stmt_bind_param($stmtProveedor, "i", $proveedorID);
    mysqli_stmt_execute($stmtProveedor);
    $resultProveedor = mysqli_stmt_get_result($stmtProveedor);

    if ($resultProveedor && mysqli_num_rows($resultProveedor) > 0) {
        $rowProveedor = mysqli_fetch_assoc($resultProveedor);

    } else {
        echo "<p>No se encontró información para el proveedor con ID $proveedorID.</p>";
    }
} else {
    echo "<p>No se proporcionó un ID de proveedor.</p>";
}

$queryCompras = "SELECT c.*, ec.NombreEstado FROM compras c
     INNER JOIN estado_compra ec ON c.EstadoCompraID = ec.EstadoCompraID
     WHERE c.ProveedorID = ?";
$stmtCompras = mysqli_prepare($conexion, $queryCompras);
mysqli_stmt_bind_param($stmtCompras, "i", $proveedorID);
mysqli_stmt_execute($stmtCompras);
$resultCompras = mysqli_stmt_get_result($stmtCompras);

$compras = [];
if ($resultCompras && mysqli_num_rows($resultCompras) > 0) {
    while ($rowCompra = mysqli_fetch_assoc($resultCompras)) {
        $compras[] = $rowCompra;
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Información del Proveedor</title>

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
                                <p><strong>Nombre Proveedor:</strong><br>
                                    <?php echo $rowProveedor['NombreProveedor']; ?>
                                </p>
                                <p><strong>Correo Electrónico:</strong><br>
                                    <?php echo $rowProveedor['CorreoElectronico']; ?>
                                </p>
                                <p><strong>Teléfono:</strong><br><?php echo $rowProveedor['Telefono']; ?></p>
                                <p><strong>Contacto:</strong><br><?php echo $rowProveedor['Contacto']; ?></p>
                                <p><strong>Estado:</strong><br><?php echo ($rowProveedor['Activo'] ? 'Activo' : 'Inactivo'); ?>
                                </p>
                                <br>

                                <ul class="nav nav-pills flex-column flex-md-row justify-content-center">
                                    <li class="nav-item">
                                        <a href='compra.php?proveedorID=<?php echo $proveedorID; ?>'
                                            style="color: #F9F6EE">
                                            <button data-mdb-ripple-init type="button"
                                                class="btn btn-primary btn-rounded">
                                                <i class="fas fa-user pe-2"></i>Agregar compra</button>
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
                                        <h1>Compras realizadas</h1>
                                    </div>
                                </div>
                            </div>

                            <?php if (!empty($compras)): ?>
                                <div class="row mt-4">
                                    <div class="col-md-12">
                                        <table
                                            class="table table-striped table-responsive rounded-9 overflow-hidden table-hover"
                                            id="sortTable">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th>Fecha de Compra</th>
                                                    <th>Valor Compra</th>
                                                    <th>Estado</th>
                                                    <th class="text-center">Modificar</th>
                                                    <th class="text-center">Cambiar Estado</th>
                                                    <th class="text-center">Reporte</th>
                                                </tr>
                                            </thead>
                                            <?php foreach ($compras as $rowCompra): ?>
                                                <?php if ($rowCompra['EstadoCompraID'] != 4): ?>
                                                    <tr>
                                                        <td><?= $rowCompra['FechaCompra'] ?></td>
                                                        <td><?= $rowCompra['ValorCompra'] ?></td>
                                                        <td><?= $rowCompra['NombreEstado'] ?></td>
                                                        <td class="text-center">
                                                            <?php if (!in_array($rowCompra['EstadoCompraID'], [2, 3, 4, 1])): ?>
                                                                <a data-mdb-ripple-init class='btn btn-warning'
                                                                    href='agregar_detalle_compra.php?compraID=<?= $rowCompra['CompraID'] ?>&proveedorID=<?= $rowCompra['ProveedorID'] ?>'>Modificar</a>
                                                            <?php else: ?>
                                                                No editable
                                                            <?php endif; ?>
                                                        </td>
                                                        <td class="text-center">
                                                            <?php if ($rowCompra['EstadoCompraID'] == 1): ?>
                                                                <select name='estado' class="form-select bg-transparent"
                                                                    data-mdb-select-init
                                                                    onchange='cambiarEstado(this.value, <?= $rowCompra['CompraID'] ?>)'>
                                                                    <option value='1'>Pedido Enviado</option>
                                                                    <option value='2'>Cancelar</option>
                                                                    <option value='3'>Pedido Recibido</option>
                                                                </select>
                                                            <?php elseif ($rowCompra['EstadoCompraID'] == 2): ?>
                                                                Cancelada
                                                            <?php elseif ($rowCompra['EstadoCompraID'] == 3): ?>
                                                                En Proceso
                                                            <?php elseif ($rowCompra['EstadoCompraID'] == 4): ?>
                                                                Archivado
                                                            <?php elseif ($rowCompra['EstadoCompraID'] == 5): ?>
                                                                <select name='estado' class="form-select bg-transparent"
                                                                    data-mdb-select-init
                                                                    onchange='cambiarEstado(this.value, <?= $rowCompra['CompraID'] ?>)'>
                                                                    <option value='5'>Pedido Creado</option>
                                                                    <option value='1'>Enviar pedido</option>
                                                                </select>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <a class="btn btn-danger btn-rounded"
                                                                href="generar_reporte_compra.php?compraID=<?php echo $rowCompra['CompraID']; ?>"
                                                                style="color: #F9F6EE">
                                                                PDF
                                                            </a>
                                                        </td>
                                                    </tr>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </table>
                                    <?php else: ?>
                                        <p>No se encontraron compras para este proveedor.</p>
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
    function cambiarEstado(nuevoEstado, compraID) {
        var confirmacion = confirm("¿Estás seguro de cambiar el estado?");
        if (confirmacion) {
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {
                    location.reload();
                }
            };
            xmlhttp.open("GET", "actualizar_compra.php?compraID=" + compraID + "&nuevoEstado=" + nuevoEstado, true);
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
        var url = <?php echo ($_SESSION['RolID'] == 3) ? "'gestion_proveedores_financiero.php'" : "'gestionproveedores.php'"; ?>;
        window.location.href = url;
    }
</script>