-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 24-06-2024 a las 23:51:50
-- Versión del servidor: 10.4.27-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `wp_test`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `wp_cm_owners`
--

CREATE TABLE `wp_cm_owners` (
  `id` int(11) NOT NULL,
  `nombre` varchar(255) DEFAULT NULL,
  `apellido` varchar(255) DEFAULT NULL,
  `rut` varchar(15) DEFAULT NULL,
  `telefono` varchar(15) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `torre` varchar(15) DEFAULT NULL,
  `depa` varchar(10) DEFAULT NULL,
  `parking` varchar(10) DEFAULT NULL,
  `bodega` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `wp_cm_rents`
--

CREATE TABLE `wp_cm_rents` (
  `id` int(11) NOT NULL,
  `nombre_rent` varchar(255) DEFAULT NULL,
  `apellido_rent` varchar(255) DEFAULT NULL,
  `rut_rent` varchar(15) DEFAULT NULL,
  `telf_rent` varchar(15) DEFAULT NULL,
  `email_rent` varchar(255) DEFAULT NULL,
  `dia_ini` date DEFAULT NULL,
  `dia_fin` date DEFAULT NULL,
  `depa_rent` varchar(10) DEFAULT NULL,
  `torre_rent` varchar(10) DEFAULT NULL,
  `esta_rent` varchar(3) DEFAULT NULL,
  `patent_rent` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `wp_cm_visit`
--

CREATE TABLE `wp_cm_visit` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `apellido` varchar(50) NOT NULL,
  `rut` varchar(15) NOT NULL,
  `patente` varchar(10) DEFAULT NULL,
  `torre` varchar(15) DEFAULT NULL,
  `apart` varchar(3) DEFAULT NULL,
  `habilita` text DEFAULT NULL,
  `fecha` date NOT NULL,
  `hora` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `wp_cm_owners`
--
ALTER TABLE `wp_cm_owners`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `wp_cm_rents`
--
ALTER TABLE `wp_cm_rents`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Indices de la tabla `wp_cm_visit`
--
ALTER TABLE `wp_cm_visit`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `wp_cm_owners`
--
ALTER TABLE `wp_cm_owners`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `wp_cm_rents`
--
ALTER TABLE `wp_cm_rents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `wp_cm_visit`
--
ALTER TABLE `wp_cm_visit`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
