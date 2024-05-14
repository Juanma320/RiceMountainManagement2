<?php
require_once('TCPDF/tcpdf.php');
include('includes/includes.php');
include('includes/pdf_helper.php');
// Obtener el ID de la compra de la URL
$compraID = $_GET['compraID'];
$nombreUsuario = $_SESSION['NombreUsuario'];

// Obtener los datos del usuario desde la base de datos
$datosUsuario = obtenerDatosUsuarioPDF($conexion, $nombreUsuario);
// Verificar si se recibió un ID de compra
if ($compraID) {
    // Obtener información de la compra
    $queryCompra = "SELECT c.*, p.NombreProveedor FROM compras c
                    JOIN proveedores p ON c.ProveedorID = p.ProveedorID
                    WHERE c.CompraID = $compraID";
    $resultCompra = mysqli_query($conexion, $queryCompra);
    $rowCompra = mysqli_fetch_assoc($resultCompra);

    // Obtener detalles de la compra
    $queryDetallesCompra = "SELECT dc.*, p.NombreProducto, m.NombreMarca, cat.NombreCategoria, pr.NombrePresentacion, pc.PrecioUnitario
                            FROM detalle_compra dc
                            JOIN productos p ON dc.ProductoID = p.ProductoID
                            JOIN marcas m ON p.MarcaID = m.MarcaID
                            JOIN categorias cat ON p.CategoriaID = cat.CategoriaID
                            JOIN presentaciones pr ON p.PresentacionID = pr.PresentacionID
                            JOIN precio_compras pc ON dc.ProductoID = pc.ProductoID
                            WHERE dc.CompraID = $compraID";
    $resultDetallesCompra = mysqli_query($conexion, $queryDetallesCompra);

    // Crear una nueva instancia de TCPDF
    $pdf = new TCPDF();
    $pdf->SetCreator('Nombre del Creador');
    $pdf->SetAuthor('Nombre del Autor');
    $pdf->SetTitle('Reporte de Compra');

    // Agregar una página
    $pdf->AddPage();
    $pdf->SetFont('helvetica', 'B', 20);

    // Agregar texto al PDF con los datos de la compra
    
    // Agregar texto al PDF con los datos del usuario
    $pdf->Cell(0, 10, 'Reporte de compra - ' . date('m/Y'), 0, 1, 'C');
    $pdf->Ln();
    $pdf->SetFont('helvetica', '', 12);
    $pdf->Cell(0, 10, 'Generado por el usuario: ' . $datosUsuario['NombreUsuario'], 0, 1, 'L');
    $pdf->Cell(0, 10, 'Correo: ' . $datosUsuario['Correo'], 0, 1, 'L');
    $pdf->Ln();
    $pdf->Cell(0, 10, 'Detalles de la compra', 0, 1, 'L');
    $pdf->SetFont('times', '', 10); // Cambiar la fuente a times y el tamaño a 12
    $pdf->Write(10, 'Productos:');
    $pdf->Cell(0, 10, 'Proveedor: ' . $rowCompra['NombreProveedor'], 0, 1, 'L');
    $pdf->Cell(0, 10, 'Fecha de Compra: ' . $rowCompra['FechaCompra'], 0, 1, 'L');
    $pdf->Cell(0, 10, 'Valor Total de la Compra: ' . $rowCompra['ValorCompra'], 0, 1, 'L');

    // Agregar tabla de detalles de compra
    $pdf->Ln();
    $pdf->Cell(0, 10, 'Detalles de la Compra', 0, 1, 'L');
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(30, 7, 'Nombre Producto', 1);
    $pdf->Cell(30, 7, 'Marca', 1);
    $pdf->Cell(30, 7, 'Categoria', 1);
    $pdf->Cell(30, 7, 'Presentacion', 1);
    $pdf->Cell(20, 7, 'Cantidad', 1);
    $pdf->Cell(30, 7, 'Precio Unitario', 1);
    $pdf->Cell(20, 7, 'Valor Total', 1);
    $pdf->Ln();

    while ($rowDetalleCompra = mysqli_fetch_assoc($resultDetallesCompra)) {
        $valorTotal = $rowDetalleCompra['Cantidad'] * $rowDetalleCompra['PrecioUnitario'];
        $pdf->Cell(30, 7, $rowDetalleCompra['NombreProducto'], 1);
        $pdf->Cell(30, 7, $rowDetalleCompra['NombreMarca'], 1);
        $pdf->Cell(30, 7, $rowDetalleCompra['NombreCategoria'], 1);
        $pdf->Cell(30, 7, $rowDetalleCompra['NombrePresentacion'], 1);
        $pdf->Cell(20, 7, $rowDetalleCompra['Cantidad'], 1);
        $pdf->Cell(30, 7, $rowDetalleCompra['PrecioUnitario'], 1);
        $pdf->Cell(20, 7, $valorTotal, 1);
        $pdf->Ln();
    }

    // Salida del PDF
    $pdf->Output('reporte_compra_' . $compraID . '.pdf', 'I');

    ob_end_clean(); // Limpiar el buffer de salida
} else {
    echo "<p>No se proporcionó un ID de compra.</p>";
}
?>
