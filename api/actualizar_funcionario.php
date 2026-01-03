<?php
session_start();
require_once '../includes/conexion.php';

function getIP() {
    return $_SERVER['REMOTE_ADDR'] ?? 'Desconocida';
}

function getDispositivo() {
    return $_SERVER['HTTP_USER_AGENT'] ?? 'Desconocido';
}

if (!isset($_SESSION['ID_Usuario'])) {
    $_SESSION['error'] = "Sesión expirada.";
    header("Location: ../administrador/funcionarios.php");
    exit;
}

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    $pdo->beginTransaction();

    /* ================= DATOS ================= */
    $Id_funcionario = $_POST['Id_funcionario'] ?? null;
    if (!$Id_funcionario) {
        throw new Exception("Funcionario no válido.");
    }

    $data = [
        'Nombre'      => $_POST['Nombre'] ?? null,
        'Apellidos'   => $_POST['Apellidos'] ?? null,
        'Dip_Pasaporte' => $_POST['Dip_Pasaporte'] ?? null,
        'Sexo'        => $_POST['Sexo'] ?? null,
        'Fecha_nacimiento' => $_POST['Fecha_nacimiento'] ?? null,
        'Lugar_nacimiento' => $_POST['Lugar_nacimiento'] ?? null,
        'Nacionalidad' => $_POST['Nacionalidad'] ?? null,
        'Telefono'    => $_POST['Telefono'] ?? null,
        'Correo'      => $_POST['Correo'] ?? null,
        'Domicilio'   => $_POST['Domicilio'] ?? null,
        'Num_carnet_fun' => $_POST['Num_carnet_fun'] ?? null,

        'Fecha_nombramiento' => $_POST['Fecha_nombramiento'] ?? null,
        'Fecha_posesion'     => $_POST['Fecha_posesion'] ?? null,

        'Id_seccion'  => $_POST['Id_seccion'] ?? null,
        'Funcion'     => $_POST['Funcion'] ?? null,
        'Id_categoria'=> $_POST['Id_categoria'] ?? null,

        'Profesion'   => $_POST['Profesion'] ?? null,
        'Maximo_nivel_estudios' => $_POST['Maximo_nivel_estudios'] ?? null,
        'Titulacion_academica'  => $_POST['Titulacion_academica'] ?? null,
        'Universidad_centro_formacion' => $_POST['Universidad_centro_formacion'] ?? null,
        'Fecha_graduacion' => $_POST['Fecha_graduacion'] ?? null,

        'Estado_Laboral' => $_POST['Estado_Laboral'] ?? 'Activo'
    ];

    /* ========= OBTENER ARCHIVOS ACTUALES ========= */
    $stmt = $pdo->prepare("SELECT * FROM funcionarios WHERE Id_funcionario = ?");
    $stmt->execute([$Id_funcionario]);
    $actual = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$actual) {
        throw new Exception("Funcionario no encontrado.");
    }

    /* ========= MANEJO DE ARCHIVOS ========= */
    $basePath = realpath(__DIR__ . '/../api/');
    $uploadPath = $basePath . '/funcionarios/';
    if (!is_dir($uploadPath)) mkdir($uploadPath, 0777, true);

    $fileFields = [
        'Foto',
        'Dip_pass_copia',
        'Copia_doc_nomb',
        'Copia_carnet_func',
        'Copia_doc_tom_posesion',
        'Copia_doc_academicos'
    ];

    foreach ($fileFields as $field) {
        if (!empty($_FILES[$field]['name'])) {
            $ext = pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION);
            $newName = uniqid($field . '_') . '.' . strtolower($ext);
            $destino = $uploadPath . $newName;

            if (!move_uploaded_file($_FILES[$field]['tmp_name'], $destino)) {
                throw new Exception("Error al subir $field");
            }

            // eliminar archivo anterior
            if (!empty($actual[$field])) {
                $old = $basePath . '/' . $actual[$field];
                if (file_exists($old)) unlink($old);
            }

            $data[$field] = 'funcionarios/' . $newName;
        } else {
            $data[$field] = $actual[$field]; // conservar
        }
    }

    /* ========= UPDATE ========= */
    $sql = "UPDATE funcionarios SET
        Nombre = :Nombre,
        Apellidos = :Apellidos,
        Dip_Pasaporte = :Dip_Pasaporte,
        Sexo = :Sexo,
        Fecha_nacimiento = :Fecha_nacimiento,
        Lugar_nacimiento = :Lugar_nacimiento,
        Nacionalidad = :Nacionalidad,
        Telefono = :Telefono,
        Correo = :Correo,
        Domicilio = :Domicilio,
        Num_carnet_fun = :Num_carnet_fun,

        Fecha_nombramiento = :Fecha_nombramiento,
        Fecha_posesion = :Fecha_posesion,

        Id_seccion = :Id_seccion,
        Funcion = :Funcion,
        Id_categoria = :Id_categoria,

        Profesion = :Profesion,
        Maximo_nivel_estudios = :Maximo_nivel_estudios,
        Titulacion_academica = :Titulacion_academica,
        Universidad_centro_formacion = :Universidad_centro_formacion,
        Fecha_graduacion = :Fecha_graduacion,

        Foto = :Foto,
        Dip_pass_copia = :Dip_pass_copia,
        Copia_doc_nomb = :Copia_doc_nomb,
        Copia_carnet_func = :Copia_carnet_func,
        Copia_doc_tom_posesion = :Copia_doc_tom_posesion,
        Copia_doc_academicos = :Copia_doc_academicos,

        Estado_Laboral = :Estado_Laboral
        WHERE Id_funcionario = :Id_funcionario";

    $data['Id_funcionario'] = $Id_funcionario;
    $stmt = $pdo->prepare($sql);
    $stmt->execute($data);

    /* ========= LOG ========= */
    $log = $pdo->prepare("
        INSERT INTO logs (Usuario_id, Tabla_afectada, Accion, Registro_id, IP, Dispositivo)
        VALUES (?, 'funcionarios', 'UPDATE', ?, ?, ?)
    ");
    $log->execute([
        $_SESSION['ID_Usuario'],
        $Id_funcionario,
        getIP(),
        getDispositivo()
    ]);

    $pdo->commit();
    $_SESSION['exito'] = "Funcionario actualizado correctamente.";

} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    $_SESSION['error'] = $e->getMessage();
}

header("Location: ../administrador/funcionarios.php");
exit;
