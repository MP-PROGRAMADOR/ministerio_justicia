-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 05-01-2026 a las 09:41:19
-- Versión del servidor: 10.4.25-MariaDB
-- Versión de PHP: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `themis_ministeriojusticia`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cargos`
--

CREATE TABLE `cargos` (
  `Id_cargo` int(11) NOT NULL,
  `Nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Nivel_jerarquico` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `cargos`
--

INSERT INTO `cargos` (`Id_cargo`, `Nombre`, `Nivel_jerarquico`) VALUES
(1, 'Jefe de Seccion', '1');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `Id_categoria` int(11) NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`Id_categoria`, `nombre`, `descripcion`) VALUES
(1, 'A1', 'cargo de ministro');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `direcciones`
--

CREATE TABLE `direcciones` (
  `Id_direccion` int(11) NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ubicacion` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `distrito` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `provincia` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `region` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `direcciones`
--

INSERT INTO `direcciones` (`Id_direccion`, `nombre`, `ubicacion`, `distrito`, `provincia`, `region`) VALUES
(1, 'MALABO II', 'Malabo II', 'Bioko Norte', 'Bioko norte', 'Insular'),
(2, 'Luba', 'Cerca el ayuntamiento', 'Luba', 'Bioko Norte', 'Region Insular');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `funcionarios`
--

CREATE TABLE `funcionarios` (
  `Id_funcionario` int(11) NOT NULL,
  `CODIGO` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Nombre` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Apellidos` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Estado_Laboral` enum('Activo','Baja Temporal','Jubilado','Cesado','Permiso','Vacaciones') COLLATE utf8mb4_unicode_ci DEFAULT 'Activo',
  `Dip_Pasaporte` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Sexo` enum('Masculino','Femenino','Otro') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Fecha_nacimiento` date DEFAULT NULL,
  `Lugar_nacimiento` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Nacionalidad` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Telefono` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Correo` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Domicilio` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Num_carnet_fun` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Fecha_nombramiento` date DEFAULT NULL,
  `Fecha_posesion` date DEFAULT NULL,
  `Id_seccion` int(11) DEFAULT NULL,
  `Funcion` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Id_categoria` int(11) DEFAULT NULL,
  `Profesion` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Maximo_nivel_estudios` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Titulacion_academica` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Universidad_centro_formacion` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Fecha_graduacion` date DEFAULT NULL,
  `Foto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Dip_pass_copia` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Copia_doc_nomb` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Copia_carnet_func` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Copia_doc_tom_posesion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Copia_doc_academicos` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Usuario_creador` int(11) NOT NULL,
  `Fecha_registro` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `funcionarios`
--

INSERT INTO `funcionarios` (`Id_funcionario`, `CODIGO`, `Nombre`, `Apellidos`, `Estado_Laboral`, `Dip_Pasaporte`, `Sexo`, `Fecha_nacimiento`, `Lugar_nacimiento`, `Nacionalidad`, `Telefono`, `Correo`, `Domicilio`, `Num_carnet_fun`, `Fecha_nombramiento`, `Fecha_posesion`, `Id_seccion`, `Funcion`, `Id_categoria`, `Profesion`, `Maximo_nivel_estudios`, `Titulacion_academica`, `Universidad_centro_formacion`, `Fecha_graduacion`, `Foto`, `Dip_pass_copia`, `Copia_doc_nomb`, `Copia_carnet_func`, `Copia_doc_tom_posesion`, `Copia_doc_academicos`, `Usuario_creador`, `Fecha_registro`) VALUES
(1, 'SAL66FF6', 'salvador', 'METE BIJERI', 'Activo', '174708', 'Masculino', '1998-06-18', 'Malabo', 'Ecutoguineano', '222155113', 'salvadormete2@gmail.com', 'bar estadio', '89365', '2024-08-08', '2025-12-04', 1, '1', 1, 'Informatico', 'Master', 'Master en informatica', 'Master d', '2025-11-07', 'funcionarios/Foto_6957c4c6545f4.png', 'funcionarios/Dip_pass_copia_6957c4c6549db.pdf', 'funcionarios/Copia_doc_nomb_6957c4c654daa.pdf', 'funcionarios/Copia_carnet_func_6957c4c65518a.pdf', 'funcionarios/Copia_doc_tom_posesion_6957c4c65554e.pdf', 'funcionarios/Copia_doc_academicos_6957c4c655863.pdf', 2, '2026-01-02 14:14:46'),
(2, 'MAX62BE6', 'Maximiliano ', 'Compe Puye', 'Activo', 'P1007846', 'Masculino', '1998-06-17', 'BATA', 'Ecutoguineano', '555971145', 'minerva@prueba.com', 'Malabo II', 'N-8746', '2025-02-04', '2025-02-26', 1, '1', 1, 'Fiscal', 'Licenciado', 'Licenciado en Derecho', 'Universidad de Murcia', '2025-09-11', 'funcionarios/Foto_6958f2ebccaa7.jpeg', 'funcionarios/Dip_pass_copia_6958f1fb3aa42.pdf', 'funcionarios/Copia_doc_nomb_6958f1fb3b1b9.pdf', 'funcionarios/Copia_carnet_func_6958f1fb3bce8.png', 'funcionarios/Copia_doc_tom_posesion_6958f1fb3c303.pdf', 'funcionarios/Copia_doc_academicos_6958f1fb3c5fd.pdf', 2, '2026-01-03 11:39:55'),
(3, 'SER759CC', 'serafin', 'Riberi Belope', 'Activo', '094764', 'Masculino', '1998-06-17', 'BATA', 'Ecutoguineano', '555890876', 'serafinriberi@gmail.com', 'Ela Nguema', 'N-7465', '2025-04-03', '2025-07-16', 1, '1', 1, 'Economista', 'Grado', 'Grado en economia', 'Universidad de Murcia', '2025-10-09', 'funcionarios/Foto_695b7420b8ca1.jpeg', 'funcionarios/Dip_pass_copia_695b7420b9354.pdf', 'funcionarios/Copia_doc_nomb_695b7420b9930.pdf', 'funcionarios/Copia_carnet_func_695b7420ba35f.pdf', 'funcionarios/Copia_doc_tom_posesion_695b7420ba730.pdf', 'funcionarios/Copia_doc_academicos_695b7420baac3.pdf', 2, '2026-01-05 09:19:44');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `logs`
--

CREATE TABLE `logs` (
  `Id_log` int(11) NOT NULL,
  `Usuario_id` int(11) NOT NULL,
  `Tabla_afectada` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Accion` enum('INSERT','UPDATE','DELETE','LOGIN','LOGOUT') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Registro_id` int(11) DEFAULT NULL,
  `Fecha` datetime DEFAULT current_timestamp(),
  `IP` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Dispositivo` text COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `logs`
--

INSERT INTO `logs` (`Id_log`, `Usuario_id`, `Tabla_afectada`, `Accion`, `Registro_id`, `Fecha`, `IP`, `Dispositivo`) VALUES
(1, 2, 'categorias', 'UPDATE', 1, '2026-01-02 09:42:15', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0'),
(2, 2, 'categorias', 'UPDATE', 1, '2026-01-02 09:43:20', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0'),
(3, 2, 'direcciones', 'INSERT', 1, '2026-01-02 09:58:28', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0'),
(4, 2, 'direcciones', 'INSERT', 2, '2026-01-02 10:06:12', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0'),
(5, 2, 'secciones', 'INSERT', 1, '2026-01-02 11:03:15', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0'),
(6, 2, 'secciones', 'INSERT', 2, '2026-01-02 11:23:42', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0'),
(7, 2, 'secciones', 'INSERT', 3, '2026-01-02 11:24:07', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0'),
(8, 2, 'secciones', 'INSERT', 4, '2026-01-02 11:25:05', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0'),
(9, 2, 'secciones', 'UPDATE', 2, '2026-01-02 11:27:25', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0'),
(10, 2, 'funcionarios', 'UPDATE', 1, '2026-01-03 11:03:56', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36'),
(11, 2, 'funcionarios', 'UPDATE', 2, '2026-01-03 11:43:55', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nombramientos`
--

CREATE TABLE `nombramientos` (
  `Id_nombramiento` int(11) NOT NULL,
  `Id_funcionario` int(11) NOT NULL,
  `Id_cargo` int(11) NOT NULL,
  `Fecha_nombramiento` date DEFAULT NULL,
  `Fecha_toma_posesion` date DEFAULT NULL,
  `Id_direccion` int(11) DEFAULT NULL,
  `Id_seccion` int(11) DEFAULT NULL,
  `Id_categoria` int(11) DEFAULT NULL,
  `Copia_doc_nomb` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Copia_doc_tom_posesion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Usuario_creador` int(11) NOT NULL,
  `Fecha_registro` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_registro` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `secciones`
--

CREATE TABLE `secciones` (
  `Id_seccion` int(11) NOT NULL,
  `Id_direccion` int(11) DEFAULT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `secciones`
--

INSERT INTO `secciones` (`Id_seccion`, `Id_direccion`, `nombre`) VALUES
(1, 1, 'Sección de informática'),
(2, 2, 'Sección de catástrofes'),
(3, 1, 'Sección de RRHH'),
(4, 2, 'Sección de economica');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_cursos`
--

CREATE TABLE `tbl_cursos` (
  `ID_Curso` int(11) NOT NULL,
  `Nombre_Curso` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Descripcion` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Fecha_Inicio` date DEFAULT NULL,
  `Fecha_Fin` date DEFAULT NULL,
  `Cupo` int(11) DEFAULT 0,
  `Usuario_creador` int(11) DEFAULT NULL,
  `Fecha_registro` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_cursos_funcionarios`
--

CREATE TABLE `tbl_cursos_funcionarios` (
  `ID` int(11) NOT NULL,
  `ID_Funcionario` int(11) DEFAULT NULL,
  `ID_Curso` int(11) DEFAULT NULL,
  `Fecha_Matricula` date DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_instrucciones`
--

CREATE TABLE `tbl_instrucciones` (
  `ID_Instruccion` int(11) NOT NULL,
  `ID_Funcionario` int(11) NOT NULL,
  `Titulo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Mensaje` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `Fecha_Envio` datetime DEFAULT current_timestamp(),
  `Leido` tinyint(1) DEFAULT 0,
  `Usuario_creador` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_permisos`
--

CREATE TABLE `tbl_permisos` (
  `ID_Permiso` int(11) NOT NULL,
  `ID_Funcionario` int(11) NOT NULL,
  `Tipo_Permiso` enum('Vacaciones','Enfermedad','Maternidad','Paternidad','Asuntos Propios','Estudios','Comisión Servicio','Otro') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Fecha_Solicitud` date DEFAULT curdate(),
  `Fecha_Inicio_Permiso` date DEFAULT NULL,
  `Fecha_Fin_Permiso` date DEFAULT NULL,
  `Estado_Permiso` enum('Pendiente','Aprobado','Denegado','Cancelado','Disfrutado') COLLATE utf8mb4_unicode_ci DEFAULT 'Pendiente',
  `Motivo` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Documento_Soporte_URL` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Usuario_creador` int(11) NOT NULL,
  `Fecha_registro` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_quejas_sugerencias`
--

CREATE TABLE `tbl_quejas_sugerencias` (
  `ID_QS` int(11) NOT NULL,
  `ID_Funcionario` int(11) DEFAULT NULL,
  `Tipo` enum('queja','sugerencia') COLLATE utf8mb4_unicode_ci NOT NULL,
  `Mensaje` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `Fecha_Envio` datetime DEFAULT current_timestamp(),
  `Estado` enum('pendiente','revisado','resuelto') COLLATE utf8mb4_unicode_ci DEFAULT 'pendiente',
  `Anonimo` tinyint(1) DEFAULT 0,
  `Usuario_creador` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_usuarios`
--

CREATE TABLE `tbl_usuarios` (
  `ID_Usuario` int(11) NOT NULL,
  `Nombre_Usuario` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Contrasena_Hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Rol_Usuario` enum('Administrador','Recursos Humanos','Jefe Personal','Auditor','Secretaria','Usuario') COLLATE utf8mb4_unicode_ci NOT NULL,
  `Nombre` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Apellidos` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Email_Contacto` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Fecha_Creacion` datetime DEFAULT current_timestamp(),
  `Ultimo_Acceso` datetime DEFAULT NULL,
  `Activo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tbl_usuarios`
--

INSERT INTO `tbl_usuarios` (`ID_Usuario`, `Nombre_Usuario`, `Contrasena_Hash`, `Rol_Usuario`, `Nombre`, `Apellidos`, `Email_Contacto`, `Fecha_Creacion`, `Ultimo_Acceso`, `Activo`) VALUES
(2, 'Mh123', '$2y$10$3JOO3f.29T7kwjCl7W/jZO59mHHNjRXbAgl1oMlz9QzJsO38gaAoq', 'Administrador', NULL, NULL, 'salvadormete2@gmail.com', '2025-06-27 13:10:33', '2026-01-03 10:59:02', 1),
(4, 'Usuario', '$2y$10$9UJOCVre0YY9XcJtAZKm6OgKpBojscaagh63HQYMsIe.shI33qLXu', 'Usuario', NULL, NULL, 'minerva@prueba.com', '2025-08-11 14:55:04', '2025-08-12 13:29:53', 1),
(5, 'JPersonal', '$2y$10$Jo7ciXxrQG9Albdw0jQLaudjhHpPU4stWXxwQ00xU2t/dAe1Pp5k2', 'Jefe Personal', NULL, NULL, 'jefepersonal@gmail.com', '2025-08-12 08:56:16', '2025-08-18 10:26:09', 1);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `cargos`
--
ALTER TABLE `cargos`
  ADD PRIMARY KEY (`Id_cargo`);

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`Id_categoria`);

--
-- Indices de la tabla `direcciones`
--
ALTER TABLE `direcciones`
  ADD PRIMARY KEY (`Id_direccion`);

--
-- Indices de la tabla `funcionarios`
--
ALTER TABLE `funcionarios`
  ADD PRIMARY KEY (`Id_funcionario`),
  ADD UNIQUE KEY `CODIGO` (`CODIGO`),
  ADD KEY `Id_seccion` (`Id_seccion`),
  ADD KEY `Id_categoria` (`Id_categoria`),
  ADD KEY `Usuario_creador` (`Usuario_creador`);

--
-- Indices de la tabla `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`Id_log`),
  ADD KEY `Usuario_id` (`Usuario_id`);

--
-- Indices de la tabla `nombramientos`
--
ALTER TABLE `nombramientos`
  ADD PRIMARY KEY (`Id_nombramiento`),
  ADD KEY `Id_funcionario` (`Id_funcionario`),
  ADD KEY `Id_cargo` (`Id_cargo`),
  ADD KEY `Id_direccion` (`Id_direccion`),
  ADD KEY `Id_seccion` (`Id_seccion`),
  ADD KEY `Id_categoria` (`Id_categoria`),
  ADD KEY `Usuario_creador` (`Usuario_creador`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `secciones`
--
ALTER TABLE `secciones`
  ADD PRIMARY KEY (`Id_seccion`),
  ADD KEY `Id_direccion` (`Id_direccion`);

--
-- Indices de la tabla `tbl_cursos`
--
ALTER TABLE `tbl_cursos`
  ADD PRIMARY KEY (`ID_Curso`),
  ADD KEY `Usuario_creador` (`Usuario_creador`);

--
-- Indices de la tabla `tbl_cursos_funcionarios`
--
ALTER TABLE `tbl_cursos_funcionarios`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `ID_Funcionario` (`ID_Funcionario`),
  ADD KEY `ID_Curso` (`ID_Curso`);

--
-- Indices de la tabla `tbl_instrucciones`
--
ALTER TABLE `tbl_instrucciones`
  ADD PRIMARY KEY (`ID_Instruccion`),
  ADD KEY `ID_Funcionario` (`ID_Funcionario`),
  ADD KEY `Usuario_creador` (`Usuario_creador`);

--
-- Indices de la tabla `tbl_permisos`
--
ALTER TABLE `tbl_permisos`
  ADD PRIMARY KEY (`ID_Permiso`),
  ADD KEY `ID_Funcionario` (`ID_Funcionario`),
  ADD KEY `Usuario_creador` (`Usuario_creador`);

--
-- Indices de la tabla `tbl_quejas_sugerencias`
--
ALTER TABLE `tbl_quejas_sugerencias`
  ADD PRIMARY KEY (`ID_QS`),
  ADD KEY `ID_Funcionario` (`ID_Funcionario`),
  ADD KEY `Usuario_creador` (`Usuario_creador`);

--
-- Indices de la tabla `tbl_usuarios`
--
ALTER TABLE `tbl_usuarios`
  ADD PRIMARY KEY (`ID_Usuario`),
  ADD UNIQUE KEY `Nombre_Usuario` (`Nombre_Usuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `cargos`
--
ALTER TABLE `cargos`
  MODIFY `Id_cargo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `Id_categoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `direcciones`
--
ALTER TABLE `direcciones`
  MODIFY `Id_direccion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `funcionarios`
--
ALTER TABLE `funcionarios`
  MODIFY `Id_funcionario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `logs`
--
ALTER TABLE `logs`
  MODIFY `Id_log` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `nombramientos`
--
ALTER TABLE `nombramientos`
  MODIFY `Id_nombramiento` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `secciones`
--
ALTER TABLE `secciones`
  MODIFY `Id_seccion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `tbl_cursos`
--
ALTER TABLE `tbl_cursos`
  MODIFY `ID_Curso` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tbl_cursos_funcionarios`
--
ALTER TABLE `tbl_cursos_funcionarios`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tbl_instrucciones`
--
ALTER TABLE `tbl_instrucciones`
  MODIFY `ID_Instruccion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tbl_permisos`
--
ALTER TABLE `tbl_permisos`
  MODIFY `ID_Permiso` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tbl_quejas_sugerencias`
--
ALTER TABLE `tbl_quejas_sugerencias`
  MODIFY `ID_QS` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tbl_usuarios`
--
ALTER TABLE `tbl_usuarios`
  MODIFY `ID_Usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `funcionarios`
--
ALTER TABLE `funcionarios`
  ADD CONSTRAINT `funcionarios_ibfk_1` FOREIGN KEY (`Id_seccion`) REFERENCES `secciones` (`Id_seccion`),
  ADD CONSTRAINT `funcionarios_ibfk_2` FOREIGN KEY (`Id_categoria`) REFERENCES `categorias` (`Id_categoria`),
  ADD CONSTRAINT `funcionarios_ibfk_3` FOREIGN KEY (`Usuario_creador`) REFERENCES `tbl_usuarios` (`ID_Usuario`);

--
-- Filtros para la tabla `logs`
--
ALTER TABLE `logs`
  ADD CONSTRAINT `logs_ibfk_1` FOREIGN KEY (`Usuario_id`) REFERENCES `tbl_usuarios` (`ID_Usuario`);

--
-- Filtros para la tabla `nombramientos`
--
ALTER TABLE `nombramientos`
  ADD CONSTRAINT `nombramientos_ibfk_1` FOREIGN KEY (`Id_funcionario`) REFERENCES `funcionarios` (`Id_funcionario`),
  ADD CONSTRAINT `nombramientos_ibfk_2` FOREIGN KEY (`Id_cargo`) REFERENCES `cargos` (`Id_cargo`),
  ADD CONSTRAINT `nombramientos_ibfk_3` FOREIGN KEY (`Id_direccion`) REFERENCES `direcciones` (`Id_direccion`),
  ADD CONSTRAINT `nombramientos_ibfk_4` FOREIGN KEY (`Id_seccion`) REFERENCES `secciones` (`Id_seccion`),
  ADD CONSTRAINT `nombramientos_ibfk_5` FOREIGN KEY (`Id_categoria`) REFERENCES `categorias` (`Id_categoria`),
  ADD CONSTRAINT `nombramientos_ibfk_6` FOREIGN KEY (`Usuario_creador`) REFERENCES `tbl_usuarios` (`ID_Usuario`);

--
-- Filtros para la tabla `secciones`
--
ALTER TABLE `secciones`
  ADD CONSTRAINT `secciones_ibfk_1` FOREIGN KEY (`Id_direccion`) REFERENCES `direcciones` (`Id_direccion`);

--
-- Filtros para la tabla `tbl_cursos`
--
ALTER TABLE `tbl_cursos`
  ADD CONSTRAINT `tbl_cursos_ibfk_1` FOREIGN KEY (`Usuario_creador`) REFERENCES `tbl_usuarios` (`ID_Usuario`);

--
-- Filtros para la tabla `tbl_cursos_funcionarios`
--
ALTER TABLE `tbl_cursos_funcionarios`
  ADD CONSTRAINT `tbl_cursos_funcionarios_ibfk_1` FOREIGN KEY (`ID_Funcionario`) REFERENCES `funcionarios` (`Id_funcionario`),
  ADD CONSTRAINT `tbl_cursos_funcionarios_ibfk_2` FOREIGN KEY (`ID_Curso`) REFERENCES `tbl_cursos` (`ID_Curso`);

--
-- Filtros para la tabla `tbl_instrucciones`
--
ALTER TABLE `tbl_instrucciones`
  ADD CONSTRAINT `tbl_instrucciones_ibfk_1` FOREIGN KEY (`ID_Funcionario`) REFERENCES `funcionarios` (`Id_funcionario`) ON DELETE CASCADE,
  ADD CONSTRAINT `tbl_instrucciones_ibfk_2` FOREIGN KEY (`Usuario_creador`) REFERENCES `tbl_usuarios` (`ID_Usuario`);

--
-- Filtros para la tabla `tbl_permisos`
--
ALTER TABLE `tbl_permisos`
  ADD CONSTRAINT `tbl_permisos_ibfk_1` FOREIGN KEY (`ID_Funcionario`) REFERENCES `funcionarios` (`Id_funcionario`),
  ADD CONSTRAINT `tbl_permisos_ibfk_2` FOREIGN KEY (`Usuario_creador`) REFERENCES `tbl_usuarios` (`ID_Usuario`);

--
-- Filtros para la tabla `tbl_quejas_sugerencias`
--
ALTER TABLE `tbl_quejas_sugerencias`
  ADD CONSTRAINT `tbl_quejas_sugerencias_ibfk_1` FOREIGN KEY (`ID_Funcionario`) REFERENCES `funcionarios` (`Id_funcionario`),
  ADD CONSTRAINT `tbl_quejas_sugerencias_ibfk_2` FOREIGN KEY (`Usuario_creador`) REFERENCES `tbl_usuarios` (`ID_Usuario`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
