-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 15-10-2024 a las 00:35:52
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
-- Base de datos: `refrigerios`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `auxiliar`
--

CREATE TABLE `auxiliar` (
  `ID_AUXILIAR` bigint(20) NOT NULL,
  `CURSO_AUXILIAR` varchar(20) DEFAULT NULL,
  `JORNADA_AUXILIAR` varchar(20) DEFAULT NULL,
  `ID_USUARIOS` bigint(20) DEFAULT NULL,
  `ACTIVO` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `auxiliar`
--

INSERT INTO `auxiliar` (`ID_AUXILIAR`, `CURSO_AUXILIAR`, `JORNADA_AUXILIAR`, `ID_USUARIOS`, `ACTIVO`) VALUES
(24, NULL, NULL, 71, 1),
(25, NULL, NULL, 74, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `coordinador`
--

CREATE TABLE `coordinador` (
  `ID_COORDINADOR` bigint(20) NOT NULL,
  `OFICINA_COORDINADOR` varchar(50) DEFAULT NULL,
  `ID_USUARIOS` bigint(20) DEFAULT NULL,
  `ACTIVO` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `coordinador`
--

INSERT INTO `coordinador` (`ID_COORDINADOR`, `OFICINA_COORDINADOR`, `ID_USUARIOS`, `ACTIVO`) VALUES
(1, NULL, 1, 1),
(35, NULL, 72, 1),
(36, NULL, 73, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `curso`
--

CREATE TABLE `curso` (
  `ID_CURSO` bigint(20) NOT NULL,
  `ID_UBICACION` int(11) NOT NULL,
  `CANTIDAD_ALUMNOS_CURSO` bigint(20) DEFAULT NULL,
  `DIRECTOR_CURSO` varchar(50) DEFAULT NULL,
  `ESTADO_CURSO` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `curso`
--

INSERT INTO `curso` (`ID_CURSO`, `ID_UBICACION`, `CANTIDAD_ALUMNOS_CURSO`, `DIRECTOR_CURSO`, `ESTADO_CURSO`) VALUES
(301, 1, NULL, NULL, NULL),
(302, 1, NULL, NULL, NULL),
(303, 2, 29, 'Carolina', '1'),
(304, 2, NULL, NULL, NULL),
(401, 1, NULL, NULL, NULL),
(402, 1, NULL, NULL, NULL),
(403, 2, NULL, NULL, NULL),
(404, 2, NULL, NULL, NULL),
(501, 1, NULL, NULL, NULL),
(502, 1, NULL, NULL, NULL),
(503, 2, NULL, NULL, NULL),
(504, 2, NULL, NULL, NULL),
(601, 1, NULL, NULL, NULL),
(602, 1, NULL, NULL, NULL),
(603, 2, NULL, NULL, NULL),
(604, 2, NULL, NULL, NULL),
(701, 1, NULL, NULL, NULL),
(702, 1, NULL, NULL, NULL),
(703, 2, NULL, NULL, NULL),
(704, 2, NULL, NULL, NULL),
(801, 1, NULL, NULL, NULL),
(802, 1, NULL, NULL, NULL),
(803, 2, NULL, NULL, NULL),
(804, 2, NULL, NULL, NULL),
(901, 1, NULL, NULL, NULL),
(902, 1, NULL, NULL, NULL),
(903, 2, NULL, NULL, NULL),
(904, 2, NULL, NULL, NULL),
(1001, 1, NULL, NULL, NULL),
(1002, 1, NULL, NULL, NULL),
(1003, 2, NULL, NULL, NULL),
(1004, 2, NULL, NULL, NULL),
(1101, 1, NULL, NULL, NULL),
(1102, 1, NULL, NULL, NULL),
(1103, 2, NULL, NULL, NULL),
(1104, 2, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rector`
--

CREATE TABLE `rector` (
  `ID_RECTOR` bigint(20) NOT NULL,
  `ID_USUARIOS` bigint(20) NOT NULL,
  `ACTIVO` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `rector`
--

INSERT INTO `rector` (`ID_RECTOR`, `ID_USUARIOS`, `ACTIVO`) VALUES
(1, 75, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `refrigerio`
--

CREATE TABLE `refrigerio` (
  `ID_REFRIGERIO` bigint(20) NOT NULL,
  `IMAGEN_REFRIGERIO` varchar(100) NOT NULL,
  `DIA_REFRIGERIO` varchar(100) NOT NULL,
  `FECHA_REFRIGERIO` date NOT NULL DEFAULT curdate(),
  `HORA_REFRIGERIO` time NOT NULL DEFAULT curtime(),
  `TIPO_REFRIGERIO` varchar(20) DEFAULT NULL,
  `ID_UBICACION` int(11) NOT NULL,
  `DESCRIPCION_REFRIGERIO` varchar(100) DEFAULT NULL,
  `ESTADO_REFRIGERIO` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `refrigerios_curso`
--

CREATE TABLE `refrigerios_curso` (
  `ID_ASIG_REF_CUR` bigint(20) NOT NULL,
  `FECHA_ASIGNACION` date DEFAULT curdate(),
  `ID_REFRIGERIO` bigint(20) DEFAULT NULL,
  `ID_CURSO` bigint(20) DEFAULT NULL,
  `CANTIDAD_ASIGNADO` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol_usuario`
--

CREATE TABLE `rol_usuario` (
  `ID_ROL` int(20) NOT NULL,
  `DESCRIPCION` varchar(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `rol_usuario`
--

INSERT INTO `rol_usuario` (`ID_ROL`, `DESCRIPCION`) VALUES
(1, 'Coordinador'),
(2, 'Auxiliar'),
(3, 'Rector');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ubicaciones`
--

CREATE TABLE `ubicaciones` (
  `ID_UBICACION` int(11) NOT NULL,
  `NOMBRE_UBICACION` varchar(100) NOT NULL,
  `DIRECCION_UBICACION` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ubicaciones`
--

INSERT INTO `ubicaciones` (`ID_UBICACION`, `NOMBRE_UBICACION`, `DIRECCION_UBICACION`) VALUES
(1, 'Sede A', ''),
(2, 'Sede B', ''),
(3, 'Sede C', ''),
(4, 'Todas', '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `ID_USUARIOS` bigint(20) NOT NULL,
  `NOMBRE_USUARIO` varchar(50) NOT NULL,
  `CORREO_USUARIO` varchar(50) DEFAULT NULL,
  `TELEFONO_USUARIO` varchar(20) DEFAULT NULL,
  `ID_UBICACION` int(11) NOT NULL,
  `CONTRASENA_USUARIO` varchar(255) NOT NULL,
  `ACTIVO` tinyint(1) DEFAULT 1,
  `ID_ROL` int(11) NOT NULL,
  `FECHA_CREACION` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`ID_USUARIOS`, `NOMBRE_USUARIO`, `CORREO_USUARIO`, `TELEFONO_USUARIO`, `ID_UBICACION`, `CONTRASENA_USUARIO`, `ACTIVO`, `ID_ROL`, `FECHA_CREACION`) VALUES
(1, 'Jhon Freddy', 'paezgarayjhonfreddy@gmail.com', '3022309478', 2, '$2y$10$4EM4is9glF9W56BpyfYHS.Am1j3LrxlF6OwxCvW1AKwVTGKDLEx56', 1, 1, '2024-10-08 13:46:34'),
(71, 'Jhas V', 'jhas@gmail.com', '3425264727', 2, '$2y$10$DcECwAafhpQlKY.BpUaUF.x1lsqEv.VgtEqT7Trmo/2N3DOiwFoH6', 1, 2, '2024-10-08 19:53:23'),
(72, 'angie', 'Angie@gmail.com', '3425264727', 2, '$2y$10$YgO5Nf4P4WPInJXsmrDvHOT7WdGP.sJf2/HZ93QOrwlA/Mj4aHw3W', 1, 1, '2024-10-08 20:20:00'),
(73, 'Alison', 'Alison@gmail.com', '3425264727', 1, '$2y$10$G9AeL33r1J/V/mrUXq.s1euTqIgo14Gho0gu.XZOR8ZFl4jznis/a', 1, 1, '2024-10-09 10:20:57'),
(74, 'Valentina', 'Valentina@gmail.com', '3425264727', 1, '$2y$10$CBG4G29XUOSyFgK3Elm2IeJfHNs2FxsIaHE4ZhQ59m7AfX05WJsR.', 1, 2, '2024-10-09 20:42:34'),
(75, 'Yury', 'Yury@gmail.com', '3022309478', 4, '$2y$10$4EM4is9glF9W56BpyfYHS.Am1j3LrxlF6OwxCvW1AKwVTGKDLEx56', 1, 3, '2024-10-10 17:59:24');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `auxiliar`
--
ALTER TABLE `auxiliar`
  ADD PRIMARY KEY (`ID_AUXILIAR`),
  ADD KEY `auxiliar_ibfk_1` (`ID_USUARIOS`);

--
-- Indices de la tabla `coordinador`
--
ALTER TABLE `coordinador`
  ADD PRIMARY KEY (`ID_COORDINADOR`),
  ADD KEY `coordinador_ibfk_1` (`ID_USUARIOS`);

--
-- Indices de la tabla `curso`
--
ALTER TABLE `curso`
  ADD PRIMARY KEY (`ID_CURSO`),
  ADD KEY `ID_UBICACION` (`ID_UBICACION`);

--
-- Indices de la tabla `rector`
--
ALTER TABLE `rector`
  ADD PRIMARY KEY (`ID_RECTOR`),
  ADD KEY `ID_USUARIOS` (`ID_USUARIOS`);

--
-- Indices de la tabla `refrigerio`
--
ALTER TABLE `refrigerio`
  ADD PRIMARY KEY (`ID_REFRIGERIO`),
  ADD KEY `ID_UBICACION` (`ID_UBICACION`);

--
-- Indices de la tabla `refrigerios_curso`
--
ALTER TABLE `refrigerios_curso`
  ADD PRIMARY KEY (`ID_ASIG_REF_CUR`),
  ADD KEY `refrigerios_curso_ibfk_1` (`ID_CURSO`),
  ADD KEY `refrigerios_curso_ibfk_2` (`ID_REFRIGERIO`);

--
-- Indices de la tabla `rol_usuario`
--
ALTER TABLE `rol_usuario`
  ADD PRIMARY KEY (`ID_ROL`);

--
-- Indices de la tabla `ubicaciones`
--
ALTER TABLE `ubicaciones`
  ADD PRIMARY KEY (`ID_UBICACION`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`ID_USUARIOS`),
  ADD KEY `ID_ROL` (`ID_ROL`),
  ADD KEY `ID_UBICACION` (`ID_UBICACION`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `auxiliar`
--
ALTER TABLE `auxiliar`
  MODIFY `ID_AUXILIAR` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT de la tabla `coordinador`
--
ALTER TABLE `coordinador`
  MODIFY `ID_COORDINADOR` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT de la tabla `rector`
--
ALTER TABLE `rector`
  MODIFY `ID_RECTOR` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `refrigerio`
--
ALTER TABLE `refrigerio`
  MODIFY `ID_REFRIGERIO` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;

--
-- AUTO_INCREMENT de la tabla `refrigerios_curso`
--
ALTER TABLE `refrigerios_curso`
  MODIFY `ID_ASIG_REF_CUR` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT de la tabla `ubicaciones`
--
ALTER TABLE `ubicaciones`
  MODIFY `ID_UBICACION` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `ID_USUARIOS` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=92;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `auxiliar`
--
ALTER TABLE `auxiliar`
  ADD CONSTRAINT `auxiliar_ibfk_1` FOREIGN KEY (`ID_USUARIOS`) REFERENCES `usuarios` (`ID_USUARIOS`);

--
-- Filtros para la tabla `coordinador`
--
ALTER TABLE `coordinador`
  ADD CONSTRAINT `coordinador_ibfk_1` FOREIGN KEY (`ID_USUARIOS`) REFERENCES `usuarios` (`ID_USUARIOS`);

--
-- Filtros para la tabla `curso`
--
ALTER TABLE `curso`
  ADD CONSTRAINT `curso_ibfk_1` FOREIGN KEY (`ID_UBICACION`) REFERENCES `ubicaciones` (`ID_UBICACION`);

--
-- Filtros para la tabla `rector`
--
ALTER TABLE `rector`
  ADD CONSTRAINT `rector_ibfk_1` FOREIGN KEY (`ID_USUARIOS`) REFERENCES `usuarios` (`ID_USUARIOS`);

--
-- Filtros para la tabla `refrigerio`
--
ALTER TABLE `refrigerio`
  ADD CONSTRAINT `refrigerio_ibfk_1` FOREIGN KEY (`ID_UBICACION`) REFERENCES `ubicaciones` (`ID_UBICACION`);

--
-- Filtros para la tabla `refrigerios_curso`
--
ALTER TABLE `refrigerios_curso`
  ADD CONSTRAINT `refrigerios_curso_ibfk_1` FOREIGN KEY (`ID_CURSO`) REFERENCES `curso` (`ID_CURSO`),
  ADD CONSTRAINT `refrigerios_curso_ibfk_2` FOREIGN KEY (`ID_REFRIGERIO`) REFERENCES `refrigerio` (`ID_REFRIGERIO`);

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`ID_ROL`) REFERENCES `rol_usuario` (`ID_ROL`),
  ADD CONSTRAINT `usuarios_ibfk_2` FOREIGN KEY (`ID_UBICACION`) REFERENCES `ubicaciones` (`ID_UBICACION`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
