<?php
include ('includes/includes.php');
include ('includes/funciones.php');

$row = obtenerDatosUsuario($conexion, $_SESSION['UsuarioID']);

$ventaID = $_GET['ventaID'];
$clienteID = $_GET['clienteID'];

if ($ventaID) {
    $queryVenta = "SELECT v.*, c.NombreCliente, d.Direccion FROM Ventas v
                   JOIN Clientes c ON v.ClienteID = c.ClienteID
                   JOIN Direcciones_Clientes d ON v.DireccionID = d.DireccionID
                   WHERE v.VentaID = $ventaID";
    $resultVenta = mysqli_query($conexion, $queryVenta);
    $rowVenta = mysqli_fetch_assoc($resultVenta);

    $queryValorTotal = "SELECT IFNULL(ROUND(SUM(dv.Cantidad * (pc.PrecioUnitario + (pc.PrecioUnitario * pc.PorcentajeBeneficio / 100))), 2), 0) AS ValorTotal
                        FROM detalles_venta dv
                        JOIN precio_compras pc ON dv.ProductoID = pc.ProductoID
                        WHERE dv.VentaID = $ventaID";
    $resultValorTotal = mysqli_query($conexion, $queryValorTotal);
    $rowValorTotal = mysqli_fetch_assoc($resultValorTotal);
    $valorTotalVenta = $rowValorTotal['ValorTotal'];

    $queryActualizarTotalVenta = "UPDATE Ventas SET TotalVenta = $valorTotalVenta WHERE VentaID = $ventaID";
    $resultActualizarTotalVenta = mysqli_query($conexion, $queryActualizarTotalVenta);

    $queryDetalles = "SELECT dv.*, p.NombreProducto, m.NombreMarca, pre.NombrePresentacion, pc.PrecioUnitario, pc.PorcentajeBeneficio
                      FROM detalles_venta dv
                      JOIN productos p ON dv.ProductoID = p.ProductoID
                      JOIN marcas m ON p.MarcaID = m.MarcaID
                      JOIN presentaciones pre ON p.PresentacionID = pre.PresentacionID
                      JOIN precio_compras pc ON p.ProductoID = pc.ProductoID
                      WHERE dv.VentaID = $ventaID";
    $resultDetalles = mysqli_query($conexion, $queryDetalles);
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Información de Venta</title>

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
            role="button" onclick="window.location.href='infocliente.php?clienteID=<?php echo $clienteID; ?>'">
            <i class="fas fa-angle-left"></i>
        </a>

        <div class="container">
            <div class="row">
                <div class="col-md-3 mb-5">
                    <div class="container">
                        <div class="card">
                            <div class="card-body">
                                <div class="pb-4">
                                    <h1 class="infoprov">Información de la venta</h1>
                                </div>
                                <?php if ($ventaID): ?>
                                    <p><strong>Cliente:</strong><br>
                                        <?= $rowVenta['NombreCliente'] ?>
                                    </p>
                                    <p><strong>Dirección de envío:</strong><br>
                                        <?= $rowVenta['Direccion'] ?>
                                    </p>
                                    <p><strong>Fecha de la venta:</strong><br>
                                        <?= $rowVenta['FechaVenta'] ?>
                                    </p>
                                    <p><strong>Valor Total de la Venta:</strong><br>
                                        <?= $valorTotalVenta ?> Cop
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
                                            <h1>Agregar producto a la venta</h1>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-4">
                                    <div class="col-md-12"></div>
                                    <form action='procesar_detalle_venta.php' method='post'
                                        onsubmit='return validarCantidad()'>
                                        <input type='hidden' name='ventaID' value='<?= $ventaID ?>'>
                                        <input type='hidden' name='clienteID' value='<?= $clienteID ?>'>

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
                                                            <?php
                                                            $queryMarcas = "SELECT * FROM marcas";
                                                            $resultMarcas = mysqli_query($conexion, $queryMarcas);
                                                            while ($rowMarca = mysqli_fetch_assoc($resultMarcas)) {
                                                                echo "<option value='{$rowMarca['MarcaID']}'>{$rowMarca['NombreMarca']}</option>";
                                                            }
                                                            ?>
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
                                                            <?php
                                                            $queryCategorias = "SELECT * FROM categorias";
                                                            $resultCategorias = mysqli_query($conexion, $queryCategorias);
                                                            while ($rowCategoria = mysqli_fetch_assoc($resultCategorias)) {
                                                                echo "<option value='{$rowCategoria['CategoriaID']}'>{$rowCategoria['NombreCategoria']}</option>";
                                                            }
                                                            ?>
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
                                                            <?php
                                                            $queryPresentaciones = "SELECT * FROM presentaciones";
                                                            $resultPresentaciones = mysqli_query($conexion, $queryPresentaciones);
                                                            while ($rowPresentacion = mysqli_fetch_assoc($resultPresentaciones)) {
                                                                echo "<option value='{$rowPresentacion['PresentacionID']}'>{$rowPresentacion['NombrePresentacion']}</option>";
                                                            }
                                                            ?>
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
                                                    <input type='submit' value='Agregar venta' class="btn btn-primary"
                                                        data-mdb-ripple-init>
                                                </div>
                                    </form>

                                    <div class="order-1 p-2">
                                        <form action='infocliente.php' method='get'>
                                            <input type='hidden' name='clienteID' value='<?= $clienteID ?>'>
                                            <input type='submit' value='Terminar Venta' class="btn btn-secondary"
                                                data-mdb-ripple-init>
                                        </form>
                                    </div>

                                    <div class="order-2 p-2">
                                        <form id='formCancelarVenta' action='cancelar_venta.php' method='post'>
                                            <input type='hidden' name='ventaID' value='<?= $ventaID ?>'>
                                            <input type='hidden' name='clienteID' value='<?= $clienteID ?>'>
                                            <input type='submit' value='Cancelar Venta' class="btn btn-tertiary"
                                                data-mdb-ripple-init data-mdb-ripple-color="light" onclick='return
                                            confirmarCancelacion();'>
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
        <p>No se proporcionó un ID de venta.</p>
    <?php endif; ?>

    <div class="col-md-12">
        <div class="container">
            <div class="card p-4">
                <div class="row">
                    <div class="col-md-12">
                        <div class="d-flex justify-content-between align-items-center">
                            <h1>Detalles de la venta</h1>
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
                            <?php while ($rowDetalle = mysqli_fetch_assoc($resultDetalles)): ?>
                                <?php
                                $precioUnitario = $rowDetalle['PrecioUnitario'] + ($rowDetalle['PrecioUnitario'] * $rowDetalle['PorcentajeBeneficio'] / 100);
                                $valorTotal = $rowDetalle['Cantidad'] * $precioUnitario;
                                ?>
                                <tr>
                                    <td><?= $rowDetalle['NombreProducto'] ?></td>
                                    <td><?= $rowDetalle['NombreMarca'] ?></td>
                                    <td><?= $rowDetalle['NombrePresentacion'] ?></td>
                                    <td><?= $rowDetalle['Cantidad'] ?></td>
                                    <td><?= $precioUnitario ?></td>
                                    <td><?= $valorTotal ?></td>
                                    <td class="text-center"><a data-mdb-ripple-init class='btn btn-danger'
                                            href='eliminar_producto_venta.php?detalleVentaID=<?= $rowDetalle['DetalleVentaID'] ?>&ventaID=<?= $ventaID ?>&clienteID=<?= $clienteID ?>'>
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

</body>

</html>

<script>
    function validarCantidad() {
        var cantidad = document.getElementById('cantidad').value;
        var productoID = document.getElementById('producto').value;

        // Realizar una solicitud AJAX para obtener la cantidad disponible del producto
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                var inventario = JSON.parse(this.responseText);
                var cantidadDisponible = inventario.CantidadInicial + inventario.CantidadComprada - inventario.CantidadVendida;
                if (cantidad > cantidadDisponible) {
                    alert('La cantidad seleccionada supera el stock disponible.');
                    return false; // Detener el envío del formulario
                }
                return true; // Continuar con el envío del formulario
            }
        };
        xhttp.open("GET", "obtener_inventario.php?productoID=" + productoID, false);
        xhttp.send();
        var inventario = JSON.parse(xhttp.responseText);
        var cantidadDisponible = inventario.CantidadInicial + inventario.CantidadComprada - inventario.CantidadVendida;
        if (cantidad > cantidadDisponible) {
            alert('La cantidad seleccionada supera el stock disponible.');
            return false; // Detener el envío del formulario
        }
        return true; // Continuar con el envío del formulario
    }

    function actualizarProductos() {
        var marcaID = document.getElementById('marca').value;
        var categoriaID = document.getElementById('categoria').value;
        var presentacionID = document.getElementById('presentacion').value;

        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById('producto').innerHTML = this.responseText;
            }
        };
        xhttp.open("GET", "obtener_productos.php?marca=" + marcaID + "&categoria=" + categoriaID + "&presentacion=" + presentacionID, true);
        xhttp.send();
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