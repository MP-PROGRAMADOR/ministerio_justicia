-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 01-07-2025 a las 14:51:19
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
(1, 1, 1, 1, 2, '2022-12-01', '2026-10-21', 2, '2025-06-30 12:50:05', 2, '2025-06-30 14:03:54');

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
(1, 'Jefe de Área 2', 'Responsable del área técnica 4', 3, 2, '2025-06-30 12:12:25', 2, '2025-06-30 12:29:38');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_departamentos`
--

CREATE TABLE `tbl_departamentos` (
  `ID_Departamento` int(11) NOT NULL,
  `Nombre_Departamento` varchar(100) NOT NULL,
  `Ubicacion` varchar(200) DEFAULT NULL,
  `Telefono_Departamento` varchar(20) DEFAULT NULL,
  `ID_Usuario_Creador` int(11) NOT NULL,
  `Fecha_Creacion_Registro` datetime DEFAULT current_timestamp(),
  `ID_Usuario_Ultima_Modificacion` int(11) DEFAULT NULL,
  `Fecha_Ultima_Modificacion` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `Ciudad` varchar(250) NOT NULL,
  `Distrito` varchar(250) NOT NULL,
  `Provincia` varchar(250) NOT NULL
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
(2, 'Juzgado de Mlabo', 'Juzgado', 'malabo II', 'Malabo', '222478702', 2, '2025-06-30 11:51:52', 2, '2025-06-30 12:04:18', 'Malabo', 'Bioko Norte', '2020-06-28', '2028-11-17');

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
(3, 2, 'Lienciado en Informatica', 'INSTTIC', '2022-06-15', 'Grado', 2, '2025-07-01 12:17:34', NULL, NULL);

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
(1, 'SAL5D1DF', 'Salvador', 'Mete Bijeri', '384762', '2001-02-14', 'Masculino', 'Ecutoguineano', 'Ela Nguema', '555908765', 'salvadormete@gmail.com', '2025-06-28', 'Activo', 'funcionarios/func_685fc814109e6.jpeg', 2, '2025-06-28 11:09:21', 2, '2025-06-28 11:47:00'),
(2, 'NAZ7B4A2', 'Nazario Monebama', 'Etoho Nsaha', '160506', '1998-02-04', 'Masculino', 'Ecutoguineano', 'Argentina', '555712824', 'nazariomen@gmail.com', '2025-07-01', 'Activo', 'funcionarios/func_6863c33ebd2d8.jpg', 2, '2025-07-01 12:15:10', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_permisos`
--

CREATE TABLE `tbl_permisos` (
  `ID_Permiso` int(11) NOT NULL,
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

INSERT INTO `tbl_permisos` (`ID_Permiso`, `ID_Funcionario`, `Tipo_Permiso`, `Fecha_Solicitud`, `Fecha_Inicio_Permiso`, `Fecha_Fin_Permiso`, `Estado_Permiso`, `Motivo`, `Observaciones`, `Documento_Soporte_URL`, `ID_Usuario_Creador`, `Fecha_Creacion_Registro`, `ID_Usuario_Ultima_Modificacion`, `Fecha_Ultima_Modificacion`) VALUES
(1, 1, 'Enfermedad', '2025-06-28', '2025-06-01', '2025-06-30', 'Pendiente', 'permiso por enfermedad grave.', 'todo bien y en orden.', 'uploads/permisos/permiso_1751110078_c61fd7c36b.docx', 2, '2025-06-28 12:27:58', 2, '2025-06-30 08:30:48');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_usuarios`
--

CREATE TABLE `tbl_usuarios` (
  `ID_Usuario` int(11) NOT NULL,
  `Nombre_Usuario` varchar(50) NOT NULL,
  `Contrasena_Hash` varchar(255) NOT NULL,
  `Rol_Usuario` enum('Administrador','Recursos Humanos','Consulta','Auditor','Secretaria') NOT NULL,
  `Email_Contacto` varchar(100) NOT NULL,
  `Fecha_Creacion` datetime DEFAULT current_timestamp(),
  `Ultimo_Acceso` datetime DEFAULT NULL,
  `Activo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `tbl_usuarios`
--

INSERT INTO `tbl_usuarios` (`ID_Usuario`, `Nombre_Usuario`, `Contrasena_Hash`, `Rol_Usuario`, `Email_Contacto`, `Fecha_Creacion`, `Ultimo_Acceso`, `Activo`) VALUES
(2, 'Mh123', '$2y$10$3JOO3f.29T7kwjCl7W/jZO59mHHNjRXbAgl1oMlz9QzJsO38gaAoq', 'Administrador', 'salvadormete2@gmail.com', '2025-06-27 13:10:33', '2025-07-01 12:37:15', 1);

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
-- Indices de la tabla `tbl_permisos`
--
ALTER TABLE `tbl_permisos`
  ADD PRIMARY KEY (`ID_Permiso`),
  ADD KEY `ID_Funcionario` (`ID_Funcionario`),
  ADD KEY `ID_Usuario_Creador` (`ID_Usuario_Creador`),
  ADD KEY `ID_Usuario_Ultima_Modificacion` (`ID_Usuario_Ultima_Modificacion`);

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
  MODIFY `ID_Asignacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `tbl_capacitaciones`
--
ALTER TABLE `tbl_capacitaciones`
  MODIFY `ID_Capacitacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `tbl_cargos`
--
ALTER TABLE `tbl_cargos`
  MODIFY `ID_Cargo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `tbl_departamentos`
--
ALTER TABLE `tbl_departamentos`
  MODIFY `ID_Departamento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `tbl_destinos`
--
ALTER TABLE `tbl_destinos`
  MODIFY `ID_Destino` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `tbl_formacion_academica`
--
ALTER TABLE `tbl_formacion_academica`
  MODIFY `ID_Formacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `tbl_funcionarios`
--
ALTER TABLE `tbl_funcionarios`
  MODIFY `ID_Funcionario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `tbl_permisos`
--
ALTER TABLE `tbl_permisos`
  MODIFY `ID_Permiso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `tbl_usuarios`
--
ALTER TABLE `tbl_usuarios`
  MODIFY `ID_Usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
-- Filtros para la tabla `tbl_permisos`
--
ALTER TABLE `tbl_permisos`
  ADD CONSTRAINT `tbl_permisos_ibfk_1` FOREIGN KEY (`ID_Funcionario`) REFERENCES `tbl_funcionarios` (`ID_Funcionario`),
  ADD CONSTRAINT `tbl_permisos_ibfk_2` FOREIGN KEY (`ID_Usuario_Creador`) REFERENCES `tbl_usuarios` (`ID_Usuario`),
  ADD CONSTRAINT `tbl_permisos_ibfk_3` FOREIGN KEY (`ID_Usuario_Ultima_Modificacion`) REFERENCES `tbl_usuarios` (`ID_Usuario`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
