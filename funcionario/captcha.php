<?php
session_start();

$width = 120;
$height = 40;

$image = imagecreate($width, $height);
$bgColor = imagecolorallocate($image, 255, 255, 255);
$txtColor = imagecolorallocate($image, 0, 0, 0);

// Generar código aleatorio
$codigo = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890'), 0, 5);
$_SESSION['captcha'] = $codigo;

// Fondo y texto
imagefill($image, 0, 0, $bgColor);
imagestring($image, 5, 15, 10, $codigo, $txtColor);

// Salida
header('Content-Type: image/png');
imagepng($image);
imagedestroy($image);
