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
$titulo = "Calibración";
$archivo = "configuracion_calibracion.php";
$modulo = "oea";   // oea | ctpat: define el menú lateral y el selector de la barra superior
$version = "20261127";
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
<body class="ar-app" data-pantalla="calibracion" <?php $menu->renderizarUsuarioJSON($usuario); ?>>

<?php $menu->renderizarBarraSuperior($usuario, true, $modulo); ?>

<div class="ar-layout">
  <aside class="ar-sidebar">
    <?php $menu->renderizar($usuario, $archivo, $modulo); ?>
  </aside>

  <main class="ar-main">

    <div class="ar-page-hd">
      <div>
        <div class="ar-crumb">ARGOS › Configuración</div>
        <h1>Calibración</h1>
        <p id="versionInfo">Cargando…</p>
      </div>
      <div class="ar-acciones">
        <select id="versionSelect" class="form-control" aria-label="Versión de la calibración"></select>
        <button type="button" class="btn btn-primary" id="editarBoton" hidden><i class="fa fa-pencil"></i> Editar</button>
      </div>
    </div>

    <section class="ar-main-contenido">
      <div class="ar-card" id="barraEdicion" hidden>
        <b>Borrador:</b> los cambios no se aplican hasta guardar una nueva versión. Un nivel menos grave no puede ser más grave por tener menos probabilidad ni más consecuencia (la pantalla lo valida al guardar).
        <div class="ar-acciones" style="margin-top:10px">
          <button type="button" class="btn btn-default" id="cancelarEdicionBoton">Cancelar</button>
          <button type="button" class="btn btn-default" id="impactoBoton"><i class="fa fa-search"></i> Ver impacto</button>
          <button type="button" class="btn btn-success" id="guardarVersionBoton"><i class="fa fa-save"></i> Guardar nueva versión</button>
        </div>
        <div class="ar-callout" id="impactoResultado" hidden></div>
      </div>
      <div class="ar-callout">La calibración es la misma para todos los clientes. Las evaluaciones ya hechas conservan la versión con la que se hicieron; las nuevas usan la vigente.</div>

      <div class="ar-grid ar-g2">
        <div class="ar-card">
          <h3>Probabilidad</h3>
          <div class="ar-sub">Con qué frecuencia se presenta el evento.</div>
          <table class="ar-tabla" id="tablaProbabilidad"><thead><tr><th>Clasificación</th><th>Descripción</th></tr></thead><tbody></tbody></table>
        </div>
        <div class="ar-card">
          <h3>Consecuencia</h3>
          <div class="ar-sub">Qué tan grave es el impacto si ocurre.</div>
          <table class="ar-tabla" id="tablaConsecuencia"><thead><tr><th>Clasificación</th><th>Descripción</th></tr></thead><tbody></tbody></table>
        </div>
      </div>

      <div class="ar-grid ar-g32">
        <div class="ar-card">
          <h3>Matriz de nivel de riesgo</h3>
          <div class="ar-sub">Probabilidad × consecuencia. Nivel 1 = más grave · 6 = menos grave.</div>
          <div class="ar-hm" id="matrizNivel"></div>
        </div>
        <div class="ar-card">
          <h3>Plazo máximo por nivel</h3>
          <div class="ar-sub">Para la fecha compromiso de las acciones.</div>
          <table class="ar-tabla" id="tablaPlazos"><thead><tr><th>Nivel</th><th>Plazo</th></tr></thead><tbody></tbody></table>
        </div>
      </div>
    </section>

    <?php $menu->renderizarFooter(); ?>
  </main>
</div>

<div class="modal fade" id="versionModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document"><div class="modal-content">
    <div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h4 class="modal-title">Guardar nueva versión</h4></div>
    <div class="modal-body">
      <div class="form-group"><label for="vigenteDesdeInput">Vigente desde (vacío = hoy)</label><input type="date" class="form-control" id="vigenteDesdeInput"></div>
      <div class="form-group"><label for="notaInput">Nota del cambio (obligatoria)</label><textarea class="form-control" id="notaInput" rows="3" maxlength="500"></textarea></div>
      <div class="ar-callout">Las evaluaciones ya hechas conservan su versión y su nivel; las nuevas usan esta versión desde la fecha indicada.</div>
    </div>
    <div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button><button type="button" class="btn btn-success" id="confirmarVersionBoton">Guardar versión</button></div>
  </div></div>
</div>

<!-- Ventana emergente de ejemplo: bloques con título, campos con ⓘ y botones de acción -->
<div class="modal fade" id="ejemploModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Ventana de ejemplo</h4>
      </div>
      <div class="modal-body">
        <div class="ar-bloque">
          <h4>Identificación</h4>
          <div class="form-group">
            <label for="ejemploSituacion">Situación <i class="fa fa-info-circle ar-info" data-toggle="tooltip" title="El hecho observado hoy."></i></label>
            <textarea id="ejemploSituacion" class="form-control" rows="2"></textarea>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary">Guardar</button>
      </div>
    </div>
  </div>
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
<script src="<?php echo getHandelAPI()?>/js/vistas/vista.js?v=20261028"></script>

<!-- Repositorio, Presentador y Vista de la pantalla (handel_api) -->
<script src="<?php echo getHandelAPI()?>/js/repositorios/argos_configuracion_repositorio.js?v=<?php echo $version; ?>"></script>
<script src="<?php echo getHandelAPI()?>/js/presentadores/argos_configuracion_presentador.js?v=<?php echo $version; ?>"></script>
<script src="<?php echo getHandelAPI()?>/js/vistas/argos_configuracion_vista.js?v=<?php echo $version; ?>"></script>
<script src="js/argos.js?v=<?php echo $version; ?>"></script>

<img src="img/cargando.svg" id="indicador" alt="Cargando">
</body>
</html>
