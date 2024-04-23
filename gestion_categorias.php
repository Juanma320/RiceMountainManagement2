<?php
include ('includes/includes.php');
include ('includes/funciones.php');
$row = obtenerDatosUsuario($conexion, $_SESSION['NombreUsuario']);
// Verificar si el usuario tiene el rol de administrador
if ($_SESSION['RolID'] != 1) {
    // Si no es administrador, redirigir a la página de inicio
    header('Location: login.php');
    exit();
}

// Consulta para obtener las categorías
$queryCategorias = 'SELECT * FROM categorias';
$resultadoCategorias = mysqli_query($conexion, $queryCategorias);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Categorías</title>

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

        #tdcenter {
            display: flex;
            justify-content: center;
        }
    </style>

    <div class="mx-4 my-4">
        <a class="btn text-white btn-lg btn-floating" data-mdb-ripple-init style="background-color: #ac2bac;"
            role="button" href="gestionproductosA.php">
            <i class="fas fa-angle-left"></i>
        </a>

        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <h1>Gestión de Categorías</h1>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-md-12">
                    <ul class="nav nav-pills flex-column flex-md-row">
                        <li class="nav-item">
                            <a href="agregar_categoria.php" style="color: #F9F6EE">
                                <button data-mdb-ripple-init type="button" class="btn btn-primary btn-rounded">
                                    <i class="fas fa-user pe-2"></i>Agregar categoría</button>
                            </a>
                        </li>
                    </ul>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <table class="table table-striped table-responsive rounded-9 overflow-hidden table-hover"
                                id="sortTable">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Nombre de la categoría</th>
                                        <th class="text-center">Eliminar categoría</th>
                                    </tr>
                                </thead>
                                <?php
                                while ($rowCategoria = mysqli_fetch_assoc($resultadoCategorias)) {
                                    echo "<tr>";
                                    echo "<td>{$rowCategoria['NombreCategoria']}</td>";
                                    // Verificar si la categoría está siendo usada en productos
                                    $queryProductosCategoria = "SELECT COUNT(*) AS total FROM productos WHERE CategoriaID = {$rowCategoria['CategoriaID']}";
                                    $resultProductosCategoria = mysqli_query($conexion, $queryProductosCategoria);
                                    $rowProductosCategoria = mysqli_fetch_assoc($resultProductosCategoria);
                                    $totalProductos = $rowProductosCategoria['total'];
                                    if ($totalProductos > 0) {
                                        echo "<td class='text-center'>La categoría aún está siendo usada</td>";
                                    } else {
                                        echo "<td class='text-center'><a data-mdb-ripple-init type='button' class='btn btn-danger'
                                href='eliminar_categoria.php?categoriaID={$rowCategoria['CategoriaID']}'>
                                <i class='fas fa-ban pe-2'></i>Eliminar categoría</a></td>";
                                    }
                                    echo "</tr>";
                                }
                                ?>
                            </table>
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
<script>$('#sortTable').DataTable({ order: [[0, 'asc']] })</script>
<script>
    // Initialization for ES Users
    import { Ripple, initMDB } from "mdb-ui-kit";

    initMDB({ Ripple });
</script>