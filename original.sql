-- 1. Modificar tabla permisos: añadir token y documento_permiso
ALTER TABLE tbl_permisos 
ADD COLUMN token VARCHAR(255) AFTER ID_Permiso,
ADD COLUMN documento_permiso VARCHAR(255) AFTER token;


DROP TABLE IF EXISTS tbl_quejas_sugerencias;

CREATE TABLE tbl_quejas_sugerencias (
    ID_QS INT AUTO_INCREMENT PRIMARY KEY,
    ID_Funcionario INT NULL,
    Tipo ENUM('queja', 'sugerencia') NOT NULL,
    Mensaje TEXT NOT NULL,
    Fecha_Envio DATETIME DEFAULT CURRENT_TIMESTAMP,
    Estado ENUM('pendiente', 'revisado', 'resuelto') DEFAULT 'pendiente',
    Anonimo TINYINT(1) DEFAULT 0, -- 0 = No, 1 = Sí
    FOREIGN KEY (ID_Funcionario) REFERENCES tbl_funcionarios(ID_Funcionario) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


-- 2. Crear tabla cursos (cursos que ofrece el ministerio)
CREATE TABLE tbl_cursos (
    ID_Curso INT AUTO_INCREMENT PRIMARY KEY,
    Nombre_Curso VARCHAR(255) NOT NULL,
    Descripcion TEXT,
    Fecha_Inicio DATE,
    Fecha_Fin DATE,
    Cupo INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 3. Crear tabla cursos_funcionarios (matriculas de funcionarios en cursos)
CREATE TABLE tbl_cursos_funcionarios (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    ID_Funcionario INT NOT NULL,
    ID_Curso INT NOT NULL,
    Fecha_Matricula DATE DEFAULT CURRENT_DATE,
    FOREIGN KEY (ID_Funcionario) REFERENCES tbl_funcionarios(ID_Funcionario) ON DELETE CASCADE,
    FOREIGN KEY (ID_Curso) REFERENCES tbl_cursos(ID_Curso) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 4. Crear tabla instrucciones (instrucciones diarias para funcionarios)
CREATE TABLE tbl_instrucciones (
    ID_Instruccion INT AUTO_INCREMENT PRIMARY KEY,
    ID_Funcionario INT NOT NULL,
    Titulo VARCHAR(255) NOT NULL,
    Mensaje TEXT NOT NULL,
    Fecha_Envio DATETIME DEFAULT CURRENT_TIMESTAMP,
    Leido TINYINT(1) DEFAULT 0,
    FOREIGN KEY (ID_Funcionario) REFERENCES tbl_funcionarios(ID_Funcionario) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 5. Crear tabla quejas_sugerencias (para quejas y sugerencias de funcionarios)
CREATE TABLE tbl_quejas_sugerencias (
    ID_QS INT AUTO_INCREMENT PRIMARY KEY,
    ID_Funcionario INT NOT NULL,
    Tipo ENUM('queja', 'sugerencia') NOT NULL,
    Mensaje TEXT NOT NULL,
    Fecha_Envio DATETIME DEFAULT CURRENT_TIMESTAMP,
    Estado ENUM('pendiente', 'revisado', 'resuelto') DEFAULT 'pendiente',
    FOREIGN KEY (ID_Funcionario) REFERENCES tbl_funcionarios(ID_Funcionario) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
