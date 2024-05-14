<?php
// Archivo: login.php

// Incluir configuraciones y funciones comunes
include ('includes/includes.php');
include ('includes/funciones.php');

// Verificar si hay una sesión iniciada
if (isset($_SESSION['UsuarioID'])) {
    // Si hay sesión iniciada, redirigir según el rol
    switch ($_SESSION['RolID']) {
        case 1: // Admin
            header('Location: indexadmin.php');
            break;
        case 2: // Coordinador
            header('Location: indexcoordinador.php');
            break;
        case 3: // Financiero
            header('Location: indexfinanciero.php');
            break;
        default:
            // En caso de un rol desconocido, redirigir a una página por defecto o mostrar un mensaje de error.
            header('Location: login.php'); // Redirigir al login
            break;
    }
    exit(); // Asegúrate de salir después de la redirección.
}

$mensajeError = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verificar el reCAPTCHA
    $captcha = $_POST['g-recaptcha-response'];
    $secretKey = "6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe";
    $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=" . $secretKey . "&response=" . $captcha);
    $responseKeys = json_decode($response, true);
    $ip = $_SERVER['REMOTE_ADDR'];
    $horaActual = time();
    $intentosMaximos = 3;
    $tiempoVentana = 60; // 1 minuto
    // Si el reCAPTCHA no es válido, mostrar un mensaje de error
    if (!$responseKeys["success"]) {
        $mensajeError = 'Por favor, completa el reCAPTCHA.';
    } else {

        $query = "SELECT COUNT(*) AS intentos FROM intentos_login WHERE ip = '$ip' AND hora > ($horaActual - $tiempoVentana)";
        $resultado = mysqli_query($conexion, $query);
        $fila = mysqli_fetch_assoc($resultado);
        $intentos = $fila['intentos'];
        if ($intentos >= $intentosMaximos) {
            // Mostrar un mensaje de error
            $mensajeError = 'Se han excedido los intentos de inicio de sesión. Por favor, espere unos minutos antes de intentarlo nuevamente.';
            header('Location: login.php?error=' . urlencode($mensajeError));
            exit();
        } else {
            // Verificar si se excedió la cantidad máxima de intentos permitidos

            // El reCAPTCHA es válido, continuar con el proceso de inicio de sesión

            // Continuar con el proceso de inicio de sesión
            $nombreUsuario = mysqli_real_escape_string($conexion, $_POST['nombreUsuario']);
            $contrasena = mysqli_real_escape_string($conexion, $_POST['contrasena']);

            // Consultar la base de datos para verificar las credenciales
            $usuario = verificarCredenciales($conexion, $nombreUsuario, $contrasena);

            // Verificar las credenciales del usuario
            if ($usuario !== false) {
                // Guardar información del usuario en la sesión
                $_SESSION['UsuarioID'] = $usuario['UsuarioID'];
                $_SESSION['NombreUsuario'] = $usuario['NombreUsuario'];

                // Verificar si la clave 'RolID' existe antes de acceder a ella
                if (isset($usuario['RolID'])) {
                    $_SESSION['RolID'] = $usuario['RolID'];

                    // Redireccionar según el rol
                    switch ($usuario['RolID']) {
                        case 1: // Admin
                            header('Location: indexadmin.php');
                            break;
                        case 2: // Coordinador
                            header('Location: indexcoordinador.php');
                            break;
                        case 3: // Financiero
                            header('Location: indexfinanciero.php');
                            break;
                        default:
                            // En caso de un rol desconocido, podrías redirigir a una página por defecto o mostrar un mensaje de error.
                            break;
                    }

                    exit(); // Asegúrate de salir después de la redirección.
                } else {
                    // La clave 'RolID' no está definida en los datos del usuario
                    $mensajeError = 'watafa mi loco.';
                }
            } else {
                // Mostrar mensaje de usuario desconocido o contraseña incorrecta.
                $mensajeError = 'Los datos ingresados no son válidos';

                if ($intentos >= $intentosMaximos) {
                    // Mostrar un mensaje de error
                    $mensajeError = 'Se han excedido los intentos de inicio de sesión. Por favor, espere unos minutos antes de intentarlo nuevamente.';
                }
                // Limpiar el campo de la contraseña
                $_POST['contrasena'] = '';

                $ip = $_SERVER['REMOTE_ADDR'];
                $horaActual = time();

                $query = "INSERT INTO intentos_login (ip, hora) VALUES ('$ip', $horaActual)";
                mysqli_query($conexion, $query);

                // Redirigir al usuario a la página de login y mantener el mensaje de error
                header('Location: login.php?error=' . urlencode($mensajeError));
                exit();
            }
        }
    }
}

// Recuperar el mensaje de error de la URL si existe
if (isset($_GET['error'])) {
    $mensajeError = $_GET['error'];
}
?>

<!DOCTYPE html>
<html lang="es">

<head>

    <title>Login</title>

    <!-- MDB icon -->
    <link rel="icon" href="img/mdb-favicon.ico" type="image/x-icon" />
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <!-- Google Fonts Roboto -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" />
    <!-- MDB -->
    <link rel="stylesheet" href="css/mdb.min.css" />
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <style>
        .gradient-custom {
            background: radial-gradient(circle at 52.1% -29.6%, rgb(144, 17, 105) 0%, rgb(51, 0, 131) 100.2%);
        }
    </style>
</head>

<body>
    <form method="post" action="">
        <section class="vh-100 gradient-custom">
            <div class="container py-5 h-100">
                <div class="row d-flex justify-content-center align-items-center h-100">
                    <div class="col-12 col-md-8 col-lg-6 col-xl-5">
                        <div class="card bg-dark text-white" style="border-radius: 1rem;">
                            <div class="card-body p-5 text-center">
                                <div class="mb-md-5 mt-md-4 pb-5">
                                    <h2 class="fw-bold mb-2 text-uppercase">Iniciar sesión</h2>
                                    <?php if (!empty($mensajeError)): ?>
                                        <div class="alert alert-danger" role="alert">
                                            <?php echo $mensajeError; ?>
                                        </div>
                                    <?php endif; ?>
                                    <p class="text-white-50 mb-5">Ingresa tu nombre de usuario y tu contraseña</p>

                                    <div data-mdb-input-init class="form-outline form-white mb-4">
                                        <input type="text" id="typeEmailX" class="form-control form-control-lg"
                                            name="nombreUsuario" />
                                        <label class="form-label" for="nombreUsuario">Nombre de usuario</label>
                                    </div>

                                    <div data-mdb-input-init class="form-outline form-white mb-4">
                                        <input type="password" id="typePasswordX" class="form-control form-control-lg"
                                            name="contrasena" />
                                        <label class="form-label" for="contrasena">Contraseña</label>
                                    </div>
                                    <!-- Agregar esto antes del botón de inicio de sesión -->
                                    <div class="g-recaptcha" data-sitekey="6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI">
                                    </div>
                                    </br>
                                    <button data-mdb-button-init data-mdb-ripple-init
                                        class="btn btn-outline-light btn-lg px-5" id="loginButton">Iniciar
                                        sesión</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </form>

</body>

</html>

<script type="text/javascript" src="js/mdb.umd.min.js"></script>
<script type="text/javascript"></script>
<script>
    const loginButton = document.getElementById('loginButton');

    loginButton.addEventListener('click', (event) => {
        // Verificar si el reCAPTCHA es válido antes de enviar el formulario
        if (grecaptcha.getResponse() == "") {
            event.preventDefault(); // Detener el envío del formulario
            alert('Por favor, completa el reCAPTCHA.');
        } else {
            // Si el reCAPTCHA es válido, enviar el formulario
            document.querySelector('form').submit();
        }
    });
</script>