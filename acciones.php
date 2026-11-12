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
$titulo = "Acciones";
$archivo = "acciones.php";
$modulo = "oea";   // oea | ctpat: define el menú lateral y el selector de la barra superior
$version = "20261208";
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
        <h1>Acciones</h1>
        <p>Plan de acción con seguimiento por sede. <span id="totalAcciones"></span></p>
      </div>
      <div class="ar-acciones">
        <div class="btn-group ar-exportar" data-exportar></div>
        <button type="button" class="btn btn-primary" id="nuevaButton" hidden><i class="fa fa-plus"></i> Nueva acción</button>
      </div>
    </div>

    <section class="ar-main-contenido">
      <div class="ar-card">
        <form id="filtrosForm" class="ar-filtros" onsubmit="return false;">
          <input type="text" class="form-control" id="textoFiltro" placeholder="Buscar acción, riesgo o item">
          <select class="form-control" id="responsableFiltro" aria-label="Responsable"></select>
          <select class="form-control" id="sedeFiltro" aria-label="Sede"></select>
          <select class="form-control ar-filtro-revision" id="revisionFiltro" aria-label="Revisión del Comité" style="display:none"></select>
          <select class="form-control" id="estadoFiltro" aria-label="Estado">
            <option value="">Todos los estados</option><option>Pendiente</option><option>En proceso</option><option>Vencida</option><option>Completada</option>
          </select>
        </form>
        <div class="ar-callout" id="avisoSede" hidden></div>
        <div class="ar-tabla-scroll">
          <table class="ar-tabla" id="tablaAcciones">
            <thead><tr><th>Riesgo</th><th>Acción</th><th>Responsable</th><th>Apoyo</th><th>Fecha compromiso</th><th>Avance por sede</th><th>Estado</th><th></th></tr></thead>
            <tbody></tbody>
          </table>
        </div>
        <div class="ar-vacio" id="sinAcciones" hidden><i class="fa fa-inbox"></i>No hay acciones con esos filtros.</div>
      </div>
    </section>

    <?php $menu->renderizarFooter(); ?>
  </main>
</div>

<!-- Nueva acción -->
<div class="modal fade" id="nuevaAccionModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Nueva acción</h4>
      </div>
      <div class="modal-body">
        <form id="accionForm" class="ar-form-secciones" onsubmit="return false;">
          <div class="ar-bloque">
            <h4>Riesgo</h4>
            <div class="form-group"><label for="riesgoSelect">Riesgo (evaluado y no aceptado)</label><select id="riesgoSelect" class="form-control"></select></div>
            <div class="form-group"><label>Sedes que ejecutan la acción <i class="fa fa-info-circle ar-info" data-toggle="tooltip" title="Cada sede reporta por separado su avance."></i></label><div id="sedesAccion"></div></div>
          </div>
          <div class="ar-bloque">
            <h4>Acción</h4>
            <div class="form-group"><label for="descripcionAccion">Qué se va a hacer <i class="fa fa-info-circle ar-info" data-toggle="tooltip" title="Qué se va a hacer para reducir el riesgo. Debe ser concreta y verificable."></i></label><textarea id="descripcionAccion" class="form-control" rows="2" maxlength="4000" placeholder="Ej. Asignar y ejecutar presupuesto de mantenimiento para luminarias"></textarea></div>
            <div class="row">
              <div class="col-sm-6 form-group"><label for="responsableSelect">Responsable <i class="fa fa-info-circle ar-info" data-toggle="tooltip" title="Usuario de ARGOS que ejecuta la acción. Recibe avisos por correo."></i></label><select id="responsableSelect" class="form-control"></select></div>
              <div class="col-sm-6 form-group"><label for="apoyoSelect">Apoyo <i class="fa fa-info-circle ar-info" data-toggle="tooltip" title="Usuario de ARGOS que ayuda o autoriza la acción (por ejemplo, Dirección)."></i></label><select id="apoyoSelect" class="form-control"></select></div>
            </div>
            <div class="form-group"><label for="fechaAccionInput">Fecha compromiso <i class="fa fa-info-circle ar-info" data-toggle="tooltip" title="Se sugiere según el nivel de riesgo: niveles 1–2, 3 meses; 3–4, 6 meses; 5–6, 9 meses. Si eliges una fecha posterior, se pide justificación."></i></label>
              <input type="date" id="fechaAccionInput" class="form-control"><span class="text-muted" id="fechaSugeridaTexto"></span></div>
            <div class="form-group" id="justificacionPlazoGrupo" hidden><label for="justificacionPlazoInput">Justificación del plazo</label><textarea id="justificacionPlazoInput" class="form-control" rows="2" maxlength="4000" placeholder="Ej. La obra requiere presupuesto del siguiente ejercicio"></textarea></div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" id="guardarAccionButton">Guardar acción</button>
      </div>
    </div>
  </div>
</div>

<!-- Detalle de la acción y bitácora -->
<div class="modal fade" id="accionModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title" id="accionTitulo"></h4>
      </div>
      <div class="modal-body">
        <div id="accionResumen"></div>
        <div class="ar-bloque"><h4>Seguimiento por sede</h4>
          <table class="ar-tabla" id="seguimientoSedes"><thead><tr><th>Sede</th><th>Estado</th><th>Avance</th></tr></thead><tbody></tbody></table></div>
        <div class="ar-bloque" id="publicarGrupo"><h4>Publicar avance</h4>
          <form id="publicarForm" onsubmit="return false;">
            <div class="form-group"><label>Sedes a las que aplica</label><div id="sedesPublicacion"></div></div>
            <div class="row">
              <div class="col-sm-4 form-group"><label for="estadoPublicacion">Estado</label>
                <select id="estadoPublicacion" class="form-control"><option value="">Selecciona…</option><option>Pendiente</option><option>En proceso</option><option>Completada</option></select></div>
              <div class="col-sm-3 form-group" id="avanceGrupo" hidden><label for="avancePublicacion">Avance (1–99 %)</label><input type="number" min="1" max="99" id="avancePublicacion" class="form-control" placeholder="% avance"></div>
            </div>
            <div class="form-group"><label for="textoPublicacion">Publicación <i class="fa fa-info-circle ar-info" data-toggle="tooltip" title="Menciona a alguien con @Nombre Apellido para avisarle."></i></label><textarea id="textoPublicacion" class="form-control" rows="2" maxlength="4000" placeholder="¿Qué se hizo? Describe el avance, el problema o el cierre… Usa @Nombre Apellido para mencionar a alguien."></textarea></div>
            <div class="form-group"><label for="enlacePublicacion">Enlace de la evidencia (opcional)</label><input type="url" id="enlacePublicacion" class="form-control" maxlength="500" placeholder="🔗 Enlace a evidencia (opcional)"></div>
            <button type="button" class="btn btn-primary" id="publicarButton">Publicar</button>
          </form>
        </div>
        <p class="text-muted" id="sinPermisoPublicar" hidden>No tienes asignada ninguna de las sedes de esta acción, por eso no puedes publicar en ella.</p>
        <div class="ar-bloque"><h4>Bitácora</h4><ul class="ar-lista" id="publicaciones"></ul></div>
      </div>
      <div class="modal-footer"><button type="button" class="btn btn-primary" data-dismiss="modal">Cerrar</button></div>
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
<script src="<?php echo getHandelAPI()?>/js/componentes/argos_avatar.js?v=<?php echo $version; ?>"></script>
<script src="<?php echo getHandelAPI()?>/js/vistas/vista.js?v=20261110a"></script>

<!-- Repositorios, Presentador y Vista de la pantalla (handel_api) -->
<script src="<?php echo getHandelAPI()?>/js/repositorios/argos_base_repositorio.js?v=<?php echo $version; ?>"></script>
<script src="<?php echo getHandelAPI()?>/js/repositorios/argos_bandeja_repositorio.js?v=<?php echo $version; ?>"></script>
<script src="<?php echo getHandelAPI()?>/js/repositorios/argos_riesgos_repositorio.js?v=<?php echo $version; ?>"></script>
<script src="<?php echo getHandelAPI()?>/js/repositorios/argos_acciones_repositorio.js?v=<?php echo $version; ?>"></script>
<script src="<?php echo getHandelAPI()?>/js/presentadores/argos_acciones_presentador.js?v=<?php echo $version; ?>"></script>
<script src="<?php echo getHandelAPI()?>/js/componentes/argos_historial.js?v=<?php echo $version; ?>"></script>
<script src="<?php echo getHandelAPI()?>/js/vistas/argos_acciones_vista.js?v=<?php echo $version; ?>"></script>
<script src="<?php echo getHandelAPI()?>/js/componentes/argos_exportacion.js?v=<?php echo $version; ?>"></script>
<script src="js/argos.js?v=<?php echo $version; ?>"></script>

<img src="img/cargando.svg" id="indicador" alt="Cargando">
</body>
</html>
