-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 14-05-2024 a las 07:11:13
-- Versión del servidor: 8.2.0
-- Versión de PHP: 8.2.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `mountaindb`
--

DELIMITER $$
--
-- Funciones
--
DROP FUNCTION IF EXISTS `actualizar_precios`$$
CREATE DEFINER=`root`@`localhost` FUNCTION `actualizar_precios` () RETURNS INT  BEGIN
UPDATE precio_compras SET PrecioUnitario = NuevoPrecio, NuevoPrecio = NULL, FechaInicio = FechaFin, FechaFin = NULL WHERE FechaFin IS NOT NULL AND FechaFin <= CURDATE();
RETURN 1;
END$$

DROP FUNCTION IF EXISTS `actualizar_precio_beneficio`$$
CREATE DEFINER=`root`@`localhost` FUNCTION `actualizar_precio_beneficio` () RETURNS INT  BEGIN
UPDATE precio_compras SET PorcentajeBeneficio = NuevoBeneficio, NuevoBeneficio = NULL, FechaInicioBeneficio = FechaFinBeneficio, FechaFinBeneficio = NULL WHERE FechaFinBeneficio IS NOT NULL AND FechaFinBeneficio <= CURDATE();
RETURN 1;
END$$

DROP FUNCTION IF EXISTS `archivar_ventas`$$
CREATE DEFINER=`root`@`localhost` FUNCTION `archivar_ventas` () RETURNS INT  BEGIN
    UPDATE ventas SET EstadoVentaID = 5 WHERE EstadoVentaID = 3;
    RETURN 1;
END$$

DROP FUNCTION IF EXISTS `retirar_compras_canceladas`$$
CREATE DEFINER=`root`@`localhost` FUNCTION `retirar_compras_canceladas` () RETURNS INT  BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE compra_id INT;
    DECLARE cur CURSOR FOR SELECT CompraID FROM compras WHERE EstadoCompraID = 2;
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

    OPEN cur;
    read_loop: LOOP
        FETCH cur INTO compra_id;
        IF done THEN
            LEAVE read_loop;
        END IF;

        DELETE FROM detalle_compra WHERE CompraID = compra_id;
        DELETE FROM compras WHERE CompraID = compra_id;
    END LOOP;
    CLOSE cur;

    RETURN 1;
END$$

DROP FUNCTION IF EXISTS `retirar_ventas_canceladas`$$
CREATE DEFINER=`root`@`localhost` FUNCTION `retirar_ventas_canceladas` () RETURNS INT  BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE detalles_venta_id, producto_id, cantidad_venta INT;
    DECLARE cur CURSOR FOR SELECT DetalleVentaID, ProductoID, Cantidad FROM detalles_venta WHERE VentaID IN (SELECT VentaID FROM ventas WHERE EstadoVentaID = 4);
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

    OPEN cur;
    read_loop: LOOP
        FETCH cur INTO detalles_venta_id, producto_id, cantidad_venta;
        IF done THEN
            LEAVE read_loop;
        END IF;

        UPDATE inventario_producto SET CantidadVendida = CantidadVendida - cantidad_venta WHERE ProductoID = producto_id;
    END LOOP;
    CLOSE cur;

    DELETE FROM detalles_venta WHERE VentaID IN (SELECT VentaID FROM ventas WHERE EstadoVentaID = 4);
    DELETE FROM ventas WHERE EstadoVentaID = 4;

    RETURN 1;
END$$

DROP FUNCTION IF EXISTS `sumar_cantidad_comprada`$$
CREATE DEFINER=`root`@`localhost` FUNCTION `sumar_cantidad_comprada` () RETURNS INT  BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE detalle_compra_id, producto_id, cantidad_compra INT;
    DECLARE cur CURSOR FOR SELECT DetalleCompraID, ProductoID, Cantidad FROM detalle_compra WHERE CompraID IN (SELECT CompraID FROM compras WHERE EstadoCompraID = 3);
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

    OPEN cur;
    read_loop: LOOP
        FETCH cur INTO detalle_compra_id, producto_id, cantidad_compra;
        IF done THEN
            LEAVE read_loop;
        END IF;

        UPDATE inventario_producto SET CantidadComprada = CantidadComprada + cantidad_compra WHERE ProductoID = producto_id;
    END LOOP;
    CLOSE cur;

    UPDATE compras SET EstadoCompraID = 4 WHERE EstadoCompraID = 3;

    RETURN 1;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

DROP TABLE IF EXISTS `categorias`;
CREATE TABLE IF NOT EXISTS `categorias` (
  `CategoriaID` int NOT NULL AUTO_INCREMENT,
  `NombreCategoria` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`CategoriaID`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`CategoriaID`, `NombreCategoria`) VALUES
(1, 'cereales'),
(2, 'jabones'),
(3, 'Atunes'),
(4, 'Verduras enlatadas'),
(5, 'Otros');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

DROP TABLE IF EXISTS `clientes`;
CREATE TABLE IF NOT EXISTS `clientes` (
  `ClienteID` int NOT NULL AUTO_INCREMENT,
  `NombreCliente` varchar(200) DEFAULT NULL,
  `CorreoElectronico` varchar(200) DEFAULT NULL,
  `Telefono` varchar(20) DEFAULT NULL,
  `NIT` varchar(20) DEFAULT NULL,
  `TelefonoEncargado` varchar(20) DEFAULT NULL,
  `CoordinadorID` int DEFAULT NULL,
  `NombreEncargado` varchar(200) DEFAULT NULL,
  `UltimaAsignacion` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`ClienteID`),
  UNIQUE KEY `CorreoElectronico` (`CorreoElectronico`),
  KEY `CoordinadorID` (`CoordinadorID`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`ClienteID`, `NombreCliente`, `CorreoElectronico`, `Telefono`, `NIT`, `TelefonoEncargado`, `CoordinadorID`, `NombreEncargado`, `UltimaAsignacion`) VALUES
(1, 'EMPRESAS H', 'ArmonyTeam@hotmail.com', '332123231', '10032231', '222-222-2222', 10, 'Macanaochi ipo', '2024-05-10 08:38:54'),
(2, 'EMPRESA H', 'empresaA@example.com', '111-111-1111', '1321312', '1231231312', 4, 'Emiliano zapata', '2024-05-10 08:38:54'),
(3, 'EMPRESA B', 'empresaB@example.com', '222-222-2222', NULL, NULL, 2, 'Nombre del Encargado B', '2024-05-10 08:38:54'),
(4, 'EMPRESA C', 'empresaC@example.com', '333-333-3333', NULL, NULL, 2, 'Nombre del Encargado C', '2024-05-10 08:38:54'),
(5, 'EMPRESAS HARG', 's@s', '23123', '213123', '12312313123', 12, 'dddd', '2024-05-10 08:38:54');

--
-- Disparadores `clientes`
--
DROP TRIGGER IF EXISTS `before_update_coordinador`;
DELIMITER $$
CREATE TRIGGER `before_update_coordinador` BEFORE UPDATE ON `clientes` FOR EACH ROW BEGIN
    -- Actualizar la columna UltimaAsignacion con la fecha actual
    SET NEW.UltimaAsignacion = NOW();
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `trg_Unique_CorreoElectronico_client`;
DELIMITER $$
CREATE TRIGGER `trg_Unique_CorreoElectronico_client` BEFORE INSERT ON `clientes` FOR EACH ROW BEGIN
    IF EXISTS (
        SELECT 1
        FROM clientes
        WHERE CorreoElectronico = NEW.CorreoElectronico
    ) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Ya existe otro cliente con el mismo correo electrónico.';
    END IF;
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `trg_Unique_NIT_client`;
DELIMITER $$
CREATE TRIGGER `trg_Unique_NIT_client` BEFORE INSERT ON `clientes` FOR EACH ROW BEGIN
    IF EXISTS (
        SELECT 1
        FROM clientes
        WHERE NIT = NEW.NIT
    ) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Ya existe otro cliente con el mismo NIT.';
    END IF;
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `trg_Unique_NombreCliente`;
DELIMITER $$
CREATE TRIGGER `trg_Unique_NombreCliente` BEFORE INSERT ON `clientes` FOR EACH ROW BEGIN
    IF EXISTS (
        SELECT 1
        FROM clientes
        WHERE NombreCliente = NEW.NombreCliente
    ) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Ya existe otro cliente con el mismo nombre.';
    END IF;
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `trg_Unique_TelefonoEncargado_client`;
DELIMITER $$
CREATE TRIGGER `trg_Unique_TelefonoEncargado_client` BEFORE INSERT ON `clientes` FOR EACH ROW BEGIN
    IF EXISTS (
        SELECT 1
        FROM clientes
        WHERE TelefonoEncargado = NEW.TelefonoEncargado
    ) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Ya existe otro cliente con el mismo número de teléfono del encargado.';
    END IF;
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `trg_Unique_Telefono_client`;
DELIMITER $$
CREATE TRIGGER `trg_Unique_Telefono_client` BEFORE INSERT ON `clientes` FOR EACH ROW BEGIN
    IF EXISTS (
        SELECT 1
        FROM clientes
        WHERE Telefono = NEW.Telefono
    ) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Ya existe otro cliente con el mismo número de teléfono.';
    END IF;
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `trg_Unique_Update_CorreoElectronico_client`;
DELIMITER $$
CREATE TRIGGER `trg_Unique_Update_CorreoElectronico_client` BEFORE UPDATE ON `clientes` FOR EACH ROW BEGIN
    IF EXISTS (
        SELECT 1
        FROM clientes
        WHERE CorreoElectronico = NEW.CorreoElectronico AND ClienteID != NEW.ClienteID
    ) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Ya existe otro cliente con el mismo correo electrónico.';
    END IF;
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `trg_Unique_Update_NIT_client`;
DELIMITER $$
CREATE TRIGGER `trg_Unique_Update_NIT_client` BEFORE UPDATE ON `clientes` FOR EACH ROW BEGIN
    IF EXISTS (
        SELECT 1
        FROM clientes
        WHERE NIT = NEW.NIT AND ClienteID != NEW.ClienteID
    ) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Ya existe otro cliente con el mismo NIT.';
    END IF;
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `trg_Unique_Update_NombreCliente`;
DELIMITER $$
CREATE TRIGGER `trg_Unique_Update_NombreCliente` BEFORE UPDATE ON `clientes` FOR EACH ROW BEGIN
    IF EXISTS (
        SELECT 1
        FROM clientes
        WHERE NombreCliente = NEW.NombreCliente AND ClienteID != NEW.ClienteID
    ) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Ya existe otro cliente con el mismo nombre.';
    END IF;
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `trg_Unique_Update_TelefonoEncargado_client`;
DELIMITER $$
CREATE TRIGGER `trg_Unique_Update_TelefonoEncargado_client` BEFORE UPDATE ON `clientes` FOR EACH ROW BEGIN
    IF EXISTS (
        SELECT 1
        FROM clientes
        WHERE TelefonoEncargado = NEW.TelefonoEncargado AND ClienteID != NEW.ClienteID
    ) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Ya existe otro cliente con el mismo número de teléfono del encargado.';
    END IF;
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `trg_Unique_Update_Telefono_client`;
DELIMITER $$
CREATE TRIGGER `trg_Unique_Update_Telefono_client` BEFORE UPDATE ON `clientes` FOR EACH ROW BEGIN
    IF EXISTS (
        SELECT 1
        FROM clientes
        WHERE Telefono = NEW.Telefono AND ClienteID != NEW.ClienteID
    ) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Ya existe otro cliente con el mismo número de teléfono.';
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `compras`
--

DROP TABLE IF EXISTS `compras`;
CREATE TABLE IF NOT EXISTS `compras` (
  `CompraID` int NOT NULL AUTO_INCREMENT,
  `FechaCompra` date NOT NULL,
  `ProveedorID` int NOT NULL,
  `FechaCreacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ValorCompra` decimal(10,2) NOT NULL,
  `UsuarioID` int NOT NULL,
  `EstadoCompraID` int NOT NULL,
  PRIMARY KEY (`CompraID`),
  KEY `ProveedorID` (`ProveedorID`),
  KEY `UsuarioID` (`UsuarioID`),
  KEY `idx_estado_id` (`EstadoCompraID`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `compras`
--

INSERT INTO `compras` (`CompraID`, `FechaCompra`, `ProveedorID`, `FechaCreacion`, `ValorCompra`, `UsuarioID`, `EstadoCompraID`) VALUES
(1, '2024-04-02', 1, '2024-03-31 21:17:12', 0.00, 1, 4),
(2, '2024-04-02', 1, '2024-03-31 21:31:38', 0.00, 1, 4),
(4, '2024-02-27', 1, '2024-03-31 23:13:37', 0.00, 1, 4),
(6, '2024-04-04', 1, '2024-04-01 03:38:13', 0.00, 1, 5),
(7, '2024-03-13', 1, '2024-04-01 03:53:02', 0.00, 1, 4),
(9, '2024-04-01', 1, '2024-04-02 04:47:01', 0.00, 1, 5),
(10, '2024-04-05', 1, '2024-04-05 02:20:39', 44000.00, 1, 4),
(11, '2024-04-20', 1, '2024-04-05 04:05:00', 122000.00, 1, 4),
(12, '2024-04-17', 1, '2024-04-08 06:42:59', 30000.00, 1, 5),
(13, '2024-04-12', 1, '2024-04-12 02:08:39', 44000.00, 1, 5),
(14, '2024-04-20', 1, '2024-04-17 04:38:42', 66000.00, 1, 5),
(15, '2024-04-29', 4, '2024-04-27 21:22:15', 0.00, 1, 5),
(16, '2024-05-08', 1, '2024-04-27 21:22:52', 66000.00, 1, 4),
(17, '2024-05-01', 1, '2024-04-27 21:36:47', 0.00, 1, 5),
(18, '2024-05-01', 4, '2024-05-03 04:58:39', 0.00, 1, 5),
(19, '2024-05-08', 1, '2024-05-08 02:07:45', 0.00, 1, 5),
(20, '2024-05-08', 1, '2024-05-08 02:20:42', 0.00, 1, 5),
(21, '2024-05-16', 1, '2024-05-08 02:20:49', 0.00, 1, 2),
(22, '2024-05-08', 1, '2024-05-08 02:21:28', 0.00, 1, 5),
(23, '2024-05-15', 1, '2024-05-14 05:32:51', 24000.00, 1, 5);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalles_venta`
--

DROP TABLE IF EXISTS `detalles_venta`;
CREATE TABLE IF NOT EXISTS `detalles_venta` (
  `DetalleVentaID` int NOT NULL AUTO_INCREMENT,
  `VentaID` int NOT NULL,
  `ProductoID` int NOT NULL,
  `Cantidad` int NOT NULL,
  `Valor` decimal(10,2) NOT NULL,
  PRIMARY KEY (`DetalleVentaID`),
  KEY `VentaID` (`VentaID`),
  KEY `ProductoID` (`ProductoID`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `detalles_venta`
--

INSERT INTO `detalles_venta` (`DetalleVentaID`, `VentaID`, `ProductoID`, `Cantidad`, `Valor`) VALUES
(12, 4, 1, 24, 2300.00),
(13, 9, 1, 20, 2300.00),
(14, 11, 1, 22, 2300.00),
(15, 3, 1, 130, 2300.00),
(17, 14, 2, 2, 3300.00),
(18, 14, 2, 2, 3300.00),
(21, 15, 2, 23, 3300.00),
(26, 16, 1, 11, 2200.00),
(28, 20, 1, 9, 2200.00),
(29, 22, 1, 4, 2200.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_compra`
--

DROP TABLE IF EXISTS `detalle_compra`;
CREATE TABLE IF NOT EXISTS `detalle_compra` (
  `DetalleCompraID` int NOT NULL AUTO_INCREMENT,
  `CompraID` int NOT NULL,
  `ProductoID` int NOT NULL,
  `Cantidad` int NOT NULL,
  `Valor` decimal(10,2) NOT NULL,
  PRIMARY KEY (`DetalleCompraID`),
  KEY `CompraID` (`CompraID`),
  KEY `ProductoID` (`ProductoID`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `detalle_compra`
--

INSERT INTO `detalle_compra` (`DetalleCompraID`, `CompraID`, `ProductoID`, `Cantidad`, `Valor`) VALUES
(11, 10, 1, 22, 44000.00),
(12, 11, 1, 22, 44000.00),
(13, 11, 2, 26, 78000.00),
(14, 12, 1, 15, 30000.00),
(15, 13, 1, 22, 44000.00),
(16, 14, 2, 22, 66000.00),
(17, 16, 1, 33, 66000.00),
(18, 23, 1, 12, 24000.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `direcciones_clientes`
--

DROP TABLE IF EXISTS `direcciones_clientes`;
CREATE TABLE IF NOT EXISTS `direcciones_clientes` (
  `DireccionID` int NOT NULL AUTO_INCREMENT,
  `ClienteID` int NOT NULL,
  `Direccion` varchar(200) DEFAULT NULL,
  `CodigoPostal` int NOT NULL,
  `Ciudad` varchar(255) NOT NULL,
  PRIMARY KEY (`DireccionID`),
  KEY `ClienteID` (`ClienteID`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `direcciones_clientes`
--

INSERT INTO `direcciones_clientes` (`DireccionID`, `ClienteID`, `Direccion`, `CodigoPostal`, `Ciudad`) VALUES
(1, 1, 'calle 20#16 69', 0, ''),
(2, 1, 'calle 20#8 77', 0, ''),
(3, 2, 'Calle Arroz #123', 0, ''),
(4, 3, 'calle 9#16-1A', 0, ''),
(5, 4, 'calle 10#19-2B', 0, '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estado_compra`
--

DROP TABLE IF EXISTS `estado_compra`;
CREATE TABLE IF NOT EXISTS `estado_compra` (
  `EstadoCompraID` int NOT NULL AUTO_INCREMENT,
  `NombreEstado` varchar(50) NOT NULL,
  PRIMARY KEY (`EstadoCompraID`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `estado_compra`
--

INSERT INTO `estado_compra` (`EstadoCompraID`, `NombreEstado`) VALUES
(1, 'Pedido Enviado'),
(2, 'Pedido Cancelado'),
(3, 'Pedido Recibido'),
(4, 'Archivado'),
(5, 'Pedido creado');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estado_venta`
--

DROP TABLE IF EXISTS `estado_venta`;
CREATE TABLE IF NOT EXISTS `estado_venta` (
  `EstadoVentaID` int NOT NULL AUTO_INCREMENT,
  `NombreEstado` varchar(50) NOT NULL,
  PRIMARY KEY (`EstadoVentaID`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `estado_venta`
--

INSERT INTO `estado_venta` (`EstadoVentaID`, `NombreEstado`) VALUES
(1, 'creada'),
(2, 'en proceso'),
(3, 'realizada'),
(4, 'cancelada'),
(5, 'archivada'),
(6, 'Retraso');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `intentos_login`
--

DROP TABLE IF EXISTS `intentos_login`;
CREATE TABLE IF NOT EXISTS `intentos_login` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ip` varchar(45) NOT NULL,
  `hora` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=55 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `intentos_login`
--

INSERT INTO `intentos_login` (`id`, `ip`, `hora`) VALUES
(54, '::1', 1715668870),
(53, '::1', 1715636259),
(52, '::1', 1715636255),
(51, '::1', 1715636251),
(50, '::1', 1715636174),
(49, '::1', 1715636170),
(48, '::1', 1715636166),
(47, '::1', 1715635960),
(46, '::1', 1715635955),
(45, '::1', 1715635951),
(44, '::1', 1715635946),
(43, '::1', 1715635859),
(42, '::1', 1715635850),
(41, '::1', 1715635845),
(40, '::1', 1715635827),
(39, '::1', 1715635667),
(38, '::1', 1715635663),
(37, '::1', 1715635659),
(36, '::1', 1715635453),
(26, '::1', 1715634030),
(27, '::1', 1715634175),
(28, '::1', 1715634181),
(29, '::1', 1715634721),
(30, '::1', 1715635084),
(31, '::1', 1715635122),
(32, '::1', 1715635238),
(33, '::1', 1715635242),
(34, '::1', 1715635246),
(35, '::1', 1715635251);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inventario_producto`
--

DROP TABLE IF EXISTS `inventario_producto`;
CREATE TABLE IF NOT EXISTS `inventario_producto` (
  `InventarioID` int NOT NULL AUTO_INCREMENT,
  `ProductoID` int NOT NULL,
  `FechaInicial` date NOT NULL,
  `FechaFinal` date NOT NULL,
  `CantidadComprada` int NOT NULL DEFAULT '0',
  `CantidadInicial` int NOT NULL DEFAULT '0',
  `CantidadVendida` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`InventarioID`),
  KEY `ProductoID` (`ProductoID`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `inventario_producto`
--

INSERT INTO `inventario_producto` (`InventarioID`, `ProductoID`, `FechaInicial`, `FechaFinal`, `CantidadComprada`, `CantidadInicial`, `CantidadVendida`) VALUES
(1, 1, '2024-01-01', '0000-00-00', 177, 51, 174),
(2, 2, '2024-01-01', '0000-00-00', 226, 100, 112),
(3, 3, '2024-01-01', '0000-00-00', 150, 80, 30),
(5, 13, '2024-03-11', '0000-00-00', 0, 0, 0),
(6, 14, '2024-03-19', '0000-00-00', 0, 12, 0),
(8, 16, '2024-04-27', '0000-00-00', 0, 0, 0),
(9, 17, '2024-05-08', '0000-00-00', 0, 0, 0),
(10, 18, '2024-05-08', '0000-00-00', 0, 0, 0),
(11, 19, '2024-05-08', '0000-00-00', 0, 0, 0),
(12, 20, '2024-05-08', '0000-00-00', 0, 0, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `marcas`
--

DROP TABLE IF EXISTS `marcas`;
CREATE TABLE IF NOT EXISTS `marcas` (
  `MarcaID` int NOT NULL AUTO_INCREMENT,
  `NombreMarca` varchar(200) DEFAULT NULL,
  `ProveedorID` int NOT NULL,
  `Estado` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`MarcaID`),
  KEY `fk_proveedor` (`ProveedorID`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `marcas`
--

INSERT INTO `marcas` (`MarcaID`, `NombreMarca`, `ProveedorID`, `Estado`) VALUES
(1, 'Reyna', 1, 1),
(2, 'Simamon', 1, 1),
(3, 'Diana', 2, 1),
(4, 'vancam', 2, 1),
(5, 'Roa', 3, 1),
(6, 'Marinela', 1, 1),
(10, 'Manancoli', 4, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `precio_compras`
--

DROP TABLE IF EXISTS `precio_compras`;
CREATE TABLE IF NOT EXISTS `precio_compras` (
  `PrecioID` int NOT NULL AUTO_INCREMENT,
  `ProductoID` int NOT NULL,
  `PrecioUnitario` decimal(10,2) NOT NULL,
  `NuevoPrecio` decimal(10,2) DEFAULT NULL,
  `FechaInicio` date NOT NULL,
  `FechaFin` date DEFAULT NULL,
  `PorcentajeBeneficio` decimal(5,2) DEFAULT NULL,
  `FechaInicioBeneficio` date NOT NULL,
  `FechaFinBeneficio` date DEFAULT NULL,
  `NuevoBeneficio` decimal(5,2) DEFAULT NULL,
  PRIMARY KEY (`PrecioID`),
  KEY `ProductoID` (`ProductoID`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `precio_compras`
--

INSERT INTO `precio_compras` (`PrecioID`, `ProductoID`, `PrecioUnitario`, `NuevoPrecio`, `FechaInicio`, `FechaFin`, `PorcentajeBeneficio`, `FechaInicioBeneficio`, `FechaFinBeneficio`, `NuevoBeneficio`) VALUES
(1, 1, 2000.00, 11111.00, '0000-00-00', '2024-04-20', 10.00, '2024-04-06', '2024-04-19', 10.00),
(2, 2, 3000.00, 8888.00, '0000-00-00', '2024-04-27', 10.00, '2024-04-04', '2024-05-02', 10.00),
(3, 3, 3333.00, NULL, '0000-00-00', NULL, 10.00, '0000-00-00', NULL, NULL),
(4, 13, 5001.00, NULL, '2024-04-08', NULL, 16.70, '2024-04-05', NULL, NULL),
(5, 14, 2090.00, 666.00, '2024-04-08', '2024-05-01', 29.00, '0000-00-00', NULL, NULL),
(7, 16, 800.00, NULL, '2024-04-27', NULL, 20.00, '0000-00-00', NULL, NULL),
(8, 17, 1900.00, NULL, '2024-05-08', NULL, 20.00, '0000-00-00', NULL, NULL),
(9, 18, 1000.00, NULL, '2024-05-08', NULL, 30.00, '0000-00-00', NULL, NULL),
(10, 19, 2000.00, NULL, '2024-05-08', NULL, 30.00, '0000-00-00', NULL, NULL),
(11, 20, 2000.00, NULL, '2024-05-08', NULL, 20.00, '0000-00-00', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `presentaciones`
--

DROP TABLE IF EXISTS `presentaciones`;
CREATE TABLE IF NOT EXISTS `presentaciones` (
  `PresentacionID` int NOT NULL AUTO_INCREMENT,
  `NombrePresentacion` varchar(200) DEFAULT NULL,
  `Medida` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`PresentacionID`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `presentaciones`
--

INSERT INTO `presentaciones` (`PresentacionID`, `NombrePresentacion`, `Medida`) VALUES
(1, 'Media', '500g'),
(2, 'Bolsa', '1000g'),
(3, 'unidad', '1'),
(4, 'paquete', '6'),
(5, 'Paca', '2000g'),
(13, 'payaringas', '22ml');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

DROP TABLE IF EXISTS `productos`;
CREATE TABLE IF NOT EXISTS `productos` (
  `ProductoID` int NOT NULL AUTO_INCREMENT,
  `NombreProducto` varchar(200) DEFAULT NULL,
  `FinancieroID` int DEFAULT NULL,
  `FechaCreacion` datetime DEFAULT NULL,
  `Activo` tinyint(1) NOT NULL DEFAULT '1',
  `PresentacionID` int DEFAULT NULL,
  `CategoriaID` int DEFAULT NULL,
  `MarcaID` int DEFAULT NULL,
  PRIMARY KEY (`ProductoID`),
  KEY `FinancieroID` (`FinancieroID`) USING BTREE,
  KEY `FK_Productos_Presentacion` (`PresentacionID`),
  KEY `FK_Productos_Categoria` (`CategoriaID`),
  KEY `FK_Productos_Marca` (`MarcaID`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`ProductoID`, `NombreProducto`, `FinancieroID`, `FechaCreacion`, `Activo`, `PresentacionID`, `CategoriaID`, `MarcaID`) VALUES
(1, 'ARROZ', 3, '2024-01-31 08:00:00', 1, 1, 1, 1),
(2, 'JABON', 1, '2024-03-05 23:46:57', 1, 3, 5, 2),
(3, 'PAPAS', 1, '2024-03-05 23:55:23', 1, 3, 5, 5),
(13, 'ARROZ', 1, '2024-03-11 03:44:29', 1, 5, 1, 5),
(14, 'O´S', 1, '2024-03-19 03:25:34', 1, 2, 1, 5),
(16, 'GALLETAS', 1, '2024-04-27 16:33:15', 1, 4, 5, 10),
(17, 'ARROZ', 1, '2024-05-08 00:13:48', 1, 2, 1, 1),
(18, 'TRIGO', 1, '2024-05-08 00:15:23', 1, 1, 1, 1),
(19, 'LENTEJAS', 1, '2024-05-08 01:13:07', 1, 3, 4, 5),
(20, 'ESPAGUETIS', 1, '2024-05-08 03:31:27', 1, 4, 5, 10);

--
-- Disparadores `productos`
--
DROP TRIGGER IF EXISTS `trg_Unique_Product_Insert`;
DELIMITER $$
CREATE TRIGGER `trg_Unique_Product_Insert` BEFORE INSERT ON `productos` FOR EACH ROW BEGIN
    /* Condicional que verifica si ya existe un producto con los mismos valores en los campos NombreProducto, PresentacionID, CategoriaID y MarcaID*/
    IF EXISTS (
        SELECT 1
        FROM productos
        WHERE NombreProducto = NEW.NombreProducto
        AND PresentacionID = NEW.PresentacionID
        AND CategoriaID = NEW.CategoriaID
        AND MarcaID = NEW.MarcaID
    ) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Ya existe un producto con los mismos detalles.';
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedores`
--

DROP TABLE IF EXISTS `proveedores`;
CREATE TABLE IF NOT EXISTS `proveedores` (
  `ProveedorID` int NOT NULL AUTO_INCREMENT,
  `NombreProveedor` varchar(200) DEFAULT NULL,
  `CorreoElectronico` varchar(200) DEFAULT NULL,
  `Telefono` varchar(20) DEFAULT NULL,
  `Activo` tinyint NOT NULL DEFAULT '1',
  `Contacto` varchar(200) DEFAULT NULL,
  `TelefonoContacto` varchar(20) DEFAULT NULL,
  `NIT` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`ProveedorID`),
  UNIQUE KEY `CorreoElectronico` (`CorreoElectronico`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `proveedores`
--

INSERT INTO `proveedores` (`ProveedorID`, `NombreProveedor`, `CorreoElectronico`, `Telefono`, `Activo`, `Contacto`, `TelefonoContacto`, `NIT`) VALUES
(1, 'DISTRIBUIDORA ARROCES', 'arroces@example.com', '123-456-78903', 1, 'Juan Pérez', '320242517', '123456789-0'),
(2, 'DISTRIBUIDORA CONDIMENTOS', 'condimentos@example.com', '987-654-3210', 1, 'María López', '987-654-3210', '987654321-0'),
(3, 'DISTRIBUIDORA LÁCTEOS', 'lacteos@example.com', '456-789-0123', 1, 'Pedro Ramírez', '456-789-0123', '456789012-0'),
(4, 'ASDGCKAN131231', 'nadsadad@example.com', '31457563213123', 1, 'nocsdffd', '33333312312', '4567890120'),
(5, 'DISTRIBUIDORA ARROCES2', 'arroces2@example.com', '123-456-78902', 1, 'Juan Pérez', '123-456-78902', '123456789-02'),
(6, 'DISTRIBUIDORA ARROCES23', 'arroces@example.com23', '123-456-7890', 1, 'Juan Pérez', '3333333333', '123456789-023'),
(7, 'Distribuidora Atunes', 'Atunes@example.com', '123-456-78908', 1, 'Juan Molina', '3138766565', '1231231243'),
(8, 'Distribuidora A', 'arrocesA@example.com', '123-456-7890333', 1, 'Juan Topo', '33333333334433', '10032231333'),
(9, 'EXITO', 'Eito@example.com', '31333333', 1, 'Maria Cano', '31389888', '1223334565'),
(11, 'DISTRIBUIDORES BROCOLY', 'BrocolyCorp@corpation.com', '3138763635', 1, 'Harvy Arce', '313877777', '1003807222');

--
-- Disparadores `proveedores`
--
DROP TRIGGER IF EXISTS `trg_Unique_CorreoElectronico`;
DELIMITER $$
CREATE TRIGGER `trg_Unique_CorreoElectronico` BEFORE INSERT ON `proveedores` FOR EACH ROW BEGIN
    IF EXISTS (
        SELECT 1
        FROM proveedores
        WHERE CorreoElectronico = NEW.CorreoElectronico
    ) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Ya existe otro proveedor con el mismo correo electrónico.';
    END IF;
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `trg_Unique_NIT`;
DELIMITER $$
CREATE TRIGGER `trg_Unique_NIT` BEFORE INSERT ON `proveedores` FOR EACH ROW BEGIN
    IF EXISTS (
        SELECT 1
        FROM proveedores
        WHERE NIT = NEW.NIT
    ) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Ya existe otro proveedor con el mismo NIT.';
    END IF;
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `trg_Unique_NombreProveedor`;
DELIMITER $$
CREATE TRIGGER `trg_Unique_NombreProveedor` BEFORE INSERT ON `proveedores` FOR EACH ROW BEGIN
    IF EXISTS (
        SELECT 1
        FROM proveedores
        WHERE NombreProveedor = NEW.NombreProveedor
    ) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Ya existe otro proveedor con el mismo nombre.';
    END IF;
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `trg_Unique_Telefono`;
DELIMITER $$
CREATE TRIGGER `trg_Unique_Telefono` BEFORE INSERT ON `proveedores` FOR EACH ROW BEGIN
    IF EXISTS (
        SELECT 1
        FROM proveedores
        WHERE Telefono = NEW.Telefono
    ) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Ya existe otro proveedor con el mismo teléfono.';
    END IF;
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `trg_Unique_TelefonoContacto`;
DELIMITER $$
CREATE TRIGGER `trg_Unique_TelefonoContacto` BEFORE INSERT ON `proveedores` FOR EACH ROW BEGIN
    IF EXISTS (
        SELECT 1
        FROM proveedores
        WHERE TelefonoContacto = NEW.TelefonoContacto
    ) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Ya existe otro proveedor con el mismo teléfono de contacto.';
    END IF;
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `trg_Unique_Update_CorreoElectronico`;
DELIMITER $$
CREATE TRIGGER `trg_Unique_Update_CorreoElectronico` BEFORE UPDATE ON `proveedores` FOR EACH ROW BEGIN
    IF EXISTS (
        SELECT 1
        FROM proveedores
        WHERE CorreoElectronico = NEW.CorreoElectronico AND ProveedorID != NEW.ProveedorID
    ) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Ya existe otro proveedor con el mismo correo electrónico.';
    END IF;
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `trg_Unique_Update_NIT`;
DELIMITER $$
CREATE TRIGGER `trg_Unique_Update_NIT` BEFORE UPDATE ON `proveedores` FOR EACH ROW BEGIN
    IF EXISTS (
        SELECT 1
        FROM proveedores
        WHERE NIT = NEW.NIT AND ProveedorID != NEW.ProveedorID
    ) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Ya existe otro proveedor con el mismo NIT.';
    END IF;
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `trg_Unique_Update_NombreProveedor`;
DELIMITER $$
CREATE TRIGGER `trg_Unique_Update_NombreProveedor` BEFORE UPDATE ON `proveedores` FOR EACH ROW BEGIN
    IF EXISTS (
        SELECT 1
        FROM proveedores
        WHERE NombreProveedor = NEW.NombreProveedor AND ProveedorID != NEW.ProveedorID
    ) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Ya existe otro proveedor con el mismo nombre.';
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedores_direcciones`
--

DROP TABLE IF EXISTS `proveedores_direcciones`;
CREATE TABLE IF NOT EXISTS `proveedores_direcciones` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `ProveedorID` int NOT NULL,
  `Direccion` varchar(200) DEFAULT NULL,
  `Ciudad` varchar(10) NOT NULL,
  `codigoPostal` int NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `proveedores` (`ProveedorID`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `proveedores_direcciones`
--

INSERT INTO `proveedores_direcciones` (`ID`, `ProveedorID`, `Direccion`, `Ciudad`, `codigoPostal`) VALUES
(3, 2, 'Avenida Condimentos #456', '', 0),
(4, 3, 'Carrera Lácteos #789', '', 0),
(5, 1, 'calle 9#16-1A', 'Huila', 40001);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

DROP TABLE IF EXISTS `roles`;
CREATE TABLE IF NOT EXISTS `roles` (
  `RolID` int NOT NULL,
  `NombreRol` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`RolID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`RolID`, `NombreRol`) VALUES
(1, 'Administrador'),
(2, 'Coordinador'),
(3, 'Financiero');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE IF NOT EXISTS `usuarios` (
  `UsuarioID` int NOT NULL AUTO_INCREMENT,
  `NombreUsuario` varchar(100) DEFAULT NULL,
  `Nombre` varchar(255) NOT NULL,
  `Contra` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `DocumentoIdentidad` varchar(200) DEFAULT NULL,
  `UltimoInicioSesion` timestamp NULL DEFAULT NULL,
  `FotoPerfil` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `RolID` int DEFAULT NULL,
  `Activo` tinyint(1) NOT NULL DEFAULT '1',
  `FechaUltimaActividad` timestamp NULL DEFAULT NULL,
  `Correo` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`UsuarioID`),
  UNIQUE KEY `NombreUsuario` (`NombreUsuario`),
  KEY `RolID` (`RolID`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`UsuarioID`, `NombreUsuario`, `Nombre`, `Contra`, `DocumentoIdentidad`, `UltimoInicioSesion`, `FotoPerfil`, `RolID`, `Activo`, `FechaUltimaActividad`, `Correo`) VALUES
(1, 'Admin', 'ADMIN FUNDADOR', '$2y$10$Wtm8SpiK/oPbxSVKH5qD..l9US5.Z7oEEGfg/vlyjYu8KAqtEpQ7S', '1003807201', '2024-01-29 12:25:01', 'src/assets/WhatsApp Image 2024-04-03 at 4.59.43 PM.jpeg', 1, 1, '2024-05-14 11:26:45', 'Admin@hotmail.com'),
(2, 'coordinador', 'COORDINADOR FUNDADOR', '$2y$10$GJ/31fbtCK9vOvy3YK1J0uLaDum5Aw.xN79XAz586CB7KkcRNfZb.', '1008388607', '2024-01-29 12:25:01', 'coordinador.jpg', 2, 1, '2024-05-14 11:41:16', 'usuarioC@example.com'),
(3, 'financiero', 'FINANCIERO FUNDADOR', '$2y$10$7Rjs9lNYsbYY9gzYfYVKUeeiIKmzKNntnphuNq2F0qN3hmAulg3T6', '1308388607', '2024-01-29 12:25:01', 'src/assets/D_NQ_NP_735412MPE29584996082_032019V.jpg', 3, 1, '2024-05-14 09:40:56', 'usuarioF@example.com'),
(4, 'Cooooordinador', 'SUJETO PRUEBA', '$2y$10$0PQZ3MoWzetKRxiQqeKoiuSWyE8tjtbikx7ltvzDk4qgX3cZq57VG', '555656', '2024-04-07 01:01:25', NULL, 2, 1, '2024-03-06 06:30:03', 'cooooordinador@hotmail.com'),
(10, 'Kjosua', 'KEYWINTH JOSUA', '$2y$10$AZypZFQ2Q19Rz1JKYzAF5uc9ehoNFn.LCksVfSmG6LaHgoTc4AaEK', '8388607', NULL, NULL, 2, 1, NULL, 'Kjosua@hotmail.com'),
(11, 'CJhonson', 'CARLOS  JHONSON', '$2y$10$DlL6gsrgYJUHWfpBXn4KlOh2QQQJDSym6WZZl2BgYCQPCuM8SE4zC', '33322365', NULL, NULL, 2, 1, NULL, 'CarloJ@hotmail.com'),
(12, 'CJaramillo', 'CARL JARAMILLO', '$2y$10$X1fD5pRx04JY/nOnjE1cAOkE7Uj/Z2HDowmiQoqREKCObRcKRPvqO', '11323232', NULL, NULL, 2, 1, NULL, 'CarlJaramillo@hotmail.com'),
(13, 'JMiranda', 'JUAN MIRANDA', '$2y$10$SnsAoCyeeWRWEG77hLMvGuXVZncGG1yiXftdITNvNF1ivjWEorsiK', '1132323211', NULL, NULL, 2, 1, NULL, 'Jmiranda@hotmail.com'),
(15, 'MisterF', 'MISTER FULANO', '$2y$10$sTsexdj6sXJwbN/f.DdwLuJkt2tm3iRP0GySZEt9y13f3BqJiMywW', '23123123', NULL, NULL, 3, 1, NULL, 'MisterF@hotmail.com'),
(16, 'SRamirez', 'SANTIAGO RAMIREZ', '$2y$10$7FUB2iMwnlIWfsb6/emvkOwFBI0KZGzDliWu4ECRDRvqU01Jxci1O', '1003999666', NULL, NULL, 3, 1, NULL, 'Sramirez@hotmail.com'),
(17, 'MCortes', 'MARTIN CORTES', '$2y$10$FUWBF9.NixjWWUdjBO6VMOdSM74aKtqQQR.qLBN3f9ohiGKO/AqEu', '11111245', NULL, NULL, 2, 1, NULL, 'Mcortes@hotmail.com'),
(18, 'JManue', 'JASON MANUE', '$2y$10$9s2jIeGp.Dw.5Uh.u/JNj.tgc9zRphXovvo1jgxy2HkERtcCCPtdu', '6565656565', NULL, NULL, 2, 1, NULL, 'Jmanue@hotmail.com'),
(20, 'LGonzales', 'LUISA GONZALES', '$2y$10$MbQAAXxgX9bQSOp1VTLEqu05lpctSggDboc8GMk6oCrXC1buHxw2C', '10089898', NULL, NULL, 2, 1, NULL, 'LuizaG@gmail.com'),
(21, 'LMartize', 'LOLA MARTINEZ', '$2y$10$dhA3OVy6h7amXD2/.ao1jOXHIZ84cKo7ma6.3spAh7xgdPW7N2VHO', '1009777777', NULL, NULL, 3, 1, NULL, 'Lmartinez@yahoo.com'),
(22, 'AdminPrueba', 'DON PRUEBA', '$2y$10$WFx7Vazq3fVXv4ADXYL/M.el3FskN8KSf1SzTYd/2MuYPtvNbWYWa', '00999333', NULL, NULL, 1, 1, NULL, 'DonPrueba@hotmail.com');

--
-- Disparadores `usuarios`
--
DROP TRIGGER IF EXISTS `trg_Unique_Correo`;
DELIMITER $$
CREATE TRIGGER `trg_Unique_Correo` BEFORE INSERT ON `usuarios` FOR EACH ROW BEGIN
    /*Condicional para verificar que no tome el mismo registro como duplicado*/
    IF EXISTS (
        SELECT 1
        FROM usuarios
        WHERE Correo = NEW.Correo
    ) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El correo electrónico ya se encuentra registrado';
    END IF;
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `trg_Unique_DocumentoIdentidad`;
DELIMITER $$
CREATE TRIGGER `trg_Unique_DocumentoIdentidad` BEFORE INSERT ON `usuarios` FOR EACH ROW BEGIN
    /*Condicional para verificar que no tome el mismo registro como duplicado*/
    IF EXISTS (
        SELECT 1
        FROM usuarios
        WHERE DocumentoIdentidad = NEW.DocumentoIdentidad
    ) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El documento de identidad ya se encuentra registrado';
    END IF;
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `trg_Unique_NombreUsuario`;
DELIMITER $$
CREATE TRIGGER `trg_Unique_NombreUsuario` BEFORE INSERT ON `usuarios` FOR EACH ROW BEGIN
    IF EXISTS (
        SELECT 1
        FROM usuarios
        WHERE NombreUsuario = NEW.NombreUsuario
    ) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El nombre de usuario ya existe en la tabla.';
    END IF;
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `trg_Unique_Update_Correo`;
DELIMITER $$
CREATE TRIGGER `trg_Unique_Update_Correo` BEFORE UPDATE ON `usuarios` FOR EACH ROW BEGIN
    IF EXISTS (
        SELECT 1
        FROM usuarios
        WHERE Correo = NEW.Correo AND UsuarioID != NEW.UsuarioID
    ) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Ya existe otro registro con ese correo electrónico.';
    END IF;
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `trg_Unique_Update_DocumentoIdentidad`;
DELIMITER $$
CREATE TRIGGER `trg_Unique_Update_DocumentoIdentidad` BEFORE UPDATE ON `usuarios` FOR EACH ROW BEGIN
    IF EXISTS (
        SELECT 1
        FROM usuarios
        WHERE DocumentoIdentidad = NEW.DocumentoIdentidad AND UsuarioID != NEW.UsuarioID
    ) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Ya existe otro registro con ese documento de identidad.';
    END IF;
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `trg_Unique_Update_NombreUsuario`;
DELIMITER $$
CREATE TRIGGER `trg_Unique_Update_NombreUsuario` BEFORE UPDATE ON `usuarios` FOR EACH ROW BEGIN
    /*Condicional para verificar que no tome el mismo registro como duplicado*/
    IF EXISTS (
        SELECT 1
        FROM usuarios
        /* NEW. verifica que no haya un registro con el mismo nombre ingresado, UsuarioID != NEW.UsuarioID verifica que no compare el mismo registro ala hora de actualizar*/
        WHERE NombreUsuario = NEW.NombreUsuario AND UsuarioID != NEW.UsuarioID
    ) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Ya existe otro registro con ese nombre de usuario.';
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

DROP TABLE IF EXISTS `ventas`;
CREATE TABLE IF NOT EXISTS `ventas` (
  `VentaID` int NOT NULL AUTO_INCREMENT,
  `FechaVenta` date NOT NULL,
  `ClienteID` int NOT NULL,
  `TotalVenta` decimal(10,2) NOT NULL,
  `UsuarioID` int NOT NULL,
  `DireccionID` int NOT NULL,
  `EstadoVentaID` int NOT NULL DEFAULT '1',
  `FechaCreacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`VentaID`),
  KEY `ClienteID` (`ClienteID`),
  KEY `UsuarioID` (`UsuarioID`),
  KEY `idx_direccion_id` (`DireccionID`),
  KEY `idx_estado_id` (`EstadoVentaID`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `ventas`
--

INSERT INTO `ventas` (`VentaID`, `FechaVenta`, `ClienteID`, `TotalVenta`, `UsuarioID`, `DireccionID`, `EstadoVentaID`, `FechaCreacion`) VALUES
(3, '2024-03-19', 2, 286000.00, 1, 1, 5, '2024-03-19 22:43:37'),
(11, '2024-04-10', 2, 0.00, 1, 2, 5, '2024-04-02 15:05:24'),
(13, '2024-04-18', 1, 0.00, 1, 5, 4, '2024-04-07 20:38:27'),
(14, '2024-04-12', 1, 0.00, 1, 5, 2, '2024-04-11 14:55:21'),
(15, '2024-04-24', 1, 0.00, 1, 5, 1, '2024-04-11 16:44:41'),
(16, '2024-04-19', 2, 24200.00, 1, 1, 1, '2024-04-11 16:46:39'),
(17, '2024-04-28', 1, 0.00, 1, 2, 1, '2024-04-27 11:29:27'),
(18, '2024-05-01', 1, 0.00, 1, 1, 1, '2024-05-02 18:59:09'),
(19, '2024-05-08', 1, 0.00, 1, 1, 1, '2024-05-07 16:09:30'),
(20, '2024-05-16', 1, 19800.00, 1, 1, 1, '2024-05-07 16:23:37'),
(21, '2024-05-16', 1, 0.00, 1, 1, 1, '2024-05-07 16:25:42'),
(22, '2024-05-14', 1, 8800.00, 1, 1, 1, '2024-05-13 19:59:50');

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD CONSTRAINT `clientes_ibfk_1` FOREIGN KEY (`CoordinadorID`) REFERENCES `usuarios` (`UsuarioID`);

--
-- Filtros para la tabla `compras`
--
ALTER TABLE `compras`
  ADD CONSTRAINT `compras_ibfk_2` FOREIGN KEY (`UsuarioID`) REFERENCES `usuarios` (`UsuarioID`),
  ADD CONSTRAINT `compras_ibfk_4` FOREIGN KEY (`EstadoCompraID`) REFERENCES `estado_compra` (`EstadoCompraID`),
  ADD CONSTRAINT `compras_ibfk_5` FOREIGN KEY (`ProveedorID`) REFERENCES `proveedores` (`ProveedorID`);

--
-- Filtros para la tabla `detalles_venta`
--
ALTER TABLE `detalles_venta`
  ADD CONSTRAINT `detalles_venta_ibfk_1` FOREIGN KEY (`ProductoID`) REFERENCES `productos` (`ProductoID`);

--
-- Filtros para la tabla `detalle_compra`
--
ALTER TABLE `detalle_compra`
  ADD CONSTRAINT `detalle_compra_ibfk_1` FOREIGN KEY (`CompraID`) REFERENCES `compras` (`CompraID`),
  ADD CONSTRAINT `detalle_compra_ibfk_2` FOREIGN KEY (`ProductoID`) REFERENCES `productos` (`ProductoID`);

--
-- Filtros para la tabla `direcciones_clientes`
--
ALTER TABLE `direcciones_clientes`
  ADD CONSTRAINT `direcciones_clientes_ibfk_1` FOREIGN KEY (`ClienteID`) REFERENCES `clientes` (`ClienteID`);

--
-- Filtros para la tabla `inventario_producto`
--
ALTER TABLE `inventario_producto`
  ADD CONSTRAINT `inventario_producto_ibfk_1` FOREIGN KEY (`ProductoID`) REFERENCES `productos` (`ProductoID`);

--
-- Filtros para la tabla `marcas`
--
ALTER TABLE `marcas`
  ADD CONSTRAINT `marcas_ibfk_1` FOREIGN KEY (`ProveedorID`) REFERENCES `proveedores` (`ProveedorID`);

--
-- Filtros para la tabla `precio_compras`
--
ALTER TABLE `precio_compras`
  ADD CONSTRAINT `precio_compras_ibfk_1` FOREIGN KEY (`ProductoID`) REFERENCES `productos` (`ProductoID`);

--
-- Filtros para la tabla `productos`
--
ALTER TABLE `productos`
  ADD CONSTRAINT `productos_ibfk_1` FOREIGN KEY (`FinancieroID`) REFERENCES `usuarios` (`UsuarioID`),
  ADD CONSTRAINT `productos_ibfk_2` FOREIGN KEY (`CategoriaID`) REFERENCES `categorias` (`CategoriaID`),
  ADD CONSTRAINT `productos_ibfk_3` FOREIGN KEY (`PresentacionID`) REFERENCES `presentaciones` (`PresentacionID`),
  ADD CONSTRAINT `productos_ibfk_4` FOREIGN KEY (`MarcaID`) REFERENCES `marcas` (`MarcaID`);

--
-- Filtros para la tabla `proveedores_direcciones`
--
ALTER TABLE `proveedores_direcciones`
  ADD CONSTRAINT `proveedores_direcciones_ibfk_1` FOREIGN KEY (`ProveedorID`) REFERENCES `proveedores` (`ProveedorID`);

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`RolID`) REFERENCES `roles` (`RolID`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Filtros para la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD CONSTRAINT `ventas_ibfk_1` FOREIGN KEY (`UsuarioID`) REFERENCES `usuarios` (`UsuarioID`),
  ADD CONSTRAINT `ventas_ibfk_2` FOREIGN KEY (`EstadoVentaID`) REFERENCES `estado_venta` (`EstadoVentaID`),
  ADD CONSTRAINT `ventas_ibfk_3` FOREIGN KEY (`ClienteID`) REFERENCES `clientes` (`ClienteID`);

DELIMITER $$
--
-- Eventos
--
DROP EVENT IF EXISTS `EliminarUsuariosInactivos`$$
CREATE DEFINER=`root`@`localhost` EVENT `EliminarUsuariosInactivos` ON SCHEDULE EVERY 1 DAY STARTS '2024-01-30 03:12:48' ON COMPLETION NOT PRESERVE ENABLE DO BEGIN
    DELETE FROM Usuarios
    WHERE Activo = 0 AND FechaUltimaActividad < NOW() - INTERVAL 30 DAY;
END$$

DROP EVENT IF EXISTS `LimpiarProductosInactivos`$$
CREATE DEFINER=`root`@`localhost` EVENT `LimpiarProductosInactivos` ON SCHEDULE EVERY 1 DAY STARTS '2024-01-31 07:35:16' ON COMPLETION NOT PRESERVE ENABLE DO BEGIN
    DECLARE limiteFecha DATETIME;
    SET limiteFecha = NOW() - INTERVAL 30 DAY;

    DELETE FROM Productos WHERE Activo = 0 AND FechaUltimaActividad < limiteFecha;
END$$

DROP EVENT IF EXISTS `eliminar_clientes_sin_coordinador`$$
CREATE DEFINER=`root`@`localhost` EVENT `eliminar_clientes_sin_coordinador` ON SCHEDULE EVERY 1 DAY STARTS '2024-02-02 13:27:31' ON COMPLETION NOT PRESERVE ENABLE DO BEGIN
    -- Eliminar clientes con CoordinadorID nulo y más de 30 días de antigüedad
    DELETE FROM Clientes
    WHERE CoordinadorID IS NULL
    AND UltimaAsignacion <= NOW() - INTERVAL 30 DAY;
END$$

DROP EVENT IF EXISTS `actualizar_precios_evento`$$
CREATE DEFINER=`root`@`localhost` EVENT `actualizar_precios_evento` ON SCHEDULE EVERY 1 DAY STARTS '2024-04-12 02:00:00' ON COMPLETION NOT PRESERVE ENABLE DO CALL actualizar_precios()$$

DROP EVENT IF EXISTS `retirar_ventas_canceladas`$$
CREATE DEFINER=`root`@`localhost` EVENT `retirar_ventas_canceladas` ON SCHEDULE EVERY 6 HOUR STARTS '2024-04-11 22:00:00' ON COMPLETION NOT PRESERVE ENABLE DO BEGIN
    CALL restar_ventas_canceladas();
END$$

DROP EVENT IF EXISTS `archivar_ventas`$$
CREATE DEFINER=`root`@`localhost` EVENT `archivar_ventas` ON SCHEDULE EVERY 1 MONTH STARTS '2024-05-01 20:28:42' ON COMPLETION NOT PRESERVE ENABLE DO BEGIN
    CALL actualizar_estado_compra();
 END$$

DROP EVENT IF EXISTS `sumar_cantida_comprada`$$
CREATE DEFINER=`root`@`localhost` EVENT `sumar_cantida_comprada` ON SCHEDULE EVERY 1 DAY STARTS '2024-04-11 23:58:10' ON COMPLETION NOT PRESERVE ENABLE DO BEGIN
    CALL sumar_cantidad_comprada();
END$$

DROP EVENT IF EXISTS `retirar_compras_canceladas`$$
CREATE DEFINER=`root`@`localhost` EVENT `retirar_compras_canceladas` ON SCHEDULE EVERY 1 DAY STARTS '2024-04-11 23:59:48' ON COMPLETION NOT PRESERVE ENABLE DO BEGIN
    CALL restar_compras_canceladas();
END$$

DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
