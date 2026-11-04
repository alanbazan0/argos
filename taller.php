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
$titulo = "Modo taller";
$archivo = "taller.php";
$modulo = "oea";   // oea | ctpat: define el menú lateral y el selector de la barra superior
$version = "20261118";
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
        <h1>Modo taller</h1>
        <p>Captura como en el taller: primero identifica los riesgos en la tabla; después evalúalos uno por uno.</p>
      </div>
    </div>

    <section class="ar-main-contenido">
      <div class="ar-card" id="sinPermiso" hidden><div class="ar-vacio"><i class="fa fa-lock"></i>No tienes sedes asignadas para dar de alta riesgos.</div></div>
      <ul class="nav nav-tabs ar-tabs" id="faseTabs">
        <li class="active"><a href="#" data-fase="1">Fase 1 · Identificar</a></li>
        <li><a href="#" data-fase="2">Fase 2 · Evaluar <span class="ar-chip" id="contFase2">0</span></a></li>
      </ul>

      <div id="faseUno" class="ar-card">
        <div class="ar-sub">Solo identificación. Los riesgos entran <b>Sin evaluar</b> y no aparecen en el mapa de calor hasta evaluarse. Puedes pegar renglones copiados de Excel en situación, ¿por qué?, riesgo u origen (columnas separadas por tabulador).</div>
        <div class="ar-hoja"><table class="ar-tabla" id="hojaTaller">
          <thead><tr><th>#</th><th>Subestándar</th><th>Departamento</th><th>Sedes</th><th>Situación</th><th>¿Por qué es un riesgo?</th><th>Riesgo</th><th>Fuente</th><th>¿Quién lo origina?</th><th>Estado</th><th></th></tr></thead>
          <tbody></tbody>
        </table></div>
        <div class="ar-acciones" style="margin-top:12px">
          <button type="button" class="btn btn-default" id="agregarFilasBoton"><i class="fa fa-plus"></i> Agregar 5 renglones</button>
          <button type="button" class="btn btn-success" id="guardarLoteBoton"><i class="fa fa-save"></i> Guardar renglones</button>
        </div>
      </div>

      <div id="faseDos" hidden>
        <div class="ar-card ar-vacio" id="faseDosVacio" hidden></div>
        <div id="faseDosTarjeta" class="ar-grid ar-g32">
          <div class="ar-card ar-proyeccion">
            <div class="ar-sub" id="avanceFase2"></div>
            <h2 id="proyItem"></h2>
            <p class="ar-situacion" id="proySubestandar"></p>
            <p><b>Situación</b></p><p id="proySituacion" class="ar-proy-texto"></p>
            <p><b>Riesgo</b></p><p id="proyRiesgo" class="ar-proy-texto"></p>
            <p class="text-muted">Departamento: <span id="proyDepto"></span> · Fuente: <span id="proyFuente"></span><br>Sedes: <span id="proySedes"></span></p>
          </div>
          <div class="ar-card">
            <div class="ar-callout" id="sinPermisoRiesgo" hidden>Este riesgo incluye sedes que no puedes evaluar. Pásalo con «Omitir».</div>
            <form id="evaluarForm" onsubmit="return false;">
              <h3>Evaluación</h3>
              <div class="form-group"><label for="probSelect">Probabilidad</label><select class="form-control" id="probSelect"></select></div>
              <div class="form-group"><label for="consSelect">Consecuencia</label><select class="form-control" id="consSelect"></select></div>
              <p>Nivel: <span id="nivelProyectado">—</span></p>
              <div id="accionOpcional">
                <label class="checkbox-inline"><input type="checkbox" id="conAccionCheck"> Agregar la primera acción</label>
                <div id="accionBloque" hidden style="margin-top:10px">
                  <div class="form-group"><label for="descripcionAccion">Acción</label><textarea class="form-control" id="descripcionAccion" rows="2"></textarea></div>
                  <div class="form-group"><label for="responsableSelect">Responsable</label><select class="form-control" id="responsableSelect"></select></div>
                  <div class="form-group"><label for="apoyoSelect">Apoyo (opcional)</label><select class="form-control" id="apoyoSelect"></select></div>
                  <div class="form-group"><label for="fechaAccionInput">Fecha compromiso</label><input type="date" class="form-control" id="fechaAccionInput"><small class="text-muted" id="fechaSugeridaTexto"></small></div>
                  <div class="form-group" id="justificacionGrupo" hidden><label for="justificacionAccion">Justifica el plazo mayor al sugerido</label><textarea class="form-control" id="justificacionAccion" rows="2"></textarea></div>
                </div>
              </div>
            </form>
            <div class="ar-acciones" style="margin-top:14px">
              <button type="button" class="btn btn-default" id="omitirBoton">Omitir</button>
              <button type="button" class="btn btn-success" id="guardarSiguienteBoton">Guardar y siguiente <i class="fa fa-arrow-right"></i></button>
            </div>
          </div>
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
<script src="<?php echo getHandelAPI()?>/js/vistas/vista.js?v=20261028"></script>

<!-- Repositorios, Presentador y Vista de la pantalla (handel_api) -->
<script src="<?php echo getHandelAPI()?>/js/repositorios/argos_riesgos_repositorio.js?v=<?php echo $version; ?>"></script>
<script src="<?php echo getHandelAPI()?>/js/repositorios/argos_evaluaciones_repositorio.js?v=<?php echo $version; ?>"></script>
<script src="<?php echo getHandelAPI()?>/js/repositorios/argos_acciones_repositorio.js?v=<?php echo $version; ?>"></script>
<script src="<?php echo getHandelAPI()?>/js/repositorios/argos_configuracion_repositorio.js?v=<?php echo $version; ?>"></script>
<script src="<?php echo getHandelAPI()?>/js/presentadores/argos_taller_presentador.js?v=<?php echo $version; ?>"></script>
<script src="<?php echo getHandelAPI()?>/js/vistas/argos_taller_vista.js?v=<?php echo $version; ?>"></script>
<script src="js/argos.js?v=<?php echo $version; ?>"></script>

<img src="img/cargando.svg" id="indicador" alt="Cargando">
</body>
</html>
