-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 07-08-2025 a las 21:56:02
-- Versión del servidor: 10.1.38-MariaDB
-- Versión de PHP: 5.6.40

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `DB_Salas`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Mensaje`
--

CREATE TABLE `Mensaje` (
  `id_msj` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_sala` int(11) NOT NULL,
  `msj_msj` text NOT NULL,
  `fecha_envio` datetime DEFAULT CURRENT_TIMESTAMP,
  `adjunto` varchar(255) DEFAULT NULL,
  `est_msj` enum('leido','no_leido') DEFAULT 'no_leido'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `Mensaje`
--

INSERT INTO `Mensaje` (`id_msj`, `id_user`, `id_sala`, `msj_msj`, `fecha_envio`, `adjunto`, `est_msj`) VALUES
(2, 1, 1, 'adad', '2025-08-05 15:20:07', NULL, 'no_leido'),
(3, 2, 2, 'asasas', '2025-08-05 17:30:21', NULL, 'no_leido'),
(4, 1, 2, 'acac', '2025-08-05 17:33:10', NULL, 'no_leido'),
(5, 1, 4, 'KAKAKAKKskskska\r\n\r\n', '2025-08-05 17:58:19', NULL, 'no_leido'),
(6, 1, 2, 'ftftff', '2025-08-05 18:00:10', NULL, 'no_leido'),
(7, 1, 5, 'okokokokk', '2025-08-05 18:00:36', NULL, 'no_leido'),
(8, 1, 5, 'plpll', '2025-08-05 18:00:51', NULL, 'no_leido'),
(9, 1, 2, 'llp', '2025-08-05 18:18:56', NULL, 'no_leido'),
(10, 5, 4, 'pruebaaa', '2025-08-05 18:24:35', NULL, 'no_leido'),
(11, 1, 4, 'adada', '2025-08-05 18:24:48', NULL, 'no_leido'),
(12, 1, 2, 'gtggt', '2025-08-05 18:31:40', NULL, 'no_leido'),
(13, 1, 5, 'okkoko', '2025-08-05 18:45:11', NULL, 'no_leido'),
(14, 5, 4, 'aasasasa', '2025-08-05 18:59:52', NULL, 'no_leido'),
(15, 2, 5, 'aasasasa', '2025-08-05 19:01:36', NULL, 'no_leido'),
(16, 1, 5, 'yyyyy', '2025-08-06 19:35:34', NULL, 'no_leido'),
(17, 1, 2, 'kkkkk', '2025-08-06 21:58:29', NULL, 'no_leido'),
(18, 1, 2, 'dsds', '2025-08-06 22:20:06', NULL, 'no_leido'),
(19, 1, 5, 'asas', '2025-08-06 22:20:43', NULL, 'no_leido'),
(20, 2, 5, 'sds', '2025-08-06 22:21:21', NULL, 'no_leido'),
(21, 2, 5, 'sds', '2025-08-06 22:24:47', NULL, 'no_leido'),
(22, 1, 5, 'sds', '2025-08-06 22:25:29', NULL, 'no_leido'),
(23, 1, 5, 'sdsd', '2025-08-06 22:25:36', NULL, 'no_leido'),
(24, 1, 5, 'adas', '2025-08-07 00:21:54', NULL, 'no_leido'),
(25, 1, 5, 'ssss', '2025-08-07 00:22:01', NULL, 'no_leido'),
(26, 2, 5, 'ksksk', '2025-08-07 00:22:13', NULL, 'no_leido');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Sala`
--

CREATE TABLE `Sala` (
  `id_sala` int(11) NOT NULL,
  `nom_sala` varchar(255) NOT NULL DEFAULT 'Nueva sala',
  `desc_sala` text,
  `ini_sala` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `Sala`
--

INSERT INTO `Sala` (`id_sala`, `nom_sala`, `desc_sala`, `ini_sala`) VALUES
(1, '1', 'Sala de ejemplo 1', '2025-08-05 14:52:16'),
(2, 'Chat entre 2 y 1', NULL, '2025-08-05 17:28:46'),
(3, 'Chat entre 5 y 4', NULL, '2025-08-05 17:54:16'),
(4, 'Chat entre 1 y 5', NULL, '2025-08-05 17:57:59'),
(5, 'Chat con admin, admin2, usuario4, usuario3', NULL, '2025-08-05 18:00:30'),
(6, 'General', NULL, '2025-08-06 22:11:22');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Usuario`
--

CREATE TABLE `Usuario` (
  `id_user` int(100) NOT NULL,
  `nom_user` varchar(70) NOT NULL,
  `tel_user` varchar(12) NOT NULL,
  `fot_user` varchar(100) DEFAULT NULL,
  `det_user` varchar(100) DEFAULT NULL,
  `pass_user` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `Usuario`
--

INSERT INTO `Usuario` (`id_user`, `nom_user`, `tel_user`, `fot_user`, `det_user`, `pass_user`) VALUES
(1, 'admin', '', NULL, NULL, 'admin'),
(2, 'admin2', '', NULL, NULL, 'admin2'),
(3, 'usuario4', '', NULL, NULL, 'usuario4'),
(4, 'usuario3', '', NULL, NULL, 'usuario3'),
(5, 'Usuario 5', '5530852322', NULL, 'Ejemplo de descripciòn', 'Usuario 5');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Usuario_Sala`
--

CREATE TABLE `Usuario_Sala` (
  `id_user_sala` int(100) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  `id_sala` int(11) DEFAULT NULL,
  `fec_fec` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `Usuario_Sala`
--

INSERT INTO `Usuario_Sala` (`id_user_sala`, `id_user`, `id_sala`, `fec_fec`) VALUES
(9, 2, 2, '2025-08-05 17:28:46'),
(10, 1, 2, '2025-08-05 17:28:46'),
(11, 5, 3, '2025-08-05 17:54:16'),
(12, 4, 3, '2025-08-05 17:54:16'),
(13, 1, 4, '2025-08-05 17:57:59'),
(14, 5, 4, '2025-08-05 17:57:59'),
(15, 1, 5, '2025-08-05 18:00:30'),
(16, 4, 5, '2025-08-05 18:00:30'),
(17, 3, 5, '2025-08-05 18:16:41'),
(18, 2, 5, '2025-08-05 19:01:21');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `Mensaje`
--
ALTER TABLE `Mensaje`
  ADD PRIMARY KEY (`id_msj`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_sala` (`id_sala`);

--
-- Indices de la tabla `Sala`
--
ALTER TABLE `Sala`
  ADD PRIMARY KEY (`id_sala`);

--
-- Indices de la tabla `Usuario`
--
ALTER TABLE `Usuario`
  ADD PRIMARY KEY (`id_user`);

--
-- Indices de la tabla `Usuario_Sala`
--
ALTER TABLE `Usuario_Sala`
  ADD PRIMARY KEY (`id_user_sala`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_sala` (`id_sala`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `Mensaje`
--
ALTER TABLE `Mensaje`
  MODIFY `id_msj` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT de la tabla `Sala`
--
ALTER TABLE `Sala`
  MODIFY `id_sala` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `Usuario`
--
ALTER TABLE `Usuario`
  MODIFY `id_user` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `Usuario_Sala`
--
ALTER TABLE `Usuario_Sala`
  MODIFY `id_user_sala` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `Mensaje`
--
ALTER TABLE `Mensaje`
  ADD CONSTRAINT `Mensaje_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `Usuario` (`id_user`),
  ADD CONSTRAINT `Mensaje_ibfk_2` FOREIGN KEY (`id_sala`) REFERENCES `Sala` (`id_sala`);

--
-- Filtros para la tabla `Usuario_Sala`
--
ALTER TABLE `Usuario_Sala`
  ADD CONSTRAINT `Usuario_Sala_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `Usuario` (`id_user`),
  ADD CONSTRAINT `Usuario_Sala_ibfk_2` FOREIGN KEY (`id_sala`) REFERENCES `Sala` (`id_sala`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
