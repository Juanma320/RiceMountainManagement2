<?php
// Incluir el archivo de conexión a la base de datos
include ('includes/includes.php');
include ('includes/funciones.php');
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
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar Precio</title>

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

        .near-moon-gradient {
            background: radial-gradient(circle at 10% 20%, rgb(64, 84, 178) 0%, rgb(219, 2, 234) 90%);
        }

        /* Estilo para cambiar el color del icono del selector de fecha a blanco */
        input[type="date"]::-webkit-calendar-picker-indicator {
            filter: invert(1);
        }
    </style>

    <div class="mx-4 my-4">
        <a class="btn text-white btn-lg btn-floating" data-mdb-ripple-init style="background-color: #ac2bac;"
            role="button" onclick="window.location.href='infoproveedor.php?proveedorID=<?php echo $proveedorID; ?>'">
            <i class="fas fa-angle-left"></i>
        </a>

        <div class="container">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col-12 col-md-8 col-lg-6 col-xl-5">
                    <div class="card near-moon-gradient form-white" style="border-radius: 1rem;">
                        <div class="card-body p-5">
                            <?php if (isset($rowProveedor)): ?>
                                <form method="POST" action="procesar_compra.php">
                                    <h3 class="text-center indigo-text font-bold py-4 fw-bold text-uppercase">
                                        <strong style="color: #fff">Agregar Compra</strong>
                                    </h3>

                                    <input type='hidden' name='proveedorID' value='<?= $proveedorID ?>'>
                                    <input type='hidden' name='usuarioID' value='<?= $row['UsuarioID'] ?>'>

                                    <div class="mb-4">
                                        <div class="d-flex flex-nowrap">
                                            <div class="order-0 col-md-1 d-flex align-items-center">
                                                <i class="fas fa-dollar-sign prefix white-text" style="color: #fff"></i>
                                            </div>
                                            <div data-mdb-input-init class="order-1 form-outline form-white">
                                                <input type="text" id="proveedor" name="proveedor"
                                                    class="form-control form-control-lg"
                                                    value='<?= $rowProveedor['NombreProveedor'] ?>' readonly required />
                                                <label class="form-label" for="proveedor">Proveedor</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-4">
                                        <div class="d-flex flex-nowrap">
                                            <div class="order-0 col-md-1 d-flex align-items-center">
                                                <i class="far fa-calendar prefix white-text" style="color: #fff"></i>
                                            </div>
                                            <div data-mdb-input-init class="order-1 form-outline form-white">
                                                <input type="date" id="fecha" class="form-control form-control-lg"
                                                    name="fecha" required />
                                                <label class="form-label" for="fecha">Fecha de compra</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex flex-row-reverse justify-content-center">
                                        <div class="order-0 p-2">
                                            <button data-mdb-ripple-init type="submit" class="btn btn-light"
                                                onclick="return confirmacionActualizacion()">
                                                <i class="far fa-calendar-plus pe-2"></i>Agregar Compra</button>
                                        </div>
                                        <div class="order-1 p-2">
                                            <button data-mdb-ripple-init type="button" class="btn btn-danger"
                                                onclick="window.location.href='infoproveedor.php?proveedorID=<?php echo $proveedorID; ?>'">
                                                <i class="fas fa-ban pe-2"></i>Cancelar
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            <?php else: ?>
                                <p>No se encontró información para el proveedor con ID <?= $proveedorID ?>.</p>
                            <?php endif; ?>
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
<script>
    import { Ripple, initMDB } from "mdb-ui-kit";

    initMDB({ Ripple });
</script>