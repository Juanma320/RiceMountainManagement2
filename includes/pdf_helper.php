<?php
function obtenerDatosUsuarioPDF($conexion, $nombreUsuario)
{
    $query = "SELECT UsuarioID, NombreUsuario, FotoPerfil, RolID, DocumentoIdentidad, Correo FROM Usuarios WHERE NombreUsuario = ?";
    $stmt = mysqli_prepare($conexion, $query);
    mysqli_stmt_bind_param($stmt, "s", $nombreUsuario);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $usuarioID, $nombreUsuarioResultado, $fotoPerfil, $rolID, $documentoIdentidad, $correo);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    $datosUsuario = [
        'UsuarioID' => $usuarioID,
        'NombreUsuario' => $nombreUsuarioResultado,
        'FotoPerfil' => $fotoPerfil,
        'RolID' => $rolID,
        'DocumentoIdentidad' => $documentoIdentidad,
        'Correo' => $correo
    ];

    return $datosUsuario;
}
function obtenerPrecioUnitarioPDF($conexion, $productoID)
{
    $query = "SELECT PrecioUnitario FROM precio_compras WHERE ProductoID = $productoID ORDER BY FechaInicio DESC LIMIT 1";
    $resultado = mysqli_query($conexion, $query);
    if ($resultado && mysqli_num_rows($resultado) > 0) {
        $row = mysqli_fetch_assoc($resultado);
        return $row['PrecioUnitario'];
    } else {
        return null;
    }
}