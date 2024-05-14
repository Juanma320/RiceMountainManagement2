<?php
require_once('TCPDF/tcpdf.php');
include('includes/includes.php');
include('includes/pdf_helper.php');

// Obtener el ID de la venta de la URL
$ventaID = $_GET['ventaID'];
$nombreUsuario = $_SESSION['NombreUsuario'];

// Obtener los datos del usuario desde la base de datos
$datosUsuario = obtenerDatosUsuarioPDF($conexion, $nombreUsuario);
// Verificar si se recibió un ID de venta
if ($ventaID) {
    // Obtener información de la venta
    $queryVenta = "SELECT v.*, c.NombreCliente, d.Direccion FROM Ventas v
                   JOIN Clientes c ON v.ClienteID = c.ClienteID
                   JOIN Direcciones_Clientes d ON v.DireccionID = d.DireccionID
                   WHERE v.VentaID = $ventaID";
    $resultVenta = mysqli_query($conexion, $queryVenta);
    $rowVenta = mysqli_fetch_assoc($resultVenta);

    // Obtener detalles de la venta
    $queryDetallesVenta = "SELECT dv.*, p.NombreProducto, m.NombreMarca, pre.NombrePresentacion, pc.PrecioUnitario, pc.PorcentajeBeneficio
                  FROM detalles_venta dv
                  JOIN productos p ON dv.ProductoID = p.ProductoID
                  JOIN marcas m ON p.MarcaID = m.MarcaID
                  JOIN presentaciones pre ON p.PresentacionID = pre.PresentacionID
                  JOIN precio_compras pc ON p.ProductoID = pc.ProductoID
                  WHERE dv.VentaID = $ventaID";
    $resultDetallesVenta = mysqli_query($conexion, $queryDetallesVenta);

    // Calcular el valor total de la venta
    $queryValorTotal = "SELECT IFNULL(ROUND(SUM(dv.Cantidad * (pc.PrecioUnitario + (pc.PrecioUnitario * pc.PorcentajeBeneficio / 100))), 2), 0) AS ValorTotal
                        FROM detalles_venta dv
                        JOIN precio_compras pc ON dv.ProductoID = pc.ProductoID
                        WHERE dv.VentaID = $ventaID";

    $resultValorTotal = mysqli_query($conexion, $queryValorTotal);
    $rowValorTotal = mysqli_fetch_assoc($resultValorTotal);
    $valorTotalVenta = $rowValorTotal['ValorTotal'];

    // Crear una nueva instancia de TCPDF
    $pdf = new TCPDF();
    $pdf->SetCreator('Nombre del Creador');
    $pdf->SetAuthor('Nombre del Autor');
    $pdf->SetTitle('Reporte de Venta');

    // Agregar una página
    $pdf->AddPage();
    $pdf->SetFont('helvetica', 'B', 20);

    // Agregar texto al PDF con los datos del usuario
    $pdf->Cell(0, 10, 'Reporte de Venta - ' . date('m/Y'), 0, 1, 'C');
    $pdf->Ln();
    $pdf->SetFont('helvetica', '', 12);
    $pdf->Cell(0, 10, 'Generado por el usuario: ' . $datosUsuario['NombreUsuario'], 0, 1, 'L');
    $pdf->Cell(0, 10, 'Correo: ' . $datosUsuario['Correo'], 0, 1, 'L');
    $pdf->Ln();
    // Mostrar información de la venta
    $pdf->Cell(0, 10, 'Datos de la venta', 0, 1, 'L');
    $pdf->SetFont('times', '', 11);
    $pdf->Cell(0, 10, 'Cliente: ' . $rowVenta['NombreCliente'], 0, 1);
    $pdf->Cell(0, 10, 'Dirección de envío: ' . $rowVenta['Direccion'], 0, 1);
    $pdf->Cell(0, 10, 'Fecha de la venta: ' . $rowVenta['FechaVenta'], 0, 1);
    $pdf->Cell(0, 10, 'Valor Total de la Venta: ' . $valorTotalVenta . ' Cop', 0, 1);

    $pdf->Write(10, 'Productos:');
    $pdf->Ln();
    
    $html = '<table border="1">';
    $html .= '<tr>';
    $html .= '<th>Producto</th>';
    $html .= '<th>Marca</th>';
    $html .= '<th>Presentación</th>';
    $html .= '<th>Cantidad</th>';
    $html .= '<th>Precio Unitario</th>';
    $html .= '<th>Valor Total</th>';
    $html .= '</tr>';

    while ($rowDetalleVenta = mysqli_fetch_assoc($resultDetallesVenta)) {
        $precioUnitario = $rowDetalleVenta['PrecioUnitario'] + ($rowDetalleVenta['PrecioUnitario'] * $rowDetalleVenta['PorcentajeBeneficio'] / 100);
        $valorTotal = $rowDetalleVenta['Cantidad'] * $precioUnitario;

        $html .= '<tr>';
        $html .= '<td>' . $rowDetalleVenta['NombreProducto'] . '</td>';
        $html .= '<td>' . $rowDetalleVenta['NombreMarca'] . '</td>';
        $html .= '<td>' . $rowDetalleVenta['NombrePresentacion'] . '</td>';
        $html .= '<td>' . $rowDetalleVenta['Cantidad'] . '</td>';
        $html .= '<td>' . $precioUnitario . ' Cop</td>';
        $html .= '<td>' . $valorTotal . ' Cop</td>';
        $html .= '</tr>';
    }
    $html .= '</table>';

    $pdf->writeHTML($html, true, false, true, false, '');

    // Salida del PDF
    $pdf->Output('reporte_venta_' . $ventaID . '.pdf', 'I');

    ob_end_clean(); // Limpiar el buffer de salida
} else {
    echo "<p>No se proporcionó un ID de venta.</p>";
}
?>
