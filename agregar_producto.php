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
// Verificar si el usuario tiene el rol de administrador
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener los datos del formulario
    $nombreProducto = mysqli_real_escape_string($conexion, $_POST['nombre_producto']);
    $categoriaID = mysqli_real_escape_string($conexion, $_POST['categoria']);
    $presentacionID = mysqli_real_escape_string($conexion, $_POST['presentacion']);
    $marcaID = mysqli_real_escape_string($conexion, $_POST['marca']);
    $precioUnitario = mysqli_real_escape_string($conexion, $_POST['precio_unitario']);
    $porcentajeBeneficio = mysqli_real_escape_string($conexion, $_POST['porcentaje_beneficio']);

    $sql = "SELECT * FROM Productos WHERE NombreProducto = '$nombreProducto' AND CategoriaID = '$categoriaID' AND PresentacionID = '$presentacionID' AND MarcaID = '$marcaID'";
    $result = mysqli_query($conexion, $sql);

    // Validar que el porcentaje de beneficio esté entre 0 y 100
    if (mysqli_num_rows($result) > 0) {
        $error = "Este producto ya existe ";
    } else {
        if ($porcentajeBeneficio < 0 || $porcentajeBeneficio > 100) {
            echo "<p>El porcentaje de beneficio debe estar entre 0 y 100.</p>";
        } else {
            // Obtener el ID del usuario que está creando el producto
            $creador = obtenerDatosUsuario($conexion, $_SESSION['UsuarioID']);
            $creadorID = $creador['UsuarioID'];

            // Obtener la fecha y hora actual
            $fechaCreacion = date('Y-m-d H:i:s');

            // Insertar el producto en la base de datos
            $query = "INSERT INTO Productos (NombreProducto, FinancieroID, FechaCreacion, CategoriaID, PresentacionID, MarcaID) 
                  VALUES ( Upper ('$nombreProducto'), '$creadorID', '$fechaCreacion', '$categoriaID', '$presentacionID', '$marcaID')";
            $resultado = mysqli_query($conexion, $query);

            if ($resultado) {
                // Insertar dato en Inventario_Producto
                $productoID = mysqli_insert_id($conexion); // Obtener el ID del producto recién insertado
                $query_inventario = "INSERT INTO Inventario_Producto (ProductoID, FechaInicial, CantidadInicial, CantidadComprada, CantidadVendida) 
                                 VALUES ('$productoID', '$fechaCreacion', '0', '0', '0')";
                $resultado_inventario = mysqli_query($conexion, $query_inventario);

                if ($resultado_inventario) {
                    // Insertar dato en precio_compras
                    $query_precio = "INSERT INTO precio_compras (ProductoID, PrecioUnitario, PorcentajeBeneficio, FechaInicio) 
                                 VALUES ('$productoID', '$precioUnitario', '$porcentajeBeneficio', '$fechaCreacion')";
                    $resultado_precio = mysqli_query($conexion, $query_precio);

                    if ($resultado_precio) {
                        echo "<script>alert('Producto agregado exitosamente.'); window.location.href='gestionproductosA.php';</script>";
                    } else {
                        echo "<p>Error al agregar el precio del producto.</p>";
                    }
                } else {
                    echo "<p>Error al agregar producto en el inventario.</p>";
                }
            } else {
                echo "<p>Error al agregar producto.</p>";
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
    <title>Agregar Producto</title>


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
    </style>

    <div class="mx-4 my-4">
        <a class="btn text-white btn-lg btn-floating" data-mdb-ripple-init style="background-color: #ac2bac;"
            role="button" onclick="window.location.href='gestionproductosA.php'">
            <i class="fas fa-angle-left"></i>
        </a>

        <div class="container">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col-12 col-md-8 col-lg-6 col-xl-5">
                    <div class="card near-moon-gradient form-white" style="border-radius: 1rem;">
                        <div class="card-body p-5">
                            <form method="POST" action="">
                                <h3 class="text-center indigo-text font-bold py-4 fw-bold text-uppercase">
                                    <strong style="color: #fff">Agregar Producto</strong>
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
                                            <i class="far fa-user prefix white-text" style="color: #fff"></i>
                                        </div>
                                        <div data-mdb-input-init class="order-1 form-outline form-white">
                                            <input type="text" id="nombre_producto" class="form-control form-control-lg"
                                                name="nombre_producto" required />
                                            <label class="form-label" for="nombre_producto">Nombre del producto</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <div class="d-flex flex-nowrap">
                                        <div class="order-0 col-md-1 d-flex align-items-center">
                                            <i class="fas fa-tag white-text" style="color: #fff"></i>
                                        </div>
                                        <div data-mdb-input-init class="order-1 form-outline form-white">
                                            <label for="categoria" class="form-label select-label"
                                                style="color: #fff">Categoría</label>
                                            <select id="categoria" name="categoria" class="form-select bg-transparent"
                                                data-mdb-select-init required>
                                                <!-- Aquí debes cargar las opciones desde la base de datos -->
                                                <?php
                                                $query_categorias = "SELECT * FROM Categorias";
                                                $resultado_categorias = mysqli_query($conexion, $query_categorias);

                                                while ($row_categoria = mysqli_fetch_assoc($resultado_categorias)) {
                                                    echo "<option value='{$row_categoria['CategoriaID']}'>{$row_categoria['NombreCategoria']}</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <div class="d-flex flex-nowrap">
                                        <div class="order-0 col-md-1 d-flex align-items-center">
                                            <i class="fas fa-weight-scale white-text" style="color: #fff"></i>
                                        </div>
                                        <div data-mdb-input-init class="order-1 form-outline form-white">
                                            <label for="presentacion" class="form-label select-label"
                                                style="color: #fff">Presentación</label>
                                            <select id="presentacion" name="presentacion"
                                                class="form-select bg-transparent" data-mdb-select-init required>
                                                <!-- Aquí debes cargar las opciones desde la base de datos -->
                                                <?php
                                                $query_presentaciones = "SELECT * FROM Presentaciones";
                                                $resultado_presentaciones = mysqli_query($conexion, $query_presentaciones);

                                                while ($row_presentacion = mysqli_fetch_assoc($resultado_presentaciones)) {
                                                    echo "<option value='{$row_presentacion['PresentacionID']}'>{$row_presentacion['NombrePresentacion']}</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <div class="d-flex flex-nowrap">
                                        <div class="order-0 col-md-1 d-flex align-items-center">
                                            <i class="fa-brands fa-font-awesome white-text" style="color: #fff"></i>
                                        </div>
                                        <div data-mdb-input-init class="order-1 form-outline form-white">
                                            <label for="marca" class="form-label select-label"
                                                style="color: #fff">marca</label>
                                            <select id="marca" name="marca" class="form-select bg-transparent"
                                                data-mdb-select-init required>
                                                <!-- Aquí debes cargar las opciones solo de las marcas activas desde la base de datos -->
                                                <?php
                                                $query_marcas = "SELECT * FROM Marcas WHERE Estado = 1"; // Solo marcas activas
                                                $resultado_marcas = mysqli_query($conexion, $query_marcas);
                                                while ($row_marca = mysqli_fetch_assoc($resultado_marcas)) {
                                                    echo "<option value='{$row_marca['MarcaID']}'>{$row_marca['NombreMarca']}</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <div class="d-flex flex-nowrap">
                                        <div class="order-0 col-md-1 d-flex align-items-center">
                                            <i class="far fa-user prefix white-text" style="color: #fff"></i>
                                        </div>
                                        <div data-mdb-input-init class="order-1 form-outline form-white">
                                            <input type="number" id="precio_unitario"
                                                class="form-control form-control-lg" name="precio_unitario" min="0"
                                                step="0.01" required />
                                            <label class="form-label" for="precio_unitario">Precio unitario</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <div class="d-flex flex-nowrap">
                                        <div class="order-0 col-md-1 d-flex align-items-center">
                                            <i class="far fa-user prefix white-text" style="color: #fff"></i>
                                        </div>
                                        <div data-mdb-input-init class="order-1 form-outline form-white">
                                            <input type="number" id="porcentaje_beneficio"
                                                class="form-control form-control-lg" name="porcentaje_beneficio" min="0"
                                                max="100" required />
                                            <label class="form-label" for="porcentaje_beneficio">Porcentaje de Beneficio
                                                (0 - 100)</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex flex-row-reverse justify-content-center">
                                    <div class="order-0 p-2">
                                        <button data-mdb-ripple-init type="submit" class="btn btn-light">
                                            <i class="fas fa-user-plus pe-2"></i>Agregar Producto</button>
                                    </div>
                                    <div class="order-1 p-2">
                                        <button data-mdb-ripple-init type="button" class="btn btn-danger"
                                            onclick="window.location.href='gestionproductosA.php'">
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