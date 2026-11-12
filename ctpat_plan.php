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
$titulo = "Plan de acción";
$archivo = "ctpat_plan.php";
$modulo = "ctpat";   // oea | ctpat: define el menú lateral y el selector de la barra superior
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
<body class="ar-app" <?php $menu->renderizarUsuarioJSON($usuario); ?>>

<?php $menu->renderizarBarraSuperior($usuario, true, $modulo); ?>

<div class="ar-layout">
  <aside class="ar-sidebar">
    <?php $menu->renderizar($usuario, $archivo, $modulo); ?>
  </aside>

  <main class="ar-main">

    <div class="ar-page-hd">
      <div>
        <div class="ar-crumb">ARGOS › CTPAT › Paso 4</div>
        <h1>Plan de acción</h1>
        <p>Acciones para corregir las vulnerabilidades del paso 3, con responsable de la empresa y del socio, fechas y evidencia (anexo E)</p>
      </div>
      <div class="ar-acciones"><button type="button" class="btn btn-default" id="exportarBoton">⬇ Exportar anexo E</button><select id="ejercicioSelect" class="form-control" aria-label="Ejercicio"></select><button type="button" class="btn btn-primary" id="nuevaAccionBoton" hidden>＋ Nueva acción</button></div>
    </div>
    <section class="ar-main-contenido">
      <div class="ar-grid ar-g4 ar-grid-kpis" id="kpisPlan"></div>
      <div class="ar-tabm" id="planTabs">
        <button type="button" class="ar-on" data-p="plan">Plan de acción <span class="ar-tabm-n" id="contPlan">0</span></button>
        <button type="button" data-p="sugeridas">Sugeridas <span class="ar-tabm-n" id="contSugeridas">0</span></button>
        <button type="button" data-p="socio">Por socio <span class="ar-tabm-n" id="contSocio">0</span></button>
      </div>
      <div id="panelPlan">
        <div class="ar-hm-filtros"><div class="btn-group" id="filtroPlan"></div></div>
        <div class="ar-card"><div class="ar-tabla-scroll"><table class="ar-tabla" id="accionesTabla"><thead><tr><th>Prioridad</th><th>Socio · estándar</th><th>Vulnerabilidad</th><th>Acción</th><th>Responsables</th><th>Revisión</th><th>Fecha límite</th><th>Evidencia</th><th>Estatus</th></tr></thead><tbody></tbody></table></div>
        <div class="ar-vacio" id="sinAcciones" hidden><i class="fa fa-inbox"></i>Sin acciones con este filtro.</div></div>
      </div>
      <div id="panelSugeridas" hidden><div id="sugerenciasLista"></div><details class="ar-estandar" id="descartadasBloque"><summary><b>Descartadas</b> <span class="ar-situacion" id="contDescartadas">0</span></summary><ul class="ar-lista" id="descartadasLista" style="padding:0 12px 8px"></ul></details></div>
      <div id="panelSocio" hidden></div>
    </section>
    <div class="modal fade" id="accionModal" tabindex="-1" role="dialog"><div class="modal-dialog modal-lg" role="document"><div class="modal-content">
      <div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h4 class="modal-title" id="accionTitulo"></h4></div>
      <div class="modal-body" id="accionCuerpo"></div>
      <div class="modal-footer" id="accionPie"></div>
    </div></div></div>
    <div class="modal fade" id="nuevaModal" tabindex="-1" role="dialog"><div class="modal-dialog" role="document"><div class="modal-content">
      <div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h4 class="modal-title" id="nuevaTitulo"></h4></div>
      <div class="modal-body">
        <div class="form-group"><label for="nuevaSocio">Socio</label><select id="nuevaSocio" class="form-control"></select></div>
        <div class="form-group"><label for="nuevaEstandar">Estándar (1 a 12)</label><input type="number" id="nuevaEstandar" class="form-control" min="1" max="12"></div>
        <div id="nuevaManual">
          <div class="form-group"><label for="nuevaPrioridad">Prioridad</label><select id="nuevaPrioridad" class="form-control"><option value="alto">Alto</option><option value="medio">Medio</option><option value="bajo">Bajo</option></select></div>
          <div class="form-group"><label for="nuevaVuln">Vulnerabilidad identificada</label><textarea id="nuevaVuln" class="form-control" rows="2" placeholder="Ej. No existe control de acceso de visitantes"></textarea></div>
          <div class="form-group"><label for="nuevaAccionTxt">Acción correctiva o mitigación</label><textarea id="nuevaAccionTxt" class="form-control" rows="2" placeholder="Ej. Implementar bitácora de visitantes y gafetes"></textarea></div>
          <div class="form-group"><label for="nuevaEvid">Evidencia esperada</label><input type="text" id="nuevaEvid" class="form-control" placeholder="Ej. Procedimiento firmado y registros de las últimas 4 semanas"></div>
        </div>
        <div class="form-group"><label for="nuevaRespEmpresa">Responsable de la empresa</label><select id="nuevaRespEmpresa" class="form-control"></select></div>
        <div class="form-group"><label for="nuevaRespSocio">Responsable del socio</label><input type="text" id="nuevaRespSocio" class="form-control" maxlength="150" placeholder="Ej. Gerente de seguridad del socio"></div>
        <div class="form-group"><label for="nuevaFecha">Fecha límite (vacío = la sugerida por la prioridad)</label><input type="date" id="nuevaFecha" class="form-control"></div>
      </div>
      <div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button><button type="button" class="btn btn-success" id="guardarNuevaBoton">Crear acción</button></div>
    </div></div></div>

    <div class="modal fade" id="descarteModal" tabindex="-1" role="dialog"><div class="modal-dialog" role="document"><div class="modal-content">
      <div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h4 class="modal-title">Descartar sugerencia</h4></div>
      <div class="modal-body"><div class="form-group"><label for="motivoDescarte">Motivo (obligatorio)</label><textarea id="motivoDescarte" class="form-control" rows="3" maxlength="500" placeholder="Ej. El socio dejará de prestar el servicio en diciembre"></textarea></div></div>
      <div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button><button type="button" class="btn btn-danger" id="confirmarDescarteBoton">Descartar</button></div>
    </div></div></div>

    <div class="modal fade" id="evidenciaModal" tabindex="-1" role="dialog"><div class="modal-dialog" role="document"><div class="modal-content">
      <div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h4 class="modal-title">Agregar evidencia</h4></div>
      <div class="modal-body"><div class="form-group"><label for="evidenciaUrl">Enlace (https://)</label><input type="url" id="evidenciaUrl" class="form-control" placeholder="Enlace (OneDrive, SharePoint, Google Drive…)"></div><div class="form-group"><label for="evidenciaDesc">Descripción</label><input type="text" id="evidenciaDesc" class="form-control" maxlength="300" placeholder="Descripción"></div><div class="ar-callout">Las evidencias son enlaces; no se suben archivos.</div></div>
      <div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button><button type="button" class="btn btn-success" id="guardarEvidenciaBoton">Agregar</button></div>
    </div></div></div>

    <div class="modal fade" id="reprogModal" tabindex="-1" role="dialog"><div class="modal-dialog" role="document"><div class="modal-content">
      <div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h4 class="modal-title">Reprogramar acción</h4></div>
      <div class="modal-body"><div class="form-group"><label for="reprogFecha">Nueva fecha límite (posterior a hoy)</label><input type="date" id="reprogFecha" class="form-control"></div><div class="form-group"><label for="reprogMotivo">Motivo (obligatorio)</label><textarea id="reprogMotivo" class="form-control" rows="3" maxlength="500" placeholder="Ej. El proveedor de cámaras entrega en enero"></textarea></div></div>
      <div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button><button type="button" class="btn btn-success" id="guardarReprogBoton">Reprogramar</button></div>
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
<script src="<?php echo getHandelAPI()?>/js/repositorios/ctpat_plan_repositorio.js?v=<?php echo $version; ?>"></script>
<script src="<?php echo getHandelAPI()?>/js/presentadores/ctpat_plan_presentador.js?v=<?php echo $version; ?>"></script>
<script src="<?php echo getHandelAPI()?>/js/componentes/argos_historial.js?v=<?php echo $version; ?>"></script>
<script src="<?php echo getHandelAPI()?>/js/vistas/ctpat_plan_vista.js?v=<?php echo $version; ?>"></script>
<script src="js/argos.js?v=<?php echo $version; ?>"></script>

<img src="img/cargando.svg" id="indicador" alt="Cargando">
</body>
</html>
