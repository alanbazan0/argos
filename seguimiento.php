<?php
// Enlace único del socio comercial (CTPAT): sin inicio de sesión. La credencial es el token del enlace.
include 'php/configuracion.php';
$version = "20261208";
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="robots" content="noindex">
  <link rel="shortcut icon" href="img/favicon.png">
  <title>ARGOS · CTPAT</title>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&family=Outfit:wght@500;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?php echo getHandelAPI()?>/lib/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="css/argos.css?v=<?php echo $version; ?>">
</head>
<body class="ar-app ar-publico">
<main class="ar-publico-caja">
  <div class="ar-publico-hd"><img src="img/argos-logo.png" alt="ARGOS" height="38" onerror="this.style.display='none'"><div id="idiomaBloque"><button type="button" class="btn btn-default btn-xs" data-idioma="es">ES</button> <button type="button" class="btn btn-default btn-xs" data-idioma="en">EN</button></div></div>
  <h1 id="titulo"></h1>
  <p id="intro"></p>
  <div id="mensaje"></div>
  <div id="acciones"></div>
</main>
<script src="<?php echo getHandelAPI()?>/lib/jquery-3.3.1/jquery.min.js"></script>
<script src="<?php echo getHandelAPI()?>/js/clases/configuracion.js"></script>
<script src="js/seguimiento.js?v=<?php echo $version; ?>"></script>
</body>
</html>
