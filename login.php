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

// Verificar si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener datos del formulario
    $nombreUsuario = $_POST['nombreUsuario'];
    $contrasena = $_POST['contrasena'];

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
            echo "watafa mi loco.";
        }
    } else {
        // Mostrar mensaje de usuario desconocido o contraseña incorrecta.
        echo "Error: Usuario desconocido o contraseña incorrecta. Rellene nuevamente los datos.";
    }
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

    <!-- MDB -->
    <script type="text/javascript" src="js/mdb.umd.min.js"></script>
    <!-- Custom scripts -->
    <script type="text/javascript"></script>

    <script>
        const loginButton = document.getElementById('loginButton');

        loginButton.addEventListener('click', () => {
            // Enviar el formulario aquí
            const form = document.querySelector('form'); // Selecciona el formulario
            form.submit(); // Envía el formulario
        });
    </script>

</body>

</html>