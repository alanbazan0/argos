<?php
use php\clases\Menu;

include 'php/configuracion.php';
include 'php/Aplicacion.php';
include 'php/clases/Menu.php';

$aplicacion = new Aplicacion();
$usuario = $aplicacion->iniciar();
// El Tablero ya está liberado para todos con permiso; el resto del menú solo lo ve quien puede ver el menú completo.
if(!$aplicacion->puedeVerRegistro($usuario))
{
    header('Location: panel.php');
    exit;
}
$menu = new Menu();
$menu->setHandelAPI(getHandelAPI());
$menu->setSoloArchivos($aplicacion->puedeVerMenu($usuario) ? null : Aplicacion::archivosLiberados());
$titulo = "Tablero";
$archivo = "tablero.php";
$modulo = "oea";   // oea | ctpat: define el menú lateral y el selector de la barra superior
$version = "20261195";
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
        <div class="ar-crumb">ARGOS › Análisis de riesgo OEA</div>
        <h1>Tablero</h1>
        <p>Panorama del análisis de riesgo. <span id="periodoTexto"></span></p>
      </div>
      <div class="ar-acciones"><div class="btn-group ar-exportar" data-exportar></div></div>
    </div>

    <section class="ar-main-contenido">
      <div class="ar-grid ar-g4 ar-grid-kpis" id="kpisTablero">
        <div class="ar-card ar-kpi"><div class="ar-n" id="kpiActivos">0</div><div class="ar-l">Riesgos activos</div><div class="ar-t" id="kpiActivosNota"></div></div>
        <div class="ar-card ar-kpi"><div class="ar-n" id="kpiNivel12" style="color:var(--ar-l1)">0</div><div class="ar-l">Activos en nivel 1–2</div><div class="ar-t" style="color:var(--ar-l1)">plazo máximo 3 meses</div></div>
        <div class="ar-card ar-kpi"><div class="ar-n" id="kpiVencidas" style="color:var(--ar-red)">0</div><div class="ar-l">Acciones vencidas</div><div class="ar-t" id="kpiVencidasNota"></div></div>
        <a class="ar-card ar-kpi ar-kpi-enlace" href="revisiones.php"><div class="ar-n" id="kpiRevision">—</div><div class="ar-l" id="kpiRevisionTexto">Revisión anual</div><div class="ar-t" id="kpiRevisionNota"></div></a>
      </div>

      <div class="ar-grid ar-g32">
        <div class="ar-card">
          <div class="ar-hm-cab">
            <div><h3>Mapa de calor <i class="fa fa-info-circle ar-info" data-toggle="tooltip" title="Cada celda cuenta los riesgos cuya evaluación vigente a la fecha del periodo cae en ese cruce. Sin filtro de sede se usa la sede más grave; con filtro, la de esa sede. Los riesgos sin evaluar no aparecen."></i></h3>
              <div class="ar-sub" style="margin-bottom:0"><b id="totalRiesgos">0</b> riesgos evaluados · <b id="totalSinEvaluar">0</b> sin evaluar. Clic en una celda para ver sus riesgos</div></div>
          </div>
          <form id="filtrosForm" class="ar-hm-filtros" onsubmit="return false;">
            <select class="form-control" id="revisionSelect" aria-label="Periodo"><option value="">Actual (hoy)</option></select>
            <select class="form-control" id="sedeFiltro" aria-label="Sede"></select>
            <label class="ar-chk-comparar"><input type="checkbox" id="compararCheck"> <span id="compararTexto">Comparar</span></label>
            <input type="hidden" id="alFechaInput"><input type="hidden" id="comparaConInput">
          </form>
          <div id="mapaCalor"></div>
          <div class="ar-hm-leyenda" id="mapaLeyenda"></div>
          <ul class="ar-lista" id="riesgosCelda"></ul>
        </div>
        <div>
          <div class="ar-card"><h3 id="cambiosTitulo">Cambios de nivel</h3><div class="ar-sub">Riesgos que subieron, bajaron o se dieron de alta</div><div id="cambios"></div></div>
          <div class="ar-card"><h3>Evolución por periodo</h3><div class="ar-sub">Riesgos por nivel en cada revisión aprobada y hoy</div><div class="ar-pbars" id="evolucionBarras"></div></div>
        </div>
      </div>

      <div class="ar-card">
        <div class="ar-barra-titulo"><div><h3>Acciones por responsable</h3><div class="ar-sub">Clic en una persona para ver su lista de acciones</div></div>
          <div class="ar-leyenda"><span><i style="background:var(--ar-red)"></i>Vencida</span><span><i style="background:#C9D3E3"></i>Pendiente</span><span><i style="background:#5B9BD5"></i>En proceso</span><span><i style="background:var(--ar-green-br)"></i>Completada</span></div></div>
        <div id="barrasResponsables"></div>
        <div class="ar-vacio" id="sinResponsables" hidden><i class="fa fa-inbox"></i>Aún no hay acciones.</div>
      </div>
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
<script src="<?php echo getHandelAPI()?>/js/repositorios/argos_base_repositorio.js?v=<?php echo $version; ?>"></script>
<script src="<?php echo getHandelAPI()?>/js/repositorios/argos_bandeja_repositorio.js?v=<?php echo $version; ?>"></script>
<script src="<?php echo getHandelAPI()?>/js/vistas/vista.js?v=20261110a"></script>

<!-- Repositorios, Presentador y Vista de la pantalla (handel_api) -->
<script src="<?php echo getHandelAPI()?>/js/repositorios/argos_riesgos_repositorio.js?v=<?php echo $version; ?>"></script>
<script src="<?php echo getHandelAPI()?>/js/repositorios/argos_acciones_repositorio.js?v=<?php echo $version; ?>"></script>
<script src="<?php echo getHandelAPI()?>/js/repositorios/argos_configuracion_repositorio.js?v=<?php echo $version; ?>"></script>
<script src="<?php echo getHandelAPI()?>/js/repositorios/argos_revisiones_repositorio.js?v=<?php echo $version; ?>"></script>
<script src="<?php echo getHandelAPI()?>/js/repositorios/argos_tablero_repositorio.js?v=<?php echo $version; ?>"></script>
<script src="<?php echo getHandelAPI()?>/js/presentadores/argos_tablero_presentador.js?v=<?php echo $version; ?>"></script>
<script src="<?php echo getHandelAPI()?>/js/componentes/argos_avatar.js?v=<?php echo $version; ?>"></script>
<script src="<?php echo getHandelAPI()?>/js/vistas/argos_tablero_vista.js?v=<?php echo $version; ?>"></script>
<script src="<?php echo getHandelAPI()?>/js/componentes/argos_exportacion.js?v=<?php echo $version; ?>"></script>
<script src="js/argos.js?v=<?php echo $version; ?>"></script>

<img src="img/cargando.svg" id="indicador" alt="Cargando">
</body>
</html>
