<?php
use php\clases\Menu;

include 'php/configuracion.php';
include 'php/Aplicacion.php';
include 'php/clases/Menu.php';

$aplicacion = new Aplicacion();
$usuario = $aplicacion->iniciar();
// Configuración ya está liberada para todos con permiso; el resto del menú solo lo ve quien puede ver el menú completo.
if(!$aplicacion->puedeVerConfiguracion($usuario) || (int)$usuario->tipoUsuarioId !== 1)
{
    header('Location: panel.php');
    exit;
}
$menuCompleto = $aplicacion->puedeVerMenu($usuario);
$menu = new Menu();
$menu->setHandelAPI(getHandelAPI());
$menu->setSoloArchivos($menuCompleto ? null : Aplicacion::archivosLiberados());
$titulo = "Plantillas de correo";
$archivo = "configuracion_calibracion.php";   // la opción del menú es una sola: Calibración y catálogos
$pestana = "plantillas";
$modulo = "oea";   // oea | ctpat: define el menú lateral y el selector de la barra superior
$version = "20261205";
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
<body class="ar-app" data-pantalla="plantillas" <?php $menu->renderizarUsuarioJSON($usuario); ?>>

<?php $menu->renderizarBarraSuperior($usuario, true, $modulo); ?>

<div class="ar-layout">
  <aside class="ar-sidebar">
    <?php $menu->renderizar($usuario, $archivo, $modulo); ?>
  </aside>

  <main class="ar-main">

    <div class="ar-page-hd">
      <div>
        <div class="ar-crumb">ARGOS › Configuración</div>
        <h1>Calibración y catálogos</h1>
        <p>Valores globales de la metodología, administrados por Händel.</p>
      </div>
    </div>

    <ul class="nav nav-tabs ar-tabs">
      <li<?php echo $pestana == 'calibracion' ? ' class="active"' : ''; ?>><a href="configuracion_calibracion.php">Calibración</a></li>
      <li<?php echo $pestana == 'estandares' ? ' class="active"' : ''; ?>><a href="configuracion_estandares.php">Estándares y subestándares</a></li>
<?php if((int)$usuario->tipoUsuarioId === 1) { ?>
      <li<?php echo $pestana == 'parametros' ? ' class="active"' : ''; ?>><a href="configuracion_parametros.php">Parámetros</a></li>
      <li<?php echo $pestana == 'plantillas' ? ' class="active"' : ''; ?>><a href="configuracion_plantillas.php">✉️ Plantillas de correo</a></li>
<?php } ?>
    </ul>

    <section class="ar-main-contenido">
      <div class="ar-amz-hd"><p class="ar-sub" style="margin:0">Los avisos que ARGOS manda por correo. El Administrador Händel edita los textos y puede desactivar cualquiera; la acción en ARGOS ocurre igual aunque el correo esté inactivo.</p><button type="button" class="btn btn-default" id="remitenteBoton">⚙ Remitente y pie</button></div>
      <div class="ar-callout" style="margin:12px 0">Cada aviso de «por vencer» y «vencida» se envía <b>una sola vez</b>; después el pendiente solo aparece en el resumen del lunes. Si a una persona le toca el mismo aviso por dos papeles (por ejemplo responsable y Coordinador), recibe <b>un solo correo</b>.</div>
      <div id="plantillasLista"></div>
    </section>
    <?php $menu->renderizarFooter(); ?>
  </main>
</div>


<div class="modal fade" id="correoModal" tabindex="-1" role="dialog"><div class="modal-dialog modal-lg" role="document"><div class="modal-content"></div></div></div>

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

<!-- Repositorio, Presentador y Vista de la pantalla (handel_api) -->
<script src="<?php echo getHandelAPI()?>/js/repositorios/argos_configuracion_repositorio.js?v=<?php echo $version; ?>"></script>
<script src="<?php echo getHandelAPI()?>/js/presentadores/argos_configuracion_presentador.js?v=<?php echo $version; ?>"></script>
<script src="<?php echo getHandelAPI()?>/js/vistas/argos_configuracion_vista.js?v=<?php echo $version; ?>"></script>
<script src="js/argos.js?v=<?php echo $version; ?>"></script>

<img src="img/cargando.svg" id="indicador" alt="Cargando">

<div class="modal fade" id="historialModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document"><div class="modal-content">
    <div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h4 class="modal-title" id="historialTitulo"></h4></div>
    <div class="modal-body"><ul class="ar-lista" id="historialLista"></ul></div>
    <div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button></div>
  </div></div>
</div>
</body>
</html>
