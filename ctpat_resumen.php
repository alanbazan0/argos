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
$titulo = "Resumen CTPAT";
$archivo = "ctpat_resumen.php";
$modulo = "ctpat";   // oea | ctpat: define el menú lateral y el selector de la barra superior
$version = "20261181";
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
        <div class="ar-crumb">ARGOS › CTPAT</div>
        <h1>Resumen CTPAT</h1>
        <p>Análisis de riesgo de la cadena de suministro con la guía de 5 pasos de CBP.</p>
      </div>
      <div class="ar-acciones">
        <select id="ejercicioSelect" class="form-control" aria-label="Ejercicio"></select>
        <button type="button" class="btn btn-primary" id="nuevoEjercicioBoton" hidden><i class="fa fa-plus"></i> Nuevo ejercicio</button>
        <button type="button" class="btn btn-default" id="cerrarEjercicioBoton" hidden><i class="fa fa-lock"></i> Cerrar ejercicio</button>
      </div>
    </div>

    <section class="ar-main-contenido">
      <div class="ar-card ar-vacio" id="sinEjercicio" hidden><i class="fa fa-folder-open-o"></i>Aún no hay un ejercicio CTPAT. Inicia el de este año para empezar con el mapeo de socios y cadenas.</div>
      <div id="pasosBloque" hidden>
        <div class="ar-card"><div id="ejercicioEstado"></div></div>
        <div class="ar-pasos" id="pasos"></div>
        <div class="ar-card"><h3>Bitácora del ejercicio</h3><div class="ar-sub">Todo cambio de estatus, fecha, calificación o configuración queda con usuario y fecha.</div><ul class="ar-lista" id="bitacoraLista"></ul></div>
      </div>
    </section>

    <div class="modal fade" id="nuevoModal" tabindex="-1" role="dialog"><div class="modal-dialog" role="document"><div class="modal-content">
      <div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h4 class="modal-title">Nuevo ejercicio</h4></div>
      <div class="modal-body">
        <div class="form-group"><label for="anioInput">Año</label><input type="number" class="form-control" id="anioInput" min="2000" max="2100"></div>
        <div class="form-group"><label for="nombreInput">Nombre (opcional)</label><input type="text" class="form-control" id="nombreInput" maxlength="150" placeholder="Análisis de riesgo CTPAT 2026"></div>
        <div class="form-group"><label for="copiarDeSelect">Punto de partida</label><select class="form-control" id="copiarDeSelect"></select></div>
        <div class="ar-callout">Una copia trae las cadenas con sus etapas y las acciones que no se verificaron (marcadas «viene de» el año anterior). Los cuestionarios se envían de nuevo al iniciar el seguimiento.</div>
      </div>
      <div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button><button type="button" class="btn btn-success" id="confirmarNuevoBoton">Iniciar ejercicio</button></div>
    </div></div></div>

    <div class="modal fade" id="cierreModal" tabindex="-1" role="dialog"><div class="modal-dialog" role="document"><div class="modal-content">
      <div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h4 class="modal-title">Cerrar ejercicio</h4></div>
      <div class="modal-body">
        <p id="pendientesTexto"></p>
        <div class="form-group" id="motivoCierreGrupo"><label for="motivoCierreInput">Motivo del cierre</label><textarea class="form-control" id="motivoCierreInput" rows="3" maxlength="500"></textarea></div>
        <div class="ar-callout">Al cerrar se guarda una fotografía de todo el ejercicio y queda de solo consulta: se pueden ver y exportar sus documentos, pero no se modifica nada.</div>
      </div>
      <div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button><button type="button" class="btn btn-danger" id="confirmarCierreBoton">Cerrar ejercicio</button></div>
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
<script src="<?php echo getHandelAPI()?>/js/presentadores/ctpat_resumen_presentador.js?v=<?php echo $version; ?>"></script>
<script src="<?php echo getHandelAPI()?>/js/vistas/ctpat_resumen_vista.js?v=<?php echo $version; ?>"></script>
<script src="js/argos.js?v=<?php echo $version; ?>"></script>

<img src="img/cargando.svg" id="indicador" alt="Cargando">
</body>
</html>
