<?php
include 'php/configuracion.php';
include 'php/Aplicacion.php';

$aplicacion = new Aplicacion();
$usuario = $aplicacion->iniciar();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>ARGOS</title>
  <link rel="stylesheet" href="css/estilo.css">
</head>
<body>
  <h1>ARGOS</h1>
</body>
</html>
