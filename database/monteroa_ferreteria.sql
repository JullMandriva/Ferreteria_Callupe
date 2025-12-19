-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 19-12-2025 a las 12:26:04
-- Versión del servidor: 10.11.13-MariaDB-cll-lve
-- Versión de PHP: 8.4.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `monteroa_ferreteria`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `fecha_creacion` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id`, `nombre`, `descripcion`, `fecha_creacion`) VALUES
(1, 'Herramientas Manuales', 'Martillos, destornilladores, alicates, etc.', '2025-12-14 19:03:06'),
(2, 'Electricidad', 'Cables, interruptores, focos, etc.', '2025-12-14 19:03:06'),
(3, 'Fontanería', 'Tuberías, llaves, conectores, etc.', '2025-12-14 19:03:06'),
(4, 'Pintura', 'Pinturas, brochas, rodillos, etc.', '2025-12-14 19:03:06'),
(5, 'Fijación', 'Tornillos, clavos, anclajes, etc.', '2025-12-14 19:03:06');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contactos`
--

CREATE TABLE `contactos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `asunto` varchar(200) NOT NULL,
  `mensaje` text NOT NULL,
  `tipo_consulta` varchar(50) DEFAULT NULL,
  `fecha_envio` timestamp NULL DEFAULT current_timestamp(),
  `leido` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `contactos`
--

INSERT INTO `contactos` (`id`, `nombre`, `email`, `telefono`, `asunto`, `mensaje`, `tipo_consulta`, `fecha_envio`, `leido`) VALUES
(1, 'Martin', 'admin@ferreteria.com', '123456789', 'Reporte', 'leeee', 'cotizacion', '2025-12-15 12:48:31', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_ventas`
--

CREATE TABLE `detalle_ventas` (
  `id` int(11) NOT NULL,
  `id_venta` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `detalle_ventas`
--

INSERT INTO `detalle_ventas` (`id`, `id_venta`, `id_producto`, `cantidad`, `precio_unitario`, `subtotal`) VALUES
(1, 1, 8, 1, 4.25, 4.25),
(2, 1, 11, 2, 35.00, 70.00),
(3, 2, 11, 2, 35.00, 70.00),
(4, 2, 9, 7, 22.00, 154.00),
(5, 2, 8, 2, 4.25, 8.50),
(6, 2, 10, 3, 18.75, 56.25),
(7, 2, 17, 1, 1.50, 1.50),
(8, 3, 8, 1, 15.90, 15.90),
(9, 4, 28, 1, 29.90, 29.90),
(10, 4, 8, 3, 15.90, 47.70),
(11, 5, 28, 1, 29.90, 29.90),
(12, 5, 8, 1, 15.90, 15.90),
(13, 6, 8, 1, 15.90, 15.90);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `movimientos_inventario`
--

CREATE TABLE `movimientos_inventario` (
  `id` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `tipo` enum('entrada','salida','ajuste') NOT NULL,
  `cantidad` int(11) NOT NULL,
  `motivo` varchar(255) DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL DEFAULT 0.00,
  `stock` int(11) NOT NULL DEFAULT 0,
  `imagen` varchar(255) DEFAULT NULL,
  `fecha_creacion` timestamp NULL DEFAULT current_timestamp(),
  `stock_minimo` int(11) DEFAULT 5,
  `categoria` varchar(50) DEFAULT 'general',
  `id_categoria` int(11) DEFAULT NULL,
  `stock_reservado` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `nombre`, `descripcion`, `precio`, `stock`, `imagen`, `fecha_creacion`, `stock_minimo`, `categoria`, `id_categoria`, `stock_reservado`) VALUES
(1, 'Martillo de carpintero', 'Martillo con mango de madera y cabeza de acero forjado. Ideal para clavar y extraer clavos.', 59.90, 47, 'martillo.png', '2025-12-06 22:14:06', 10, 'herramientas', NULL, 0),
(2, 'Destornillador Phillips', 'Destornillador de punta Phillips con mango ergonómico. Tamaño mediano.', 32.50, 30, '61OFJLmDGoL._AC_UF894,1000_QL80_.jpg', '2025-12-06 22:14:06', 5, 'herramientas', NULL, 0),
(3, 'Juego de llaves inglesas', 'Juego de 5 llaves inglesas de acero cromado, de: 6\", 8\", 10\".', 149.90, 20, 'HADWK031.jpg', '2025-12-06 22:14:06', 5, 'herramientas', NULL, 0),
(4, 'Taladro percutor', 'Taladro percutor inalámbrico 18V. Incluye batería y cargador.', 389.00, 15, '36820773-800-auto.webp', '2025-12-06 22:14:06', 3, 'herramientas', NULL, 0),
(5, 'Sierra circular', 'Sierra circular de 7 1/4\" con disco de diamante. Potencia 1200W.', 289.00, 10, 'productos3_5934-300x300.jpg', '2025-12-06 22:14:06', 2, 'herramientas', NULL, 0),
(6, 'Clavos de acero 2\"', 'Caja de 100 clavos de acero galvanizado de 2 pulgadas.', 19.90, 199, 'clavo-cemento-1-27-x-25mm-prodac-x-500-gr.jpg', '2025-12-06 22:14:06', 50, 'ferreteria', NULL, 0),
(7, 'Tornillos madera 3\" x 50 Und.', 'Caja de 50 tornillos para madera de 3 pulgadas con cabeza hexagonal.', 24.90, 150, 'images (3).jpg', '2025-12-06 22:14:06', 30, 'ferreteria', NULL, 0),
(8, 'Bisagra de latón', 'Bisagra de latón de 3\" para puertas. Paquete de 2 unidades.', 15.90, 48, 'D_NQ_NP_986981-MPE76374196791_052024-O.webp', '2025-12-06 22:14:06', 20, 'ferreteria', NULL, 0),
(9, 'Cerradura de puerta', 'Cerradura de puerta con llave y pestillo. Incluye 3 llaves.', 79.90, 14, 'images.jpg', '2025-12-06 22:14:06', 5, 'ferreteria', NULL, 0),
(10, 'Alambre de acero', 'Rollo de alambre de acero galvanizado calibre 16. 50 metros.', 49.90, 4, '975747-a1.jpg', '2025-12-06 22:14:06', 10, 'ferreteria', NULL, 0),
(11, 'Cable eléctrico 12 AWG', 'Cable eléctrico THHN calibre 12. Color negro. 100 metros.', 89.90, 18, 'CABLE_CU_THWN_12_NG_ROLLO_ELECTROVERA.png', '2025-12-06 22:14:06', 10, 'electricidad', NULL, 0),
(12, 'Tomacorriente', 'Tomacorriente estándar 125V 15A. Color blanco.', 12.90, 100, 'images (2).jpg', '2025-12-06 22:14:06', 20, 'electricidad', NULL, 0),
(13, 'Interruptor de luz', 'Interruptor de luz simple. Color blanco.', 9.90, 80, 'w=1500,h=1500,fit=pad.webp', '2025-12-06 22:14:06', 15, 'electricidad', NULL, 0),
(14, 'Cinta aislante', 'Rollo de cinta aislante eléctrica. Color negro. 19mm x 20m.', 14.90, 59, 'CINTATemflex15518.png', '2025-12-06 22:14:06', 10, 'electricidad', NULL, 0),
(15, 'Portalámpara', 'Portalámpara plástico con rosca E27. Para instalaciones interiores.', 7.90, 120, 'portalamparas-con-arandela-e27-4a-250v.jpg', '2025-12-06 22:14:06', 30, 'electricidad', NULL, 0),
(16, 'Tubo PVC 1/2', 'Tubo PVC de 1/2 pulgada. Longitud 3 metros.', 22.90, 50, 'Slide12.jpg', '2025-12-06 22:14:06', 10, 'fontanería', NULL, 0),
(17, 'Codo PVC 90°', 'Codo PVC de 90° para tubo de 1/2 pulgada.', 5.90, 99, 'images (1).jpg', '2025-12-06 22:14:06', 20, 'fontanería', NULL, 0),
(18, 'Válvula de paso', 'Válvula de paso de bronce de 1/2 pulgada.', 39.90, 30, 'valvulacompuertabronce.webp', '2025-12-06 22:14:06', 5, 'fontanería', NULL, 0),
(19, 'Flexómetro 5m', 'Flexómetro metálico de 5 metros con bloqueo automático.', 39.90, 40, 'D_NQ_NP_872990-MLU73423366831_122023-O.webp', '2025-12-06 22:14:06', 10, 'herramientas', NULL, 0),
(20, 'Nivel de burbuja', 'Nivel de burbuja de aluminio de 24\". Precisión 0.5mm/m.', 49.90, 25, 'nivel burbuja.jpg', '2025-12-06 22:14:06', 5, 'herramientas', NULL, 0),
(21, 'Llave stillson 10\"', 'Llave stillson de 10\" con dientes ajustables. Para tuberías.', 89.90, 15, 'llave-stillson-10-15836-truper.webp', '2025-12-06 22:14:06', 3, 'herramientas', NULL, 0),
(22, 'Soldadora de estaño', 'Soldadora de estaño eléctrica 40W con punta de cobre.', 39.90, 20, '1745868623065.jpg', '2025-12-06 22:14:06', 5, 'electricidad', NULL, 0),
(23, 'Pistola de silicona', 'Pistola de calafatear para cartuchos de silicona. Metal.', 29.90, 35, 'pistola-de-calafatear-para-cartuchos-silicona.png', '2025-12-06 22:14:06', 5, 'ferreteria', NULL, 0),
(24, 'Cinta métrica x 10 m', 'Cinta métrica de fibra de 10 metros. Carcasa de plástico.', 24.90, 44, 'wincha-flexometro-gripper-10-metros-contra-impacto-tpr-cinta-extra-ancha-carcasa-abs-fh-10m-14582-truper.webp', '2025-12-06 22:14:06', 10, 'herramientas', NULL, 0),
(25, 'Escalera de aluminio', 'Escalera de aluminio plegable de 3 escalones. Capacidad 150kg.', 129.90, 10, 'imageUrl_1.webp', '2025-12-06 22:14:06', 2, 'herramientas', NULL, 0),
(26, 'Socket Plano E27 Blanco Pack x 3', 'Socket Plano Werken E27 Blanco Pack x 3:\r\nDiseño plano de alta calidad.\r\nFabricado en polipropileno y acero.\r\nTipo de rosca E27 y amperaje de 0.16666667 A.\r\nGarantía de 1 año para tu tranquilidad.\r\nIncluye tornillos para una instalación sencilla.', 11.90, 20, 'null.webp', '2025-12-12 01:36:31', 20, 'general', NULL, 0),
(27, 'Cuchillo Dyangrip retractable Stanley', 'Cuchilla con mango anti-deslizante aún con el uso de guantes, Cuenta con un mecanismo patentado de nariz superpuesta que mantiene la hoja segura en su posición. Incluye 3 hojas.', 49.90, 10, '10-779_1.jpg', '2025-12-12 01:45:45', 10, 'general', NULL, 0),
(28, 'Alicate Pico de Loro 10\" ', 'Alicate Pico de Loro 10\" 17351 Truper', 29.90, 2, 'X_pex-10x-e16265.jpg', '2025-12-12 01:50:03', 8, 'general', NULL, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedores`
--

CREATE TABLE `proveedores` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `ruc` varchar(20) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `direccion` text DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reclamos`
--

CREATE TABLE `reclamos` (
  `id` int(11) NOT NULL,
  `codigo_reclamo` varchar(20) NOT NULL,
  `tipo_reclamo` enum('reclamo','queja','sugerencia') NOT NULL,
  `fecha_incidente` date NOT NULL,
  `fecha_registro` datetime DEFAULT current_timestamp(),
  `tipo_documento` enum('DNI','RUC','CE','Pasaporte') NOT NULL,
  `numero_documento` varchar(20) NOT NULL,
  `nombres_apellidos` varchar(200) NOT NULL,
  `domicilio` varchar(300) NOT NULL,
  `departamento` varchar(50) DEFAULT NULL,
  `provincia` varchar(50) DEFAULT NULL,
  `distrito` varchar(50) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `descripcion_hechos` text NOT NULL,
  `pedido_reclamo` text NOT NULL,
  `monto_reclamado` decimal(10,2) DEFAULT 0.00,
  `tipo_bien` enum('producto','servicio','ambos') NOT NULL,
  `descripcion_bien` text DEFAULT NULL,
  `marca` varchar(100) DEFAULT NULL,
  `modelo` varchar(100) DEFAULT NULL,
  `numero_serie` varchar(100) DEFAULT NULL,
  `tipo_comprobante` enum('boleta','factura','ticket','ninguno') DEFAULT 'ninguno',
  `numero_comprobante` varchar(50) DEFAULT NULL,
  `fecha_compra` date DEFAULT NULL,
  `monto_compra` decimal(10,2) DEFAULT NULL,
  `estado` enum('registrado','en_revision','procesado','resuelto','archivado') DEFAULT 'registrado',
  `observaciones_internas` text DEFAULT NULL,
  `fecha_respuesta` date DEFAULT NULL,
  `respuesta_empresa` text DEFAULT NULL,
  `nombre_representante` varchar(200) DEFAULT NULL,
  `cargo_representante` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `reclamos`
--

INSERT INTO `reclamos` (`id`, `codigo_reclamo`, `tipo_reclamo`, `fecha_incidente`, `fecha_registro`, `tipo_documento`, `numero_documento`, `nombres_apellidos`, `domicilio`, `departamento`, `provincia`, `distrito`, `telefono`, `email`, `descripcion_hechos`, `pedido_reclamo`, `monto_reclamado`, `tipo_bien`, `descripcion_bien`, `marca`, `modelo`, `numero_serie`, `tipo_comprobante`, `numero_comprobante`, `fecha_compra`, `monto_compra`, `estado`, `observaciones_internas`, `fecha_respuesta`, `respuesta_empresa`, `nombre_representante`, `cargo_representante`) VALUES
(1, 'REC-20251219-72A42A', 'queja', '2025-02-15', '2025-12-19 04:15:51', 'DNI', '75375747', 'Martin', 'sedc', NULL, NULL, NULL, '936602622', 'martinserna021@gmail.com', 'fr', '2', 15.00, 'producto', NULL, NULL, NULL, NULL, 'ninguno', NULL, NULL, NULL, 'registrado', NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `seguimiento_reclamos`
--

CREATE TABLE `seguimiento_reclamos` (
  `id` int(11) NOT NULL,
  `reclamo_id` int(11) NOT NULL,
  `fecha_seguimiento` datetime DEFAULT current_timestamp(),
  `usuario_id` int(11) DEFAULT NULL,
  `accion` varchar(100) NOT NULL,
  `descripcion` text NOT NULL,
  `estado_anterior` varchar(50) DEFAULT NULL,
  `estado_nuevo` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `seguimiento_reclamos`
--

INSERT INTO `seguimiento_reclamos` (`id`, `reclamo_id`, `fecha_seguimiento`, `usuario_id`, `accion`, `descripcion`, `estado_anterior`, `estado_nuevo`) VALUES
(1, 1, '2025-12-19 04:15:51', NULL, 'Registro', 'Reclamo registrado en el sistema', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` enum('admin','cajero','almacenero') NOT NULL DEFAULT 'cajero',
  `fecha_registro` timestamp NULL DEFAULT current_timestamp(),
  `activo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `email`, `password`, `rol`, `fecha_registro`, `activo`) VALUES
(1, 'Administrador', 'admin@ferreteria.com', '$2y$10$HC6ZSTgDtkaVX0/I6qbC9O0afpkTYK5VTtzhcx3YODzVT4VFAAIZ2', 'admin', '2025-12-06 22:09:39', 1),
(4, 'Carlos Cajero', 'cajero@ferreteria.com', '$2y$10$DUjTx2JfiHaKIOMZ3wh2tu27K/dZrXDkjXG4WwSZtd5t0BMLIHYtK', 'cajero', '2025-12-14 19:12:56', 1),
(5, 'Ana Almacenera', 'almacen@ferreteria.com', 'ferreteria123', 'almacenero', '2025-12-14 19:12:56', 1),
(6, 'Jesus Rodriguez', 'rodriguezjesas6@autonoma.edu.pe', '$2y$10$4T1kFXoKAgmHq9SOhAF4v.1JRUBu8BPMFcc05fi.W3v1n..uxNwY6', 'admin', '2025-12-15 03:05:16', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

CREATE TABLE `ventas` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL DEFAULT 1,
  `fecha` timestamp NULL DEFAULT current_timestamp(),
  `total` decimal(10,2) NOT NULL,
  `tipo_documento` enum('boleta','factura') NOT NULL,
  `numero_documento` varchar(20) NOT NULL,
  `nombre_cliente` varchar(100) DEFAULT NULL,
  `tipo_documento_cliente` varchar(20) DEFAULT NULL,
  `numero_documento_cliente` varchar(20) DEFAULT NULL,
  `email_cliente` varchar(100) DEFAULT NULL,
  `telefono_cliente` varchar(20) DEFAULT NULL,
  `direccion_cliente` text DEFAULT NULL,
  `estado` enum('pendiente','completada','cancelada') DEFAULT 'pendiente'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `ventas`
--

INSERT INTO `ventas` (`id`, `id_usuario`, `fecha`, `total`, `tipo_documento`, `numero_documento`, `nombre_cliente`, `tipo_documento_cliente`, `numero_documento_cliente`, `email_cliente`, `telefono_cliente`, `direccion_cliente`, `estado`) VALUES
(1, 1, '2025-12-06 22:32:37', 74.25, 'boleta', 'B-510760', NULL, NULL, NULL, NULL, NULL, NULL, 'pendiente'),
(2, 1, '2025-12-06 23:10:31', 290.25, 'boleta', 'B-852874', NULL, NULL, NULL, NULL, NULL, NULL, 'pendiente'),
(3, 1, '2025-12-14 20:02:06', 15.90, 'boleta', 'B-20251214-821', 'martin', 'DNI', '75375747', 'martinserna@gmail.com', '936602622', 'villa', 'pendiente'),
(4, 1, '2025-12-15 00:28:47', 77.60, 'boleta', 'B-20251215-833', 'Juan', 'DNI', '75375747', 'martin.cs.1528@gmail.com', '932123227', 'Aquí', 'pendiente'),
(5, 1, '2025-12-15 12:47:26', 45.80, 'factura', 'F-20251215-681', 'Jull', 'RUC', '10753757479', 'jull@gmail.com', '978456123', 'Villa el Salvador', 'pendiente'),
(6, 1, '2025-12-19 03:48:57', 15.90, 'boleta', 'B-20251219-229', 'MARTIN', 'DNI', '75375747', 'MARTINSERNA021@GMAIL.COM', '936602622', 'A', 'pendiente');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `contactos`
--
ALTER TABLE `contactos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `detalle_ventas`
--
ALTER TABLE `detalle_ventas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_venta` (`id_venta`),
  ADD KEY `id_producto` (`id_producto`);

--
-- Indices de la tabla `movimientos_inventario`
--
ALTER TABLE `movimientos_inventario`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_producto` (`id_producto`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_producto_categoria` (`id_categoria`);

--
-- Indices de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `reclamos`
--
ALTER TABLE `reclamos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo_reclamo` (`codigo_reclamo`),
  ADD KEY `idx_codigo` (`codigo_reclamo`),
  ADD KEY `idx_estado` (`estado`),
  ADD KEY `idx_fecha` (`fecha_registro`);

--
-- Indices de la tabla `seguimiento_reclamos`
--
ALTER TABLE `seguimiento_reclamos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `idx_reclamo` (`reclamo_id`),
  ADD KEY `idx_fecha` (`fecha_seguimiento`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indices de la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `contactos`
--
ALTER TABLE `contactos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `detalle_ventas`
--
ALTER TABLE `detalle_ventas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `movimientos_inventario`
--
ALTER TABLE `movimientos_inventario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `reclamos`
--
ALTER TABLE `reclamos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `seguimiento_reclamos`
--
ALTER TABLE `seguimiento_reclamos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `detalle_ventas`
--
ALTER TABLE `detalle_ventas`
  ADD CONSTRAINT `detalle_ventas_ibfk_1` FOREIGN KEY (`id_venta`) REFERENCES `ventas` (`id`),
  ADD CONSTRAINT `detalle_ventas_ibfk_2` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id`);

--
-- Filtros para la tabla `movimientos_inventario`
--
ALTER TABLE `movimientos_inventario`
  ADD CONSTRAINT `movimientos_inventario_ibfk_1` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id`);

--
-- Filtros para la tabla `productos`
--
ALTER TABLE `productos`
  ADD CONSTRAINT `fk_producto_categoria` FOREIGN KEY (`id_categoria`) REFERENCES `categorias` (`id`);

--
-- Filtros para la tabla `seguimiento_reclamos`
--
ALTER TABLE `seguimiento_reclamos`
  ADD CONSTRAINT `seguimiento_reclamos_ibfk_1` FOREIGN KEY (`reclamo_id`) REFERENCES `reclamos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `seguimiento_reclamos_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD CONSTRAINT `ventas_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
