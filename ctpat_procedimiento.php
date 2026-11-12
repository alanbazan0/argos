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
$titulo = "Procedimiento";
$archivo = "ctpat_procedimiento.php";
$modulo = "ctpat";   // oea | ctpat: define el menú lateral y el selector de la barra superior
$version = "20261203";
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
        <div class="ar-crumb">ARGOS › CTPAT › Paso 5</div>
        <h1>Procedimiento documentado</h1>
        <p>Cómo hace la empresa su análisis de riesgo: lo que CBP revisa en la validación. ARGOS lo redacta con lo capturado en los pasos 1 a 4.</p>
      </div>
      <div class="ar-acciones"><button type="button" class="btn btn-default" id="datosBoton">✎ Datos del procedimiento</button><button type="button" class="btn btn-default" id="verProcedimientoBoton">👁 Ver procedimiento</button><a href="ctpat_expediente.php" class="btn btn-primary">📦 Expediente completo</a></div>
    </div>

    <section class="ar-main-contenido">
      <div class="ar-grid ar-g32">
        <div class="ar-card"><div class="ar-amz-hd"><h3 style="margin:0">Elementos mínimos (guía de 5 pasos)</h3><span class="ar-crit" id="avanceElementos"></span></div><div id="elementosLista"></div></div>
        <div>
          <div class="ar-card" style="margin-bottom:14px"><div class="ar-amz-hd"><h3 style="margin:0" id="versionActualTitulo">Versión</h3><span id="versionActualEstado"></span></div>
            <dl class="dl-horizontal" id="versionActualDatos" style="margin:8px 0 0"></dl>
            <div id="versionActualAcciones" style="margin-top:10px"></div>
            <div class="ar-situacion" id="versionFaltan" style="margin-top:6px;color:#8A6A00"></div>
            <div class="form-group" style="margin-top:10px" id="notaVersionBloque" hidden><input type="text" id="notaVersion" class="form-control" maxlength="500" placeholder="Ej. Se integra ARGOS: cuestionarios por tipo de socio y biblioteca de amenazas"></div>
            <button type="button" class="btn btn-default btn-sm" id="nuevaVersionBoton" hidden>＋ Nueva versión</button></div>
          <div class="ar-card"><h3>Historial de versiones</h3><ul class="ar-lista" id="versionesLista"></ul></div>
        </div>
      </div>
    </section>

    <div class="modal fade" id="datosModal" tabindex="-1" role="dialog"><div class="modal-dialog modal-lg" role="document"><div class="modal-content">
      <div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h4 class="modal-title">Datos del procedimiento</h4></div>
      <div class="modal-body">
          <div class="row">
            <div class="col-sm-4 form-group"><label for="fechaEstablecimiento">Fecha de establecimiento</label><input type="date" id="fechaEstablecimiento" class="form-control"></div>
            <div class="col-sm-4 form-group"><label for="responsableSelect">Responsable del proceso</label><select id="responsableSelect" class="form-control"></select></div>
            <div class="col-sm-4 form-group"><label for="suplenteSelect">Suplente</label><select id="suplenteSelect" class="form-control"></select></div>
          </div>
          <div class="row">
            <div class="col-sm-6 form-group"><label for="frecuenciaSelect">Frecuencia del análisis de riesgo <i class="fa fa-info-circle ar-info" title="La guía recomienda cada tres meses para transportistas por carretera."></i></label><select id="frecuenciaSelect" class="form-control"></select></div>
            <div class="col-sm-6 form-group"><label for="frecuenciaRevision">Revisión del procedimiento</label><input type="text" id="frecuenciaRevision" class="form-control" maxlength="100" placeholder="Ej. Anual"></div>
          </div>
          <div class="form-group"><label>Además, se hace un análisis cuando…</label><div class="ar-sedes-box" id="disparadoresBloque"></div></div>
          <div class="form-group"><label for="politicaVisitas">Seguimiento de acciones: visitas a socios</label><textarea id="politicaVisitas" class="form-control" rows="2" placeholder="Ej. Visita en sitio a los socios con vulnerabilidad 3 antes de la fecha límite de sus acciones"></textarea></div>
          <div class="form-group"><label for="criterioSustitucion">Seguimiento de acciones: cuándo se sustituye a un socio</label><textarea id="criterioSustitucion" class="form-control" rows="2" placeholder="Ej. Se sustituye al socio que no corrige una vulnerabilidad crítica después de dos plazos vencidos"></textarea></div>
          <div class="form-group"><label for="capacitacion">Capacitación del personal clave</label><textarea id="capacitacion" class="form-control" rows="3" placeholder="Ej. Al asignar el puesto y una vez al año: metodología de 5 pasos, uso de ARGOS y revisión de cuestionarios"></textarea></div>
          <div class="row"><div class="col-sm-6 form-group"><label for="supervisor">Supervisión: quién aprueba y recibe los reportes</label><input type="text" id="supervisor" class="form-control" maxlength="200" placeholder="Ej. Director general"></div>
          <div class="col-sm-6 form-group"><label for="frecuenciaReporte">Frecuencia del reporte a la dirección</label><input type="text" id="frecuenciaReporte" class="form-control" maxlength="100" placeholder="Ej. Trimestral"></div></div>
      </div>
      <div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button><button type="button" class="btn btn-success" id="guardarDatosBoton">Guardar</button></div>
    </div></div></div>

    <div class="modal fade" id="textoModal" tabindex="-1" role="dialog"><div class="modal-dialog modal-lg" role="document"><div class="modal-content">
      <div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h4 class="modal-title">Procedimiento de análisis de riesgo (vista previa)</h4></div>
      <div class="modal-body"><pre class="ar-proc-texto" id="textoProcedimiento"></pre></div>
    </div></div></div>

    <div class="modal fade" id="aprobarModal" tabindex="-1" role="dialog"><div class="modal-dialog" role="document"><div class="modal-content">
      <div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h4 class="modal-title">Aprobar versión</h4></div>
      <div class="modal-body"><div class="form-group"><label for="aproboNombre">Nombre de quien aprueba</label><input type="text" id="aproboNombre" class="form-control" maxlength="150" placeholder="Ej. Ana Torres"></div><div class="form-group"><label for="aproboPuesto">Puesto</label><input type="text" id="aproboPuesto" class="form-control" maxlength="100" placeholder="Ej. Directora general"></div>
        <div class="ar-callout">No se aprueba con elementos incompletos. Al aprobar, la versión se congela con las cifras de hoy; para cambiarla se crea una versión nueva.</div></div>
      <div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button><button type="button" class="btn btn-success" id="confirmarAprobarBoton">Aprobar</button></div>
    </div></div></div>

    <div class="modal fade" id="versionModal" tabindex="-1" role="dialog"><div class="modal-dialog modal-lg" role="document"><div class="modal-content">
      <div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h4 class="modal-title" id="versionTitulo"></h4></div>
      <div class="modal-body"><pre class="ar-proc-texto" id="versionTexto"></pre></div>
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
<script src="<?php echo getHandelAPI()?>/js/repositorios/ctpat_procedimiento_repositorio.js?v=<?php echo $version; ?>"></script>
<script src="<?php echo getHandelAPI()?>/js/presentadores/ctpat_procedimiento_presentador.js?v=<?php echo $version; ?>"></script>
<script src="<?php echo getHandelAPI()?>/js/componentes/argos_historial.js?v=<?php echo $version; ?>"></script>
<script src="<?php echo getHandelAPI()?>/js/vistas/ctpat_procedimiento_vista.js?v=<?php echo $version; ?>"></script>
<script src="js/argos.js?v=<?php echo $version; ?>"></script>

<img src="img/cargando.svg" id="indicador" alt="Cargando">
</body>
</html>
