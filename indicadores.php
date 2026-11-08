<?php
use php\clases\Menu;

include 'php/configuracion.php';
include 'php/Aplicacion.php';
include 'php/clases/Menu.php';

$aplicacion = new Aplicacion();
$usuario = $aplicacion->iniciar();
// OEA está liberado para todos los usuarios con permiso de ARGOS.
if(!$aplicacion->puedeVerRegistro($usuario))
{
    header('Location: panel.php');
    exit;
}
$menu = new Menu();
$menu->setHandelAPI(getHandelAPI());
$menu->setSoloArchivos($aplicacion->puedeVerMenu($usuario) ? null : Aplicacion::archivosLiberados());
$titulo = "Indicadores";
$archivo = "indicadores.php";
$modulo = "oea";   // oea | ctpat: define el menú lateral y el selector de la barra superior
$version = "20261176";
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
        <h1>Indicadores</h1>
        <p>Cumplimiento del plan de acción y exposición al riesgo. <span id="periodoTexto"></span></p>
      </div>
      <div><button type="button" class="btn btn-default" id="guardarPeriodoBoton"><i class="fa fa-save"></i> Guardar periodo</button></div>
    </div>

    <section class="ar-main-contenido">
      <div class="ar-grid ar-g4 ar-grid-kpis" id="kpisIndicadores">
        <div class="ar-card ar-kpi"><div class="ar-n" id="kpiGeneral">—</div><div class="ar-l">Cumplimiento general</div><div class="ar-t" id="kpiGeneralNota"></div></div>
        <div class="ar-card ar-kpi"><div class="ar-n" id="kpiSede">—</div><div class="ar-l">Sede con menor cumplimiento</div><div class="ar-t" id="kpiSedeNota"></div></div>
        <div class="ar-card ar-kpi"><div class="ar-n" id="kpiUsuario">—</div><div class="ar-l">Usuario con menor cumplimiento</div><div class="ar-t" id="kpiUsuarioNota"></div></div>
        <div class="ar-card ar-kpi"><div class="ar-n" id="kpiRojas">0</div><div class="ar-l">Sedes en rojo</div><div class="ar-t" id="kpiRojasNota"></div></div>
      </div>

      <div class="ar-card">
        <form id="filtrosForm" class="ar-filtros" onsubmit="return false;">
          <select class="form-control" id="sedeFiltro" aria-label="Sede"></select>
          <label>Periodo <input type="month" class="form-control" id="periodoInput"></label>
          <span class="text-muted" id="cortesTexto"></span>
        </form>
      </div>

      <div class="ar-grid ar-g32">
        <div>
          <div class="ar-card"><h3>Cumplimiento por sede <i class="fa fa-info-circle ar-info" data-toggle="tooltip" title="60 % avance de las acciones (ponderado por el nivel del riesgo) + 40 % exposición (100 menos 10 por riesgo de nivel 1-2 y 3 por riesgo de nivel 3-4)."></i></h3>
            <table class="ar-tabla" id="tablaSedes"><thead><tr><th>Sede</th><th>Riesgos activos</th><th>Acciones</th><th>Avance</th><th>Exposición</th><th>Cumplimiento</th></tr></thead><tbody></tbody></table>
            <div class="ar-vacio" id="sinSedes" hidden><i class="fa fa-inbox"></i>Sin datos para este periodo.</div>
          </div>
          <div class="ar-card"><h3>Ranking de usuarios <i class="fa fa-info-circle ar-info" data-toggle="tooltip" title="Avance de las acciones donde el usuario es responsable. Solo aparece quien tiene al menos una acción; los empates comparten lugar."></i></h3>
            <table class="ar-tabla" id="tablaUsuarios"><thead><tr><th>#</th><th>Usuario</th><th>Acciones</th><th>Vencidas</th><th>Cumplimiento</th></tr></thead><tbody></tbody></table>
            <div class="ar-vacio" id="sinUsuarios" hidden><i class="fa fa-inbox"></i>Aún no hay acciones asignadas.</div>
          </div>
        </div>
        <div class="ar-card"><h3>Tendencia mensual</h3><div class="ar-sub">Promedio de las sedes en cada periodo guardado.</div><div id="tendencia"></div></div>
      </div>

      <div class="ar-card" id="comoSeCalcula">
        <h3>¿Cómo se calcula?</h3>
        <div class="ar-sub">Los pesos son parámetros configurables en Configuración › Parámetros.</div>
        <div class="ar-grid ar-g2">
          <div class="ar-explicacion" id="explicacionCalculo"></div>
          <table class="ar-tabla" id="tablaPesos"><thead><tr><th>Nivel</th><th>Peso de la acción</th><th>Resta a exposición</th></tr></thead><tbody></tbody></table>
        </div>
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
<script src="<?php echo getHandelAPI()?>/js/repositorios/argos_indicadores_repositorio.js?v=<?php echo $version; ?>"></script>
<script src="<?php echo getHandelAPI()?>/js/presentadores/argos_indicadores_presentador.js?v=<?php echo $version; ?>"></script>
<script src="<?php echo getHandelAPI()?>/js/vistas/argos_indicadores_vista.js?v=<?php echo $version; ?>"></script>
<script src="js/argos.js?v=<?php echo $version; ?>"></script>

<img src="img/cargando.svg" id="indicador" alt="Cargando">
</body>
</html>
