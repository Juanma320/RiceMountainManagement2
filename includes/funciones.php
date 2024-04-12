<?php
// Archivo: includes/funciones.php

// Otras funciones...

// Archivo: funciones.php

// Función para verificar las credenciales del usuario
function verificarCredenciales($conexion, $usuario, $contrasena) {
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


// Resto de funciones...
function actualizarFechaUltimaActividad($conexion, $usuarioID) {
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


function obtenerDatosUsuario($conexion, $nombreUsuario) {
    $query = "SELECT UsuarioID, NombreUsuario, FotoPerfil, RolID, DocumentoIdentidad, Correo FROM Usuarios WHERE NombreUsuario = ?";
    $stmt = mysqli_prepare($conexion, $query);
    mysqli_stmt_bind_param($stmt, "s", $nombreUsuario);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $usuarioID, $nombreUsuario, $fotoPerfil, $rolID, $documentoIdentidad, $correo);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    $datosUsuario = [
        'UsuarioID' => $usuarioID,
        'NombreUsuario' => $nombreUsuario,
        'FotoPerfil' => $fotoPerfil,
        'RolID' => $rolID,
        'DocumentoIdentidad' => $documentoIdentidad,
        'Correo' => $correo
    ];

    return $datosUsuario;
}
function obtenerContrasenaUsuario($conexion, $nombreUsuario) {
    $query = "SELECT Contra FROM Usuarios WHERE NombreUsuario = ?";
    $stmt = mysqli_prepare($conexion, $query);
    mysqli_stmt_bind_param($stmt, "s", $nombreUsuario);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $contrasenaHash);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    return $contrasenaHash;
}


function inactivarUsuario($conexion, $usuarioID) {
    // Actualizar el estado del usuario a inactivo en la base de datos
    $query = "UPDATE Usuarios SET Activo = 0 WHERE UsuarioID = ?";
    $statement = mysqli_prepare($conexion, $query);
    mysqli_stmt_bind_param($statement, "i", $usuarioID);
    mysqli_stmt_execute($statement);
}

function activarUsuario($conexion, $usuarioID) {
    // Actualizar el estado del usuario a activo en la base de datos
    $query = "UPDATE Usuarios SET Activo = 1 WHERE UsuarioID = ?";
    $statement = mysqli_prepare($conexion, $query);
    mysqli_stmt_bind_param($statement, "i", $usuarioID);
    mysqli_stmt_execute($statement);
}

function borrarProducto($conexion, $productoID) {
    // Agrega las validaciones necesarias antes de realizar la operación de borrado

    $query = "DELETE FROM Productos WHERE ProductoID = ?";
    $statement = mysqli_prepare($conexion, $query);

    if ($statement) {
        mysqli_stmt_bind_param($statement, "i", $productoID);
        mysqli_stmt_execute($statement);
        mysqli_stmt_close($statement);

        return true; // Éxito en el borrado
    } else {
        return false; // Error en la preparación de la declaración
    }
}
function obtenerEstadoProveedor($conexion, $proveedorID) {
    $query = "SELECT Activo FROM Proveedores WHERE ProveedorID = $proveedorID";
    $resultado = mysqli_query($conexion, $query);

    if ($resultado && mysqli_num_rows($resultado) > 0) {
        $row = mysqli_fetch_assoc($resultado);
        return $row['Activo'];
    }

    return -1; // Error al obtener el estado
}

// ... Otras funciones ...

function cambiarEstadoProveedor($conexion, $proveedorID, $estadoActual) {
    // Verificar el estado actual y cambiarlo
    $nuevoEstado = ($estadoActual == 1) ? 0 : 1;

    // Actualizar el estado en la base de datos
    $query = "UPDATE Proveedores SET Activo = $nuevoEstado WHERE ProveedorID = $proveedorID";
    $resultado = mysqli_query($conexion, $query);

    // Verificar si la consulta fue exitosa
    if ($resultado) {
        return $nuevoEstado; // Devolver el nuevo estado
    } else {
        // Manejar error si la consulta falla
        // Puedes agregar código adicional aquí según tus necesidades
        return false;
    }
}


// Archivo: funciones.php

// ... (otras funciones)

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

function editarProveedor($conexion, $proveedorID, $nombre, $telefono, $correo, $contacto, $telefonoContacto, $nit) {
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


function obtenerProveedorPorID($conexion, $proveedorID) {
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

// ... (otras funciones)
function inactivarProveedor($conexion, $proveedorID) {
    // Actualizar el estado del usuario a inactivo en la base de datos
    $query = "UPDATE Proveedores SET Activo = 0 WHERE ProveedorID = ?";
    $statement = mysqli_prepare($conexion, $query);
    mysqli_stmt_bind_param($statement, "i", $proveedorID);
    mysqli_stmt_execute($statement);
}

function activarProveedor($conexion, $proveedorID) {
    // Actualizar el estado del usuario a activo en la base de datos
    $query = "UPDATE Proveedores SET Activo = 1 WHERE ProveedorID = ?";
    $statement = mysqli_prepare($conexion, $query);
    mysqli_stmt_bind_param($statement, "i", $proveedorID);
    mysqli_stmt_execute($statement);
}
// funciones.php


function retirarProductos($conexion, $productoProveedorID) {
    // Agrega las validaciones necesarias antes de realizar la operación de borrado

    // Verificar si la relación existe antes de intentar borrarla
    $queryExistencia = "SELECT * FROM proveedores_productos WHERE ProductoProveedorID = ?";
    $statementExistencia = mysqli_prepare($conexion, $queryExistencia);

    if ($statementExistencia) {
        mysqli_stmt_bind_param($statementExistencia, "i", $productoProveedorID);
        mysqli_stmt_execute($statementExistencia);
        mysqli_stmt_store_result($statementExistencia);

        if (mysqli_stmt_num_rows($statementExistencia) > 0) {
            // La relación existe, ahora procedemos con el borrado
            $queryBorrado = "DELETE FROM proveedores_productos WHERE ProductoProveedorID = ?";
            $statementBorrado = mysqli_prepare($conexion, $queryBorrado);

            if ($statementBorrado) {
                mysqli_stmt_bind_param($statementBorrado, "i", $productoProveedorID);
                mysqli_stmt_execute($statementBorrado);
                mysqli_stmt_close($statementBorrado);

                // Aquí es seguro cerrar la declaración de existencia
                mysqli_stmt_close($statementExistencia);

                return true; // Éxito en el borrado
            } else {
                mysqli_stmt_close($statementExistencia);
                return false; // Error en la preparación de la declaración de borrado
            }
        } else {
            mysqli_stmt_close($statementExistencia);
            return false; // La relación no existe, no se puede borrar
        }
    } else {
        return false; // Error en la preparación de la declaración de existencia
    }
}
function retirarProductoProveedor($conexion, $productoProveedorID) {
    // Agrega las validaciones necesarias antes de realizar la operación de borrado

    $query = "DELETE FROM Proveedores_Productos WHERE ProductoProveedorID = ?";
    $statement = mysqli_prepare($conexion, $query);

    if ($statement) {
        mysqli_stmt_bind_param($statement, "i", $productoProveedorID);
        mysqli_stmt_execute($statement);
        mysqli_stmt_close($statement);

        return true; // Éxito en el borrado
    } else {
        return false; // Error en la preparación de la declaración
    }
}
function borrarProveedores_Productos($conexion, $productoID) {
    // Agrega las validaciones necesarias antes de realizar la operación de borrado

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

// En funciones.php

function obtenerRolUsuario($conexion, $nombreUsuario) {
    $nombreUsuario = mysqli_real_escape_string($conexion, $nombreUsuario);

    $query = "SELECT U.RolID
              FROM Usuarios U
              WHERE U.NombreUsuario = '$nombreUsuario'";

    $result = mysqli_query($conexion, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        return $row['RolID'];
    } else {
        // Manejar el error o devolver un valor por defecto, dependiendo de tus necesidades
        return null;
    }
}
function obtenerProveedorID($conexion) {
    if (isset($_GET['proveedorID'])) {
        return mysqli_real_escape_string($conexion, $_GET['proveedorID']);
    } else {
        // Manejar el caso en el que no se proporcione un ID de proveedor
        return null;
    }
}
function obtenerDireccionesProveedor($conexion, $proveedorID) {
    $query = "SELECT * FROM direcciones WHERE ProveedorID = ?";
    $statement = mysqli_prepare($conexion, $query);
    mysqli_stmt_bind_param($statement, "i", $proveedorID);
    mysqli_stmt_execute($statement);
    $result = mysqli_stmt_get_result($statement);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

function agregarDireccionProveedor($conexion, $proveedorID, $direccion) {
    $query = "INSERT INTO direcciones (ProveedorID, Direccion) VALUES (?, ?)";
    $statement = mysqli_prepare($conexion, $query);
    mysqli_stmt_bind_param($statement, "is", $proveedorID, $direccion);
    mysqli_stmt_execute($statement);
}

function eliminarDireccionProveedor($conexion, $direccionID) {
    $query = "DELETE FROM direcciones WHERE DireccionID = ?";
    $statement = mysqli_prepare($conexion, $query);
    mysqli_stmt_bind_param($statement, "i", $direccionID);
    mysqli_stmt_execute($statement);
}

function eliminarDireccion($conexion, $direccionID) {
    $query = "DELETE FROM proveedores_direcciones WHERE ID = ?";
    $statement = mysqli_prepare($conexion, $query);
    mysqli_stmt_bind_param($statement, "i", $direccionID);
    mysqli_stmt_execute($statement);
    mysqli_stmt_close($statement);
}
function agregarDireccion($conexion, $proveedorID, $direccion, $ciudad, $codigoPostal) {
    $query = "INSERT INTO proveedores_direcciones (ProveedorID, Direccion, Ciudad, codigoPostal) VALUES (?, ?, ?, ?)";
    $statement = mysqli_prepare($conexion, $query);
    mysqli_stmt_bind_param($statement, "isss", $proveedorID, $direccion, $ciudad, $codigoPostal);
    mysqli_stmt_execute($statement);
    mysqli_stmt_close($statement);
}
function agregarMarcaProveedor($conexion, $proveedorID, $nombreMarca) {
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

function obtenerPrecioUnitario($conexion, $productoID) {
    $query = "SELECT PrecioUnitario FROM precio_compras WHERE ProductoID = $productoID ORDER BY FechaInicio DESC LIMIT 1";
    $resultado = mysqli_query($conexion, $query);
    if ($resultado && mysqli_num_rows($resultado) > 0) {
        $row = mysqli_fetch_assoc($resultado);
        return $row['PrecioUnitario'];
    } else {
        return null;
    }
}
function validarCambioEstadoVenta($conexion, $estadoActual, $estadoNuevo) {
    // Definir las transiciones de estado permitidas
    $transicionesValidas = [
        1 => [2, 4],
        2 => [3, 4],
        3 => [],
        4 => [3, 7],
        5 => [],
        6 => [3, 4],
        7 => [3, 4]
    ];

    return in_array($estadoNuevo, $transicionesValidas[$estadoActual]);
}
function obtenerNombreRol($conexion, $rolID) {
    $query = "SELECT NombreRol FROM roles WHERE RolID = ?";
    $statement = mysqli_prepare($conexion, $query);
    mysqli_stmt_bind_param($statement, "i", $rolID);
    mysqli_stmt_execute($statement);
    mysqli_stmt_bind_result($statement, $nombreRol);
    mysqli_stmt_fetch($statement);
    mysqli_stmt_close($statement);
    return $nombreRol;
}

?>


