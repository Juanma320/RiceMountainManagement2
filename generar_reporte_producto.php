<?php
// Incluir la librería TCPDF
require_once('TCPDF/tcpdf.php');
include('includes/includes.php');
include('includes/pdf_helper.php');

// Obtener el nombre de usuario de la sesión
$nombreUsuario = $_SESSION['NombreUsuario'];

// Obtener los datos del usuario desde la base de datos
$datosUsuario = obtenerDatosUsuarioPDF($conexion, $nombreUsuario);

// Crear una nueva instancia de TCPDF
$pdf = new TCPDF();

// Establecer información del documento
$pdf->SetCreator('Nombre del Creador');
$pdf->SetAuthor('Nombre del Autor');
$pdf->SetTitle('Reporte de Usuario');

// Agregar una página
$pdf->AddPage();

// Configurar la fuente y el tamaño del texto
$pdf->SetFont('helvetica', 'B', 20);

// Agregar texto al PDF con los datos del usuario
$pdf->Cell(0, 10, 'Reporte de productos - ' . date('m/Y'), 0, 1, 'C');
$pdf->Ln();
$pdf->SetFont('helvetica', '', 12);
$pdf->Cell(0, 10, 'Generado por el usuario: ' . $datosUsuario['NombreUsuario'], 0, 1, 'L');
$pdf->Cell(0, 10, 'Correo: ' . $datosUsuario['Correo'], 0, 1, 'L');

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
$pdf->Ln();

// Tabla de Productos
$pdf->SetFont('times', '', 10); // Cambiar la fuente a times y el tamaño a 12
$pdf->Write(10, 'Productos:');
$pdf->Ln();


$html = '<table border="1">';
$html .= '<tr>';
$html .= '<th>Nombre Producto</th>';
$html .= '<th>Categoría</th>';
$html .= '<th>Presentación</th>';
$html .= '<th>Marca</th>';
$html .= '<th>Medida</th>';
$html .= '<th>Cantidad Final</th>';
$html .= '<th>Precio Unitario</th>';
$html .= '<th>% de Beneficio</th>';
$html .= '<th>Precio Final Unitario</th>';
$html .= '</tr>';

while ($row = mysqli_fetch_assoc($resultadoProductos)) {
    $precioUnitario = obtenerPrecioUnitarioPDF($conexion, $row['ProductoID']);
    $precioFinalUnitario = $precioUnitario * (1 + $row['PorcentajeBeneficio'] / 100);

    $html .= '<tr>';
    $html .= '<td>' . $row['NombreProducto'] . '</td>';
    $html .= '<td>' . $row['NombreCategoria'] . '</td>';
    $html .= '<td>' . $row['NombrePresentacion'] . '</td>';
    $html .= '<td>' . $row['NombreMarca'] . '</td>';
    $html .= '<td>' . $row['Medida'] . '</td>';
    $html .= '<td>' . $row['CantidadFinal'] . '</td>';
    $html .= '<td>' . $precioUnitario . ' Cop</td>';
    $html .= '<td>' . $row['PorcentajeBeneficio'] . '%</td>';
    $html .= '<td>' . $precioFinalUnitario . ' Cop</td>';
    $html .= '</tr>';
}
$html .= '</table>';

$pdf->writeHTML($html, true, false, true, false, '');
$pdf->AddPage();
// Tabla de Cambios de Precios Programados
$pdf->Ln();
$pdf->Ln();
$pdf->Write(10, 'Cambios de Precios Programados:');
$pdf->Ln();
$pdf->Ln();

$html = '<table border="1">';
$html .= '<tr>';
$html .= '<th>Producto</th>';
$html .= '<th>Marca</th>';
$html .= '<th>Categoría</th>';
$html .= '<th>Medida</th>';
$html .= '<th>Nuevo Precio</th>';
$html .= '<th>Fecha de Cambio</th>';
$html .= '</tr>';

while ($row = mysqli_fetch_assoc($resultadoCambiosPrecio)) {
    $html .= '<tr>';
    $html .= '<td>' . $row['NombreProducto'] . '</td>';
    $html .= '<td>' . $row['NombreMarca'] . '</td>';
    $html .= '<td>' . $row['NombreCategoria'] . '</td>';
    $html .= '<td>' . $row['Medida'] . '</td>';
    $html .= '<td>' . $row['NuevoPrecio'] . ' Cop</td>';
    $html .= '<td>' . $row['FechaFin'] . '</td>';
    $html .= '</tr>';
}
$html .= '</table>';

$pdf->writeHTML($html, true, false, true, false, '');

// Tabla de Cambios de Porcentaje Programados
$pdf->AddPage();
$pdf->Ln();
$pdf->Ln();
$pdf->Write(10, 'Cambios de Porcentaje Programados:');
$pdf->Ln();
$pdf->Ln();

$html = '<table border="1">';
$html .= '<tr>';
$html .= '<th>Producto</th>';
$html .= '<th>Marca</th>';
$html .= '<th>Categoría</th>';
$html .= '<th>Medida</th>';
$html .= '<th>Nuevo Porcentaje</th>';
$html .= '<th>Fecha de Cambio</th>';
$html .= '</tr>';

while ($row = mysqli_fetch_assoc($resultadoCambiosPorcentaje)) {
    $html .= '<tr>';
    $html .= '<td>' . $row['NombreProducto'] . '</td>';
    $html .= '<td>' . $row['NombreMarca'] . '</td>';
    $html .= '<td>' . $row['NombreCategoria'] . '</td>';
    $html .= '<td>' . $row['Medida'] . '</td>';
    $html .= '<td>' . $row['NuevoBeneficio'] . '%</td>';
    $html .= '<td>' . $row['FechaFinBeneficio'] . '</td>';
    $html .= '</tr>';
}
$html .= '</table>';

$pdf->writeHTML($html, true, false, true, false, '');

// Salida del PDF
$pdf->Output('reporte_productos.pdf', 'I');

ob_end_clean(); // Limpiar el buffer de salida
?>
