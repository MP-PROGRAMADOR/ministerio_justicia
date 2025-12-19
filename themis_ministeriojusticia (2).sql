-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 29-08-2025 a las 10:36:46
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
-- Estructura de tabla para la tabla `roles`
--

use Themis_MinisterioJusticia;


CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `fecha_registro` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `nombre`, `fecha_registro`) VALUES
(1, 'Administrador', '2025-06-10 17:26:17'),
(2, 'laboratorio', '2025-06-16 11:53:15'),
(6, 'doctor', '2025-06-17 11:28:47'),
(7, 'farmacia', '2025-08-12 10:04:55');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_asignaciones`
--

CREATE TABLE `tbl_asignaciones` (
  `ID_Asignacion` int(11) NOT NULL,
  `ID_Funcionario` int(11) NOT NULL,
  `ID_Cargo` int(11) NOT NULL,
  `ID_Departamento` int(11) NOT NULL,
  `ID_Destino` int(11) NOT NULL,
  `Fecha_Inicio_Asignacion` date NOT NULL,
  `Fecha_Fin_Asignacion` date DEFAULT NULL,
  `ID_Usuario_Creador` int(11) NOT NULL,
  `Fecha_Creacion_Registro` datetime DEFAULT current_timestamp(),
  `ID_Usuario_Ultima_Modificacion` int(11) DEFAULT NULL,
  `Fecha_Ultima_Modificacion` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `tbl_asignaciones`
--

INSERT INTO `tbl_asignaciones` (`ID_Asignacion`, `ID_Funcionario`, `ID_Cargo`, `ID_Departamento`, `ID_Destino`, `Fecha_Inicio_Asignacion`, `Fecha_Fin_Asignacion`, `ID_Usuario_Creador`, `Fecha_Creacion_Registro`, `ID_Usuario_Ultima_Modificacion`, `Fecha_Ultima_Modificacion`) VALUES
(1, 1, 1, 1, 2, '2022-12-01', '2026-10-21', 2, '2025-06-30 12:50:05', 2, '2025-06-30 14:03:54'),
(2, 4, 2, 1, 3, '2023-05-09', NULL, 2, '2025-07-31 11:30:32', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_capacitaciones`
--

CREATE TABLE `tbl_capacitaciones` (
  `ID_Capacitacion` int(11) NOT NULL,
  `ID_Funcionario` int(11) NOT NULL,
  `Nombre_Curso` varchar(200) NOT NULL,
  `Institucion_Organizadora` varchar(200) NOT NULL,
  `Fecha_Inicio_Curso` date DEFAULT NULL,
  `Fecha_Fin_Curso` date DEFAULT NULL,
  `Certificado_URL` varchar(255) DEFAULT NULL,
  `ID_Usuario_Creador` int(11) NOT NULL,
  `Fecha_Creacion_Registro` datetime DEFAULT current_timestamp(),
  `ID_Usuario_Ultima_Modificacion` int(11) DEFAULT NULL,
  `Fecha_Ultima_Modificacion` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `tbl_capacitaciones`
--

INSERT INTO `tbl_capacitaciones` (`ID_Capacitacion`, `ID_Funcionario`, `Nombre_Curso`, `Institucion_Organizadora`, `Fecha_Inicio_Curso`, `Fecha_Fin_Curso`, `Certificado_URL`, `ID_Usuario_Creador`, `Fecha_Creacion_Registro`, `ID_Usuario_Ultima_Modificacion`, `Fecha_Ultima_Modificacion`) VALUES
(1, 1, 'curso de word 2', 'centro cultural', '2025-06-11', '2025-07-06', 'certificados/cert_6862918c9af49_gq-solicitud-beca.pdf', 2, '2025-06-30 14:30:52', 2, '2025-06-30 14:42:12');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_cargos`
--

CREATE TABLE `tbl_cargos` (
  `ID_Cargo` int(11) NOT NULL,
  `Nombre_Cargo` varchar(100) NOT NULL,
  `Descripcion_Cargo` text DEFAULT NULL,
  `Nivel_Jerarquico` int(11) NOT NULL,
  `ID_Usuario_Creador` int(11) NOT NULL,
  `Fecha_Creacion_Registro` datetime DEFAULT current_timestamp(),
  `ID_Usuario_Ultima_Modificacion` int(11) DEFAULT NULL,
  `Fecha_Ultima_Modificacion` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `tbl_cargos`
--

INSERT INTO `tbl_cargos` (`ID_Cargo`, `Nombre_Cargo`, `Descripcion_Cargo`, `Nivel_Jerarquico`, `ID_Usuario_Creador`, `Fecha_Creacion_Registro`, `ID_Usuario_Ultima_Modificacion`, `Fecha_Ultima_Modificacion`) VALUES
(1, 'Jefe de Área 2', 'Responsable del área técnica 4', 3, 2, '2025-06-30 12:12:25', 2, '2025-06-30 12:29:38'),
(2, 'Jefe de RRHH', 'jefe de recursos Humanos', 3, 2, '2025-07-31 11:28:11', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_cursos`
--

CREATE TABLE `tbl_cursos` (
  `ID_Curso` int(11) NOT NULL,
  `Nombre_Curso` varchar(255) NOT NULL,
  `Descripcion` text DEFAULT NULL,
  `Fecha_Inicio` date DEFAULT NULL,
  `Fecha_Fin` date DEFAULT NULL,
  `Cupo` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `tbl_cursos`
--

INSERT INTO `tbl_cursos` (`ID_Curso`, `Nombre_Curso`, `Descripcion`, `Fecha_Inicio`, `Fecha_Fin`, `Cupo`) VALUES
(1, 'Seminario de formación manejo del archivo', 'este seminario se centrara en hacer que el personal administrativo sepa manejar el archivo del ministerio.', '2025-08-01', '2025-08-22', 50);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_cursos_funcionarios`
--

CREATE TABLE `tbl_cursos_funcionarios` (
  `ID` int(11) NOT NULL,
  `ID_Funcionario` int(11) NOT NULL,
  `ID_Curso` int(11) NOT NULL,
  `Fecha_Matricula` date DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `tbl_cursos_funcionarios`
--

INSERT INTO `tbl_cursos_funcionarios` (`ID`, `ID_Funcionario`, `ID_Curso`, `Fecha_Matricula`) VALUES
(16, 1, 1, '2025-08-14'),
(17, 2, 1, '2025-08-14');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_departamentos`
--

CREATE TABLE `tbl_departamentos` (
  `ID_Departamento` int(11) NOT NULL,
  `Nombre_Departamento` varchar(100) NOT NULL,
  `direccion` varchar(250) NOT NULL,
  `Ubicacion` varchar(200) DEFAULT NULL,
  `Telefono_Departamento` varchar(20) DEFAULT NULL,
  `ID_Usuario_Creador` int(11) NOT NULL,
  `Fecha_Creacion_Registro` datetime DEFAULT current_timestamp(),
  `ID_Usuario_Ultima_Modificacion` int(11) DEFAULT NULL,
  `Fecha_Ultima_Modificacion` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `Ciudad` varchar(250) NOT NULL,
  `Distrito` varchar(250) NOT NULL,
  `Provincia` varchar(250) NOT NULL,
  `region` varchar(250) NOT NULL

) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `tbl_departamentos`
--

INSERT INTO `tbl_departamentos` (`ID_Departamento`, `Nombre_Departamento`, `Ubicacion`, `Telefono_Departamento`, `ID_Usuario_Creador`, `Fecha_Creacion_Registro`, `ID_Usuario_Ultima_Modificacion`, `Fecha_Ultima_Modificacion`, `Ciudad`, `Distrito`, `Provincia`) VALUES
(1, 'Recursos Humanos', 'ministerio de justicia', '333213456', 2, '2025-06-30 09:13:57', 2, '2025-06-30 09:22:37', 'Malabo', 'Bioko Norte', 'Región Insular');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_destinos`
--

CREATE TABLE `tbl_destinos` (
  `ID_Destino` int(11) NOT NULL,
  `Nombre_Destino` varchar(150) NOT NULL,
  `Tipo_Destino` enum('Juzgado','Tribunal','Fiscalia','Sede Central','Oficina Regional','Otro') DEFAULT NULL,
  `Direccion_Destino` varchar(255) DEFAULT NULL,
  `Ciudad` varchar(100) DEFAULT NULL,
  `Telefono_Destino` varchar(20) DEFAULT NULL,
  `ID_Usuario_Creador` int(11) NOT NULL,
  `Fecha_Creacion_Registro` datetime DEFAULT current_timestamp(),
  `ID_Usuario_Ultima_Modificacion` int(11) DEFAULT NULL,
  `Fecha_Ultima_Modificacion` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `Distrito` varchar(255) NOT NULL,
  `Provincia` varchar(250) NOT NULL,
  `Fecha_Destino` date NOT NULL,
  `Fecha_Fin_Destino` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `tbl_destinos`
--

INSERT INTO `tbl_destinos` (`ID_Destino`, `Nombre_Destino`, `Tipo_Destino`, `Direccion_Destino`, `Ciudad`, `Telefono_Destino`, `ID_Usuario_Creador`, `Fecha_Creacion_Registro`, `ID_Usuario_Ultima_Modificacion`, `Fecha_Ultima_Modificacion`, `Distrito`, `Provincia`, `Fecha_Destino`, `Fecha_Fin_Destino`) VALUES
(2, 'Juzgado de Mlabo', 'Juzgado', 'malabo II', 'Malabo', '222478702', 2, '2025-06-30 11:51:52', 2, '2025-06-30 12:04:18', 'Malabo', 'Bioko Norte', '2020-06-28', '2028-11-17'),
(3, 'Ministerio de Justicia de Malabo', 'Sede Central', 'malabo II', 'Malabo', '222155113', 2, '2025-07-31 11:29:49', NULL, NULL, 'Malabo', 'Bioko Norte', '2023-02-08', '2027-10-21');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_formacion_academica`
--

CREATE TABLE `tbl_formacion_academica` (
  `ID_Formacion` int(11) NOT NULL,
  `ID_Funcionario` int(11) NOT NULL,
  `Titulo_Obtenido` varchar(200) NOT NULL,
  `Institucion_Educativa` text NOT NULL,
  `Fecha_Graduacion` date DEFAULT NULL,
  `Nivel_Educativo` enum('Bachiller','Grado','Postgrado','Maestria','Doctorado','Otro') NOT NULL,
  `ID_Usuario_Creador` int(11) NOT NULL,
  `Fecha_Creacion_Registro` datetime DEFAULT current_timestamp(),
  `ID_Usuario_Ultima_Modificacion` int(11) DEFAULT NULL,
  `Fecha_Ultima_Modificacion` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `tbl_formacion_academica`
--

INSERT INTO `tbl_formacion_academica` (`ID_Formacion`, `ID_Funcionario`, `Titulo_Obtenido`, `Institucion_Educativa`, `Fecha_Graduacion`, `Nivel_Educativo`, `ID_Usuario_Creador`, `Fecha_Creacion_Registro`, `ID_Usuario_Ultima_Modificacion`, `Fecha_Ultima_Modificacion`) VALUES
(1, 1, 'Graduado en derecho', 'UNGE', '2021-05-04', 'Grado', 2, '2025-06-30 09:35:41', 2, '2025-06-30 09:46:31'),
(2, 1, 'Master en Relaciones Internacionales', 'Universidad Complutense de Madrid', '2020-06-17', 'Maestria', 2, '2025-06-30 09:40:02', NULL, NULL),
(3, 2, 'Lienciado en Informatica', 'INSTTIC', '2022-06-15', 'Grado', 2, '2025-07-01 12:17:34', NULL, NULL),
(4, 4, 'Graduado en derecho', 'UNGE', '2022-06-09', 'Grado', 2, '2025-07-31 11:07:19', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_funcionarios`
--

CREATE TABLE `tbl_funcionarios` (
  `ID_Funcionario` int(11) NOT NULL,
  `Codigo_Funcionario` varchar(20) NOT NULL,
  `Nombres` varchar(100) NOT NULL,
  `Apellidos` varchar(100) NOT NULL,
  `DNI_Pasaporte` varchar(50) NOT NULL,
  `Fecha_Nacimiento` date DEFAULT NULL,
  `Genero` enum('Masculino','Femenino','Otro') DEFAULT NULL,
  `Nacionalidad` varchar(50) DEFAULT NULL,
  `Direccion_Residencia` varchar(255) DEFAULT NULL,
  `Telefono_Contacto` varchar(20) DEFAULT NULL,
  `Email_Oficial` varchar(100) DEFAULT NULL,
  `Fecha_Ingreso` date NOT NULL,
  `categoria` char(2) not null,
  `Estado_Laboral` enum('Activo','Baja Temporal','Jubilado','Cesado','Permiso','Vacaciones') DEFAULT 'Activo',
  `Fotografia` varchar(255) DEFAULT NULL,
  `ID_Usuario_Creador` int(11) NOT NULL,
  `Fecha_Creacion_Registro` datetime DEFAULT current_timestamp(),
  `ID_Usuario_Ultima_Modificacion` int(11) DEFAULT NULL,
  `Fecha_Ultima_Modificacion` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `tbl_funcionarios`
--

INSERT INTO `tbl_funcionarios` (`ID_Funcionario`, `Codigo_Funcionario`, `Nombres`, `Apellidos`, `DNI_Pasaporte`, `Fecha_Nacimiento`, `Genero`, `Nacionalidad`, `Direccion_Residencia`, `Telefono_Contacto`, `Email_Oficial`, `Fecha_Ingreso`, `Estado_Laboral`, `Fotografia`, `ID_Usuario_Creador`, `Fecha_Creacion_Registro`, `ID_Usuario_Ultima_Modificacion`, `Fecha_Ultima_Modificacion`) VALUES
(1, 'SAL5D1DF', 'Salvador', 'Mete Bijeri', '384762', '2001-02-14', 'Masculino', 'Ecutoguineano', 'Ela Nguema', '555908765', 'salvadormete@gmail.com', '2025-06-28', 'Activo', 'funcionarios/func_688b3fd09575e.png', 2, '2025-06-28 11:09:21', 2, '2025-08-11 14:48:37'),
(2, 'NAZ7B4A2', 'Nazario Monebama', 'Etoho Nsaha', '160506', '1998-02-04', 'Masculino', 'Ecutoguineano', 'Argentina', '555712824', 'nazariomen@gmail.com', '2025-07-01', 'Activo', 'funcionarios/func_6863c33ebd2d8.jpg', 2, '2025-07-01 12:15:10', NULL, NULL),
(4, 'SERE8872', 'Serafin', 'Riberi Belope', '16050656', '2005-06-14', 'Masculino', 'Ecutoguineano', 'calle mongomo', '+240555908765', 'serafinriberi@gmail.com', '2024-11-14', 'Activo', 'funcionarios/func_688b450d4cb46.png', 2, '2025-07-31 11:06:33', 2, '2025-07-31 11:27:25');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_instrucciones`
--

CREATE TABLE `tbl_instrucciones` (
  `ID_Instruccion` int(11) NOT NULL,
  `ID_Funcionario` int(11) NOT NULL,
  `Titulo` varchar(255) NOT NULL,
  `Mensaje` text NOT NULL,
  `Fecha_Envio` datetime DEFAULT current_timestamp(),
  `Leido` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_permisos`
--

CREATE TABLE `tbl_permisos` (
  `ID_Permiso` int(11) NOT NULL,
  `token` int(1) DEFAULT NULL,
  `documento_permiso` varchar(255) DEFAULT NULL,
  `ID_Funcionario` int(11) NOT NULL,
  `Tipo_Permiso` enum('Vacaciones','Enfermedad','Maternidad','Paternidad','Asuntos Propios','Estudios','Comisión Servicio','Otro') NOT NULL,
  `Fecha_Solicitud` date DEFAULT curdate(),
  `Fecha_Inicio_Permiso` date NOT NULL,
  `Fecha_Fin_Permiso` date NOT NULL,
  `Estado_Permiso` enum('Pendiente','Aprobado','Denegado','Cancelado','Disfrutado') DEFAULT 'Pendiente',
  `Motivo` text DEFAULT NULL,
  `Observaciones` text DEFAULT NULL,
  `Documento_Soporte_URL` varchar(255) DEFAULT NULL,
  `ID_Usuario_Creador` int(11) NOT NULL,
  `Fecha_Creacion_Registro` datetime DEFAULT current_timestamp(),
  `ID_Usuario_Ultima_Modificacion` int(11) DEFAULT NULL,
  `Fecha_Ultima_Modificacion` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `tbl_permisos`
--

INSERT INTO `tbl_permisos` (`ID_Permiso`, `token`, `documento_permiso`, `ID_Funcionario`, `Tipo_Permiso`, `Fecha_Solicitud`, `Fecha_Inicio_Permiso`, `Fecha_Fin_Permiso`, `Estado_Permiso`, `Motivo`, `Observaciones`, `Documento_Soporte_URL`, `ID_Usuario_Creador`, `Fecha_Creacion_Registro`, `ID_Usuario_Ultima_Modificacion`, `Fecha_Ultima_Modificacion`) VALUES
(1, 1, 'uploads/documentos_permiso/docperm_689b3dab7b7be_perm_689b111fb14b5_trabajo diagnostico.pdf', 1, 'Enfermedad', '2025-06-28', '2025-06-01', '2025-07-01', 'Aprobado', 'permiso por enfermedad grave.', 'todo bien y en orden.', 'uploads/permisos/perm_689b3d1edffc9_docperm_689b3486847c6_539204.pdf', 2, '2025-06-28 12:27:58', 2, '2025-08-12 14:12:11'),
(2, 0, NULL, 2, 'Vacaciones', '2025-08-05', '2025-09-01', '2025-08-30', 'Pendiente', 'Vacaciones por derecho', 'volverá muy pronto', NULL, 2, '2025-08-05 14:24:50', 2, '2025-08-12 14:09:15');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_quejas_sugerencias`
--

CREATE TABLE `tbl_quejas_sugerencias` (
  `ID_QS` int(11) NOT NULL,
  `ID_Funcionario` int(11) DEFAULT NULL,
  `Tipo` enum('queja','sugerencia') NOT NULL,
  `Mensaje` text NOT NULL,
  `Fecha_Envio` datetime DEFAULT current_timestamp(),
  `Estado` enum('pendiente','revisado','resuelto') DEFAULT 'pendiente',
  `Anonimo` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `tbl_quejas_sugerencias`
--

INSERT INTO `tbl_quejas_sugerencias` (`ID_QS`, `ID_Funcionario`, `Tipo`, `Mensaje`, `Fecha_Envio`, `Estado`, `Anonimo`) VALUES
(13, 1, 'queja', 'No funciona correctamente la impresora del área de recursos humanos.', '2025-08-13 11:10:51', 'pendiente', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_usuarios`
--

CREATE TABLE `tbl_usuarios` (
  `ID_Usuario` int(11) NOT NULL,
  `Nombre_Usuario` varchar(50) NOT NULL,
  `Contrasena_Hash` varchar(255) NOT NULL,
  `Rol_Usuario` enum('Administrador','Recursos Humanos','Jefe Personal','Auditor','Secretaria','Usuario') NOT NULL,
  `Email_Contacto` varchar(100) NOT NULL,
  `Fecha_Creacion` datetime DEFAULT current_timestamp(),
  `Ultimo_Acceso` datetime DEFAULT NULL,
  `Activo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `tbl_usuarios`
--

INSERT INTO `tbl_usuarios` (`ID_Usuario`, `Nombre_Usuario`, `Contrasena_Hash`, `Rol_Usuario`, `Email_Contacto`, `Fecha_Creacion`, `Ultimo_Acceso`, `Activo`) VALUES
(2, 'Mh123', '$2y$10$3JOO3f.29T7kwjCl7W/jZO59mHHNjRXbAgl1oMlz9QzJsO38gaAoq', 'Administrador', 'salvadormete2@gmail.com', '2025-06-27 13:10:33', '2025-08-18 12:28:50', 1),
(4, 'Usuario', '$2y$10$9UJOCVre0YY9XcJtAZKm6OgKpBojscaagh63HQYMsIe.shI33qLXu', 'Usuario', 'minerva@prueba.com', '2025-08-11 14:55:04', '2025-08-12 13:29:53', 1),
(5, 'JPersonal', '$2y$10$Jo7ciXxrQG9Albdw0jQLaudjhHpPU4stWXxwQ00xU2t/dAe1Pp5k2', 'Jefe Personal', 'jefepersonal@gmail.com', '2025-08-12 08:56:16', '2025-08-18 10:26:09', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_pruebas`
--

CREATE TABLE `tipo_pruebas` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `fecha_registro` datetime DEFAULT current_timestamp(),
  `id_usuario` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `tipo_pruebas`
--

INSERT INTO `tipo_pruebas` (`id`, `nombre`, `precio`, `fecha_registro`, `id_usuario`) VALUES
(1, 'PALUDISMO', '5000.00', '2025-06-12 12:38:09', 1),
(2, 'HEPATITIS B', '9000.00', '2025-06-12 12:39:44', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usos_deposito`
--

CREATE TABLE `usos_deposito` (
  `id` int(11) NOT NULL,
  `deposito_id` int(11) DEFAULT NULL,
  `paciente_id` int(11) DEFAULT NULL,
  `concepto` varchar(225) DEFAULT NULL,
  `monto_usado` decimal(10,2) DEFAULT NULL,
  `descuento` decimal(10,2) DEFAULT NULL,
  `fecha` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
--
-- Estructura de tabla para la tabla `configuracion`
--
CREATE TABLE `configuracion` (
  `clave` varchar(100) NOT NULL,
  `valor` text DEFAULT NULL,
  PRIMARY KEY (`clave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos inicial (Opcional, si quieres definir valores por defecto)
--
INSERT INTO `configuracion` (`clave`, `valor`) VALUES
('nombre_sistema', 'THEMIS - Ministerio de Justicia'),
('email_soporte', 'soporte@ministeriojusticia.gq'),
('limite_funcionarios', '500'),
('tiempo_sesion', '3600'),
('habilitar_registro', '0');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `tbl_asignaciones`
--
ALTER TABLE `tbl_asignaciones`
  ADD PRIMARY KEY (`ID_Asignacion`),
  ADD KEY `ID_Funcionario` (`ID_Funcionario`),
  ADD KEY `ID_Cargo` (`ID_Cargo`),
  ADD KEY `ID_Departamento` (`ID_Departamento`),
  ADD KEY `ID_Destino` (`ID_Destino`),
  ADD KEY `ID_Usuario_Creador` (`ID_Usuario_Creador`),
  ADD KEY `ID_Usuario_Ultima_Modificacion` (`ID_Usuario_Ultima_Modificacion`);

--
-- Indices de la tabla `tbl_capacitaciones`
--
ALTER TABLE `tbl_capacitaciones`
  ADD PRIMARY KEY (`ID_Capacitacion`),
  ADD KEY `ID_Funcionario` (`ID_Funcionario`),
  ADD KEY `ID_Usuario_Creador` (`ID_Usuario_Creador`),
  ADD KEY `ID_Usuario_Ultima_Modificacion` (`ID_Usuario_Ultima_Modificacion`);

--
-- Indices de la tabla `tbl_cargos`
--
ALTER TABLE `tbl_cargos`
  ADD PRIMARY KEY (`ID_Cargo`),
  ADD UNIQUE KEY `Nombre_Cargo` (`Nombre_Cargo`),
  ADD KEY `ID_Usuario_Creador` (`ID_Usuario_Creador`),
  ADD KEY `ID_Usuario_Ultima_Modificacion` (`ID_Usuario_Ultima_Modificacion`);

--
-- Indices de la tabla `tbl_cursos`
--
ALTER TABLE `tbl_cursos`
  ADD PRIMARY KEY (`ID_Curso`);

--
-- Indices de la tabla `tbl_cursos_funcionarios`
--
ALTER TABLE `tbl_cursos_funcionarios`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `ID_Funcionario` (`ID_Funcionario`),
  ADD KEY `ID_Curso` (`ID_Curso`);

--
-- Indices de la tabla `tbl_departamentos`
--
ALTER TABLE `tbl_departamentos`
  ADD PRIMARY KEY (`ID_Departamento`),
  ADD UNIQUE KEY `Nombre_Departamento` (`Nombre_Departamento`),
  ADD KEY `ID_Usuario_Creador` (`ID_Usuario_Creador`),
  ADD KEY `ID_Usuario_Ultima_Modificacion` (`ID_Usuario_Ultima_Modificacion`);

--
-- Indices de la tabla `tbl_destinos`
--
ALTER TABLE `tbl_destinos`
  ADD PRIMARY KEY (`ID_Destino`),
  ADD UNIQUE KEY `Nombre_Destino` (`Nombre_Destino`),
  ADD KEY `ID_Usuario_Creador` (`ID_Usuario_Creador`),
  ADD KEY `ID_Usuario_Ultima_Modificacion` (`ID_Usuario_Ultima_Modificacion`);

--
-- Indices de la tabla `tbl_formacion_academica`
--
ALTER TABLE `tbl_formacion_academica`
  ADD PRIMARY KEY (`ID_Formacion`),
  ADD KEY `ID_Funcionario` (`ID_Funcionario`),
  ADD KEY `ID_Usuario_Creador` (`ID_Usuario_Creador`),
  ADD KEY `ID_Usuario_Ultima_Modificacion` (`ID_Usuario_Ultima_Modificacion`);

--
-- Indices de la tabla `tbl_funcionarios`
--
ALTER TABLE `tbl_funcionarios`
  ADD PRIMARY KEY (`ID_Funcionario`),
  ADD UNIQUE KEY `Codigo_Funcionario` (`Codigo_Funcionario`),
  ADD UNIQUE KEY `DNI_Pasaporte` (`DNI_Pasaporte`),
  ADD UNIQUE KEY `Email_Oficial` (`Email_Oficial`),
  ADD KEY `ID_Usuario_Creador` (`ID_Usuario_Creador`),
  ADD KEY `ID_Usuario_Ultima_Modificacion` (`ID_Usuario_Ultima_Modificacion`);

--
-- Indices de la tabla `tbl_instrucciones`
--
ALTER TABLE `tbl_instrucciones`
  ADD PRIMARY KEY (`ID_Instruccion`),
  ADD KEY `ID_Funcionario` (`ID_Funcionario`);

--
-- Indices de la tabla `tbl_permisos`
--
ALTER TABLE `tbl_permisos`
  ADD PRIMARY KEY (`ID_Permiso`),
  ADD KEY `ID_Funcionario` (`ID_Funcionario`),
  ADD KEY `ID_Usuario_Creador` (`ID_Usuario_Creador`),
  ADD KEY `ID_Usuario_Ultima_Modificacion` (`ID_Usuario_Ultima_Modificacion`);

--
-- Indices de la tabla `tbl_quejas_sugerencias`
--
ALTER TABLE `tbl_quejas_sugerencias`
  ADD PRIMARY KEY (`ID_QS`),
  ADD KEY `ID_Funcionario` (`ID_Funcionario`);

--
-- Indices de la tabla `tbl_usuarios`
--
ALTER TABLE `tbl_usuarios`
  ADD PRIMARY KEY (`ID_Usuario`),
  ADD UNIQUE KEY `Nombre_Usuario` (`Nombre_Usuario`),
  ADD UNIQUE KEY `Email_Contacto` (`Email_Contacto`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `tbl_asignaciones`
--
ALTER TABLE `tbl_asignaciones`
  MODIFY `ID_Asignacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `tbl_capacitaciones`
--
ALTER TABLE `tbl_capacitaciones`
  MODIFY `ID_Capacitacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `tbl_cargos`
--
ALTER TABLE `tbl_cargos`
  MODIFY `ID_Cargo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `tbl_cursos`
--
ALTER TABLE `tbl_cursos`
  MODIFY `ID_Curso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `tbl_cursos_funcionarios`
--
ALTER TABLE `tbl_cursos_funcionarios`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de la tabla `tbl_departamentos`
--
ALTER TABLE `tbl_departamentos`
  MODIFY `ID_Departamento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `tbl_destinos`
--
ALTER TABLE `tbl_destinos`
  MODIFY `ID_Destino` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `tbl_formacion_academica`
--
ALTER TABLE `tbl_formacion_academica`
  MODIFY `ID_Formacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `tbl_funcionarios`
--
ALTER TABLE `tbl_funcionarios`
  MODIFY `ID_Funcionario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `tbl_instrucciones`
--
ALTER TABLE `tbl_instrucciones`
  MODIFY `ID_Instruccion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tbl_permisos`
--
ALTER TABLE `tbl_permisos`
  MODIFY `ID_Permiso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `tbl_quejas_sugerencias`
--
ALTER TABLE `tbl_quejas_sugerencias`
  MODIFY `ID_QS` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `tbl_usuarios`
--
ALTER TABLE `tbl_usuarios`
  MODIFY `ID_Usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `tbl_asignaciones`
--
ALTER TABLE `tbl_asignaciones`
  ADD CONSTRAINT `tbl_asignaciones_ibfk_1` FOREIGN KEY (`ID_Funcionario`) REFERENCES `tbl_funcionarios` (`ID_Funcionario`),
  ADD CONSTRAINT `tbl_asignaciones_ibfk_2` FOREIGN KEY (`ID_Cargo`) REFERENCES `tbl_cargos` (`ID_Cargo`),
  ADD CONSTRAINT `tbl_asignaciones_ibfk_3` FOREIGN KEY (`ID_Departamento`) REFERENCES `tbl_departamentos` (`ID_Departamento`),
  ADD CONSTRAINT `tbl_asignaciones_ibfk_4` FOREIGN KEY (`ID_Destino`) REFERENCES `tbl_destinos` (`ID_Destino`),
  ADD CONSTRAINT `tbl_asignaciones_ibfk_5` FOREIGN KEY (`ID_Usuario_Creador`) REFERENCES `tbl_usuarios` (`ID_Usuario`),
  ADD CONSTRAINT `tbl_asignaciones_ibfk_6` FOREIGN KEY (`ID_Usuario_Ultima_Modificacion`) REFERENCES `tbl_usuarios` (`ID_Usuario`);

--
-- Filtros para la tabla `tbl_capacitaciones`
--
ALTER TABLE `tbl_capacitaciones`
  ADD CONSTRAINT `tbl_capacitaciones_ibfk_1` FOREIGN KEY (`ID_Funcionario`) REFERENCES `tbl_funcionarios` (`ID_Funcionario`),
  ADD CONSTRAINT `tbl_capacitaciones_ibfk_2` FOREIGN KEY (`ID_Usuario_Creador`) REFERENCES `tbl_usuarios` (`ID_Usuario`),
  ADD CONSTRAINT `tbl_capacitaciones_ibfk_3` FOREIGN KEY (`ID_Usuario_Ultima_Modificacion`) REFERENCES `tbl_usuarios` (`ID_Usuario`);

--
-- Filtros para la tabla `tbl_cargos`
--
ALTER TABLE `tbl_cargos`
  ADD CONSTRAINT `tbl_cargos_ibfk_1` FOREIGN KEY (`ID_Usuario_Creador`) REFERENCES `tbl_usuarios` (`ID_Usuario`),
  ADD CONSTRAINT `tbl_cargos_ibfk_2` FOREIGN KEY (`ID_Usuario_Ultima_Modificacion`) REFERENCES `tbl_usuarios` (`ID_Usuario`);

--
-- Filtros para la tabla `tbl_cursos_funcionarios`
--
ALTER TABLE `tbl_cursos_funcionarios`
  ADD CONSTRAINT `tbl_cursos_funcionarios_ibfk_1` FOREIGN KEY (`ID_Funcionario`) REFERENCES `tbl_funcionarios` (`ID_Funcionario`) ON DELETE CASCADE,
  ADD CONSTRAINT `tbl_cursos_funcionarios_ibfk_2` FOREIGN KEY (`ID_Curso`) REFERENCES `tbl_cursos` (`ID_Curso`) ON DELETE CASCADE;

--
-- Filtros para la tabla `tbl_departamentos`
--
ALTER TABLE `tbl_departamentos`
  ADD CONSTRAINT `tbl_departamentos_ibfk_1` FOREIGN KEY (`ID_Usuario_Creador`) REFERENCES `tbl_usuarios` (`ID_Usuario`),
  ADD CONSTRAINT `tbl_departamentos_ibfk_2` FOREIGN KEY (`ID_Usuario_Ultima_Modificacion`) REFERENCES `tbl_usuarios` (`ID_Usuario`);

--
-- Filtros para la tabla `tbl_destinos`
--
ALTER TABLE `tbl_destinos`
  ADD CONSTRAINT `tbl_destinos_ibfk_1` FOREIGN KEY (`ID_Usuario_Creador`) REFERENCES `tbl_usuarios` (`ID_Usuario`),
  ADD CONSTRAINT `tbl_destinos_ibfk_2` FOREIGN KEY (`ID_Usuario_Ultima_Modificacion`) REFERENCES `tbl_usuarios` (`ID_Usuario`);

--
-- Filtros para la tabla `tbl_formacion_academica`
--
ALTER TABLE `tbl_formacion_academica`
  ADD CONSTRAINT `tbl_formacion_academica_ibfk_1` FOREIGN KEY (`ID_Funcionario`) REFERENCES `tbl_funcionarios` (`ID_Funcionario`),
  ADD CONSTRAINT `tbl_formacion_academica_ibfk_2` FOREIGN KEY (`ID_Usuario_Creador`) REFERENCES `tbl_usuarios` (`ID_Usuario`),
  ADD CONSTRAINT `tbl_formacion_academica_ibfk_3` FOREIGN KEY (`ID_Usuario_Ultima_Modificacion`) REFERENCES `tbl_usuarios` (`ID_Usuario`);

--
-- Filtros para la tabla `tbl_funcionarios`
--
ALTER TABLE `tbl_funcionarios`
  ADD CONSTRAINT `tbl_funcionarios_ibfk_1` FOREIGN KEY (`ID_Usuario_Creador`) REFERENCES `tbl_usuarios` (`ID_Usuario`),
  ADD CONSTRAINT `tbl_funcionarios_ibfk_2` FOREIGN KEY (`ID_Usuario_Ultima_Modificacion`) REFERENCES `tbl_usuarios` (`ID_Usuario`);

--
-- Filtros para la tabla `tbl_instrucciones`
--
ALTER TABLE `tbl_instrucciones`
  ADD CONSTRAINT `tbl_instrucciones_ibfk_1` FOREIGN KEY (`ID_Funcionario`) REFERENCES `tbl_funcionarios` (`ID_Funcionario`) ON DELETE CASCADE;

--
-- Filtros para la tabla `tbl_permisos`
--
ALTER TABLE `tbl_permisos`
  ADD CONSTRAINT `tbl_permisos_ibfk_1` FOREIGN KEY (`ID_Funcionario`) REFERENCES `tbl_funcionarios` (`ID_Funcionario`),
  ADD CONSTRAINT `tbl_permisos_ibfk_2` FOREIGN KEY (`ID_Usuario_Creador`) REFERENCES `tbl_usuarios` (`ID_Usuario`),
  ADD CONSTRAINT `tbl_permisos_ibfk_3` FOREIGN KEY (`ID_Usuario_Ultima_Modificacion`) REFERENCES `tbl_usuarios` (`ID_Usuario`);

--
-- Filtros para la tabla `tbl_quejas_sugerencias`
--
ALTER TABLE `tbl_quejas_sugerencias`
  ADD CONSTRAINT `tbl_quejas_sugerencias_ibfk_1` FOREIGN KEY (`ID_Funcionario`) REFERENCES `tbl_funcionarios` (`ID_Funcionario`) ON DELETE CASCADE;
COMMIT;


/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
