<?php
include ('includes/includes.php');
include ('includes/funciones.php');

$row = obtenerDatosUsuario($conexion, $_SESSION['NombreUsuario']);

// Verificar si el usuario tiene permiso para acceder a esta página
if ($_SESSION['RolID'] != 1 && $_SESSION['RolID'] != 2 && $_SESSION['RolID'] != 3) {
    // Si no tiene un rol válido, redirigir a la página de inicio
    header('Location: login.php');
    exit();
}

// Obtener datos del usuario
$usuario = obtenerDatosUsuario($conexion, $_SESSION['NombreUsuario']);

// Verificar si $usuario es un array válido y si tiene el índice 'UsuarioID'
if (is_array($usuario) && isset($usuario['UsuarioID'])) {
    // Continuar con el código para editar el perfil
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Verificar si se envió el formulario para cambiar la contraseña
        if (isset($_POST['cambiar_contrasena'])) {
            $contrasenaActual = mysqli_real_escape_string($conexion, $_POST['contrasena_actual']);
            $nuevaContrasena = mysqli_real_escape_string($conexion, $_POST['nueva_contrasena']);
            $confirmarContrasena = mysqli_real_escape_string($conexion, $_POST['confirmar_contrasena']);

            // Verificar si la contraseña actual es correcta
            $contrasenaActualDB = obtenerContrasenaUsuario($conexion, $_SESSION['NombreUsuario']);
            if (password_verify($contrasenaActual, $contrasenaActualDB)) {
                // Las contraseñas coinciden, hashear la nueva contraseña y actualizar en la base de datos
                $hashedPassword = password_hash($nuevaContrasena, PASSWORD_DEFAULT);
                $query = "UPDATE Usuarios SET Contra = '$hashedPassword' WHERE UsuarioID = {$usuario['UsuarioID']}";
                $resultado = mysqli_query($conexion, $query);

                if ($resultado) {
                    header('Location: perfil.php');
                    exit();
                } else {
                    $mensajeError = "Error al actualizar la contraseña.";
                }
            } else {
                $mensajeError = "La contraseña actual es incorrecta.";
            }
        }
    }
} else {
    // Mostrar un mensaje de error y salir del script
    echo "Error: Usuario no válido.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambiar Contraseña</title>


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
            role="button" onclick="window.location.href='perfil.php'">
            <i class="fas fa-angle-left"></i>
        </a>

        <div class="container">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col-12 col-md-8 col-lg-6 col-xl-5">
                    <div class="card near-moon-gradient form-white" style="border-radius: 1rem;">
                        <div class="card-body p-5">
                            <form method="POST" action="">
                                <h3 class="text-center indigo-text font-bold py-4 fw-bold text-uppercase">
                                    <strong style="color: #fff">Cambiar Contraseña</strong>
                                </h3>

                                <?php if (isset($mensajeError)): ?>
                                    <p style="color: red;"><?php echo $mensajeError; ?></p>
                                <?php endif; ?>

                                <div class="mb-4">
                                    <div class="d-flex flex-nowrap">
                                        <div class="order-0 col-md-1 d-flex align-items-center">
                                            <i class="fa-regular fa-eye-slash white-text" style="color: #fff"></i>
                                        </div>
                                        <div data-mdb-input-init class="order-1 form-outline form-white">
                                            <input type="password" id="contrasena_actual"
                                                class="form-control form-control-lg" name="contrasena_actual"
                                                required />
                                            <label class="form-label" for="contrasena_actual">Contraseña actual</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <div class="d-flex flex-nowrap">
                                        <div class="order-0 col-md-1 d-flex align-items-center">
                                            <i class="fa-regular fa-eye-slash white-text" style="color: #fff"></i>
                                        </div>
                                        <div data-mdb-input-init class="order-1 form-outline form-white">
                                            <input type="password" id="nueva_contrasena"
                                                class="form-control form-control-lg" name="nueva_contrasena" required />
                                            <label class="form-label" for="nueva_contrasena">Nueva contraseña</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <div class="d-flex flex-nowrap">
                                        <div class="order-0 col-md-1 d-flex align-items-center">
                                            <i class="fa-regular fa-eye-slash white-text" style="color: #fff"></i>
                                        </div>
                                        <div data-mdb-input-init class="order-1 form-outline form-white">
                                            <input type="password" id="confirmar_contrasena"
                                                class="form-control form-control-lg" name="confirmar_contrasena"
                                                required />
                                            <label class="form-label" for="confirmar_contrasena">Confirmar
                                                contraseña</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex flex-row-reverse justify-content-center">
                                    <div class="order-0 p-2">
                                        <button data-mdb-ripple-init type="submit" class="btn btn-light"
                                            name="cambiar_contrasena">
                                            <i class="fas fa-user-plus pe-2"></i>Cambiar contraseña</button>
                                    </div>
                                    <div class="order-1 p-2">
                                        <button data-mdb-ripple-init type="button" class="btn btn-danger"
                                            onclick="window.location.href='perfil.php'">
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