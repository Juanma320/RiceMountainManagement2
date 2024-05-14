<?php
// Incluir el archivo de conexión a la base de datos
include ('includes/includes.php');
include ('includes/funciones.php');
$row = obtenerDatosUsuario($conexion, $_SESSION['UsuarioID']);

$clienteID = null;
$rowCliente = null;
$resultDirecciones = null;

// Verificar si se recibió un ID de cliente
if (isset($_GET['clienteID'])) {
    $clienteID = mysqli_real_escape_string($conexion, $_GET['clienteID']);

    // Obtener información del cliente
    $queryCliente = "SELECT * FROM Clientes WHERE ClienteID = $clienteID";
    $resultCliente = mysqli_query($conexion, $queryCliente);

    // Verificar si se encontró el cliente
    if ($resultCliente && mysqli_num_rows($resultCliente) > 0) {
        $rowCliente = mysqli_fetch_assoc($resultCliente);

        // Selección de dirección de envío
        $queryDirecciones = "SELECT * FROM Direcciones_Clientes WHERE ClienteID = $clienteID";
        $resultDirecciones = mysqli_query($conexion, $queryDirecciones);
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Venta</title>

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

        select,
        input[type="text"],
        input[type="number"] {
            -webkit-text-fill-color: white;
        }

        select {
            background-image: url('data:image/svg+xml;utf8,<svg fill="white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"><path d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z"/></svg>') !important;
        }

        .form-floating>label::after {
            background-color: transparent !important;
        }
    </style>

    <div class="mx-4 my-4">
        <a class="btn text-white btn-lg btn-floating" data-mdb-ripple-init style="background-color: #ac2bac;"
            role="button" onclick="window.location.href='infocliente.php?clienteID=<?php echo $clienteID; ?>'">
            <i class="fas fa-angle-left"></i>
        </a>

        <div class="container">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col-12 col-md-8 col-lg-6 col-xl-5">
                    <div class="card near-moon-gradient form-white" style="border-radius: 1rem;">
                        <div class="card-body p-5">
                            <?php if (isset($rowCliente)): ?>
                                <form action='procesar_venta.php' method='post'>
                                    <h3 class="text-center indigo-text font-bold py-4 fw-bold text-uppercase">
                                        <strong style="color: #fff">Agregar Venta</strong>
                                    </h3>

                                    <input type='hidden' name='clienteID' value='<?= $clienteID ?>'>
                                    <input type='hidden' name='usuarioID' value='<?= $row['UsuarioID'] ?>'>

                                    <div class="mb-4">
                                        <div class="d-flex flex-nowrap">
                                            <div class="order-0 col-md-1 d-flex align-items-center">
                                                <i class="fas fa-dollar-sign prefix white-text" style="color: #fff"></i>
                                            </div>
                                            <div data-mdb-input-init class="order-1 form-outline form-white">
                                                <input type="text" id="cliente" name="cliente"
                                                    class="form-control form-control-lg"
                                                    value='<?= $rowCliente['NombreCliente'] ?>' readonly required />
                                                <label class="form-label" for="cliente">Cliente</label>
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
                                                <label class="form-label" for="fecha">Fecha de venta</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-4">
                                        <div class="d-flex flex-nowrap">
                                            <div class="order-0 col-md-1 d-flex align-items-center">
                                                <i class="far fa-user prefix white-text" style="color: #fff"></i>
                                            </div>
                                            <div class="order-1 form-outline form-white form-floating">
                                                <select name='direccion' id='direccion' class="form-select bg-transparent"
                                                    data-mdb-select-init required>
                                                    <?php while ($rowDireccion = mysqli_fetch_assoc($resultDirecciones)): ?>
                                                        <option value='<?= $rowDireccion['DireccionID'] ?>'>
                                                            <?= $rowDireccion['Direccion'] ?>
                                                        </option>
                                                    <?php endwhile; ?>
                                                </select>
                                                <label for="floatingSelect" style="color: #fff">Dirección de envío</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex flex-row-reverse justify-content-center">
                                        <div class="order-0 p-2">
                                            <button data-mdb-ripple-init type="submit" class="btn btn-light">
                                                <i class="far fa-calendar-plus pe-2"></i>Agregar Venta</button>
                                        </div>
                                        <div class="order-1 p-2">
                                            <button data-mdb-ripple-init type="button" class="btn btn-danger"
                                                onclick="window.location.href='infocliente.php?clienteID=<?php echo $clienteID; ?>'">
                                                <i class="fas fa-ban pe-2"></i>Cancelar
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            <?php else: ?>
                                <p>No se encontró información para el cliente con ID <?= $clienteID ?>.</p>
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
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const fechaInput = document.getElementById('fecha');
        const form = document.querySelector('form');

        // Función para validar la fecha
        function validarFecha() {
            const fechaActual = new Date();
            const fechaVenta = new Date(fechaInput.value);

            if (fechaVenta < fechaActual) {
                alert('Fecha de venta no permitida.');
                return false;
            }

            return true;
        }

        // Agregar evento de validación al enviar el formulario
        form.addEventListener('submit', function (event) {
            if (!validarFecha()) {
                event.preventDefault(); // Evitar el envío del formulario si la fecha es inválida
            }
        });
    });
</script>