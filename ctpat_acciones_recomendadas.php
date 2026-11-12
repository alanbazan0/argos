<?php
use php\clases\Menu;

include 'php/configuracion.php';
include 'php/Aplicacion.php';
include 'php/clases/Menu.php';

$aplicacion = new Aplicacion();
$usuario = $aplicacion->iniciar();
// Liberada para los Administradores; el resto de CTPAT sigue en desarrollo (puedeVerMenu).
if(!$aplicacion->puedeVerMenu($usuario) && (int)$usuario->tipoUsuarioId !== 1)
{
    header('Location: panel.php');
    exit;
}
$menu = new Menu();
$menu->setHandelAPI(getHandelAPI());
$menu->setSoloArchivos($aplicacion->puedeVerMenu($usuario) ? null : Aplicacion::archivosLiberados());
$titulo = "Acciones recomendadas";
$archivo = "ctpat_acciones_recomendadas.php";
$modulo = "ctpat";   // oea | ctpat: define el menú lateral y el selector de la barra superior
$version = "20261199";
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
<body class="ar-app" data-pantalla="acciones" <?php $menu->renderizarUsuarioJSON($usuario); ?>>

<?php $menu->renderizarBarraSuperior($usuario, true, $modulo); ?>

<div class="ar-layout">
  <aside class="ar-sidebar">
    <?php $menu->renderizar($usuario, $archivo, $modulo); ?>
  </aside>

  <main class="ar-main">

    <div class="ar-page-hd"><div><div class="ar-crumb">ARGOS › CTPAT › Configuración</div><h1>Catálogo de acciones recomendadas</h1><p>Acción correctiva y evidencia esperada para cada pregunta de los cuestionarios · el plan de acción (paso 4) las propone automáticamente</p></div></div>

    <section class="ar-main-contenido">
      <form class="ar-filtros" onsubmit="return false;">
        <select id="accionesCuestionario" class="form-control" aria-label="Tipo de socio" style="width:auto"></select>
        <input id="accionesBuscar" class="form-control" placeholder="Buscar en preguntas o acciones…" style="max-width:340px">
        <span class="ar-situacion" id="accionesTotal"></span>
      </form>
      <div id="accionesLista"></div>
    </section>

    <div class="modal fade" id="accionModal" tabindex="-1" role="dialog"><div class="modal-dialog modal-lg" role="document"><div class="modal-content">
      <div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h4 class="modal-title"><i class="fa fa-pencil"></i> Acción recomendada</h4></div>
      <div class="modal-body">
        <div class="ar-callout" style="margin:0 0 12px" id="accionPregunta"></div>
        <div class="row">
          <div class="col-sm-6 form-group"><label for="accionEs">Acción recomendada (ES)</label><textarea id="accionEs" class="form-control" rows="3" maxlength="2000" placeholder="Ej. Elaborar una declaración de apoyo a la seguridad firmada por un alto directivo"></textarea></div>
          <div class="col-sm-6 form-group"><label for="accionEn">Recommended action (EN)</label><textarea id="accionEn" class="form-control" rows="3" maxlength="2000" placeholder="Ej. Draft a statement of support for supply chain security signed by a senior executive"></textarea></div>
        </div>
        <div class="row">
          <div class="col-sm-6 form-group"><label for="evidenciaEs">Evidencia esperada (ES)</label><input id="evidenciaEs" class="form-control" maxlength="1000" placeholder="Ej. Declaración firmada y fechada"></div>
          <div class="col-sm-6 form-group"><label for="evidenciaEn">Expected evidence (EN)</label><input id="evidenciaEn" class="form-control" maxlength="1000" placeholder="Ej. Signed and dated statement"></div>
        </div>
        <div class="text-danger" id="accionError"></div>
      </div>
      <div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button><button type="button" class="btn btn-success" id="guardarAccionBoton">Guardar</button></div>
    </div></div></div>

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
<script src="<?php echo getHandelAPI()?>/js/repositorios/ctpat_ejercicios_repositorio.js?v=<?php echo $version; ?>"></script>
<script src="<?php echo getHandelAPI()?>/js/repositorios/ctpat_configuracion_repositorio.js?v=<?php echo $version; ?>"></script>
<script src="<?php echo getHandelAPI()?>/js/presentadores/ctpat_configuracion_presentador.js?v=<?php echo $version; ?>"></script>
<script src="<?php echo getHandelAPI()?>/js/componentes/argos_historial.js?v=<?php echo $version; ?>"></script>
<script src="<?php echo getHandelAPI()?>/js/vistas/ctpat_configuracion_vista.js?v=<?php echo $version; ?>"></script>
<script src="js/argos.js?v=<?php echo $version; ?>"></script>

<img src="img/cargando.svg" id="indicador" alt="Cargando">
</body>
</html>
