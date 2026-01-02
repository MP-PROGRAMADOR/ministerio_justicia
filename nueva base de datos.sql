/* =========================================================
   BASE DE DATOS
   ========================================================= */

DROP DATABASE IF EXISTS themis_ministeriojusticia;
CREATE DATABASE themis_ministeriojusticia
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE themis_ministeriojusticia;

/* =========================================================
   ROLES
   ========================================================= */

CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE,
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

/* =========================================================
   USUARIOS
   ========================================================= */

CREATE TABLE tbl_usuarios (
    ID_Usuario INT AUTO_INCREMENT PRIMARY KEY,
    Nombre_Usuario VARCHAR(50) NOT NULL UNIQUE,
    Contrasena_Hash VARCHAR(255) NOT NULL,
    Rol_Usuario ENUM(
        'Administrador','Recursos Humanos','Jefe Personal',
        'Auditor','Secretaria','Usuario'
    ) NOT NULL,
    Nombre VARCHAR(100),
    Apellidos VARCHAR(100),
    Email_Contacto VARCHAR(100) NOT NULL,
    Fecha_Creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    Ultimo_Acceso DATETIME NULL,
    Activo TINYINT(1) DEFAULT 1
) ENGINE=InnoDB;

/* =========================================================
   TABLAS ESTRUCTURALES
   ========================================================= */

CREATE TABLE categorias (
    Id_categoria INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT
) ENGINE=InnoDB;

CREATE TABLE cargos (
    Id_cargo INT AUTO_INCREMENT PRIMARY KEY,
    Nombre VARCHAR(100) NOT NULL,
    Nivel_jerarquico VARCHAR(50)
) ENGINE=InnoDB;

CREATE TABLE direcciones (
    Id_direccion INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    ubicacion VARCHAR(150),
    distrito VARCHAR(100),
    provincia VARCHAR(100),
    region VARCHAR(100)
) ENGINE=InnoDB;

CREATE TABLE secciones (
    Id_seccion INT AUTO_INCREMENT PRIMARY KEY,
    Id_direccion INT,
    nombre VARCHAR(100),
    FOREIGN KEY (Id_direccion) REFERENCES direcciones(Id_direccion)
) ENGINE=InnoDB;

/* =========================================================
   FUNCIONARIOS
   ========================================================= */

CREATE TABLE funcionarios (
    Id_funcionario INT AUTO_INCREMENT PRIMARY KEY,
    CODIGO VARCHAR(30) NOT NULL UNIQUE,

    Nombre VARCHAR(100),
    Apellidos VARCHAR(100),
    Dip_Pasaporte VARCHAR(50),
    Sexo ENUM('Masculino','Femenino','Otro'),
    Fecha_nacimiento DATE,
    Lugar_nacimiento VARCHAR(100),
    Nacionalidad VARCHAR(100),
    Telefono VARCHAR(20),
    Correo VARCHAR(100),
    Domicilio TEXT,
    Num_carnet_fun VARCHAR(50),

    Fecha_nombramiento DATE,
    Fecha_posesion DATE,

    Id_seccion INT,
    Funcion VARCHAR(100),
    Id_categoria INT,

    Profesion VARCHAR(100),
    Maximo_nivel_estudios VARCHAR(100),
    Titulacion_academica VARCHAR(150),
    Universidad_centro_formacion VARCHAR(150),
    Fecha_graduacion DATE,

    Foto VARCHAR(255),
    Dip_pass_copia VARCHAR(255),
    Copia_doc_nomb VARCHAR(255),
    Copia_carnet_func VARCHAR(255),
    Copia_doc_tom_posesion VARCHAR(255),
    Copia_doc_academicos VARCHAR(255),

    Usuario_creador INT NOT NULL,
    Fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (Id_seccion) REFERENCES secciones(Id_seccion),
    FOREIGN KEY (Id_categoria) REFERENCES categorias(Id_categoria),
    FOREIGN KEY (Usuario_creador) REFERENCES tbl_usuarios(ID_Usuario)
) ENGINE=InnoDB;

/* =========================================================
   NOMBRAMIENTOS
   ========================================================= */

CREATE TABLE nombramientos (
    Id_nombramiento INT AUTO_INCREMENT PRIMARY KEY,
    Id_funcionario INT NOT NULL,
    Id_cargo INT NOT NULL,
    Fecha_nombramiento DATE,
    Fecha_toma_posesion DATE,
    Id_direccion INT,
    Id_seccion INT,
    Id_categoria INT,

    Copia_doc_nomb VARCHAR(255),
    Copia_doc_tom_posesion VARCHAR(255),

    Usuario_creador INT NOT NULL,
    Fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (Id_funcionario) REFERENCES funcionarios(Id_funcionario),
    FOREIGN KEY (Id_cargo) REFERENCES cargos(Id_cargo),
    FOREIGN KEY (Id_direccion) REFERENCES direcciones(Id_direccion),
    FOREIGN KEY (Id_seccion) REFERENCES secciones(Id_seccion),
    FOREIGN KEY (Id_categoria) REFERENCES categorias(Id_categoria),
    FOREIGN KEY (Usuario_creador) REFERENCES tbl_usuarios(ID_Usuario)
) ENGINE=InnoDB;

/* =========================================================
   QUEJAS Y SUGERENCIAS
   ========================================================= */

CREATE TABLE tbl_quejas_sugerencias (
    ID_QS INT AUTO_INCREMENT PRIMARY KEY,
    ID_Funcionario INT,
    Tipo ENUM('queja','sugerencia') NOT NULL,
    Mensaje TEXT NOT NULL,
    Fecha_Envio DATETIME DEFAULT CURRENT_TIMESTAMP,
    Estado ENUM('pendiente','revisado','resuelto') DEFAULT 'pendiente',
    Anonimo TINYINT(1) DEFAULT 0,
    Usuario_creador INT NOT NULL,

    FOREIGN KEY (ID_Funcionario) REFERENCES funcionarios(Id_funcionario),
    FOREIGN KEY (Usuario_creador) REFERENCES tbl_usuarios(ID_Usuario)
) ENGINE=InnoDB;

/* =========================================================
   PERMISOS
   ========================================================= */

CREATE TABLE tbl_permisos (
    ID_Permiso INT AUTO_INCREMENT PRIMARY KEY,
    ID_Funcionario INT NOT NULL,
    Tipo_Permiso ENUM(
        'Vacaciones','Enfermedad','Maternidad','Paternidad',
        'Asuntos Propios','Estudios','Comisión Servicio','Otro'
    ),
    Fecha_Solicitud DATE DEFAULT CURDATE(),
    Fecha_Inicio_Permiso DATE,
    Fecha_Fin_Permiso DATE,
    Estado_Permiso ENUM(
        'Pendiente','Aprobado','Denegado','Cancelado','Disfrutado'
    ) DEFAULT 'Pendiente',
    Motivo TEXT,
    Documento_Soporte_URL VARCHAR(255),

    Usuario_creador INT NOT NULL,
    Fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (ID_Funcionario) REFERENCES funcionarios(Id_funcionario),
    FOREIGN KEY (Usuario_creador) REFERENCES tbl_usuarios(ID_Usuario)
) ENGINE=InnoDB;

/* =========================================================
   CURSOS
   ========================================================= */

CREATE TABLE tbl_cursos (
    ID_Curso INT AUTO_INCREMENT PRIMARY KEY,
    Nombre_Curso VARCHAR(255),
    Descripcion TEXT,
    Fecha_Inicio DATE,
    Fecha_Fin DATE,
    Cupo INT DEFAULT 0,
    Usuario_creador INT,
    Fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (Usuario_creador) REFERENCES tbl_usuarios(ID_Usuario)
) ENGINE=InnoDB;

CREATE TABLE tbl_cursos_funcionarios (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    ID_Funcionario INT,
    ID_Curso INT,
    Fecha_Matricula DATE DEFAULT CURDATE(),

    FOREIGN KEY (ID_Funcionario) REFERENCES funcionarios(Id_funcionario),
    FOREIGN KEY (ID_Curso) REFERENCES tbl_cursos(ID_Curso)
) ENGINE=InnoDB;

/* =========================================================
   INSTRUCCIONES
   ========================================================= */

CREATE TABLE tbl_instrucciones (
    ID_Instruccion INT AUTO_INCREMENT PRIMARY KEY,
    ID_Funcionario INT NOT NULL,
    Titulo VARCHAR(255) NOT NULL,
    Mensaje TEXT NOT NULL,
    Fecha_Envio DATETIME DEFAULT CURRENT_TIMESTAMP,
    Leido TINYINT(1) DEFAULT 0,
    Usuario_creador INT NOT NULL,

    FOREIGN KEY (ID_Funcionario)
        REFERENCES funcionarios(Id_funcionario)
        ON DELETE CASCADE,

    FOREIGN KEY (Usuario_creador)
        REFERENCES tbl_usuarios(ID_Usuario)
) ENGINE=InnoDB;

/* =========================================================
   LOGS (AUDITORÍA)
   ========================================================= */

CREATE TABLE logs (
    Id_log INT AUTO_INCREMENT PRIMARY KEY,
    Usuario_id INT NOT NULL,
    Tabla_afectada VARCHAR(100),
    Accion ENUM('INSERT','UPDATE','DELETE','LOGIN','LOGOUT'),
    Registro_id INT,
    Fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    IP VARCHAR(45),
    Dispositivo TEXT,

    FOREIGN KEY (Usuario_id) REFERENCES tbl_usuarios(ID_Usuario)
) ENGINE=InnoDB;
