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
        $nuevoCorreo = mysqli_real_escape_string($conexion, $_POST['nuevo_correo']);
        $nuevoNombreUsuario = mysqli_real_escape_string($conexion, $_POST['nuevo_usuario']);

        // Añadir la funcionalidad para modificar la imagen del perfil
        $nuevaFotoPerfil = $usuario['FotoPerfil'];
        if (isset($_FILES['nueva_foto_perfil']) && $_FILES['nueva_foto_perfil']['error'] == 0) {
            $rutaTemporal = $_FILES['nueva_foto_perfil']['tmp_name'];
            $nombreArchivo = basename($_FILES['nueva_foto_perfil']['name']);
            $rutaDestino = 'src/assets/' . $nombreArchivo;
            if (move_uploaded_file($rutaTemporal, $rutaDestino)) {
                $nuevaFotoPerfil = $rutaDestino;
            }
        }

        $query = "UPDATE Usuarios SET NombreUsuario = '$nuevoNombreUsuario', Correo = '$nuevoCorreo', FotoPerfil = '$nuevaFotoPerfil' WHERE UsuarioID = {$usuario['UsuarioID']}";
        $resultado = mysqli_query($conexion, $query);

        if ($resultado) {
            header('Location: perfil.php');
            exit();
        } else {
            $mensajeError = "Error al actualizar el perfil.";
        }
    }
} else {
    // Mostrar un mensaje de error y salir del script
    echo "Error: Usuario no válido.";
    exit();
}

?>

<?php if (isset($mensajeError)): ?>
    <p style="color: red;"><?php echo $mensajeError; ?></p>
<?php endif; ?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuario</title>

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

        input[type=file]::file-selector-button {
            color: white;
        }
    </style>

    <div class="mx-4 my-4">
        <a class="btn text-white btn-lg btn-floating" data-mdb-ripple-init style="background-color: #ac2bac;"
            role="button" href="perfil.php">
            <i class="fas fa-angle-left"></i>
        </a>

        <div class="container">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col-12 col-md-8 col-lg-6 col-xl-5">
                    <div class="card near-moon-gradient form-white" style="border-radius: 1rem;">
                        <div class="card-body p-5">
                            <form method="post" action="" enctype="multipart/form-data">
                                <h3 class="text-center indigo-text font-bold py-4 fw-bold text-uppercase">
                                    <strong style="color: #fff">Modificar Usuario</strong>
                                </h3>
                                <div class="d-flex gap-2t mb-4">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="d-flex flex-nowrap">
                                            <!-- Muestra la imagen actual del perfil -->
                                            <?php
                                            // Verificar si $usuario es un array válido y si tiene el índice 'FotoPerfil'
                                            if (is_array($usuario) && isset($usuario['FotoPerfil'])) {
                                                // Mostrar la imagen de perfil
                                                echo '<img id="imagen-perfil" src="' . $usuario['FotoPerfil'] . '" alt="Foto de perfil"
                                                style="height: 80px; width: 80px; border-radius: 50%; object-fit: cover" >';
                                            } else {
                                                // Mostrar un mensaje de error
                                                echo "Error: No se pudo cargar la foto de perfil.";
                                            }
                                            ?>
                                        </div>
                                        <div>
                                            <div class="order-0 align-items-center">
                                                <i class="far fa-image prefix white-text" style="color: #fff"></i>
                                                <label class="form-label" for="nuevo_usuario" style="color: #fff">Nueva
                                                    foto de perfil</label>
                                            </div>
                                            <div data-mdb-input-init class="order-1 form-outline form-white">
                                                <!-- Campo para subir una nueva foto de perfil -->
                                                <input type="file" id="nueva_foto_perfil"
                                                    class="form-control form-control-lg" name="nueva_foto_perfil"
                                                    required />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <div class="d-flex flex-nowrap">
                                        <div class="order-0 col-md-1 d-flex align-items-center">
                                            <i class="far fa-user prefix white-text" style="color: #fff"></i>
                                        </div>
                                        <div data-mdb-input-init class="order-1 form-outline form-white">
                                            <input type="text" id="nuevo_usuario" class="form-control form-control-lg"
                                                name="nuevo_usuario" value="<?php echo $usuario['NombreUsuario']; ?>"
                                                required />
                                            <label class="form-label" for="nuevo_usuario">Nuevo nombre de
                                                usuario</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <div class="d-flex flex-nowrap">
                                        <div class="order-0 col-md-1 d-flex align-items-center">
                                            <i class="far fa-envelope prefix white-text" style="color: #fff"></i>
                                        </div>
                                        <div data-mdb-input-init class="order-1 form-outline form-white">
                                            <input type="email" id="nuevo_correo" class="form-control form-control-lg"
                                                name="nuevo_correo" value="<?php echo $usuario['Correo']; ?>"
                                                required />
                                            <label class="form-label" for="nuevo_correo">Nuevo correo
                                                electrónico</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex flex-row-reverse justify-content-center">
                                    <div class="order-0 p-2">
                                        <button data-mdb-ripple-init type="submit" class="btn btn-light">
                                            <i class="fa-solid fa-location-crosshairs pe-2"></i>Actualizar
                                            datos</button>
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
<script>
    document.getElementById('nueva_foto_perfil').addEventListener('change', function (e) {
        var reader = new FileReader();
        reader.onload = function (e) {
            document.getElementById('imagen-perfil').src = e.target.result;
        }
        reader.readAsDataURL(e.target.files[0]);
    });
</script>
<script>
    document.getElementById('nueva_foto_perfil').addEventListener('change', function () {
        var file = this.files[0];
        if (file.type.indexOf("image") == -1) {
            alert("El archivo seleccionado no es una imagen. Por favor, selecciona una imagen.");
            this.value = '';
        }
    });
</script>