<?php
include ('includes/includes.php');
include ('includes/funciones.php');
$row = obtenerDatosUsuario($conexion, $_SESSION['NombreUsuario']);

// Verificar si el usuario tiene el rol de administrador
if ($_SESSION['RolID'] != 1 && $_SESSION['RolID'] != 3) {
    header('Location: login.php');
    exit();
}

// Obtener el producto a editar si se ha pasado el ID por GET
$producto = null;
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['id'])) {
    $productoID = mysqli_real_escape_string($conexion, $_GET['id']);
    $query = "SELECT * FROM Productos WHERE ProductoID = $productoID";
    $resultado = mysqli_query($conexion, $query);
    $producto = mysqli_fetch_assoc($resultado);
}

// Función para obtener la fecha de fin de beneficio actual
function obtenerFechaFinBeneficioActual($conexion, $productoID)
{
    $query = "SELECT FechaFinBeneficio FROM precio_compras WHERE ProductoID = $productoID";
    $resultado = mysqli_query($conexion, $query);
    if ($resultado && mysqli_num_rows($resultado) > 0) {
        $row = mysqli_fetch_assoc($resultado);
        return $row['FechaFinBeneficio'];
    }
    return null;
}

// Obtener la fecha de fin de beneficio actual para el producto
$fechaFinBeneficioActual = obtenerFechaFinBeneficioActual($conexion, $producto['ProductoID']);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Producto</title>

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
            role="button" onclick="cancelarActualizacion()">
            <i class="fas fa-angle-left"></i>
        </a>

        <div class="container">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col-12 col-md-8 col-lg-6 col-xl-5">
                    <div class="card near-moon-gradient form-white" style="border-radius: 1rem;">
                        <div class="card-body p-5">
                            <form method="POST" action="procesar_actualizacion_p.php">
                                <h3 class="text-center indigo-text font-bold py-4 fw-bold text-uppercase">
                                    <strong style="color: #fff">Modificar Producto</strong>
                                </h3>

                                <div class="mb-4">
                                    <div class="d-flex flex-nowrap">
                                        <div class="order-0 col-md-1 d-flex align-items-center">
                                            <i class="fas fa-percent prefix white-text" style="color: #fff"></i>
                                        </div>
                                        <div data-mdb-input-init class="order-1 form-outline form-white">
                                            <input type="hidden" name="productoID"
                                                value="<?php echo $producto['ProductoID'] ?? ''; ?>">
                                            <input type="number" name="nuevo_beneficio" step="any" min="0" max="100"
                                                required class="form-control form-control-lg" required />
                                            <label class="form-label" for="nuevo_beneficio">Nuevo Porcentaje
                                                Beneficio</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <div class="d-flex flex-nowrap">
                                        <div class="order-0 col-md-1 d-flex align-items-center">
                                            <i class="far fa-calendar prefix white-text" style="color: #fff"></i>
                                        </div>
                                        <input type="hidden" id="fecha_fin_beneficio_hidden"
                                            value="<?php echo $fechaFinBeneficioActual; ?>">
                                        <div data-mdb-input-init class="order-1 form-outline form-white">
                                            <input type="date" id="fecha_fin_beneficio"
                                                class="form-control form-control-lg" name="fecha_fin_beneficio"
                                                required />
                                            <label class="form-label" for="fecha_fin_beneficio">Fecha Fin
                                                Beneficio</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex flex-row-reverse justify-content-center">
                                    <div class="order-0 p-2">
                                        <button data-mdb-ripple-init type="submit" class="btn btn-light"
                                            onclick="return confirmacionGuardado()">
                                            <i class="far fa-calendar-plus pe-2"></i>Actualizar Precio</button>
                                    </div>
                                    <div class="order-1 p-2">
                                        <button data-mdb-ripple-init type="button" class="btn btn-danger"
                                            onclick="return cancelarActualizacion()">
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

<script>
    function confirmacionGuardado() {
        var fechaFinBeneficio = document.getElementById('fecha_fin_beneficio').value;
        var fechaActual = new Date().toISOString().slice(0, 10); // Obtener fecha actual en formato YYYY-MM-DD
        var fechaFinBeneficioActual = document.getElementById('fecha_fin_beneficio_hidden').value;

        // Verificar si la fecha ingresada es igual o menor a la fecha actual
        if (fechaFinBeneficio <= fechaActual) {
            alert('La fecha del cambio no puede ser igual o anterior a la fecha actual.');
            return false; // Cancelar envío del formulario
        }

        if (fechaFinBeneficioActual !== '0000-00-00' && fechaFinBeneficioActual !== '' && fechaFinBeneficioActual !== null) {
            if (fechaFinBeneficioActual < fechaActual) {
                alert('La fecha del cambio no puede ser anterior a la fecha actual.');
                return false; // Cancelar envío del formulario
            }
            return confirm('¿Estás seguro de que quieres cambiar la programación?');
        }
        return confirm('Confirmar programación del cambio del % de beneficio');
    }

    function cancelarActualizacion() {
        var url = <?php echo ($_SESSION['RolID'] == 3) ? "'gestion_productos_financiero.php'" : "'gestionproductosA.php'"; ?>;
        window.location.href = url;
    }
</script>
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