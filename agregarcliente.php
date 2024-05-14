<?php
include ('includes/includes.php');
include ('includes/funciones.php');
$row = obtenerDatosUsuario($conexion, $_SESSION['UsuarioID']);
if ($_SESSION['RolID'] != 1) {
    // Si no tiene permiso de administrador, redirigir a la página de inicio
    header('Location: login.php');
    exit();
}
// Verificar si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recuperar los datos del formulario
    $nombreCliente = mysqli_real_escape_string($conexion, $_POST['nombreCliente']);
    $nombreCliente = strtoupper($nombreCliente);
    $correoElectronico = filter_var($_POST['correoElectronico'], FILTER_VALIDATE_EMAIL);
    $telefono = mysqli_real_escape_string($conexion, $_POST['telefono']);
    $nit = mysqli_real_escape_string($conexion, $_POST['nit']);
    $telefonoEncargado = mysqli_real_escape_string($conexion, $_POST['telefonoEncargado']);
    $coordinadorID = ($_POST['coordinadorID'] !== 'null') ? mysqli_real_escape_string($conexion, $_POST['coordinadorID']) : 'NULL';
    $nombreEncargado = mysqli_real_escape_string($conexion, $_POST['nombreEncargado']);

    // Consulta para verificar si ya existe un cliente con los mismos datos
    $sql = "SELECT * FROM Clientes WHERE NombreCliente = '$nombreCliente' OR CorreoElectronico = '$correoElectronico' OR NIT = '$nit' OR Telefono = '$telefono' OR TelefonoEncargado = '$telefonoEncargado'";
    $result = mysqli_query($conexion, $sql);

    // Verificar si hay errores
    if (mysqli_num_rows($result) > 0) {
        // Mostrar mensaje de error
        $error = "Ya existe otro cliente con el mismo ";

        $camposRepetidos = [];
        while ($err = mysqli_fetch_assoc($result)) {
            if ($err['NombreCliente'] === $nombreCliente) {
                $camposRepetidos[] = "nombre";
            }
            if ($err['CorreoElectronico'] === $correoElectronico) {
                $camposRepetidos[] = "correo electrónico";
            }
            if ($err['NIT'] === $nit) {
                $camposRepetidos[] = "NIT";
            }
            if ($err['Telefono'] === $telefono) {
                $camposRepetidos[] = "teléfono";
            }
            if ($err['TelefonoEncargado'] === $telefonoEncargado) {
                $camposRepetidos[] = "teléfono del encargado";
            }
        }

        $camposRepetidos = array_unique($camposRepetidos);
        $error .= implode(", ", $camposRepetidos) . ".";
    } else {
        // Llamar a la función agregarCliente
        $agregarCliente = agregarCliente($conexion, $nombreCliente, $correoElectronico, $telefono, $nit, $telefonoEncargado, $coordinadorID, $nombreEncargado);
        // Verificar si se agregó el cliente correctamente
        if ($agregarCliente === true) {
            echo "<script>alert('Cliente agregado exitosamente.');window.location.href='gestionclientes.php';</script>";
            exit();
        } else {
            // Mostrar mensaje de error si faltan campos en el formulario
            $error = 'Por favor complete todos los campos obligatorios.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Cliente</title>

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
                                    <strong style="color: #fff">Agregar Cliente</strong>
                                </h3>
                                <?php if (isset($error)): ?>
                                    <div class="alert alert-danger" role="alert">
                                        <?php echo $error; ?>
                                    </div>
                                <?php endif; ?>
                                <div class="mb-4">
                                    <div class="d-flex flex-nowrap">
                                        <div class="order-0 col-md-1 d-flex align-items-center">
                                            <i class="fas fa-truck prefix white-text" style="color: #fff"></i>
                                        </div>
                                        <div data-mdb-input-init class="order-1 form-outline form-white">
                                            <input type="text" id="nombreCliente" class="form-control form-control-lg"
                                                name="nombreCliente" required
                                                value="<?php echo isset($_POST['nombreCliente']) ? $_POST['nombreCliente'] : ''; ?>" />
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
                                                class="form-control form-control-lg" name="correoElectronico" required
                                                value="<?php echo isset($_POST['correoElectronico']) ? $_POST['correoElectronico'] : ''; ?>" />
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
                                                name="telefono" required
                                                value="<?php echo isset($_POST['telefono']) ? $_POST['telefono'] : ''; ?>" />
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
                                                <?php
                                                $queryCoordinadores = "SELECT UsuarioID, NombreUsuario FROM Usuarios WHERE RolID = 2";
                                                $resultCoordinadores = mysqli_query($conexion, $queryCoordinadores);
                                                while ($rowCoordinador = mysqli_fetch_assoc($resultCoordinadores)) {
                                                    echo "<option value='{$rowCoordinador['UsuarioID']}'>{$rowCoordinador['NombreUsuario']}</option>";
                                                }
                                                ?>
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
                                                name="nombreEncargado" required
                                                value="<?php echo isset($_POST['nombreEncargado']) ? $_POST['nombreEncargado'] : ''; ?>" />
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
                                                class="form-control form-control-lg" name="telefonoEncargado" required
                                                value="<?php echo isset($_POST['telefonoEncargado']) ? $_POST['telefonoEncargado'] : ''; ?>" />
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
                                                value="<?php echo isset($_POST['nit']) ? $_POST['nit'] : ''; ?>" />
                                            <label class="form-label" for="nit">NIT</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex flex-row-reverse justify-content-center">
                                    <div class="order-0 p-2">
                                        <button data-mdb-ripple-init type="submit" class="btn btn-light">
                                            <i class="fas fa-user-plus pe-2"></i>Agregar Cliente</button>
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