<?php
use php\clases\Menu;

include 'php/configuracion.php';
include 'php/Aplicacion.php';
include 'php/clases/Menu.php';

$aplicacion = new Aplicacion();
$usuario = $aplicacion->iniciar();
// Acciones y Mi bandeja ya están liberadas para todos con permiso; el resto del menú solo lo ve quien puede ver el menú completo.
if(!$aplicacion->puedeVerRegistro($usuario))
{
    header('Location: panel.php');
    exit;
}
$menu = new Menu();
$menu->setHandelAPI(getHandelAPI());
$menu->setSoloArchivos($aplicacion->puedeVerMenu($usuario) ? null : Aplicacion::archivosLiberados());
$titulo = "Mi bandeja";
$nombresTipo = array(1 => 'Administrador', 2 => 'Coordinador', 5 => 'Supervisor');
$saludoRol = isset($nombresTipo[(int)$usuario->tipoUsuarioId]) ? $nombresTipo[(int)$usuario->tipoUsuarioId] : 'Usuario';
$saludoAlcance = in_array((int)$usuario->tipoUsuarioId, array(1, 2), true) ? 'todas las sedes' : 'tus sedes';   // Supervisor/Usuario: el JS pone los nombres
$archivo = "bandeja.php";
$modulo = "oea";   // oea | ctpat: define el menú lateral y el selector de la barra superior
$version = "20261178";
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
        <div class="ar-crumb">ARGOS › OEA México</div>
        <h1>Hola, <?php echo htmlspecialchars(trim(explode(' ', trim($usuario->nombre))[0]), ENT_QUOTES, 'UTF-8'); ?> 👋</h1>
        <p id="totalBandeja"><?php echo htmlspecialchars($saludoRol, ENT_QUOTES, 'UTF-8'); ?> · <span id="alcanceBandeja"><?php echo htmlspecialchars($saludoAlcance, ENT_QUOTES, 'UTF-8'); ?></span> · esto es lo que necesita tu atención hoy</p>
      </div>
    </div>

    <section class="ar-main-contenido">
      <div class="ar-callout" id="avisoSinEvaluar" hidden></div>
      <div class="ar-grid ar-g4 ar-grid-kpis">
        <div class="ar-card ar-kpi"><div class="ar-n" id="contVencidas" style="color:var(--ar-red)">0</div><div class="ar-l">Acciones vencidas</div></div>
        <div class="ar-card ar-kpi"><div class="ar-n" id="contVencen" style="color:var(--ar-orange-dk)">0</div><div class="ar-l">Vencen en los próximos 30 días</div></div>
        <div class="ar-card ar-kpi"><div class="ar-n" id="contSinActualizar" style="color:#6B4E9B">0</div><div class="ar-l">Sin actualizar en más de 30 días</div></div>
        <div class="ar-card ar-kpi"><div class="ar-n" id="contRequieren">0</div><div class="ar-l">Requieren tu respuesta</div></div>
      </div>

      <div class="ar-card ar-mi-cumplimiento" id="miCumplimiento" hidden></div>

      <div class="ar-card">
        <div class="ar-mc-cab">
          <div><h3>Acciones por atender</h3><div class="ar-sub">Ordenadas por urgencia: vencidas, vencen pronto, sin actualizar y en tiempo.</div></div>
          <a href="acciones.php" class="ar-mc-enlace">Ver todas las acciones →</a>
        </div>
        <div class="ar-tabla-scroll ar-tabla-pendientes">
          <table class="ar-tabla" id="tablaPendientes">
            <thead><tr><th>Urgencia</th><th>Riesgo</th><th>Acción / Riesgo</th><th>Fecha compromiso</th><th>Estado</th><th></th></tr></thead>
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
      <div class="ar-card"><h3>Riesgos por evaluar en tus sedes</h3><div class="ar-sub">Identificados pero todavía sin probabilidad ni consecuencia.</div><ul class="ar-lista" id="sinEvaluar"></ul><a class="btn btn-default btn-sm" id="irTallerBoton" href="taller.php?fase=2" hidden><i class="fa fa-pencil-square-o"></i> Evaluarlos en Modo taller</a></div>
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
<script src="<?php echo getHandelAPI()?>/js/vistas/vista.js?v=20261110a"></script>

<!-- Repositorio, Presentador y Vista de la pantalla (handel_api) -->
<script src="<?php echo getHandelAPI()?>/js/repositorios/argos_base_repositorio.js?v=<?php echo $version; ?>"></script>
<script src="<?php echo getHandelAPI()?>/js/repositorios/argos_indicadores_repositorio.js?v=<?php echo $version; ?>"></script>
<script src="<?php echo getHandelAPI()?>/js/repositorios/argos_bandeja_repositorio.js?v=<?php echo $version; ?>"></script>
<script src="<?php echo getHandelAPI()?>/js/presentadores/argos_bandeja_presentador.js?v=<?php echo $version; ?>"></script>
<script src="<?php echo getHandelAPI()?>/js/vistas/argos_bandeja_vista.js?v=<?php echo $version; ?>"></script>
<script src="js/argos.js?v=<?php echo $version; ?>"></script>

<img src="img/cargando.svg" id="indicador" alt="Cargando">
</body>
</html>
