<?php
// Función para verificar las credenciales del usuario
function verificarCredenciales($conexion, $usuario, $contrasena)
{
    // Consulta SQL para obtener el usuario y la contraseña hasheada
    $query = "SELECT UsuarioID, NombreUsuario, Contra, RolID FROM Usuarios WHERE NombreUsuario = ? LIMIT 1";

    // Preparar la declaración SQL
    $statement = mysqli_prepare($conexion, $query);

    // Vincular parámetro
    mysqli_stmt_bind_param($statement, "s", $usuario);

    // Ejecutar la consulta
    mysqli_stmt_execute($statement);

    // Obtener el resultado de la consulta
    $result = mysqli_stmt_get_result($statement);

    // Verificar si se encontró el usuario
    if ($row = mysqli_fetch_assoc($result)) {
        // Verificar la contraseña hasheada
        if (password_verify($contrasena, $row['Contra'])) {
            // Contraseña correcta, actualizar la fecha de última actividad
            $usuarioID = $row['UsuarioID'];
            actualizarFechaUltimaActividad($conexion, $usuarioID);
            unset($row['Contra']); // No es necesario devolver la contraseña
            return $row; // Devolver los datos del usuario
        } else {
            return false; // Contraseña incorrecta
        }
    } else {
        // Usuario no encontrado
        return false;
    }
}

function actualizarFechaUltimaActividad($conexion, $usuarioID)
{
    // Obtener la fecha y hora actual
    $fechaHoraActual = date('Y-m-d H:i:s');

    // Consulta SQL para actualizar la fecha de última actividad
    $query = "UPDATE Usuarios SET FechaUltimaActividad = ? WHERE UsuarioID = ?";

    // Preparar la declaración SQL
    $statement = mysqli_prepare($conexion, $query);

    // Vincular parámetros
    mysqli_stmt_bind_param($statement, "si", $fechaHoraActual, $usuarioID);

    // Ejecutar la consulta
    mysqli_stmt_execute($statement);

    // Verificar si se actualizó correctamente
    if (mysqli_stmt_affected_rows($statement) > 0) {
        return true; // La fecha de última actividad se actualizó correctamente
    } else {
        return false; // No se pudo actualizar la fecha de última actividad
    }
}

function obtenerDatosUsuario($conexion, $usuarioID)
{
    $query = "SELECT UsuarioID, Nombre, NombreUsuario, FotoPerfil, RolID, DocumentoIdentidad, Correo FROM Usuarios WHERE UsuarioID = ?";
    $stmt = mysqli_prepare($conexion, $query);
    mysqli_stmt_bind_param($stmt, "i", $usuarioID);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $usuarioID, $nombre, $nombreUsuario, $fotoPerfil, $rolID, $documentoIdentidad, $correo);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    $datosUsuario = [
        'UsuarioID' => $usuarioID,
        'NombreUsuario' => $nombreUsuario,
        'FotoPerfil' => $fotoPerfil,
        'RolID' => $rolID,
        'DocumentoIdentidad' => $documentoIdentidad,
        'Correo' => $correo,
        'Nombre' => $nombre
    ];

    return $datosUsuario;
}

function obtenerContrasenaUsuario($conexion, $usuarioID)
{
    $query = "SELECT Contra FROM Usuarios WHERE UsuarioID = ?";
    $stmt = mysqli_prepare($conexion, $query);
    mysqli_stmt_bind_param($stmt, "i", $usuarioID); // Usamos "i" para el tipo de dato entero
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $contrasenaHash);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    return $contrasenaHash;
}

function inactivarUsuario($conexion, $usuarioID)
{
    // Actualizar el estado del usuario a inactivo en la base de datos
    $query = "UPDATE Usuarios SET Activo = 0 WHERE UsuarioID = ?";
    $statement = mysqli_prepare($conexion, $query);
    mysqli_stmt_bind_param($statement, "i", $usuarioID);
    mysqli_stmt_execute($statement);
}

function activarUsuario($conexion, $usuarioID)
{
    // Actualizar el estado del usuario a activo en la base de datos
    $query = "UPDATE Usuarios SET Activo = 1 WHERE UsuarioID = ?";
    $statement = mysqli_prepare($conexion, $query);
    mysqli_stmt_bind_param($statement, "i", $usuarioID);
    mysqli_stmt_execute($statement);
}

function agregarProveedor($conexion, $nombre, $correo, $telefono, $contacto, $telefonoContacto, $nit)
{
    // Validar y sanitizar los datos si es necesario
    $nombre = mysqli_real_escape_string($conexion, $nombre);
    $correo = mysqli_real_escape_string($conexion, $correo);
    $telefono = mysqli_real_escape_string($conexion, $telefono);
    $contacto = mysqli_real_escape_string($conexion, $contacto);
    $telefonoContacto = mysqli_real_escape_string($conexion, $telefonoContacto);
    $nit = mysqli_real_escape_string($conexion, $nit);

    // Definir el estado inicial del proveedor (activo)
    $activo = 1;

    // Utilizar una sentencia preparada para prevenir inyecciones SQL
    $query = "INSERT INTO proveedores (NombreProveedor, CorreoElectronico, Telefono, Contacto, TelefonoContacto, NIT, Activo) VALUES (?, ?, ?, ?, ?, ?, ?)";

    // Preparar la sentencia
    $stmt = mysqli_prepare($conexion, $query);

    // Vincular parámetros
    mysqli_stmt_bind_param($stmt, "ssssssi", $nombre, $correo, $telefono, $contacto, $telefonoContacto, $nit, $activo);

    // Ejecutar la sentencia
    $resultado = mysqli_stmt_execute($stmt);

    // Cerrar la sentencia preparada
    mysqli_stmt_close($stmt);

    return $resultado;
}

function editarProveedor($conexion, $proveedorID, $nombre, $telefono, $correo, $contacto, $telefonoContacto, $nit)
{
    // Validar y sanitizar los datos si es necesario
    $nombre = mysqli_real_escape_string($conexion, $nombre);
    $telefono = mysqli_real_escape_string($conexion, $telefono);
    $correo = mysqli_real_escape_string($conexion, $correo);
    $contacto = mysqli_real_escape_string($conexion, $contacto);
    $telefonoContacto = mysqli_real_escape_string($conexion, $telefonoContacto);
    $nit = mysqli_real_escape_string($conexion, $nit);

    // Consulta SQL para actualizar la información del proveedor
    $query = "UPDATE Proveedores SET NombreProveedor = '$nombre', Telefono = '$telefono', CorreoElectronico = '$correo', Contacto = '$contacto', TelefonoContacto = '$telefonoContacto', NIT = '$nit' WHERE ProveedorID = $proveedorID";

    // Ejecutar la consulta y verificar si fue exitosa
    return mysqli_query($conexion, $query);
}

function obtenerProveedorPorID($conexion, $proveedorID)
{
    // Validar y sanitizar el ID del proveedor
    $proveedorID = mysqli_real_escape_string($conexion, $proveedorID);

    // Consulta SQL para obtener la información del proveedor por su ID
    $query = "SELECT * FROM Proveedores WHERE ProveedorID = $proveedorID";

    // Ejecutar la consulta
    $resultado = mysqli_query($conexion, $query);

    // Verificar si se obtuvieron resultados
    if ($resultado && mysqli_num_rows($resultado) > 0) {
        // Obtener la fila como arreglo asociativo
        $proveedor = mysqli_fetch_assoc($resultado);

        // Liberar el resultado de la consulta
        mysqli_free_result($resultado);

        return $proveedor;
    } else {
        return false; // No se encontró el proveedor
    }
}

function inactivarProveedor($conexion, $proveedorID)
{
    // Actualizar el estado del usuario a inactivo en la base de datos
    $query = "UPDATE Proveedores SET Activo = 0 WHERE ProveedorID = ?";
    $statement = mysqli_prepare($conexion, $query);
    mysqli_stmt_bind_param($statement, "i", $proveedorID);
    mysqli_stmt_execute($statement);
}

function activarProveedor($conexion, $proveedorID)
{
    // Actualizar el estado del usuario a activo en la base de datos
    $query = "UPDATE Proveedores SET Activo = 1 WHERE ProveedorID = ?";
    $statement = mysqli_prepare($conexion, $query);
    mysqli_stmt_bind_param($statement, "i", $proveedorID);
    mysqli_stmt_execute($statement);
}

function borrarProveedores_Productos($conexion, $productoID)
{
    $query = "DELETE FROM Proveedores_Productos WHERE ProductoID = ?";
    $statement = mysqli_prepare($conexion, $query);

    if ($statement) {
        mysqli_stmt_bind_param($statement, "i", $productoID);
        mysqli_stmt_execute($statement);

        if (mysqli_stmt_affected_rows($statement) > 0) {
            mysqli_stmt_close($statement);
            return true; // Éxito en el borrado
        } else {
            echo "No se encontró el producto proveedor con el ID proporcionado.";
        }
    } else {
        echo "Error en la preparación de la declaración: " . mysqli_error($conexion);
    }

    return false; // Error en el borrado
}

function obtenerRolUsuario($conexion, $usuarioID)
{
    $query = "SELECT RolID FROM Usuarios WHERE UsuarioID = ?";
    $stmt = mysqli_prepare($conexion, $query);
    mysqli_stmt_bind_param($stmt, "i", $usuarioID);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $rolID);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    return $rolID;
}

function obtenerProveedorID($conexion)
{
    if (isset($_GET['proveedorID'])) {
        return mysqli_real_escape_string($conexion, $_GET['proveedorID']);
    } else {
        // Manejar el caso en el que no se proporcione un ID de proveedor
        return null;
    }
}

function eliminarDireccion($conexion, $direccionID)
{
    $query = "DELETE FROM proveedores_direcciones WHERE ID = ?";
    $statement = mysqli_prepare($conexion, $query);
    mysqli_stmt_bind_param($statement, "i", $direccionID);
    mysqli_stmt_execute($statement);
    mysqli_stmt_close($statement);
}

function agregarDireccion($conexion, $proveedorID, $direccion, $ciudad, $codigoPostal)
{
    $query = "INSERT INTO proveedores_direcciones (ProveedorID, Direccion, Ciudad, codigoPostal) VALUES (?, ?, ?, ?)";
    $statement = mysqli_prepare($conexion, $query);
    mysqli_stmt_bind_param($statement, "isss", $proveedorID, $direccion, $ciudad, $codigoPostal);
    mysqli_stmt_execute($statement);
    mysqli_stmt_close($statement);
}

function agregarMarcaProveedor($conexion, $proveedorID, $nombreMarca)
{
    $query = "INSERT INTO marcas (ProveedorID, NombreMarca) VALUES (?, ?)";
    $statement = mysqli_prepare($conexion, $query);
    mysqli_stmt_bind_param($statement, "is", $proveedorID, $nombreMarca);
    mysqli_stmt_execute($statement);
    mysqli_stmt_close($statement);
}

function agregarCliente($conexion, $nombre, $correoElectronico, $telefono, $nit, $telefonoEncargado, $coordinadorID, $nombreEncargado)
{
    // Validar y sanitizar los datos si es necesario
    $nombre = mysqli_real_escape_string($conexion, $nombre);
    $correoElectronico = mysqli_real_escape_string($conexion, $correoElectronico);
    $telefono = mysqli_real_escape_string($conexion, $telefono);
    $nit = mysqli_real_escape_string($conexion, $nit);
    $telefonoEncargado = mysqli_real_escape_string($conexion, $telefonoEncargado);
    $coordinadorID = ($coordinadorID !== 'null') ? mysqli_real_escape_string($conexion, $coordinadorID) : 'NULL';
    $nombreEncargado = mysqli_real_escape_string($conexion, $nombreEncargado);

    // Insertar los datos del cliente en la base de datos
    $queryInsert = "INSERT INTO Clientes (NombreCliente, CorreoElectronico, Telefono, NIT, TelefonoEncargado, CoordinadorID, NombreEncargado) VALUES ('$nombre', '$correoElectronico', '$telefono', '$nit', '$telefonoEncargado', $coordinadorID, '$nombreEncargado')";
    $resultadoInsert = mysqli_query($conexion, $queryInsert);

    return $resultadoInsert;
}

function obtenerPrecioUnitario($conexion, $productoID)
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

function obtenerNombreRol($conexion, $rolID)
{
    $query = "SELECT NombreRol FROM roles WHERE RolID = ?";
    $statement = mysqli_prepare($conexion, $query);
    mysqli_stmt_bind_param($statement, "i", $rolID);
    mysqli_stmt_execute($statement);
    mysqli_stmt_bind_result($statement, $nombreRol);
    mysqli_stmt_fetch($statement);
    mysqli_stmt_close($statement);
    return $nombreRol;
}