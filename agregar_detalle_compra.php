<?php
include ('includes/includes.php');
include ('includes/funciones.php');

$row = obtenerDatosUsuario($conexion, $_SESSION['UsuarioID']);

$proveedorID = $_GET['proveedorID'];
$compraID = $_GET['compraID'];

if ($proveedorID) {
    $queryProveedor = "SELECT * FROM proveedores WHERE ProveedorID = $proveedorID";
    $resultProveedor = mysqli_query($conexion, $queryProveedor);
    $rowProveedor = mysqli_fetch_assoc($resultProveedor);

    $queryValorTotalCompra = "SELECT IFNULL(SUM(dc.Cantidad * pc.PrecioUnitario), 0) AS ValorTotal FROM detalle_compra dc
    INNER JOIN precio_compras pc ON dc.ProductoID = pc.ProductoID
    WHERE dc.CompraID = $compraID";
    $resultValorTotalCompra = mysqli_query($conexion, $queryValorTotalCompra);
    $rowValorTotalCompra = mysqli_fetch_assoc($resultValorTotalCompra);
    $valorTotalCompra = $rowValorTotalCompra['ValorTotal'];

    $queryCompra = "SELECT * FROM compras WHERE CompraID = $compraID";
    $resultCompra = mysqli_query($conexion, $queryCompra);
    $rowCompra = mysqli_fetch_assoc($resultCompra);

    $queryMarcasProveedor = "SELECT m.* FROM marcas m WHERE m.ProveedorID = $proveedorID";
    $resultMarcasProveedor = mysqli_query($conexion, $queryMarcasProveedor);

    $queryCategorias = "SELECT * FROM categorias";
    $resultCategorias = mysqli_query($conexion, $queryCategorias);

    $queryPresentaciones = "SELECT * FROM presentaciones";
    $resultPresentaciones = mysqli_query($conexion, $queryPresentaciones);

    $queryDetallesCompra = "SELECT dc.*, p.NombreProducto, m.NombreMarca, pr.NombrePresentacion, pc.PrecioUnitario FROM detalle_compra dc
                            INNER JOIN productos p ON dc.ProductoID = p.ProductoID
                            INNER JOIN marcas m ON p.MarcaID = m.MarcaID
                            INNER JOIN presentaciones pr ON p.PresentacionID = pr.PresentacionID
                            INNER JOIN precio_compras pc ON dc.ProductoID = pc.ProductoID
                            WHERE dc.CompraID = $compraID";
    $resultDetallesCompra = mysqli_query($conexion, $queryDetallesCompra);
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Información de Compra</title>

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

        .infoprov {
            font-size: 37px;
        }
    </style>

    <div class="mx-4 my-4">
        <a class="btn text-white btn-lg btn-floating" data-mdb-ripple-init style="background-color: #ac2bac;"
            role="button" onclick="window.location.href='infoproveedor.php?proveedorID=<?php echo $proveedorID; ?>'">
            <i class="fas fa-angle-left"></i>
        </a>

        <div class="container">
            <div class="row">
                <div class="col-md-3 mb-5">
                    <div class="container">
                        <div class="card">
                            <div class="card-body">
                                <div class="pb-4">
                                    <h1 class="infoprov">Información de la compra</h1>
                                </div>
                                <?php if ($proveedorID): ?>
                                    <p><strong>Nombre:</strong><br>
                                        <?= $rowProveedor['NombreProveedor'] ?>
                                    </p>
                                    <p><strong>Correo Electrónico:</strong><br>
                                        <?= $rowProveedor['CorreoElectronico'] ?>
                                    </p>
                                    <p><strong>Teléfono:</strong><br>
                                        <?= $rowProveedor['Telefono'] ?>
                                    </p>
                                    <p><strong>Fecha de Entrega:</strong><br>
                                        <?= $rowCompra['FechaCompra'] ?>
                                    </p>
                                    <p><strong>Valor Total de la Compra:</strong><br>
                                        <?= $valorTotalCompra ?> Cop
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-9">
                        <div class="container">
                            <div class="card p-4">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h1>Agregar detalles de compra</h1>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-4">
                                    <div class="col-md-12">
                                        <form action='procesar_detalle_compra.php' method='post'>
                                            <input type='hidden' name='proveedorID' value='<?= $proveedorID ?>'>
                                            <input type='hidden' name='compraID' value='<?= $compraID ?>'>

                                            <div class="row">
                                                <div class="col-sm-6 mb-4">
                                                    <div class="d-flex flex-nowrap">
                                                        <div class="order-0 d-flex align-items-center pe-3">
                                                            <i class="fas fa-tag white-text"></i>
                                                        </div>
                                                        <div class="order-1 form-outline form-white form-floating">
                                                            <select id="marca" name="marca" onchange="actualizarProductos()"
                                                                class="form-select bg-transparent" data-mdb-select-init
                                                                required>
                                                                <option value="">Todas las marcas</option>
                                                                <?php while ($rowMarca = mysqli_fetch_assoc($resultMarcasProveedor)): ?>
                                                                    <option value="<?= $rowMarca['MarcaID'] ?>">
                                                                        <?= $rowMarca['NombreMarca'] ?>
                                                                    </option>
                                                                <?php endwhile; ?>
                                                            </select>
                                                            <label for="floatingSelect">Marca</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6 mb-4">
                                                    <div class="d-flex flex-nowrap">
                                                        <div class="order-0 d-flex align-items-center pe-3">
                                                            <i class="fas fa-tag white-text"></i>
                                                        </div>
                                                        <div class="order-1 form-outline form-white form-floating">
                                                            <select name='categoria' id='categoria'
                                                                onchange='actualizarProductos()'
                                                                class="form-select bg-transparent" data-mdb-select-init
                                                                required>
                                                                <option value="">Todas las categorías</option>
                                                                <?php while ($rowCategoria = mysqli_fetch_assoc($resultCategorias)): ?>
                                                                    <option value='<?= $rowCategoria['CategoriaID'] ?>'>
                                                                        <?= $rowCategoria['NombreCategoria'] ?>
                                                                    </option>
                                                                <?php endwhile; ?>
                                                            </select>
                                                            <label for="floatingSelect">Categoría</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6 mb-4">
                                                    <div class="d-flex flex-nowrap">
                                                        <div class="order-0 d-flex align-items-center pe-3">
                                                            <i class="fas fa-tag white-text"></i>
                                                        </div>
                                                        <div class="order-1 form-outline form-white form-floating">
                                                            <select name='presentacion' id='presentacion'
                                                                onchange='actualizarProductos()'
                                                                class="form-select bg-transparent" data-mdb-select-init
                                                                required>
                                                                <option value="">Todas las categorías</option>
                                                                <?php while ($rowPresentacion = mysqli_fetch_assoc($resultPresentaciones)): ?>
                                                                    <option value='<?= $rowPresentacion['PresentacionID'] ?>'>
                                                                        <?= $rowPresentacion['NombrePresentacion'] ?>
                                                                    </option>
                                                                <?php endwhile; ?>
                                                            </select>
                                                            <label for="floatingSelect">Presentación</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6 mb-4">
                                                    <div class="d-flex flex-nowrap">
                                                        <div class="order-0 d-flex align-items-center pe-3">
                                                            <i class="fas fa-tag white-text"></i>
                                                        </div>
                                                        <div class="order-1 form-outline form-white form-floating">
                                                            <select name='producto' id='producto'
                                                                class="form-select bg-transparent" data-mdb-select-init
                                                                required>
                                                                <option value="">Seleccione un producto</option>
                                                            </select>
                                                            <label for="floatingSelect">Producto</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6 mb-4">
                                                    <div class="d-flex flex-nowrap">
                                                        <div class="order-0 d-flex align-items-center pe-3">
                                                            <i class="far fa-user prefix white-text"></i>
                                                        </div>
                                                        <div data-mdb-input-init class="order-1 form-outline">
                                                            <input type="number" id="cantidad"
                                                                class="form-control form-control-lg" name="cantidad" min="1"
                                                                required />
                                                            <label class="form-label" for="cantidad">Cantidad</label>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="d-flex flex-row-reverse justify-content-left">
                                                    <div class="order-0 p-2">
                                                        <input type='submit' value='Agregar compra' class="btn btn-primary"
                                                            data-mdb-ripple-init>
                                                    </div>
                                        </form>

                                        <div class="order-1 p-2">
                                            <form action='infoproveedor.php' method='get'>
                                                <input type='hidden' name='proveedorID' value='<?= $proveedorID ?>'>
                                                <input type='submit' value='Terminar compra' class="btn btn-secondary"
                                                    data-mdb-ripple-init>
                                            </form>
                                        </div>

                                        <div class="order-2 p-2">
                                            <form action='cancelar_compra.php' method='post'>
                                                <input type='hidden' name='proveedorID' value='<?= $proveedorID ?>'>
                                                <input type='hidden' name='compraID' value='<?= $compraID ?>'>
                                                <input type='submit' value='Cancelar compra' class="btn btn-tertiary"
                                                    data-mdb-ripple-init data-mdb-ripple-color="light">
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        <?php else: ?>
            <p>No se proporcionó un ID de proveedor.</p>
        <?php endif; ?>

        <div class="col-md-12">
            <div class="container">
                <div class="card p-4">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="d-flex justify-content-between align-items-center">
                                <h1>Productos agregados</h1>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <table class="table table-striped table-responsive rounded-9 overflow-hidden table-hover"
                                id="sortTable">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Nombre del Producto</th>
                                        <th>Marca</th>
                                        <th>Presentación</th>
                                        <th>Cantidad</th>
                                        <th>Valor Unitario</th>
                                        <th>Valor Total</th>
                                        <th class="text-center">Eliminar Producto</th>
                                    </tr>
                                </thead>
                                <?php while ($rowDetalleCompra = mysqli_fetch_assoc($resultDetallesCompra)): ?>
                                    <?php $valorTotal = $rowDetalleCompra['Cantidad'] * $rowDetalleCompra['PrecioUnitario']; ?>
                                    <tr>
                                        <td><?= $rowDetalleCompra['NombreProducto'] ?></td>
                                        <td><?= $rowDetalleCompra['NombreMarca'] ?></td>
                                        <td><?= $rowDetalleCompra['NombrePresentacion'] ?></td>
                                        <td><?= $rowDetalleCompra['Cantidad'] ?></td>
                                        <td><?= $rowDetalleCompra['PrecioUnitario'] ?></td>
                                        <td><?= $valorTotal ?></td>
                                        <td class="text-center"><a data-mdb-ripple-init class='btn btn-danger'
                                                href='eliminar_detalle_compra.php?detalleID=<?= $rowDetalleCompra['DetalleCompraID'] ?>&compraID=<?= $compraID ?>&proveedorID=<?= $proveedorID ?>'
                                                onclick="return confirm('¿Estás seguro de eliminar este producto?');">
                                                <i class='fas fa-ban pe-2'></i>Eliminar producto</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </table>
                        </div>
                    </div>
                </div>
                </br></br>
            </div>
        </div>
    </div>

</body>

</html>

<script>
    function actualizarProductos() {
        var marca = document.getElementById('marca').value;
        var categoria = document.getElementById('categoria').value;
        var presentacion = document.getElementById('presentacion').value;

        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById('producto').innerHTML = this.responseText;
            }
        };
        xmlhttp.open("GET", "obtener_productos.php?marca=" + marca + "&categoria=" + categoria + "&presentacion=" + presentacion, true);
        xmlhttp.send();
    }
</script>
<script type="text/javascript" src="js/mdb.umd.min.js"></script>
<script type="text/javascript"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.22/js/dataTables.bootstrap4.min.js"></script>
<script>$('#sortTable').DataTable({ order: [[0, 'des']] })</script>
<script>
    // Initialization for ES Users
    import { Ripple, initMDB } from "mdb-ui-kit";

    initMDB({ Ripple });
</script>