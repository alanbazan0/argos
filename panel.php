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
  <title>ARGOS - Panel de control</title>
  <link rel="shortcut icon" href="img/favicon.png">
  <link rel="stylesheet" href="css/estilo.css">
</head>
<body>
  <h1>Panel de control</h1>
  <p>Sesión iniciada como <?php echo htmlspecialchars($usuario->nombre . ' ' . $usuario->apellido, ENT_QUOTES); ?> (<?php echo htmlspecialchars($usuario->nombreUsuario, ENT_QUOTES); ?>).</p>
</body>
</html>
