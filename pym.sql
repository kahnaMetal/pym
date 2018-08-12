-- phpMyAdmin SQL Dump
-- version 4.6.6deb5
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 11-08-2018 a las 17:00:44
-- Versión del servidor: 10.1.29-MariaDB-6+b1
-- Versión de PHP: 7.2.4-1+b2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `pym`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cities`
--

CREATE TABLE `cities` (
  `idCity` int(11) NOT NULL COMMENT 'Llave Primaria Auto Incremento',
  `nameCity` varchar(250) COLLATE utf8_spanish2_ci NOT NULL,
  `typeCity` enum('0','1','2','3') COLLATE utf8_spanish2_ci NOT NULL DEFAULT '1' COMMENT '0: Zona, 1: Ciudad, 2: Dpto, 3: País',
  `parentCity` int(11) NOT NULL,
  `statusCity` enum('1','2') COLLATE utf8_spanish2_ci NOT NULL DEFAULT '1' COMMENT '1: Activo, 2: Eliminado',
  `createdBy_City` int(11) NOT NULL COMMENT 'Llave foranea de la tabla Users generada al hacer un INSERT ',
  `createdAt_Customer` datetime NOT NULL,
  `updatedBy_Customer` int(11) DEFAULT NULL COMMENT 'Llave foranea de la tabla Users generada al hacer un UPDATE ',
  `updatedAt_Customer` datetime DEFAULT NULL,
  `deletedBy_Customer` int(11) DEFAULT NULL COMMENT 'Llave foranea de la tabla Users generada al hacer un UPDATE SET status = ''0'' que representa una eliminación',
  `deletedAt_Customer` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ci_sessions`
--

CREATE TABLE `ci_sessions` (
  `id` varchar(40) COLLATE utf8_spanish2_ci NOT NULL,
  `ip_address` varchar(45) COLLATE utf8_spanish2_ci NOT NULL,
  `timestamp` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `data` blob NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `customers`
--

CREATE TABLE `customers` (
  `idCustomer` int(11) NOT NULL COMMENT 'Llave Primaria Auto Incremento',
  `typeCustomer` enum('1','2') COLLATE utf8_spanish2_ci NOT NULL DEFAULT '2' COMMENT '1: Persona Natural, 2: Persona Juridica',
  `documentCustomer` varchar(10) COLLATE utf8_spanish2_ci DEFAULT NULL COMMENT 'campo aplica si typeCustomer == 1',
  `nameCustomer` varchar(150) COLLATE utf8_spanish2_ci DEFAULT NULL COMMENT 'campo aplica si typeCustomer == 1',
  `lastnameCustomer` varchar(150) COLLATE utf8_spanish2_ci DEFAULT NULL COMMENT 'campo aplica si typeCustomer == 1',
  `nitCustomer` bigint(9) DEFAULT NULL COMMENT 'campo aplica si typeCustomer == 2',
  `businessDigitCustomer` int(1) DEFAULT NULL COMMENT 'campo aplica si typeCustomer == 2',
  `businessNameCustomer` varchar(250) COLLATE utf8_spanish2_ci DEFAULT NULL COMMENT 'campo aplica si typeCustomer == 2',
  `phoneCustomer` bigint(10) NOT NULL,
  `cellphoneCustomer` bigint(10) DEFAULT NULL,
  `emailCustomer` varchar(250) COLLATE utf8_spanish2_ci DEFAULT NULL,
  `addressCustomer` varchar(250) COLLATE utf8_spanish2_ci NOT NULL,
  `cityCustomer_fkCities` int(11) DEFAULT NULL COMMENT 'Llave foranea de la tabla Cities',
  `userCustomer_fkUsers` int(11) DEFAULT NULL COMMENT 'Llave foranea de la tabla Users',
  `enabledUserCustomer` enum('0','1') COLLATE utf8_spanish2_ci NOT NULL DEFAULT '0' COMMENT '0: No permite crear usuario, 1: Permite crear usuario',
  `statusCustomer` enum('0','1') COLLATE utf8_spanish2_ci NOT NULL DEFAULT '1' COMMENT '1: Activo, 0: Eliminado',
  `createdBy_Customer` int(11) NOT NULL COMMENT 'Llave foranea de la tabla Users generada al hacer un INSERT',
  `createdAt_Customer` datetime NOT NULL,
  `updatedBy_Customer` int(11) DEFAULT NULL COMMENT 'Llave foranea de la tabla Users generada al hacer un UPDATE',
  `updatedAt_Customer` datetime DEFAULT NULL,
  `deletedBy_Customer` int(11) DEFAULT NULL COMMENT 'Llave foranea de la tabla Users generada al hacer un UPDATE SET status = ''0'' que representa una eliminación',
  `deletedAt_Customer` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `logs`
--

CREATE TABLE `logs` (
  `idLog` int(11) NOT NULL,
  `typeLog` enum('1','2','3','4','5') COLLATE utf8_spanish2_ci NOT NULL COMMENT '1: Login, 2: Logout, 3: Insert, 4: Update, 5: Delete',
  `detailLog` text COLLATE utf8_spanish2_ci NOT NULL,
  `dateLog` datetime NOT NULL,
  `ipLog` varchar(15) COLLATE utf8_spanish2_ci NOT NULL,
  `agentLog` varchar(250) COLLATE utf8_spanish2_ci NOT NULL,
  `statusLog` enum('success','warning','delete') COLLATE utf8_spanish2_ci NOT NULL,
  `table_LogFK` varchar(250) COLLATE utf8_spanish2_ci NOT NULL,
  `row_LogFK` int(11) NOT NULL,
  `user_LogFK` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `payments`
--

CREATE TABLE `payments` (
  `idPayment` int(11) NOT NULL,
  `valuePayment` double NOT NULL,
  `datePayment` datetime NOT NULL,
  `salePayment_fkSales` int(11) NOT NULL,
  `createdBy_Payments` int(11) NOT NULL COMMENT 'Llave foranea de la tabla Users generada al hacer un INSERT ',
  `createdAt_Payments` date NOT NULL,
  `updatedBy_Payments` int(11) NOT NULL COMMENT 'Llave foranea de la tabla Users generada al hacer un UPDATE ',
  `updatedAt_Payments` date NOT NULL,
  `deletedBy_Payments` int(11) NOT NULL COMMENT 'Llave foranea de la tabla Users generada al hacer un UPDATE SET status = ''0'' que representa una eliminación ',
  `deletedAt_Payments` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `products`
--

CREATE TABLE `products` (
  `idProduct` int(11) NOT NULL COMMENT 'Llave Primaria Auto Incremento',
  `nameProduct` varchar(250) COLLATE utf8_spanish2_ci NOT NULL,
  `imgProduct` varchar(250) COLLATE utf8_spanish2_ci DEFAULT NULL,
  `categoryProduct` varchar(150) COLLATE utf8_spanish2_ci DEFAULT NULL COMMENT 'Debe guardar siempre en MAYUSCULA',
  `qtyProduct` int(11) NOT NULL,
  `valueProduct` double NOT NULL,
  `statusProduct` enum('0','1') COLLATE utf8_spanish2_ci NOT NULL COMMENT '1: Activo, 0: Eliminado',
  `createdBy_Product` int(11) NOT NULL COMMENT 'Llave foranea de la tabla Users generada al hacer un INSERT',
  `createdAt_Product` datetime NOT NULL,
  `updatedBy_Product` int(11) DEFAULT NULL COMMENT 'Llave foranea de la tabla Users generada al hacer un UPDATE ',
  `updatedAt_Product` datetime DEFAULT NULL,
  `deletedBy_Product` int(11) DEFAULT NULL COMMENT 'Llave foranea de la tabla Users generada al hacer un UPDATE SET status = ''0'' que representa una eliminación ',
  `deletedAt_Product` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `requests`
--

CREATE TABLE `requests` (
  `idRequest` int(11) NOT NULL COMMENT 'Llave Primaria Auto Incremento ',
  `dateRequest` datetime NOT NULL,
  `sellerRequest_fkSellers` int(11) DEFAULT NULL COMMENT 'Llave foranea de la tabla Sellers',
  `customerRequest_fkCustomers` int(11) DEFAULT NULL COMMENT 'Llave foranea de la tabla Customers',
  `statusRequest` enum('0','1','2') COLLATE utf8_spanish2_ci NOT NULL COMMENT '0: Eliminada, 1: Activa, 2: Convertida en venta',
  `createdBy_Request` int(11) NOT NULL COMMENT 'Llave foranea de la tabla Users generada al hacer un INSERT',
  `createdAt_Request` datetime NOT NULL,
  `updatedBy_Request` int(11) DEFAULT NULL COMMENT 'Llave foranea de la tabla Users generada al hacer un UPDATE',
  `updatedAt_Request` datetime DEFAULT NULL,
  `deletedBy_Request` int(11) DEFAULT NULL COMMENT 'Llave foranea de la tabla Users generada al hacer un UPDATE SET status = ''0'' que representa una eliminación',
  `deletedAt_Request` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `requests_products`
--

CREATE TABLE `requests_products` (
  `idRP` int(11) NOT NULL,
  `fkRequest` int(11) NOT NULL,
  `fkProduct` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sales`
--

CREATE TABLE `sales` (
  `idSale` int(11) NOT NULL,
  `dateSale` datetime NOT NULL,
  `qtyProductsSale` int(11) NOT NULL,
  `totalSale` double NOT NULL,
  `ivaSale` double DEFAULT NULL COMMENT 'Este campo no se va a utilizar por ahora, pero se deja abierto para modificaciones',
  `typeSale` enum('1','2') COLLATE utf8_spanish2_ci NOT NULL DEFAULT '2' COMMENT '1: Contado, 2: Crédito',
  `totalDaysSale` int(11) DEFAULT NULL COMMENT 'campo aplica si typeSale == 2',
  `totalPaymentsSale` double NOT NULL DEFAULT '0',
  `discountSale` double NOT NULL,
  `customerSale_fkCustomers` int(11) DEFAULT NULL COMMENT 'Llave foranea de la tabla Customers',
  `sellerSale_fkSellers` int(11) DEFAULT NULL COMMENT 'Llave foranea de la tabla Sellers ',
  `statusSale` enum('0','1','2') COLLATE utf8_spanish2_ci NOT NULL COMMENT '0: Eliminada, 1: Activa, 2: Paga',
  `createdBy_Sale` int(11) NOT NULL COMMENT 'Llave foranea de la tabla Users generada al hacer un INSERT ',
  `createdAt_Sale` datetime NOT NULL,
  `updatedBy_Sale` int(11) DEFAULT NULL COMMENT 'Llave foranea de la tabla Users generada al hacer un UPDATE ',
  `updatedAt_Sale` datetime DEFAULT NULL,
  `deletedBy_Sale` int(11) DEFAULT NULL COMMENT 'Llave foranea de la tabla Users generada al hacer un UPDATE SET status = ''0'' que representa una eliminación',
  `deletedAt_Sale` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sales_products`
--

CREATE TABLE `sales_products` (
  `idSP` int(11) NOT NULL,
  `fkSale` int(11) NOT NULL,
  `fkProduct` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sellers`
--

CREATE TABLE `sellers` (
  `idSeller` int(11) NOT NULL COMMENT 'Llave Primaria Auto Incremento',
  `documentSeller` varchar(10) COLLATE utf8_spanish2_ci DEFAULT NULL,
  `nameSeller` varchar(150) COLLATE utf8_spanish2_ci DEFAULT NULL,
  `lastnameSeller` varchar(150) COLLATE utf8_spanish2_ci DEFAULT NULL,
  `phoneSeller` bigint(10) DEFAULT NULL,
  `cellphoneSeller` bigint(10) NOT NULL,
  `emailSeller` varchar(250) COLLATE utf8_spanish2_ci DEFAULT NULL,
  `addressSeller` varchar(250) COLLATE utf8_spanish2_ci NOT NULL,
  `citySeller_fkCities` int(11) DEFAULT NULL COMMENT 'Llave foranea de la tabla Cities',
  `userSeller_fkUsers` int(11) DEFAULT NULL COMMENT 'Llave foranea de la tabla Cities',
  `statusSeller` enum('0','1') COLLATE utf8_spanish2_ci NOT NULL DEFAULT '1' COMMENT '1: Activo, 0: Eliminado',
  `createdBy_Seller` int(11) NOT NULL COMMENT 'Llave foranea de la tabla Users generada al hacer un INSERT',
  `createdAt_Seller` datetime NOT NULL,
  `updatedBy_Seller` int(11) DEFAULT NULL COMMENT 'Llave foranea de la tabla Users generada al hacer un UPDATE',
  `updatedAt_Seller` datetime DEFAULT NULL,
  `deletedBy_Seller` int(11) DEFAULT NULL COMMENT 'Llave foranea de la tabla Users generada al hacer un UPDATE SET status = ''0'' que representa una eliminación',
  `deletedAt_Seller` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `idUser` int(11) NOT NULL COMMENT 'Llave Primaria Auto Incremento ',
  `emailUser` varchar(250) COLLATE utf8_spanish2_ci NOT NULL,
  `imgUser` varchar(250) COLLATE utf8_spanish2_ci DEFAULT NULL COMMENT 'Ruta de la imagen',
  `usernameUser` char(15) COLLATE utf8_spanish2_ci NOT NULL COMMENT 'Guardar siempre MAYUSCULAS',
  `passwdUser` varchar(50) COLLATE utf8_spanish2_ci NOT NULL COMMENT 'Codificado en MD5',
  `statusUser` enum('0','1') COLLATE utf8_spanish2_ci NOT NULL COMMENT '1: Activo, 2: Eliminado ',
  `timeLogUser` datetime DEFAULT NULL COMMENT 'Fecha del último acceso a la plataforma',
  `cityUser_fkCities` int(11) DEFAULT NULL COMMENT ' Llave foranea de la tabla Cities ',
  `rolUser` enum('1','2','3','7','0') COLLATE utf8_spanish2_ci NOT NULL DEFAULT '3' COMMENT '0:Develop , 1: Admin, 2: Vendedor, 3: Cliente, 7: SuperAdmin'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `cities`
--
ALTER TABLE `cities`
  ADD PRIMARY KEY (`idCity`),
  ADD KEY `createdBy_City` (`createdBy_City`),
  ADD KEY `updatedBy_Customer` (`updatedBy_Customer`),
  ADD KEY `deletedBy_Customer` (`deletedBy_Customer`);

--
-- Indices de la tabla `ci_sessions`
--
ALTER TABLE `ci_sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ci_sessions_timestamp` (`timestamp`);

--
-- Indices de la tabla `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`idCustomer`),
  ADD KEY `cityCustomer_fkCities` (`cityCustomer_fkCities`),
  ADD KEY `createdBy_Customer` (`createdBy_Customer`),
  ADD KEY `updatedBy_Customer` (`updatedBy_Customer`),
  ADD KEY `deletedBy_Customer` (`deletedBy_Customer`),
  ADD KEY `userCustomer_fkUsers` (`userCustomer_fkUsers`);

--
-- Indices de la tabla `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`idLog`),
  ADD KEY `user_LogFK` (`user_LogFK`);

--
-- Indices de la tabla `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`idPayment`),
  ADD KEY `salePayment_fkSales` (`salePayment_fkSales`),
  ADD KEY `createdBy_Payments` (`createdBy_Payments`),
  ADD KEY `updatedBy_Payments` (`updatedBy_Payments`),
  ADD KEY `deletedBy_Payments` (`deletedBy_Payments`);

--
-- Indices de la tabla `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`idProduct`),
  ADD KEY `createdBy_Product` (`createdBy_Product`),
  ADD KEY `updatedBy_Product` (`updatedBy_Product`),
  ADD KEY `deletedBy_Product` (`deletedBy_Product`);

--
-- Indices de la tabla `requests`
--
ALTER TABLE `requests`
  ADD PRIMARY KEY (`idRequest`),
  ADD KEY `sellerCustomer_fkSellers` (`sellerRequest_fkSellers`),
  ADD KEY `createdBy_Request` (`createdBy_Request`),
  ADD KEY `sellerCustomer_fkSellers_2` (`sellerRequest_fkSellers`),
  ADD KEY `updatedBy_Request` (`updatedBy_Request`),
  ADD KEY `deletedBy_Request` (`deletedBy_Request`),
  ADD KEY `customerRequest_fkCustomers` (`customerRequest_fkCustomers`);

--
-- Indices de la tabla `requests_products`
--
ALTER TABLE `requests_products`
  ADD PRIMARY KEY (`idRP`),
  ADD KEY `fkRequest` (`fkRequest`),
  ADD KEY `fkProduct` (`fkProduct`);

--
-- Indices de la tabla `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`idSale`),
  ADD KEY `createdBy_Sale` (`createdBy_Sale`),
  ADD KEY `updatedBy_Sale` (`updatedBy_Sale`),
  ADD KEY `deletedBy_Sale` (`deletedBy_Sale`),
  ADD KEY `customerSale_fkCustomers` (`customerSale_fkCustomers`),
  ADD KEY `sellerSale_fkSellers` (`sellerSale_fkSellers`);

--
-- Indices de la tabla `sales_products`
--
ALTER TABLE `sales_products`
  ADD PRIMARY KEY (`idSP`),
  ADD KEY `fkSale` (`fkSale`),
  ADD KEY `fkProduct` (`fkProduct`);

--
-- Indices de la tabla `sellers`
--
ALTER TABLE `sellers`
  ADD PRIMARY KEY (`idSeller`),
  ADD KEY `citySeller_fkCities` (`citySeller_fkCities`),
  ADD KEY `userSeller_fkUsers` (`userSeller_fkUsers`),
  ADD KEY `createdBy_Seller` (`createdBy_Seller`),
  ADD KEY `updatedBy_Seller` (`updatedBy_Seller`),
  ADD KEY `deletedBy_Seller` (`deletedBy_Seller`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`idUser`),
  ADD KEY `cityUser_fkCities` (`cityUser_fkCities`),
  ADD KEY `rolUser_fkRoles` (`rolUser`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `cities`
--
ALTER TABLE `cities`
  MODIFY `idCity` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Llave Primaria Auto Incremento', AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT de la tabla `customers`
--
ALTER TABLE `customers`
  MODIFY `idCustomer` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Llave Primaria Auto Incremento', AUTO_INCREMENT=14;
--
-- AUTO_INCREMENT de la tabla `logs`
--
ALTER TABLE `logs`
  MODIFY `idLog` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;
--
-- AUTO_INCREMENT de la tabla `payments`
--
ALTER TABLE `payments`
  MODIFY `idPayment` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `products`
--
ALTER TABLE `products`
  MODIFY `idProduct` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Llave Primaria Auto Incremento', AUTO_INCREMENT=24;
--
-- AUTO_INCREMENT de la tabla `requests`
--
ALTER TABLE `requests`
  MODIFY `idRequest` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Llave Primaria Auto Incremento ';
--
-- AUTO_INCREMENT de la tabla `requests_products`
--
ALTER TABLE `requests_products`
  MODIFY `idRP` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `sales`
--
ALTER TABLE `sales`
  MODIFY `idSale` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `sales_products`
--
ALTER TABLE `sales_products`
  MODIFY `idSP` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `sellers`
--
ALTER TABLE `sellers`
  MODIFY `idSeller` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Llave Primaria Auto Incremento';
--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `idUser` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Llave Primaria Auto Incremento ', AUTO_INCREMENT=2;
--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `customers`
--
ALTER TABLE `customers`
  ADD CONSTRAINT `customers_ibfk_1` FOREIGN KEY (`createdBy_Customer`) REFERENCES `users` (`idUser`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `customers_ibfk_2` FOREIGN KEY (`updatedBy_Customer`) REFERENCES `users` (`idUser`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `customers_ibfk_3` FOREIGN KEY (`deletedBy_Customer`) REFERENCES `users` (`idUser`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `customers_ibfk_4` FOREIGN KEY (`cityCustomer_fkCities`) REFERENCES `cities` (`idCity`) ON DELETE SET NULL ON UPDATE NO ACTION,
  ADD CONSTRAINT `customers_ibfk_5` FOREIGN KEY (`userCustomer_fkUsers`) REFERENCES `users` (`idUser`) ON DELETE SET NULL ON UPDATE NO ACTION;

--
-- Filtros para la tabla `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`salePayment_fkSales`) REFERENCES `sales` (`idSale`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`createdBy_Payments`) REFERENCES `users` (`idUser`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `payments_ibfk_3` FOREIGN KEY (`updatedBy_Payments`) REFERENCES `users` (`idUser`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `payments_ibfk_4` FOREIGN KEY (`deletedBy_Payments`) REFERENCES `users` (`idUser`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`createdBy_Product`) REFERENCES `users` (`idUser`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `products_ibfk_2` FOREIGN KEY (`updatedBy_Product`) REFERENCES `users` (`idUser`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `products_ibfk_3` FOREIGN KEY (`deletedBy_Product`) REFERENCES `users` (`idUser`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `requests`
--
ALTER TABLE `requests`
  ADD CONSTRAINT `requests_ibfk_1` FOREIGN KEY (`createdBy_Request`) REFERENCES `users` (`idUser`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `requests_ibfk_2` FOREIGN KEY (`updatedBy_Request`) REFERENCES `users` (`idUser`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `requests_ibfk_3` FOREIGN KEY (`deletedBy_Request`) REFERENCES `users` (`idUser`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `requests_ibfk_4` FOREIGN KEY (`sellerRequest_fkSellers`) REFERENCES `sellers` (`idSeller`) ON DELETE SET NULL ON UPDATE NO ACTION,
  ADD CONSTRAINT `requests_ibfk_5` FOREIGN KEY (`customerRequest_fkCustomers`) REFERENCES `customers` (`idCustomer`) ON DELETE SET NULL ON UPDATE NO ACTION;

--
-- Filtros para la tabla `requests_products`
--
ALTER TABLE `requests_products`
  ADD CONSTRAINT `requests_products_ibfk_1` FOREIGN KEY (`fkRequest`) REFERENCES `requests` (`idRequest`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `requests_products_ibfk_2` FOREIGN KEY (`fkProduct`) REFERENCES `products` (`idProduct`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Filtros para la tabla `sales`
--
ALTER TABLE `sales`
  ADD CONSTRAINT `sales_ibfk_1` FOREIGN KEY (`createdBy_Sale`) REFERENCES `users` (`idUser`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `sales_ibfk_2` FOREIGN KEY (`updatedBy_Sale`) REFERENCES `users` (`idUser`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `sales_ibfk_3` FOREIGN KEY (`deletedBy_Sale`) REFERENCES `users` (`idUser`),
  ADD CONSTRAINT `sales_ibfk_4` FOREIGN KEY (`customerSale_fkCustomers`) REFERENCES `customers` (`idCustomer`) ON DELETE SET NULL ON UPDATE NO ACTION,
  ADD CONSTRAINT `sales_ibfk_5` FOREIGN KEY (`sellerSale_fkSellers`) REFERENCES `sellers` (`idSeller`) ON DELETE SET NULL ON UPDATE NO ACTION;

--
-- Filtros para la tabla `sales_products`
--
ALTER TABLE `sales_products`
  ADD CONSTRAINT `sales_products_ibfk_1` FOREIGN KEY (`fkProduct`) REFERENCES `products` (`idProduct`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `sales_products_ibfk_2` FOREIGN KEY (`fkSale`) REFERENCES `sales` (`idSale`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `sellers`
--
ALTER TABLE `sellers`
  ADD CONSTRAINT `sellers_ibfk_1` FOREIGN KEY (`citySeller_fkCities`) REFERENCES `cities` (`idCity`) ON DELETE SET NULL ON UPDATE NO ACTION,
  ADD CONSTRAINT `sellers_ibfk_2` FOREIGN KEY (`userSeller_fkUsers`) REFERENCES `users` (`idUser`) ON DELETE SET NULL ON UPDATE NO ACTION,
  ADD CONSTRAINT `sellers_ibfk_3` FOREIGN KEY (`createdBy_Seller`) REFERENCES `users` (`idUser`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `sellers_ibfk_4` FOREIGN KEY (`updatedBy_Seller`) REFERENCES `users` (`idUser`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `sellers_ibfk_5` FOREIGN KEY (`deletedBy_Seller`) REFERENCES `users` (`idUser`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`cityUser_fkCities`) REFERENCES `cities` (`idCity`) ON DELETE SET NULL ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
