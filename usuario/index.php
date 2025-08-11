<?php
if(session_status() == PHP_SESSION_NONE) session_start();
echo 'Bienvenido '.$_SESSION['nombre_usuario'];