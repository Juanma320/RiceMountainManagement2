<?php
include ('includes/includes.php');
include ('includes/funciones.php');
$row = obtenerDatosUsuario($conexion, $_SESSION['UsuarioID']);
if ($_SESSION['RolID'] != 1) {
    // Si no tiene permiso de administrador, redirigir a la página de inicio
    header('Location: login.php');
    exit();
}
// Variables para mantener los valores introducidos
$nombreUsuarioValue = isset($_POST['nombre_usuario']) ? $_POST['nombre_usuario'] : '';
$documentoIdentidadValue = isset($_POST['documento_identidad']) ? $_POST['documento_identidad'] : '';
$correoValue = isset($_POST['correo']) ? $_POST['correo'] : '';



if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener datos del formulario
    $nombreUsuario = mysqli_real_escape_string($conexion, $_POST['nombre_usuario']);
    $contra = mysqli_real_escape_string($conexion, $_POST['contra']);
    $documentoIdentidad = mysqli_real_escape_string($conexion, $_POST['documento_identidad']);
    $correo = filter_var($_POST['correo'], FILTER_VALIDATE_EMAIL);
    $nombre = isset($_POST['nombre']) ? $_POST['nombre'] : '';
    $apellido = isset($_POST['apellido']) ? $_POST['apellido'] : '';


    // Combinar nombre y apellido
    $nombreCompleto = strtoupper($nombre . ' ' . $apellido);
    if (!$correo) {
        $mensajeError = 'El correo electrónico ingresado no es válido.';
    } else {
        // Verificar duplicados antes de la inserción
        $query_verificar_duplicados = "SELECT NombreUsuario, Correo, DocumentoIdentidad FROM Usuarios WHERE NombreUsuario = '$nombreUsuario' OR Correo = '$correo' OR DocumentoIdentidad = '$documentoIdentidad'";
        $resultado_verificar_duplicados = mysqli_query($conexion, $query_verificar_duplicados);

        if (mysqli_num_rows($resultado_verificar_duplicados) > 0) {
            $mensajeError = 'No se puede agregar el coordinador, alguno de los datos proporcionados ya están en uso.';

            $camposRepetidos = [];
            while ($row_verificar_duplicados = mysqli_fetch_assoc($resultado_verificar_duplicados)) {
                if ($row_verificar_duplicados['NombreUsuario'] === $nombreUsuario) {
                    $camposRepetidos[] = "nombre de usuario";
                }
                if ($row_verificar_duplicados['Correo'] === $correo) {
                    $camposRepetidos[] = "correo electrónico";
                }
                if ($row_verificar_duplicados['DocumentoIdentidad'] === $documentoIdentidad) {
                    $camposRepetidos[] = "documento de identidad";
                }
            }

            $camposRepetidos = array_unique($camposRepetidos);
            $mensajeError .= ' Los campos repetidos son: ' . implode(", ", $camposRepetidos) . '.';
        } else {
            try {
                // Insertar nuevo usuario como coordinador
                $hashedContra = password_hash($contra, PASSWORD_DEFAULT);
                $query = "INSERT INTO Usuarios (NombreUsuario, Contra, DocumentoIdentidad, RolID, Correo, Activo, Nombre)
                      VALUES ('$nombreUsuario', '$hashedContra', '$documentoIdentidad', 2, '$correo', 1, '$nombreCompleto')";
                $resultado = mysqli_query($conexion, $query);

                if ($resultado) {
                    echo "<script>alert('Coordinador agregado exitosamente.'); window.location.href='gestioncoordinadores.php';</script>";
                } else {
                    throw new Exception('Error al agregar coordinador.');
                }
            } catch (Exception $e) {
                $mensajeError = $e->getMessage();
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
    <title>Agregar Coordinador</title>

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
            role="button" onclick="window.location.href='gestioncoordinadores.php'">
            <i class="fas fa-angle-left"></i>
        </a>

        <div class="container">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col-12 col-md-8 col-lg-6 col-xl-5">
                    <div class="card near-moon-gradient form-white" style="border-radius: 1rem;">
                        <div class="card-body p-5">
                            <form method="POST" action="">
                                <h3 class="text-center indigo-text font-bold py-4 fw-bold text-uppercase">
                                    <strong style="color: #fff">Agregar Coordinador</strong>
                                </h3>
                                <?php if (isset($mensajeError)): ?>
                                    <div class="alert alert-danger" role="alert">
                                        <?php echo $mensajeError; ?>
                                    </div>
                                <?php endif; ?>
                                <div class="mb-4">
                                    <div class="d-flex flex-nowrap">
                                        <div class="order-0 col-md-1 d-flex align-items-center">
                                            <i class="far fa-user prefix white-text" style="color: #fff"></i>
                                        </div>
                                        <div data-mdb-input-init class="order-1 form-outline form-white">
                                            <input type="text" id="nombre_usuario" class="form-control form-control-lg"
                                                name="nombre_usuario" required
                                                oninput="this.value = this.value.replace(/[^a-zA-Z0-9_.ñÑ-]/g, '');"
                                                value="<?php echo htmlspecialchars($nombreUsuarioValue); ?>" />
                                            <label class="form-label" for="nombre_usuario">Nombre de usuario</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <div class="mb-4">
                                        <div class="d-flex flex-nowrap">
                                            <div class="order-0 col-md-1 d-flex align-items-center">
                                                <i class="far fa-user white-text" style="color: #fff"></i>
                                            </div>
                                            <div data-mdb-input-init class="order-1 form-outline form-white">
                                                <input type="text" id="nombre" class="form-control form-control-lg"
                                                    name="nombre" required
                                                    oninput="this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚüÜñÑ\s]/g, '');"
                                                    value="<?php echo isset($_POST['nombre']) ? htmlspecialchars($_POST['nombre']) : ''; ?>" />
                                                <label class="form-label" for="nombre">Nombres</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-4">
                                        <div class="d-flex flex-nowrap">
                                            <div class="order-0 col-md-1 d-flex align-items-center">
                                                <i class="far fa-user white-text" style="color: #fff"></i>
                                            </div>
                                            <div data-mdb-input-init class="order-1 form-outline form-white">
                                                <input type="text" id="apellido" class="form-control form-control-lg"
                                                    name="apellido" required
                                                    oninput="this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚüÜñÑ\s]/g, '');"
                                                    value="<?php echo isset($_POST['apellido']) ? htmlspecialchars($_POST['apellido']) : ''; ?>" />
                                                <label class="form-label" for="apellido">Apellidos</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-4">
                                        <div class="d-flex flex-nowrap">
                                            <div class="order-0 col-md-1 d-flex align-items-center">
                                                <i class="fa-regular fa-eye-slash white-text" style="color: #fff"></i>
                                            </div>
                                            <div data-mdb-input-init class="order-1 form-outline form-white">
                                                <input type="password" id="contra" class="form-control form-control-lg"
                                                    name="contra" required />
                                                <label class="form-label" for="contra">Contraseña</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-4">
                                        <div class="d-flex flex-nowrap">
                                            <div class="order-0 col-md-1 d-flex align-items-center">
                                                <i class="far fa-address-card white-text" style="color: #fff"></i>
                                            </div>
                                            <div data-mdb-input-init class="order-1 form-outline form-white">
                                                <input type="text" id="documento_identidad"
                                                    class="form-control form-control-lg" name="documento_identidad"
                                                    required
                                                    value="<?php echo htmlspecialchars($documentoIdentidadValue); ?>"
                                                    oninput="this.value = this.value.replace(/[^0-9]/g, '');" />
                                                <label class="form-label" for="documento_identidad">Documento de
                                                    identidad</label>
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
                                                    value="<?php echo htmlspecialchars($correoValue); ?>" />
                                                <label class="form-label" for="correo">Correo electrónico</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex flex-row-reverse justify-content-center">
                                        <div class="order-0 p-2">
                                            <button data-mdb-ripple-init type="submit" class="btn btn-light">
                                                <i class="fas fa-user-plus pe-2"></i>Agregar Coordinador</button>
                                        </div>
                                        <div class="order-1 p-2">
                                            <button data-mdb-ripple-init type="button" class="btn btn-danger"
                                                onclick="window.location.href='gestioncoordinadores.php'">
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