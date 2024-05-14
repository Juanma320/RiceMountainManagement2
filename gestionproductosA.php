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

// Consulta para obtener los productos
$queryProductos = 'SELECT P.ProductoID, P.NombreProducto, C.NombreCategoria, PR.NombrePresentacion, PR.Medida, M.NombreMarca, 
          (IP.CantidadInicial + IP.CantidadComprada - IP.CantidadVendida) AS CantidadFinal,
          PC.PorcentajeBeneficio,
          P.Activo
          FROM Productos P
          INNER JOIN Categorias C ON P.CategoriaID = C.CategoriaID
          INNER JOIN Presentaciones PR ON P.PresentacionID = PR.PresentacionID
          INNER JOIN Marcas M ON P.MarcaID = M.MarcaID
          INNER JOIN Inventario_Producto IP ON P.ProductoID = IP.ProductoID
          INNER JOIN precio_compras PC ON P.ProductoID = PC.ProductoID';

$resultadoProductos = mysqli_query($conexion, $queryProductos);
// Consulta para obtener los cambios de precios programados
$queryCambiosPrecio = 'SELECT PC.ProductoID, PC.NuevoPrecio, PC.FechaFin, P.NombreProducto, M.NombreMarca, C.NombreCategoria, PR.Medida
                       FROM precio_compras PC
                       JOIN Productos P ON PC.ProductoID = P.ProductoID
                       JOIN Marcas M ON P.MarcaID = M.MarcaID
                       JOIN Categorias C ON P.CategoriaID = C.CategoriaID
                       JOIN Presentaciones PR ON P.PresentacionID = PR.PresentacionID
                       WHERE PC.FechaFin IS NOT NULL';

$resultadoCambiosPrecio = mysqli_query($conexion, $queryCambiosPrecio);


// Consulta para obtener los cambios de porcentaje programados
$queryCambiosPorcentaje = 'SELECT PC.ProductoID, PC.NuevoBeneficio, PC.FechaFinBeneficio, P.NombreProducto, M.NombreMarca, C.NombreCategoria, PR.Medida
                           FROM precio_compras PC
                           JOIN Productos P ON PC.ProductoID = P.ProductoID
                           JOIN Marcas M ON P.MarcaID = M.MarcaID
                           JOIN Categorias C ON P.CategoriaID = C.CategoriaID
                           JOIN Presentaciones PR ON P.PresentacionID = PR.PresentacionID
                           WHERE PC.FechaFinBeneficio IS NOT NULL';



$resultadoCambiosPorcentaje = mysqli_query($conexion, $queryCambiosPorcentaje);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Productos</title>

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
            role="button" href="indexadmin.php">
            <i class="fas fa-angle-left"></i>
        </a>

        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <h1>Gestionar Productos</h1>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-md-12">
                    <ul class="nav nav-pills flex-column flex-md-row mb-3">
                        <li class="nav-item me-auto p-2">
                            <a href="agregar_producto.php" style="color: #F9F6EE">
                                <button data-mdb-ripple-init type="button" class="btn btn-primary btn-rounded">
                                    <i class="fas fa-user pe-2"></i>Agregar Producto</button>
                            </a>
                        </li>
                        <li class="nav-item p-2">
                            <a href="gestion_categorias.php" style="color: #F9F6EE">
                                <button data-mdb-ripple-init type="button" class="btn btn-primary btn-rounded">
                                    <i class="fas fa-user pe-2"></i>Gestionar Categorías</button>
                            </a>
                        </li>
                        <li class="nav-item p-2">
                            <a href="gestion_presentaciones.php" style="color: #F9F6EE">
                                <button data-mdb-ripple-init type="button" class="btn btn-primary btn-rounded">
                                    <i class="fas fa-user pe-2"></i>Gestionar Presentaciones</button>
                            </a>
                        </li>
                        </li>
                        <li class="nav-item p-2">
                        <button type="button" class="btn btn-danger btn-rounded" data-mdb-ripple-init>
                            <a href="generar_reporte_producto.php" style="color: #F9F6EE">
                                </i>Generar PDF</button>
                            </a>
                        </li>
                    </ul>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <table class="table table-striped table-responsive rounded-9 overflow-hidden table-hover"
                                id="sortTable">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Nombre Producto</th>
                                        <th>Categoría</th>
                                        <th>Presentación</th>
                                        <th>Marca</th>
                                        <th>Medida</th>
                                        <th>Cantidad Final</th>
                                        <th>Precio Unitario</th>
                                        <th>% de Beneficio</th>
                                        <th>Precio Final Unitario</th>
                                        <th>Modificar Precio</th>
                                        <th>Modificar % Beneficio</th>
                                        <th class="text-center">Estado</th>
                                    </tr>
                                </thead>
                                <?php
                                // Iterar sobre los resultados y mostrar en la tabla
                                while ($row = mysqli_fetch_assoc($resultadoProductos)) {
                                    $precioUnitario = obtenerPrecioUnitario($conexion, $row['ProductoID']); // Pasar la conexión y el ID del producto
                                    $precioFinalUnitario = $precioUnitario * (1 + $row['PorcentajeBeneficio'] / 100);
                                    echo "<tr>";
                                    echo "<td>{$row['NombreProducto']}</td>";
                                    echo "<td>{$row['NombreCategoria']}</td>";
                                    echo "<td>{$row['NombrePresentacion']}</td>";
                                    echo "<td>{$row['NombreMarca']}</td>";
                                    echo "<td>{$row['Medida']}</td>";
                                    echo "<td>{$row['CantidadFinal']}</td>";
                                    echo "<td>{$precioUnitario} Cop</td>";
                                    echo "<td>{$row['PorcentajeBeneficio']}%</td>";
                                    echo "<td>{$precioFinalUnitario} Cop</td>";
                                    echo "<td><a data-mdb-ripple-init class='btn btn-warning' href='modificar_precio.php?productoID={$row['ProductoID']}'>Modificar</a></td>";
                                    echo "<td><a data-mdb-ripple-init class='btn btn-warning' href='editar_producto.php?id={$row['ProductoID']}'>Modificar</a></td>";
                                    echo "<td>";
                                    if ($row['Activo'] == 1) {
                                        echo "<button class='btn btn-light' onclick='cambiarEstado({$row['ProductoID']}, 0)'>Inactivar</button>";
                                    } else {
                                        echo "<button class='btn btn-dark' onclick='cambiarEstado({$row['ProductoID']}, 1)'>Activar</button>";
                                    }
                                    echo "</td>";
                                    echo "</tr>";
                                }
                                ?>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container mt-5">
            <div class="row">
                <div class="col-md-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <h2 id="cambiosPreciosProgramados">Cambios de Precios Programados</h2>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-md-12">
                    <table class="table table-striped table-responsive rounded-9 overflow-hidden table-hover"
                        id="sortTable1">
                        <thead class="table-dark">
                            <tr>
                                <th>Producto</th>
                                <th>Marca</th>
                                <th>Categoría</th>
                                <th>Medida</th>
                                <th>Nuevo Precio</th>
                                <th>Fecha de Cambio</th>
                                <th class="text-center">Cancelar Cambio</th>
                            </tr>
                        </thead>
                        <?php
                        while ($row = mysqli_fetch_assoc($resultadoCambiosPrecio)) {
                            echo "<tr>";
                            echo "<td>{$row['NombreProducto']}</td>";
                            echo "<td>{$row['NombreMarca']}</td>";
                            echo "<td>{$row['NombreCategoria']}</td>";
                            echo "<td>{$row['Medida']}</td>";
                            echo "<td>{$row['NuevoPrecio']} Cop</td>";
                            echo "<td>{$row['FechaFin']}</td>";
                            echo "<td class='text-center'><a data-mdb-ripple-init type='button' class='btn btn-danger'
                            href='cancelar_programacion.php?productoID={$row['ProductoID']}&tipo=precio'>
                            <i class='fas fa-ban pe-2'></i>Cancelar Programación</a></td>";
                            echo "</tr>";
                        }
                        ?>
                    </table>
                </div>
            </div>
        </div>

        <div class="container mt-5">
            <div class="row">
                <div class="col-md-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <h2>Cambios de Porcentaje Programados</h2>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-md-12">
                    <table class="table table-striped table-responsive rounded-9 overflow-hidden table-hover"
                        id="sortTable2">
                        <thead class="table-dark">
                            <tr>
                                <th>Producto</th>
                                <th>Marca</th>
                                <th>Categoría</th>
                                <th>Medida</th>
                                <th>Nuevo Porcentaje</th>
                                <th>Fecha de Cambio</th>
                                <th class="text-center">Cancelar Cambio</th>
                            </tr>
                        </thead>
                        <?php
                        while ($row = mysqli_fetch_assoc($resultadoCambiosPorcentaje)) {
                            echo "<tr>";
                            echo "<td>{$row['NombreProducto']}</td>";
                            echo "<td>{$row['NombreMarca']}</td>";
                            echo "<td>{$row['NombreCategoria']}</td>";
                            echo "<td>{$row['Medida']}</td>";
                            echo "<td>{$row['NuevoBeneficio']}%</td>";
                            echo "<td>{$row['FechaFinBeneficio']}</td>";
                            echo "<td class='text-center'><a data-mdb-ripple-init type='button' class='btn btn-danger'
                            href='cancelar_programacion.php?productoID={$row['ProductoID']}&tipo=porcentaje'>
                            <i class='fas fa-ban pe-2'></i>Cancelar Programación</a></td>";
                            echo "</tr>";
                        }
                        ?>
                    </table>
                    </br>
                </div>
            </div>
        </div>
    </div>

</body>

</html>

<script>
    function cambiarEstado(productoID, estado) {
        if (confirm("¿Estás seguro de cambiar el estado del producto?")) {
            // Si el usuario confirma, redirigir a procesar_estado_producto.php para cambiar el estado
            window.location.href = `procesar_estado_producto.php?productoID=${productoID}&estado=${estado}`;
        }
    }
</script>
<script type="text/javascript" src="js/mdb.umd.min.js"></script>
<script type="text/javascript"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.22/js/dataTables.bootstrap4.min.js"></script>
<script>$('#sortTable').DataTable({ order: [[0, 'asc']] })</script>
<script>$('#sortTable1').DataTable({ order: [[0, 'asc']] })</script>
<script>$('#sortTable2').DataTable({ order: [[0, 'asc']] })</script>
<script>
    // Initialization for ES Users
    import { Ripple, initMDB } from "mdb-ui-kit";

    initMDB({ Ripple });
</script>