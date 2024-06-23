-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 03-04-2024 a las 19:25:02
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `rsocial`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comentarios`
--

CREATE TABLE `comentarios` (
  `id` int(11) NOT NULL,
  `id_publicacion` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `contenido` text NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `comentarios`
--

INSERT INTO `comentarios` (`id`, `id_publicacion`, `id_usuario`, `contenido`, `fecha_creacion`, `fecha_actualizacion`) VALUES
(15, 34, 13, '', '2024-03-31 22:48:28', '2024-03-31 22:48:28'),
(16, 37, 12, '', '2024-04-01 18:37:45', '2024-04-01 18:37:45'),
(17, 74, 22, 'jtd juana', '2024-04-03 16:54:26', '2024-04-03 16:54:26');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `publicaciones`
--

CREATE TABLE `publicaciones` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `contenido` text NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `publicaciones`
--

INSERT INTO `publicaciones` (`id`, `id_usuario`, `contenido`, `fecha_creacion`, `fecha_actualizacion`) VALUES
(34, 13, '', '2024-03-31 22:48:26', '2024-03-31 22:48:26'),
(35, 12, '', '2024-04-01 18:16:07', '2024-04-01 18:16:07'),
(36, 12, '', '2024-04-01 18:35:36', '2024-04-01 18:35:36'),
(37, 12, '', '2024-04-01 18:35:45', '2024-04-01 18:35:45'),
(38, 23, 'hola\r\n', '2024-04-01 20:13:34', '2024-04-01 20:13:34'),
(39, 23, 'hola\r\n', '2024-04-01 20:30:33', '2024-04-01 20:30:33'),
(40, 23, 'hola\r\n', '2024-04-01 20:30:42', '2024-04-01 20:30:42'),
(41, 23, '', '2024-04-01 21:25:56', '2024-04-01 21:25:56'),
(42, 23, '', '2024-04-01 21:26:34', '2024-04-01 21:26:34'),
(43, 23, 'hggty', '2024-04-01 21:26:43', '2024-04-01 21:26:43'),
(44, 23, 'hggty', '2024-04-01 21:29:17', '2024-04-01 21:29:17'),
(45, 23, 'hggty', '2024-04-01 21:44:58', '2024-04-01 21:44:58'),
(46, 23, 'hggty', '2024-04-01 21:47:52', '2024-04-01 21:47:52'),
(47, 23, 'hggty', '2024-04-01 21:49:05', '2024-04-01 21:49:05'),
(48, 23, 'hggty', '2024-04-01 21:50:19', '2024-04-01 21:50:19'),
(49, 23, 'hggty', '2024-04-01 21:51:56', '2024-04-01 21:51:56'),
(50, 12, 'kuhsjs', '2024-04-01 21:53:48', '2024-04-01 21:53:48'),
(51, 12, 'kuhsjs', '2024-04-01 21:53:50', '2024-04-01 21:53:50'),
(52, 12, 'kuhsjs', '2024-04-01 22:10:48', '2024-04-01 22:10:48'),
(53, 12, 'kuhsjs', '2024-04-01 22:12:26', '2024-04-01 22:12:26'),
(54, 12, 'kuhsjs', '2024-04-01 22:14:07', '2024-04-01 22:14:07'),
(55, 12, 'kuhsjs', '2024-04-01 22:14:13', '2024-04-01 22:14:13'),
(56, 12, 'kuhsjs', '2024-04-01 22:30:08', '2024-04-01 22:30:08'),
(57, 12, 'kuhsjs', '2024-04-01 22:35:49', '2024-04-01 22:35:49'),
(58, 12, 'kuhsjs', '2024-04-01 22:38:03', '2024-04-01 22:38:03'),
(59, 12, 'kuhsjs', '2024-04-01 22:46:06', '2024-04-01 22:46:06'),
(60, 12, 'kuhsjs', '2024-04-01 22:50:53', '2024-04-01 22:50:53'),
(61, 12, 'kuhsjs', '2024-04-01 22:57:13', '2024-04-01 22:57:13'),
(62, 12, 'kuhsjs', '2024-04-01 22:58:26', '2024-04-01 22:58:26'),
(63, 12, 'kuhsjs', '2024-04-01 22:58:48', '2024-04-01 22:58:48'),
(64, 12, 'kuhsjs', '2024-04-01 23:07:05', '2024-04-01 23:07:05'),
(65, 12, 'kuhsjs', '2024-04-01 23:27:50', '2024-04-01 23:27:50'),
(66, 12, 'kuhsjs', '2024-04-01 23:30:38', '2024-04-01 23:30:38'),
(67, 12, 'kuhsjs', '2024-04-01 23:32:01', '2024-04-01 23:32:01'),
(68, 12, 'kuhsjs', '2024-04-01 23:37:02', '2024-04-01 23:37:02'),
(69, 12, 'kuhsjs', '2024-04-01 23:39:31', '2024-04-01 23:39:31'),
(70, 12, 'kuhsjs', '2024-04-01 23:47:27', '2024-04-01 23:47:27'),
(71, 12, '', '2024-04-03 00:43:20', '2024-04-03 00:43:20'),
(72, 12, '', '2024-04-03 00:44:34', '2024-04-03 00:44:34'),
(73, 12, '', '2024-04-03 00:45:15', '2024-04-03 00:45:15'),
(74, 12, 'hey', '2024-04-03 00:45:22', '2024-04-03 00:45:22');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre_usuario` varchar(255) NOT NULL,
  `correo_electronico` varchar(255) NOT NULL,
  `contrasena` varchar(255) NOT NULL,
  `rol` enum('admin','usuario') NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre_usuario`, `correo_electronico`, `contrasena`, `rol`, `fecha_creacion`, `fecha_actualizacion`) VALUES
(12, 'juana', 'juana@gmail.com', 'juana123', 'usuario', '2024-03-30 20:01:25', '2024-03-30 20:01:25'),
(13, 'amanda', 'scarletherrera@gmail.com', 'amanda123', 'admin', '2024-03-30 21:00:15', '2024-03-31 22:19:59'),
(22, 'pepe', 'lycrek@gmail.com', 'CcQYfewtuu', 'usuario', '2024-03-31 20:36:46', '2024-03-31 20:48:00'),
(23, 'lola', 'scarletherrera@hotmail.com', 'AgkxH8aOV2', 'usuario', '2024-03-31 20:38:15', '2024-03-31 20:49:05');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `comentarios`
--
ALTER TABLE `comentarios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_publicacion` (`id_publicacion`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `publicaciones`
--
ALTER TABLE `publicaciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `comentarios`
--
ALTER TABLE `comentarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de la tabla `publicaciones`
--
ALTER TABLE `publicaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=75;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `comentarios`
--
ALTER TABLE `comentarios`
  ADD CONSTRAINT `comentarios_ibfk_1` FOREIGN KEY (`id_publicacion`) REFERENCES `publicaciones` (`id`),
  ADD CONSTRAINT `comentarios_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `publicaciones`
--
ALTER TABLE `publicaciones`
  ADD CONSTRAINT `publicaciones_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
