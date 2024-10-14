-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 09-10-2024 a las 17:02:24
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
(24, NULL, NULL, 71, 1);

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
(35, NULL, 72, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `curso`
--

CREATE TABLE `curso` (
  `ID_CURSO` bigint(20) NOT NULL,
  `SEDE_CURSO` varchar(50) DEFAULT NULL,
  `CANTIDAD_ALUMNOS_CURSO` bigint(20) DEFAULT NULL,
  `DIRECTOR_CURSO` varchar(50) DEFAULT NULL,
  `ESTADO_CURSO` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `curso`
--

INSERT INTO `curso` (`ID_CURSO`, `SEDE_CURSO`, `CANTIDAD_ALUMNOS_CURSO`, `DIRECTOR_CURSO`, `ESTADO_CURSO`) VALUES
(303, 'Sede B', 29, 'Carolina', '1'),
(304, NULL, NULL, NULL, NULL),
(403, NULL, NULL, NULL, NULL),
(404, NULL, NULL, NULL, NULL),
(503, NULL, NULL, NULL, NULL),
(504, NULL, NULL, NULL, NULL),
(603, NULL, NULL, NULL, NULL),
(604, NULL, NULL, NULL, NULL),
(703, NULL, NULL, NULL, NULL),
(704, NULL, NULL, NULL, NULL),
(803, NULL, NULL, NULL, NULL),
(804, NULL, NULL, NULL, NULL),
(903, NULL, NULL, NULL, NULL),
(904, NULL, NULL, NULL, NULL),
(1003, NULL, NULL, NULL, NULL),
(1004, NULL, NULL, NULL, NULL),
(1103, NULL, NULL, NULL, NULL),
(1104, NULL, NULL, NULL, NULL);

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
  `CANTIDAD_REFRIGERIO` bigint(20) DEFAULT NULL,
  `DESCRIPCION_REFRIGERIO` varchar(100) DEFAULT NULL,
  `ESTADO_REFRIGERIO` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `refrigerio`
--

INSERT INTO `refrigerio` (`ID_REFRIGERIO`, `IMAGEN_REFRIGERIO`, `DIA_REFRIGERIO`, `FECHA_REFRIGERIO`, `HORA_REFRIGERIO`, `TIPO_REFRIGERIO`, `CANTIDAD_REFRIGERIO`, `DESCRIPCION_REFRIGERIO`, `ESTADO_REFRIGERIO`) VALUES
(66, 'uploads/descargar.jpg', 'Martes', '2024-10-08', '18:04:14', 'Jornada Escolar', NULL, 'pan', 'agotado'),
(67, 'uploads/Arroz.jpg', 'Martes', '2024-10-08', '18:06:23', 'Articulacion', NULL, 'arroz', 'agotado');

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

--
-- Volcado de datos para la tabla `refrigerios_curso`
--

INSERT INTO `refrigerios_curso` (`ID_ASIG_REF_CUR`, `FECHA_ASIGNACION`, `ID_REFRIGERIO`, `ID_CURSO`, `CANTIDAD_ASIGNADO`) VALUES
(30, '2024-10-09', 66, 303, 21);

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
(2, 'Auxiliar');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `ID_USUARIOS` bigint(20) NOT NULL,
  `NOMBRE_USUARIO` varchar(50) NOT NULL,
  `CORREO_USUARIO` varchar(50) DEFAULT NULL,
  `TELEFONO_USUARIO` varchar(20) DEFAULT NULL,
  `DIRECCION_USUARIO` varchar(20) DEFAULT NULL,
  `CONTRASENA_USUARIO` varchar(255) NOT NULL,
  `ACTIVO` tinyint(1) DEFAULT 1,
  `ID_ROL` int(11) NOT NULL,
  `FECHA_CREACION` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`ID_USUARIOS`, `NOMBRE_USUARIO`, `CORREO_USUARIO`, `TELEFONO_USUARIO`, `DIRECCION_USUARIO`, `CONTRASENA_USUARIO`, `ACTIVO`, `ID_ROL`, `FECHA_CREACION`) VALUES
(1, 'Jhon Freddy', 'paezgarayjhonfreddy@gmail.com', '3022309478', 'Sede B', '$2y$10$4EM4is9glF9W56BpyfYHS.Am1j3LrxlF6OwxCvW1AKwVTGKDLEx56', 1, 1, '2024-10-08 13:46:34'),
(71, 'Jhas', 'jhas@gmail.com', '3425264727', 'Sede B', '$2y$10$DcECwAafhpQlKY.BpUaUF.x1lsqEv.VgtEqT7Trmo/2N3DOiwFoH6', 1, 2, '2024-10-08 19:53:23'),
(72, 'angie', 'Angie@gmail.com', '3425264727', 'Sede B', '$2y$10$YgO5Nf4P4WPInJXsmrDvHOT7WdGP.sJf2/HZ93QOrwlA/Mj4aHw3W', 1, 1, '2024-10-08 20:20:00');

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
  ADD PRIMARY KEY (`ID_CURSO`);

--
-- Indices de la tabla `refrigerio`
--
ALTER TABLE `refrigerio`
  ADD PRIMARY KEY (`ID_REFRIGERIO`);

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
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`ID_USUARIOS`),
  ADD KEY `ID_ROL` (`ID_ROL`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `auxiliar`
--
ALTER TABLE `auxiliar`
  MODIFY `ID_AUXILIAR` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT de la tabla `coordinador`
--
ALTER TABLE `coordinador`
  MODIFY `ID_COORDINADOR` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT de la tabla `refrigerio`
--
ALTER TABLE `refrigerio`
  MODIFY `ID_REFRIGERIO` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- AUTO_INCREMENT de la tabla `refrigerios_curso`
--
ALTER TABLE `refrigerios_curso`
  MODIFY `ID_ASIG_REF_CUR` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `ID_USUARIOS` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

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
-- Filtros para la tabla `refrigerios_curso`
--
ALTER TABLE `refrigerios_curso`
  ADD CONSTRAINT `refrigerios_curso_ibfk_1` FOREIGN KEY (`ID_CURSO`) REFERENCES `curso` (`ID_CURSO`),
  ADD CONSTRAINT `refrigerios_curso_ibfk_2` FOREIGN KEY (`ID_REFRIGERIO`) REFERENCES `refrigerio` (`ID_REFRIGERIO`);

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`ID_ROL`) REFERENCES `rol_usuario` (`ID_ROL`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
