<?php
use php\clases\Menu;

include 'php/configuracion.php';
include 'php/Aplicacion.php';
include 'php/clases/Menu.php';

$aplicacion = new Aplicacion();
$usuario = $aplicacion->iniciar();
// Mientras el menú esté en desarrollo, esta pantalla solo la ve quien puede ver el menú.
if(!$aplicacion->puedeVerMenu($usuario))
{
    header('Location: panel.php');
    exit;
}
$menu = new Menu();
$menu->setHandelAPI(getHandelAPI());
$titulo = "Mi bandeja";
$archivo = "bandeja.php";
$modulo = "oea";   // oea | ctpat: define el menú lateral y el selector de la barra superior
$version = "20261133";
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta http-equiv="Expires" content="0">
  <meta http-equiv="Cache-Control" content="no-cache, must-revalidate">
  <meta http-equiv="Pragma" content="no-cache">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="shortcut icon" href="img/favicon.png">
  <title>ARGOS - <?php echo $titulo; ?></title>

  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&family=Outfit:wght@500;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?php echo getHandelAPI()?>/lib/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo getHandelAPI()?>/lib/font-awesome-4.7.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="<?php echo getHandelAPI()?>/lib/DataTables/datatables.min.css" media="all">
  <link rel="stylesheet" href="<?php echo getHandelAPI()?>/lib/sweetalert/sweetalert.css">
  <link rel="stylesheet" href="<?php echo getHandelAPI()?>/lib/toastr/toastr.min.css">
  <link rel="stylesheet" href="css/argos.css?v=<?php echo $version; ?>">
</head>
<body class="ar-app" <?php $menu->renderizarUsuarioJSON($usuario); ?>>

<?php $menu->renderizarBarraSuperior($usuario, true, $modulo); ?>

<div class="ar-layout">
  <aside class="ar-sidebar">
    <?php $menu->renderizar($usuario, $archivo, $modulo); ?>
  </aside>

  <main class="ar-main">

    <div class="ar-page-hd">
      <div>
        <div class="ar-crumb">ARGOS › Análisis OEA</div>
        <h1>Mi bandeja</h1>
        <p id="totalBandeja">Cargando…</p>
      </div>
    </div>

    <section class="ar-main-contenido">
      <div class="ar-grid ar-g4">
        <div class="ar-card ar-kpi"><div class="ar-n" id="contVencidas">0</div><div class="ar-l">Acciones vencidas</div></div>
        <div class="ar-card ar-kpi"><div class="ar-n" id="contVencen">0</div><div class="ar-l">Vencen en 30 días</div></div>
        <div class="ar-card ar-kpi"><div class="ar-n" id="contSinActualizar">0</div><div class="ar-l">Sin actualizar (más de 30 días)</div></div>
        <div class="ar-card ar-kpi"><div class="ar-n" id="contRequieren">0</div><div class="ar-l">Requieren tu respuesta</div></div>
      </div>

      <div class="ar-card">
        <h3>Acciones por atender</h3>
        <div class="ar-sub">Ordenadas por urgencia: vencidas, vencen pronto, sin actualizar y en tiempo.</div>
        <div class="ar-tabla-scroll">
          <table class="ar-tabla" id="tablaPendientes">
            <thead><tr><th>Urgencia</th><th>Riesgo</th><th>Acción</th><th>Fecha compromiso</th><th>Estado</th><th></th></tr></thead>
            <tbody></tbody>
          </table>
        </div>
        <div class="ar-vacio" id="sinPendientes" hidden><i class="fa fa-check-circle"></i>No tienes acciones pendientes.</div>
      </div>

      <div class="ar-grid ar-g2">
        <div class="ar-card"><h3>Evidencia por validar</h3><div class="ar-sub">Acciones que otra persona marcó como completadas.</div><ul class="ar-lista" id="porValidar"></ul></div>
        <div class="ar-card"><h3>Menciones</h3><div class="ar-sub">Te mencionaron en la bitácora con @.</div><ul class="ar-lista" id="menciones"></ul></div>
      </div>
      <div class="ar-card"><h3>Revisión anual</h3><div class="ar-sub">Aviso 30 días antes del vencimiento (Administrador y Coordinador).</div><ul class="ar-lista" id="revisionAnual"></ul></div>
      <div class="ar-card"><h3>Riesgos por evaluar en tus sedes</h3><div class="ar-sub">Identificados pero todavía sin probabilidad ni consecuencia.</div><ul class="ar-lista" id="sinEvaluar"></ul></div>
    </section>

    <?php $menu->renderizarFooter(); ?>
  </main>
</div>

<!-- Librerías comunes (handel_api) -->
<script src="<?php echo getHandelAPI()?>/lib/jquery-3.3.1/jquery.min.js"></script>
<script src="<?php echo getHandelAPI()?>/lib/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="<?php echo getHandelAPI()?>/lib/moment/moment-with-locales.js"></script>
<script src="<?php echo getHandelAPI()?>/lib/sweetalert/sweetalert.min.js"></script>
<script src="<?php echo getHandelAPI()?>/lib/toastr/toastr.min.js"></script>
<script src="<?php echo getHandelAPI()?>/lib/form-validation/jquery.validate.min.js"></script>
<script src="<?php echo getHandelAPI()?>/lib/DataTables/datatables.min.js"></script>

<!-- Clases base de handel_api -->
<script src="<?php echo getHandelAPI()?>/js/librerias/AjaxContextHandler.js"></script>
<script src="<?php echo getHandelAPI()?>/js/librerias/Ajaxv2.js"></script>
<script src="<?php echo getHandelAPI()?>/js/clases/configuracion.js"></script>
<script src="<?php echo getHandelAPI()?>/js/clases/modo.js"></script>
<script src="<?php echo getHandelAPI()?>/js/clases/tipo_usuario.js"></script>
<script src="<?php echo getHandelAPI()?>/js/componentes/tabla.js"></script>
<script src="<?php echo getHandelAPI()?>/js/repositorios/repositorio.js"></script>
<script src="<?php echo getHandelAPI()?>/js/repositorios/usuarios_repositorio.js"></script>
<script src="<?php echo getHandelAPI()?>/js/vistas/vista.js?v=20261028"></script>

<!-- Repositorio, Presentador y Vista de la pantalla (handel_api) -->
<script src="<?php echo getHandelAPI()?>/js/repositorios/argos_base_repositorio.js?v=<?php echo $version; ?>"></script>
<script src="<?php echo getHandelAPI()?>/js/repositorios/argos_bandeja_repositorio.js?v=<?php echo $version; ?>"></script>
<script src="<?php echo getHandelAPI()?>/js/presentadores/argos_bandeja_presentador.js?v=<?php echo $version; ?>"></script>
<script src="<?php echo getHandelAPI()?>/js/vistas/argos_bandeja_vista.js?v=<?php echo $version; ?>"></script>
<script src="js/argos.js?v=<?php echo $version; ?>"></script>

<img src="img/cargando.svg" id="indicador" alt="Cargando">
</body>
</html>
