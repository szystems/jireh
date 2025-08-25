-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 26-08-2025 a las 01:17:13
-- Versión del servidor: 10.4.27-MariaDB
-- Versión de PHP: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `dbjirehapp`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `articulos`
--

CREATE TABLE `articulos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `codigo` varchar(191) DEFAULT NULL,
  `nombre` varchar(191) NOT NULL,
  `imagen` varchar(191) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `precio_compra` decimal(10,2) NOT NULL,
  `precio_venta` decimal(10,2) NOT NULL,
  `stock` decimal(10,2) NOT NULL,
  `stock_inicial` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Stock inicial del artículo para auditoría',
  `stock_minimo` decimal(10,2) NOT NULL,
  `categoria_id` bigint(20) UNSIGNED NOT NULL,
  `unidad_id` bigint(20) UNSIGNED NOT NULL,
  `tipo` enum('articulo','servicio') NOT NULL,
  `mecanico_id` bigint(20) UNSIGNED DEFAULT NULL,
  `costo_mecanico` decimal(10,2) DEFAULT 0.00,
  `comision_carwash` decimal(10,2) DEFAULT 0.00,
  `estado` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(191) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `estado` tinyint(4) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(191) NOT NULL,
  `fecha_nacimiento` date NOT NULL,
  `telefono` varchar(191) DEFAULT NULL,
  `celular` varchar(191) NOT NULL,
  `direccion` text DEFAULT NULL,
  `email` varchar(191) DEFAULT NULL,
  `dpi` varchar(191) DEFAULT NULL,
  `nit` varchar(191) DEFAULT NULL,
  `fotografia` varchar(191) DEFAULT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comisiones`
--

CREATE TABLE `comisiones` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `commissionable_id` bigint(20) UNSIGNED NOT NULL,
  `commissionable_type` varchar(191) NOT NULL,
  `tipo_comision` varchar(50) NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `porcentaje` decimal(5,2) DEFAULT NULL,
  `detalle_venta_id` bigint(20) UNSIGNED DEFAULT NULL,
  `venta_id` bigint(20) UNSIGNED DEFAULT NULL,
  `articulo_id` bigint(20) UNSIGNED DEFAULT NULL,
  `estado` enum('pendiente','pagado','cancelado') NOT NULL DEFAULT 'pendiente',
  `fecha_calculo` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configs`
--

CREATE TABLE `configs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `logo` varchar(191) DEFAULT NULL,
  `email` varchar(191) DEFAULT NULL,
  `time_zone` varchar(191) NOT NULL DEFAULT 'America/Guatemala',
  `currency` varchar(191) NOT NULL DEFAULT 'GTQ Q',
  `currency_simbol` varchar(191) NOT NULL DEFAULT 'Q',
  `currency_iso` varchar(191) NOT NULL DEFAULT 'GTQ',
  `fb_link` varchar(191) DEFAULT NULL,
  `inst_link` varchar(191) DEFAULT NULL,
  `yt_link` varchar(191) DEFAULT NULL,
  `wapp_link` varchar(191) DEFAULT NULL,
  `descuento_maximo` decimal(8,2) NOT NULL DEFAULT 0.00,
  `impuesto` decimal(8,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `configs`
--

INSERT INTO `configs` (`id`, `logo`, `email`, `time_zone`, `currency`, `currency_simbol`, `currency_iso`, `fb_link`, `inst_link`, `yt_link`, `wapp_link`, `descuento_maximo`, `impuesto`, `created_at`, `updated_at`) VALUES
(1, NULL, 'info@jirehautomotriz.com', 'America/Guatemala', 'GTQ Q', 'Q', 'GTQ', NULL, NULL, NULL, NULL, '0.00', '0.00', '2025-08-25 23:06:54', '2025-08-25 23:06:54');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `descuentos`
--

CREATE TABLE `descuentos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(191) NOT NULL,
  `porcentaje_descuento` decimal(5,2) NOT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_ingresos`
--

CREATE TABLE `detalle_ingresos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ingreso_id` bigint(20) UNSIGNED NOT NULL,
  `articulo_id` bigint(20) UNSIGNED NOT NULL,
  `precio_compra` decimal(10,2) NOT NULL,
  `precio_venta` decimal(10,2) NOT NULL,
  `cantidad` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_pagos_sueldos`
--

CREATE TABLE `detalle_pagos_sueldos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `pago_sueldo_id` bigint(20) UNSIGNED NOT NULL COMMENT 'ID del lote de pago al que pertenece',
  `trabajador_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'ID del trabajador si aplica',
  `usuario_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'ID del usuario/vendedor si aplica',
  `tipo_empleado` enum('trabajador','vendedor') NOT NULL COMMENT 'Tipo de empleado: trabajador o vendedor',
  `sueldo_base` decimal(10,2) NOT NULL COMMENT 'Sueldo base del empleado',
  `bonificaciones` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Bonos, horas extra, comisiones adicionales',
  `deducciones` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Descuentos, préstamos, deducciones legales',
  `total_pagar` decimal(10,2) NOT NULL COMMENT 'Monto final a pagar (base + bonos - deducciones)',
  `observaciones` text DEFAULT NULL COMMENT 'Notas específicas para este empleado',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `horas_extra` decimal(8,2) NOT NULL DEFAULT 0.00 COMMENT 'Cantidad de horas extra trabajadas',
  `valor_hora_extra` decimal(8,2) NOT NULL DEFAULT 0.00 COMMENT 'Valor por hora extra',
  `comisiones` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Comisiones ganadas',
  `estado` enum('pendiente','pagado','cancelado') NOT NULL DEFAULT 'pendiente' COMMENT 'Estado individual del pago',
  `fecha_pago` timestamp NULL DEFAULT NULL COMMENT 'Fecha cuando se marcó como pagado',
  `observaciones_pago` text DEFAULT NULL COMMENT 'Observaciones del pago individual'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_ventas`
--

CREATE TABLE `detalle_ventas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `venta_id` bigint(20) UNSIGNED NOT NULL,
  `articulo_id` bigint(20) UNSIGNED DEFAULT NULL,
  `cantidad` decimal(10,2) NOT NULL,
  `precio_costo` decimal(10,2) NOT NULL DEFAULT 0.00,
  `precio_venta` decimal(10,2) NOT NULL DEFAULT 0.00,
  `descuento_id` bigint(20) UNSIGNED DEFAULT NULL,
  `usuario_id` bigint(20) UNSIGNED NOT NULL,
  `sub_total` decimal(10,2) NOT NULL,
  `porcentaje_impuestos` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(191) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ingresos`
--

CREATE TABLE `ingresos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `numero_factura` varchar(191) DEFAULT NULL,
  `fecha` date NOT NULL,
  `proveedor_id` varchar(191) NOT NULL,
  `tipo_compra` enum('Car Wash','CDS') NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `lotes_pago`
--

CREATE TABLE `lotes_pago` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `numero_lote` varchar(191) NOT NULL,
  `fecha_pago` date NOT NULL,
  `metodo_pago` enum('efectivo','transferencia','cheque','otro') NOT NULL DEFAULT 'efectivo',
  `referencia` varchar(191) DEFAULT NULL,
  `comprobante_imagen` varchar(191) DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `monto_total` decimal(10,2) NOT NULL,
  `cantidad_comisiones` int(11) NOT NULL,
  `estado` enum('procesando','completado','anulado') NOT NULL DEFAULT 'procesando',
  `usuario_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `metas_ventas`
--

CREATE TABLE `metas_ventas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(191) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `monto_minimo` decimal(10,2) NOT NULL,
  `monto_maximo` decimal(10,2) DEFAULT NULL,
  `porcentaje_comision` decimal(5,2) NOT NULL,
  `periodo` enum('mensual','trimestral','semestral','anual') NOT NULL DEFAULT 'mensual',
  `estado` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(191) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(5, '2024_11_25_114748_create_configs_table', 1),
(6, '2024_12_03_100154_create_clientes_table', 1),
(7, '2024_12_03_112938_create_vehiculos_table', 1),
(8, '2024_12_06_160809_create_categorias_table', 1),
(9, '2024_12_09_104742_create_proveedors_table', 1),
(10, '2025_01_03_111452_create_unidads_table', 1),
(11, '2025_01_30_105843_create_articulos_table', 1),
(12, '2025_02_12_104058_create_ingresos_table', 1),
(13, '2025_02_12_104219_create_detalle_ingresos_table', 1),
(14, '2025_03_04_164717_create_trabajadors_table', 1),
(15, '2025_03_11_104050_create_descuentos_table', 1),
(16, '2025_03_12_100432_create_ventas_table', 1),
(17, '2025_03_12_101231_create_detalle_ventas_table', 1),
(18, '2025_03_18_153146_create_pagos_table', 1),
(19, '2025_04_30_000003_create_comisiones_table', 1),
(20, '2025_04_30_000004_create_pagos_comisiones_table', 1),
(21, '2025_04_30_000005_create_metas_ventas_table', 1),
(22, '2025_04_30_000007_create_trabajador_detalle_venta_table', 1),
(23, '2025_06_30_125341_create_movimientos_stock_table', 1),
(24, '2025_08_06_000001_add_estado_to_pagos_comisiones_table', 1),
(25, '2025_08_08_114602_create_lotes_pago_table', 1),
(26, '2025_08_08_115819_add_lote_pago_id_to_pagos_comisiones_table', 1),
(27, '2025_08_14_105423_create_pagos_sueldos_table', 1),
(28, '2025_08_14_105742_create_detalle_pagos_sueldos_table', 1),
(29, '2025_08_15_170151_agregar_campos_detallados_a_detalle_pagos_sueldos', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `movimientos_stock`
--

CREATE TABLE `movimientos_stock` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `articulo_id` bigint(20) UNSIGNED NOT NULL,
  `tipo` varchar(50) NOT NULL,
  `stock_anterior` decimal(10,2) NOT NULL,
  `stock_nuevo` decimal(10,2) NOT NULL,
  `cantidad` decimal(10,2) NOT NULL,
  `referencia_tipo` varchar(50) DEFAULT NULL,
  `referencia_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagos`
--

CREATE TABLE `pagos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `venta_id` bigint(20) UNSIGNED NOT NULL,
  `fecha` date NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `metodo_pago` enum('efectivo','tarjeta_credito','tarjeta_debito','transferencia','cheque','otro') NOT NULL,
  `referencia` varchar(191) DEFAULT NULL,
  `comprobante_imagen` varchar(191) DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `usuario_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagos_comisiones`
--

CREATE TABLE `pagos_comisiones` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `lote_pago_id` bigint(20) UNSIGNED DEFAULT NULL,
  `comision_id` bigint(20) UNSIGNED NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `metodo_pago` enum('efectivo','transferencia','cheque','otro') NOT NULL DEFAULT 'efectivo',
  `usuario_id` bigint(20) UNSIGNED NOT NULL,
  `fecha_pago` date NOT NULL,
  `referencia` varchar(191) DEFAULT NULL,
  `comprobante_imagen` varchar(191) DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `estado` enum('pendiente','completado','anulado') NOT NULL DEFAULT 'pendiente',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagos_sueldos`
--

CREATE TABLE `pagos_sueldos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `numero_lote` varchar(50) NOT NULL COMMENT 'Formato: PS-YYYYMM-XXX',
  `periodo_mes` int(11) NOT NULL COMMENT '1-12 (Enero a Diciembre)',
  `periodo_anio` int(11) NOT NULL COMMENT 'Año del período de pago',
  `fecha_pago` date NOT NULL COMMENT 'Fecha en que se realiza el pago',
  `metodo_pago` enum('efectivo','transferencia','cheque') NOT NULL DEFAULT 'transferencia' COMMENT 'Método utilizado para el pago',
  `estado` enum('pendiente','pagado','cancelado') NOT NULL DEFAULT 'pendiente' COMMENT 'Estado del lote de pago',
  `total_monto` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Suma total de todos los sueldos del lote',
  `observaciones` text DEFAULT NULL COMMENT 'Notas adicionales del lote',
  `comprobante_pago` varchar(255) DEFAULT NULL COMMENT 'Archivo de imagen del comprobante',
  `usuario_creo_id` bigint(20) UNSIGNED NOT NULL COMMENT 'Usuario que creó el lote',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(191) NOT NULL,
  `token` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(191) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedors`
--

CREATE TABLE `proveedors` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(191) NOT NULL,
  `nit` varchar(191) DEFAULT NULL,
  `contacto` varchar(191) DEFAULT NULL,
  `telefono` varchar(191) DEFAULT NULL,
  `celular` varchar(191) DEFAULT NULL,
  `direccion` varchar(191) DEFAULT NULL,
  `email` varchar(191) DEFAULT NULL,
  `banco` varchar(191) DEFAULT NULL,
  `nombre_cuenta` varchar(191) DEFAULT NULL,
  `tipo_cuenta` varchar(191) DEFAULT NULL,
  `numero_cuenta` varchar(191) DEFAULT NULL,
  `estado` tinyint(4) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `servicio_articulo`
--

CREATE TABLE `servicio_articulo` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `servicio_id` bigint(20) UNSIGNED NOT NULL,
  `articulo_id` bigint(20) UNSIGNED NOT NULL,
  `cantidad` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_trabajadors`
--

CREATE TABLE `tipo_trabajadors` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(191) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `aplica_comision` tinyint(1) NOT NULL DEFAULT 0,
  `requiere_asignacion` tinyint(1) NOT NULL DEFAULT 0,
  `tipo_comision` varchar(191) DEFAULT NULL,
  `valor_comision` decimal(10,2) DEFAULT NULL,
  `porcentaje_comision` decimal(5,2) DEFAULT NULL,
  `permite_multiples_trabajadores` tinyint(1) NOT NULL DEFAULT 0,
  `configuracion_adicional` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`configuracion_adicional`)),
  `estado` enum('activo','inactivo') NOT NULL DEFAULT 'activo',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tipo_trabajadors`
--

INSERT INTO `tipo_trabajadors` (`id`, `nombre`, `descripcion`, `aplica_comision`, `requiere_asignacion`, `tipo_comision`, `valor_comision`, `porcentaje_comision`, `permite_multiples_trabajadores`, `configuracion_adicional`, `estado`, `created_at`, `updated_at`) VALUES
(1, 'Mecánico', 'Trabaja en reparación de vehículos', 1, 1, 'fijo', '50.00', NULL, 0, NULL, 'activo', '2025-08-25 23:06:52', '2025-08-25 23:06:52'),
(2, 'Car Wash', 'Trabaja en limpieza de vehículos', 1, 0, 'variable', NULL, NULL, 1, NULL, 'activo', '2025-08-25 23:06:52', '2025-08-25 23:06:52');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `trabajadors`
--

CREATE TABLE `trabajadors` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(191) NOT NULL,
  `apellido` varchar(191) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `direccion` varchar(191) DEFAULT NULL,
  `email` varchar(191) DEFAULT NULL,
  `nit` varchar(191) DEFAULT NULL,
  `dpi` varchar(191) DEFAULT NULL,
  `tipo` bigint(20) UNSIGNED DEFAULT NULL,
  `tipo_trabajador_id` bigint(20) UNSIGNED DEFAULT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `trabajador_detalle_venta`
--

CREATE TABLE `trabajador_detalle_venta` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `trabajador_id` bigint(20) UNSIGNED NOT NULL,
  `detalle_venta_id` bigint(20) UNSIGNED NOT NULL,
  `monto_comision` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `unidads`
--

CREATE TABLE `unidads` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(191) NOT NULL,
  `abreviatura` varchar(191) NOT NULL,
  `tipo` enum('unidad','decimal') NOT NULL,
  `estado` tinyint(4) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `unidads`
--

INSERT INTO `unidads` (`id`, `nombre`, `abreviatura`, `tipo`, `estado`, `created_at`, `updated_at`) VALUES
(1, 'Unidad', 'UND', 'unidad', 1, '2025-08-25 23:06:54', '2025-08-25 23:06:54'),
(2, 'Kilogramo', 'KG', 'decimal', 1, '2025-08-25 23:06:54', '2025-08-25 23:06:54'),
(3, 'Litro', 'L', 'decimal', 1, '2025-08-25 23:06:54', '2025-08-25 23:06:54'),
(4, 'Metro', 'M', 'decimal', 1, '2025-08-25 23:06:54', '2025-08-25 23:06:54'),
(5, 'Centímetro', 'CM', 'decimal', 1, '2025-08-25 23:06:54', '2025-08-25 23:06:54'),
(6, 'Mililitro', 'ML', 'decimal', 1, '2025-08-25 23:06:54', '2025-08-25 23:06:54');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `email` varchar(191) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(191) NOT NULL,
  `role_as` tinyint(4) NOT NULL DEFAULT 0,
  `principal` tinyint(4) NOT NULL DEFAULT 0,
  `estado` tinyint(4) NOT NULL DEFAULT 1,
  `fotografia` varchar(191) DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `celular` varchar(191) DEFAULT NULL,
  `telefono` varchar(191) DEFAULT NULL,
  `direccion` varchar(191) DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `role_as`, `principal`, `estado`, `fotografia`, `fecha_nacimiento`, `celular`, `telefono`, `direccion`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Emilio Rodriguez', 'jirehautomotrizventas@gmail.com', NULL, '$2y$10$XjZjF0mQvsE9WdTV623uruTIhVK8o5SLzwMQamtK9CnwVJppdljMy', 0, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-25 23:06:54', '2025-08-25 23:06:54'),
(2, 'Otto Szarata', 'szystems@hotmail.com', NULL, '$2y$10$v09jdr5NPrrUH25BFMHnSemoLffXZS0POoHa43lrC7HFaidJGLcuW', 0, 0, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-25 23:06:54', '2025-08-25 23:06:54');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vehiculos`
--

CREATE TABLE `vehiculos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `cliente_id` bigint(20) UNSIGNED NOT NULL,
  `marca` varchar(191) NOT NULL,
  `modelo` varchar(191) NOT NULL,
  `ano` year(4) NOT NULL,
  `color` varchar(191) NOT NULL,
  `placa` varchar(191) NOT NULL,
  `vin` varchar(191) NOT NULL,
  `fotografia` varchar(191) DEFAULT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

CREATE TABLE `ventas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `cliente_id` bigint(20) UNSIGNED DEFAULT NULL,
  `vehiculo_id` bigint(20) UNSIGNED DEFAULT NULL,
  `numero_factura` varchar(191) DEFAULT NULL,
  `fecha` date NOT NULL,
  `tipo_venta` enum('Car Wash','CDS') NOT NULL,
  `usuario_id` bigint(20) UNSIGNED DEFAULT NULL,
  `estado` tinyint(1) DEFAULT 1,
  `estado_pago` enum('pendiente','pagado','parcial') NOT NULL DEFAULT 'pendiente',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `articulos`
--
ALTER TABLE `articulos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `articulos_categoria_id_foreign` (`categoria_id`),
  ADD KEY `articulos_unidad_id_foreign` (`unidad_id`),
  ADD KEY `articulos_mecanico_id_foreign` (`mecanico_id`);

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `clientes_email_unique` (`email`);

--
-- Indices de la tabla `comisiones`
--
ALTER TABLE `comisiones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `comisiones_detalle_venta_id_foreign` (`detalle_venta_id`),
  ADD KEY `comisiones_venta_id_foreign` (`venta_id`),
  ADD KEY `comisiones_articulo_id_foreign` (`articulo_id`),
  ADD KEY `comisiones_commissionable_id_commissionable_type_index` (`commissionable_id`,`commissionable_type`),
  ADD KEY `comisiones_estado_index` (`estado`),
  ADD KEY `comisiones_fecha_calculo_index` (`fecha_calculo`);

--
-- Indices de la tabla `configs`
--
ALTER TABLE `configs`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `descuentos`
--
ALTER TABLE `descuentos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `detalle_ingresos`
--
ALTER TABLE `detalle_ingresos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `detalle_ingresos_ingreso_id_foreign` (`ingreso_id`),
  ADD KEY `detalle_ingresos_articulo_id_foreign` (`articulo_id`);

--
-- Indices de la tabla `detalle_pagos_sueldos`
--
ALTER TABLE `detalle_pagos_sueldos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `detalle_pagos_sueldos_trabajador_id_foreign` (`trabajador_id`),
  ADD KEY `detalle_pagos_sueldos_usuario_id_foreign` (`usuario_id`),
  ADD KEY `idx_lote_pago` (`pago_sueldo_id`),
  ADD KEY `idx_empleado_trabajador` (`tipo_empleado`,`trabajador_id`),
  ADD KEY `idx_empleado_usuario` (`tipo_empleado`,`usuario_id`);

--
-- Indices de la tabla `detalle_ventas`
--
ALTER TABLE `detalle_ventas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `detalle_ventas_venta_id_foreign` (`venta_id`),
  ADD KEY `detalle_ventas_articulo_id_foreign` (`articulo_id`),
  ADD KEY `detalle_ventas_descuento_id_foreign` (`descuento_id`);

--
-- Indices de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indices de la tabla `ingresos`
--
ALTER TABLE `ingresos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `lotes_pago`
--
ALTER TABLE `lotes_pago`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `lotes_pago_numero_lote_unique` (`numero_lote`),
  ADD KEY `lotes_pago_usuario_id_foreign` (`usuario_id`),
  ADD KEY `lotes_pago_fecha_pago_index` (`fecha_pago`),
  ADD KEY `lotes_pago_estado_index` (`estado`),
  ADD KEY `lotes_pago_numero_lote_index` (`numero_lote`);

--
-- Indices de la tabla `metas_ventas`
--
ALTER TABLE `metas_ventas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `metas_ventas_periodo_estado_index` (`periodo`,`estado`),
  ADD KEY `metas_ventas_monto_minimo_index` (`monto_minimo`);

--
-- Indices de la tabla `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `movimientos_stock`
--
ALTER TABLE `movimientos_stock`
  ADD PRIMARY KEY (`id`),
  ADD KEY `movimientos_stock_user_id_foreign` (`user_id`),
  ADD KEY `movimientos_stock_articulo_id_created_at_index` (`articulo_id`,`created_at`),
  ADD KEY `movimientos_stock_tipo_created_at_index` (`tipo`,`created_at`),
  ADD KEY `movimientos_stock_referencia_tipo_referencia_id_index` (`referencia_tipo`,`referencia_id`);

--
-- Indices de la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pagos_venta_id_foreign` (`venta_id`);

--
-- Indices de la tabla `pagos_comisiones`
--
ALTER TABLE `pagos_comisiones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pagos_comisiones_comision_id_foreign` (`comision_id`),
  ADD KEY `pagos_comisiones_usuario_id_foreign` (`usuario_id`),
  ADD KEY `pagos_comisiones_fecha_pago_index` (`fecha_pago`),
  ADD KEY `pagos_comisiones_estado_index` (`estado`),
  ADD KEY `pagos_comisiones_lote_pago_id_index` (`lote_pago_id`);

--
-- Indices de la tabla `pagos_sueldos`
--
ALTER TABLE `pagos_sueldos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `pagos_sueldos_numero_lote_unique` (`numero_lote`),
  ADD KEY `pagos_sueldos_usuario_creo_id_foreign` (`usuario_creo_id`),
  ADD KEY `idx_periodo` (`periodo_anio`,`periodo_mes`),
  ADD KEY `idx_estado` (`estado`),
  ADD KEY `idx_fecha_pago` (`fecha_pago`);

--
-- Indices de la tabla `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indices de la tabla `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indices de la tabla `proveedors`
--
ALTER TABLE `proveedors`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `servicio_articulo`
--
ALTER TABLE `servicio_articulo`
  ADD PRIMARY KEY (`id`),
  ADD KEY `servicio_articulo_servicio_id_foreign` (`servicio_id`),
  ADD KEY `servicio_articulo_articulo_id_foreign` (`articulo_id`);

--
-- Indices de la tabla `tipo_trabajadors`
--
ALTER TABLE `tipo_trabajadors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tipo_trabajadors_nombre_unique` (`nombre`);

--
-- Indices de la tabla `trabajadors`
--
ALTER TABLE `trabajadors`
  ADD PRIMARY KEY (`id`),
  ADD KEY `trabajadors_tipo_trabajador_id_foreign` (`tipo_trabajador_id`);

--
-- Indices de la tabla `trabajador_detalle_venta`
--
ALTER TABLE `trabajador_detalle_venta`
  ADD PRIMARY KEY (`id`),
  ADD KEY `trabajador_detalle_venta_detalle_venta_id_foreign` (`detalle_venta_id`),
  ADD KEY `trabajador_detalle_venta_trabajador_id_detalle_venta_id_index` (`trabajador_id`,`detalle_venta_id`);

--
-- Indices de la tabla `unidads`
--
ALTER TABLE `unidads`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indices de la tabla `vehiculos`
--
ALTER TABLE `vehiculos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `vehiculos_placa_unique` (`placa`),
  ADD UNIQUE KEY `vehiculos_vin_unique` (`vin`),
  ADD KEY `vehiculos_cliente_id_foreign` (`cliente_id`);

--
-- Indices de la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ventas_cliente_id_foreign` (`cliente_id`),
  ADD KEY `ventas_vehiculo_id_foreign` (`vehiculo_id`),
  ADD KEY `ventas_usuario_id_foreign` (`usuario_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `articulos`
--
ALTER TABLE `articulos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `comisiones`
--
ALTER TABLE `comisiones`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `configs`
--
ALTER TABLE `configs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `descuentos`
--
ALTER TABLE `descuentos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `detalle_ingresos`
--
ALTER TABLE `detalle_ingresos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `detalle_pagos_sueldos`
--
ALTER TABLE `detalle_pagos_sueldos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `detalle_ventas`
--
ALTER TABLE `detalle_ventas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `ingresos`
--
ALTER TABLE `ingresos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `lotes_pago`
--
ALTER TABLE `lotes_pago`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `metas_ventas`
--
ALTER TABLE `metas_ventas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT de la tabla `movimientos_stock`
--
ALTER TABLE `movimientos_stock`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pagos`
--
ALTER TABLE `pagos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pagos_comisiones`
--
ALTER TABLE `pagos_comisiones`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pagos_sueldos`
--
ALTER TABLE `pagos_sueldos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `proveedors`
--
ALTER TABLE `proveedors`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `servicio_articulo`
--
ALTER TABLE `servicio_articulo`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tipo_trabajadors`
--
ALTER TABLE `tipo_trabajadors`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `trabajadors`
--
ALTER TABLE `trabajadors`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `trabajador_detalle_venta`
--
ALTER TABLE `trabajador_detalle_venta`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `unidads`
--
ALTER TABLE `unidads`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `vehiculos`
--
ALTER TABLE `vehiculos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `articulos`
--
ALTER TABLE `articulos`
  ADD CONSTRAINT `articulos_categoria_id_foreign` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`),
  ADD CONSTRAINT `articulos_mecanico_id_foreign` FOREIGN KEY (`mecanico_id`) REFERENCES `trabajadors` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `articulos_unidad_id_foreign` FOREIGN KEY (`unidad_id`) REFERENCES `unidads` (`id`);

--
-- Filtros para la tabla `comisiones`
--
ALTER TABLE `comisiones`
  ADD CONSTRAINT `comisiones_articulo_id_foreign` FOREIGN KEY (`articulo_id`) REFERENCES `articulos` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `comisiones_detalle_venta_id_foreign` FOREIGN KEY (`detalle_venta_id`) REFERENCES `detalle_ventas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comisiones_venta_id_foreign` FOREIGN KEY (`venta_id`) REFERENCES `ventas` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `detalle_ingresos`
--
ALTER TABLE `detalle_ingresos`
  ADD CONSTRAINT `detalle_ingresos_articulo_id_foreign` FOREIGN KEY (`articulo_id`) REFERENCES `articulos` (`id`),
  ADD CONSTRAINT `detalle_ingresos_ingreso_id_foreign` FOREIGN KEY (`ingreso_id`) REFERENCES `ingresos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `detalle_pagos_sueldos`
--
ALTER TABLE `detalle_pagos_sueldos`
  ADD CONSTRAINT `detalle_pagos_sueldos_pago_sueldo_id_foreign` FOREIGN KEY (`pago_sueldo_id`) REFERENCES `pagos_sueldos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `detalle_pagos_sueldos_trabajador_id_foreign` FOREIGN KEY (`trabajador_id`) REFERENCES `trabajadors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `detalle_pagos_sueldos_usuario_id_foreign` FOREIGN KEY (`usuario_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `detalle_ventas`
--
ALTER TABLE `detalle_ventas`
  ADD CONSTRAINT `detalle_ventas_articulo_id_foreign` FOREIGN KEY (`articulo_id`) REFERENCES `articulos` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `detalle_ventas_descuento_id_foreign` FOREIGN KEY (`descuento_id`) REFERENCES `descuentos` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `detalle_ventas_venta_id_foreign` FOREIGN KEY (`venta_id`) REFERENCES `ventas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `lotes_pago`
--
ALTER TABLE `lotes_pago`
  ADD CONSTRAINT `lotes_pago_usuario_id_foreign` FOREIGN KEY (`usuario_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `movimientos_stock`
--
ALTER TABLE `movimientos_stock`
  ADD CONSTRAINT `movimientos_stock_articulo_id_foreign` FOREIGN KEY (`articulo_id`) REFERENCES `articulos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `movimientos_stock_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD CONSTRAINT `pagos_venta_id_foreign` FOREIGN KEY (`venta_id`) REFERENCES `ventas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `pagos_comisiones`
--
ALTER TABLE `pagos_comisiones`
  ADD CONSTRAINT `pagos_comisiones_comision_id_foreign` FOREIGN KEY (`comision_id`) REFERENCES `comisiones` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pagos_comisiones_lote_pago_id_foreign` FOREIGN KEY (`lote_pago_id`) REFERENCES `lotes_pago` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `pagos_comisiones_usuario_id_foreign` FOREIGN KEY (`usuario_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `pagos_sueldos`
--
ALTER TABLE `pagos_sueldos`
  ADD CONSTRAINT `pagos_sueldos_usuario_creo_id_foreign` FOREIGN KEY (`usuario_creo_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `servicio_articulo`
--
ALTER TABLE `servicio_articulo`
  ADD CONSTRAINT `servicio_articulo_articulo_id_foreign` FOREIGN KEY (`articulo_id`) REFERENCES `articulos` (`id`),
  ADD CONSTRAINT `servicio_articulo_servicio_id_foreign` FOREIGN KEY (`servicio_id`) REFERENCES `articulos` (`id`);

--
-- Filtros para la tabla `trabajadors`
--
ALTER TABLE `trabajadors`
  ADD CONSTRAINT `trabajadors_tipo_trabajador_id_foreign` FOREIGN KEY (`tipo_trabajador_id`) REFERENCES `tipo_trabajadors` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `trabajador_detalle_venta`
--
ALTER TABLE `trabajador_detalle_venta`
  ADD CONSTRAINT `trabajador_detalle_venta_detalle_venta_id_foreign` FOREIGN KEY (`detalle_venta_id`) REFERENCES `detalle_ventas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `trabajador_detalle_venta_trabajador_id_foreign` FOREIGN KEY (`trabajador_id`) REFERENCES `trabajadors` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `vehiculos`
--
ALTER TABLE `vehiculos`
  ADD CONSTRAINT `vehiculos_cliente_id_foreign` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD CONSTRAINT `ventas_cliente_id_foreign` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `ventas_usuario_id_foreign` FOREIGN KEY (`usuario_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `ventas_vehiculo_id_foreign` FOREIGN KEY (`vehiculo_id`) REFERENCES `vehiculos` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
