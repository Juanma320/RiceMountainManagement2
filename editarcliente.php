<?php
include ('includes/includes.php');
include ('includes/funciones.php');
$row = obtenerDatosUsuario($conexion, $_SESSION['NombreUsuario']);

// Verificar si se recibió un ID de cliente
if (isset($_GET['clienteID'])) {
    $clienteID = mysqli_real_escape_string($conexion, $_GET['clienteID']);

    // Obtener información del cliente
    $queryCliente = "SELECT c.ClienteID, c.NombreCliente, c.CorreoElectronico, c.Telefono, c.NIT, c.TelefonoEncargado, c.CoordinadorID, c.NombreEncargado, c.UltimaAsignacion, u.NombreUsuario AS NombreCoordinador
                     FROM Clientes c
                     LEFT JOIN Usuarios u ON c.CoordinadorID = u.UsuarioID
                     WHERE c.ClienteID = $clienteID";

    $resultCliente = mysqli_query($conexion, $queryCliente);

    // Obtener la lista de usuarios coordinadores, incluyendo la opción "Ninguno"
    $queryCoordinadores = "SELECT UsuarioID, NombreUsuario FROM Usuarios WHERE RolID = 2";
    $resultCoordinadores = mysqli_query($conexion, $queryCoordinadores);

    // Verificar si se encontró el cliente
    if ($resultCliente && mysqli_num_rows($resultCliente) > 0) {
        $rowCliente = mysqli_fetch_assoc($resultCliente);

        // Procesar el formulario de edición si se envió
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Recuperar los datos del formulario
            $nombreCliente = mysqli_real_escape_string($conexion, $_POST['nombreCliente']);
            $correoElectronico = mysqli_real_escape_string($conexion, $_POST['correoElectronico']);
            $telefono = mysqli_real_escape_string($conexion, $_POST['telefono']);
            $nit = mysqli_real_escape_string($conexion, $_POST['nit']);
            $telefonoEncargado = mysqli_real_escape_string($conexion, $_POST['telefonoEncargado']);
            $coordinadorID = ($_POST['coordinadorID'] !== 'null') ? mysqli_real_escape_string($conexion, $_POST['coordinadorID']) : 'NULL';
            $nombreEncargado = mysqli_real_escape_string($conexion, $_POST['nombreEncargado']);

            // Actualizar los datos del cliente en la base de datos
            $queryUpdate = "UPDATE Clientes SET
                            NombreCliente = '$nombreCliente',
                            CorreoElectronico = '$correoElectronico',
                            Telefono = '$telefono',
                            NIT = '$nit',
                            TelefonoEncargado = '$telefonoEncargado',
                            CoordinadorID = $coordinadorID,
                            NombreEncargado = '$nombreEncargado'
                            WHERE ClienteID = $clienteID";

            $resultadoUpdate = mysqli_query($conexion, $queryUpdate);

            if ($resultadoUpdate) {
                header('Location: gestionclientes.php');
                exit();
            } else {
                $error = "Error al actualizar el cliente.";
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
    <title>Modificar Cliente</title>

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
            role="button" onclick="window.location.href='gestionclientes.php'">
            <i class="fas fa-angle-left"></i>
        </a>

        <div class="container">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col-12 col-md-8 col-lg-6 col-xl-5">
                    <div class="card near-moon-gradient form-white" style="border-radius: 1rem;">
                        <div class="card-body p-5">
                            <form method="POST" action="">
                                <h3 class="text-center indigo-text font-bold py-4 fw-bold text-uppercase">
                                    <strong style="color: #fff">Modificar Cliente</strong>
                                </h3>
                                <div class="mb-4">
                                    <div class="d-flex flex-nowrap">
                                        <div class="order-0 col-md-1 d-flex align-items-center">
                                            <i class="fas fa-truck prefix white-text" style="color: #fff"></i>
                                        </div>
                                        <div data-mdb-input-init class="order-1 form-outline form-white">
                                            <input type="text" id="nombreCliente" class="form-control form-control-lg"
                                                name="nombreCliente" value='<?php echo $rowCliente['NombreCliente']; ?>'
                                                required />
                                            <label class="form-label" for="nombreCliente">Nombre del cliente</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <div class="d-flex flex-nowrap">
                                        <div class="order-0 col-md-1 d-flex align-items-center">
                                            <i class="far fa-envelope prefix white-text" style="color: #fff"></i>
                                        </div>
                                        <div data-mdb-input-init class="order-1 form-outline form-white">
                                            <input type="email" id="correoElectronico"
                                                class="form-control form-control-lg" name="correoElectronico"
                                                value='<?php echo $rowCliente['CorreoElectronico']; ?>' required />
                                            <label class="form-label" for="correoElectronico">Correo electrónico</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <div class="d-flex flex-nowrap">
                                        <div class="order-0 col-md-1 d-flex align-items-center">
                                            <i class="fas fa-phone prefix white-text" style="color: #fff"></i>
                                        </div>
                                        <div data-mdb-input-init class="order-1 form-outline form-white">
                                            <input type="text" id="telefono" class="form-control form-control-lg"
                                                name="telefono" value='<?php echo $rowCliente['Telefono']; ?>'
                                                required />
                                            <label class="form-label" for="telefono">Teléfono</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <div class="d-flex flex-nowrap">
                                        <div class="order-0 col-md-1 d-flex align-items-center">
                                            <i class="far fa-user prefix white-text" style="color: #fff"></i>
                                        </div>
                                        <div class="order-1 form-outline form-white form-floating">
                                            <select name="coordinadorID" class="form-select bg-transparent"
                                                data-mdb-select-init required>
                                                <option value="">Ninguno</option>
                                                <?php while ($rowCoordinador = mysqli_fetch_assoc($resultCoordinadores)): ?>
                                                    <option value='<?php echo $rowCoordinador['UsuarioID']; ?>' <?php echo ($rowCoordinador['UsuarioID'] == $rowCliente['CoordinadorID']) ? 'selected' : ''; ?>>
                                                        <?php echo $rowCoordinador['NombreUsuario']; ?>
                                                    </option>
                                                <?php endwhile; ?>
                                            </select>
                                            <label for="floatingSelect" style="color: #fff">Coordinador</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <div class="d-flex flex-nowrap">
                                        <div class="order-0 col-md-1 d-flex align-items-center">
                                            <i class="far fa-user prefix white-text" style="color: #fff"></i>
                                        </div>
                                        <div data-mdb-input-init class="order-1 form-outline form-white">
                                            <input type="text" id="nombreEncargado" class="form-control form-control-lg"
                                                name="nombreEncargado"
                                                value='<?php echo $rowCliente['NombreEncargado']; ?>' required />
                                            <label class="form-label" for="nombreEncargado">Nombre del encargado</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <div class="d-flex flex-nowrap">
                                        <div class="order-0 col-md-1 d-flex align-items-center">
                                            <i class="fas fa-mobile-screen-button prefix white-text"
                                                style="color: #fff"></i>
                                        </div>
                                        <div data-mdb-input-init class="order-1 form-outline form-white">
                                            <input type="text" id="telefonoEncargado"
                                                class="form-control form-control-lg" name="telefonoEncargado"
                                                value='<?php echo $rowCliente['TelefonoEncargado']; ?>' required />
                                            <label class="form-label" for="telefonoEncargado">Teléfono del
                                                encargado</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <div class="d-flex flex-nowrap">
                                        <div class="order-0 col-md-1 d-flex align-items-center">
                                            <i class="far fa-address-card white-text" style="color: #fff"></i>
                                        </div>
                                        <div data-mdb-input-init class="order-1 form-outline form-white">
                                            <input type="text" id="nit" class="form-control form-control-lg" name="nit"
                                                value='<?php echo $rowCliente['NIT']; ?>' required />
                                            <label class="form-label" for="nit">NIT</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex flex-row-reverse justify-content-center">
                                    <div class="order-0 p-2">
                                        <button data-mdb-ripple-init type="submit" class="btn btn-light">
                                            <i class="fas fa-user-plus pe-2"></i>Modificar Cliente</button>
                                    </div>
                                    <div class="order-1 p-2">
                                        <button data-mdb-ripple-init type="button" class="btn btn-danger"
                                            onclick="window.location.href='gestionclientes.php'">
                                            <i class="fas fa-ban pe-2"></i>Cancelar
                                        </button>
                                    </div>
                                </div>
                            </form>
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