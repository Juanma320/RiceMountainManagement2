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

// Verificar si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validar y obtener datos del formulario
    $nombre = mysqli_real_escape_string($conexion, $_POST['nombre']);
    $nombre = strtoupper($nombre); // Convertir a mayúsculas
    $correo = filter_var($_POST['correo'], FILTER_VALIDATE_EMAIL);
    $telefono = mysqli_real_escape_string($conexion, $_POST['telefono']);
    $contacto = mysqli_real_escape_string($conexion, $_POST['contacto']);
    $telefonoContacto = mysqli_real_escape_string($conexion, $_POST['telefonoContacto']);
    $nit = mysqli_real_escape_string($conexion, $_POST['nit']);

    // Verificar si ya existe un proveedor con el mismo nombre, correo electrónico o NIT
    $sql = "SELECT * FROM Proveedores WHERE NombreProveedor = '$nombre' OR CorreoElectronico = '$correo' OR NIT = '$nit' OR Telefono = '$telefono' OR TelefonoContacto = '$telefonoContacto'";
    $result = mysqli_query($conexion, $sql);
    if (!$correo) {
        $error = 'El correo electrónico ingresado no es válido.';
    } else {

        if (mysqli_num_rows($result) > 0) {
            $error = "Ya existe otro proveedor con el mismo ";

            $camposRepetidos = [];
            while ($err = mysqli_fetch_assoc($result)) {
                if ($err['NombreProveedor'] === $nombre) {
                    $camposRepetidos[] = "nombre";
                }
                if ($err['CorreoElectronico'] === $correo) {
                    $camposRepetidos[] = "correo electrónico";
                }
                if ($err['NIT'] === $nit) {
                    $camposRepetidos[] = "NIT";
                }
                if ($err['Telefono'] === $telefono) {
                    $camposRepetidos[] = "teléfono";
                }
                if ($err['TelefonoContacto'] === $telefonoContacto) {
                    $camposRepetidos[] = "teléfono de contacto";
                }
            }

            $camposRepetidos = array_unique($camposRepetidos);
            $error .= implode(", ", $camposRepetidos) . ".";
        } else {
            // Llamar a la función agregarProveedor
            $agregarProveedor = agregarProveedor($conexion, $nombre, $correo, $telefono, $contacto, $telefonoContacto, $nit);

            // Verificar si se agregó el proveedor correctamente
            if ($agregarProveedor === true) {
                echo "<script>alert('Proveedor agregado exitosamente.');window.location.href='gestionproveedores.php';</script>";
                exit();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Proveedor</title>

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
    </style>

    <div class="mx-4 my-4">
        <a class="btn text-white btn-lg btn-floating" data-mdb-ripple-init style="background-color: #ac2bac;"
            role="button" onclick="window.location.href='gestionproveedores.php'">
            <i class="fas fa-angle-left"></i>
        </a>

        <div class="container">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col-12 col-md-8 col-lg-6 col-xl-5">
                    <div class="card near-moon-gradient form-white" style="border-radius: 1rem;">
                        <div class="card-body p-5">
                            <form method="POST" action="">
                                <h3 class="text-center indigo-text font-bold py-4 fw-bold text-uppercase">
                                    <strong style="color: #fff">Agregar Proveedor</strong>
                                </h3>
                                <?php if (isset($error)): ?>
                                    <div class="alert alert-danger" role="alert">
                                        <?php echo $error; ?>
                                    </div>
                                <?php endif; ?>
                                <?php if (isset($exito)): ?>
                                    <div class="alert alert-success" role="alert">
                                        <?php echo $exito; ?>
                                    </div>
                                <?php endif; ?>
                                <div class="mb-4">
                                    <div class="d-flex flex-nowrap">
                                        <div class="order-0 col-md-1 d-flex align-items-center">
                                            <i class="fas fa-truck prefix white-text" style="color: #fff"></i>
                                        </div>
                                        <div data-mdb-input-init class="order-1 form-outline form-white">
                                            <input type="text" id="nombre" class="form-control form-control-lg"
                                                name="nombre" required
                                                value="<?php echo isset($_POST['nombre']) ? htmlspecialchars($_POST['nombre']) : ''; ?>" />
                                            <label class="form-label" for="nombre">Nombre del proveedor</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <div class="d-flex flex-nowrap">
                                        <div class="order-0 col-md-1 d-flex align-items-center">
                                            <i class="far fa-envelope prefix white-text" style="color: #fff"></i>
                                        </div>
                                        <div data-mdb-input-init class="order-1 form-outline form-white">
                                            <input type="email" id="correo" class="form-control form-control-lg"
                                                name="correo" required
                                                value="<?php echo isset($_POST['correo']) ? htmlspecialchars($_POST['correo']) : ''; ?>" />
                                            <label class="form-label" for="correo">Correo electrónico</label>
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
                                                value="<?php echo isset($_POST['telefono']) ? htmlspecialchars($_POST['telefono']) : ''; ?>"
                                                oninput="this.value = this.value.replace(/[^0-9]/g, '');" />
                                            <label class="form-label" for="telefono">Teléfono</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <div class="d-flex flex-nowrap">
                                        <div class="order-0 col-md-1 d-flex align-items-center">
                                            <i class="far fa-user prefix white-text" style="color: #fff"></i>
                                        </div>
                                        <div data-mdb-input-init class="order-1 form-outline form-white">
                                            <input type="text" id="contacto" class="form-control form-control-lg"
                                                name="contacto" required
                                                value="<?php echo isset($_POST['contacto']) ? htmlspecialchars($_POST['contacto']) : ''; ?>"
                                                oninput="this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚüÜñÑ\s]/g, '');" />
                                            <label class="form-label" for="contacto">Nombre de contacto</label>
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
                                            <input type="text" id="telefonoContacto"
                                                class="form-control form-control-lg" name="telefonoContacto" required
                                                value="<?php echo isset($_POST['telefonoContacto']) ? htmlspecialchars($_POST['telefonoContacto']) : ''; ?>"
                                                oninput="this.value = this.value.replace(/[^0-9]/g, '');" />
                                            <label class="form-label" for="telefonoContacto">Teléfono de
                                                contacto</label>
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
                                                value="<?php echo isset($_POST['nit']) ? htmlspecialchars($_POST['nit']) : ''; ?>"
                                                oninput="this.value = this.value.replace(/[^0-9]/g, '');" />
                                            <label class="form-label" for="nit">NIT</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex flex-row-reverse justify-content-center">
                                    <div class="order-0 p-2">
                                        <button data-mdb-ripple-init type="submit" class="btn btn-light">
                                            <i class="fas fa-user-plus pe-2"></i>Agregar Proveedor</button>
                                    </div>
                                    <div class="order-1 p-2">
                                        <button data-mdb-ripple-init type="button" class="btn btn-danger"
                                            onclick="window.location.href='gestionproveedores.php'">
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