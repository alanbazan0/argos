<?php
use php\clases\Menu;

include 'php/configuracion.php';
include 'php/Aplicacion.php';
include 'php/clases/Menu.php';

$aplicacion = new Aplicacion();
$usuario = $aplicacion->iniciar();
// El Modo taller ya está liberado para todos con permiso; el resto del menú solo lo ve quien puede ver el menú completo.
if(!$aplicacion->puedeVerRegistro($usuario))
{
    header('Location: panel.php');
    exit;
}
$menu = new Menu();
$menu->setHandelAPI(getHandelAPI());
$menu->setSoloArchivos($aplicacion->puedeVerMenu($usuario) ? null : Aplicacion::archivosLiberados());
$titulo = "Modo taller";
$archivo = "taller.php";
$modulo = "oea";   // oea | ctpat: define el menú lateral y el selector de la barra superior
$version = "20261180";
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
        <div class="ar-callout">Fase 1: el grupo identifica todas las situaciones de riesgo <b>sin evaluarlas todavía</b>. Se capturan como en una hoja de cálculo; Tab avanza de campo. Los riesgos quedan <b>Sin evaluar</b> y no aparecen en el mapa de calor hasta la fase 2. Puedes pegar renglones copiados de Excel: con las 8 columnas de la hoja (subestándar, departamento, sedes, situación, ¿por qué?, riesgo, fuente, origen) se llenan también las listas por nombre. Atajos: <b>Ctrl/Cmd + Enter</b> agrega un renglón; <b>Alt + ↑ / ↓</b> pasa al mismo campo del renglón de arriba o abajo.</div>
        <div class="row ar-fecha-taller">
          <div class="col-sm-3 form-group"><label for="fechaTallerInput">Fecha de detección <i class="fa fa-info-circle ar-info" data-toggle="tooltip" title="¿Cuándo se detectó el riesgo? Aquí es la fecha del taller y se aplica a todos los renglones de esta sesión."></i> <small>— fecha del taller</small></label><input type="date" id="fechaTallerInput" class="form-control"></div>
          <div class="col-sm-9"><span class="text-muted">Se aplica a todos los renglones de esta sesión; si algún riesgo se detectó en otra fecha, se corrige después en su ficha (Editar).</span></div>
        </div>
        <div class="ar-hoja"><table class="ar-tabla" id="hojaTaller">
          <thead><tr><th>#</th><th>Subestándar <i class="fa fa-info-circle ar-info" data-toggle="tooltip" title="Elige el estándar y subestándar OEA al que pertenece el riesgo. El item (1.1.1, 1.1.2…) se asigna solo al guardar."></i></th><th>Departamento <i class="fa fa-info-circle ar-info" data-toggle="tooltip" title="El área de la empresa donde está el riesgo."></i></th><th>Sedes <i class="fa fa-info-circle ar-info" data-toggle="tooltip" title="Todas las sedes o una o varias sedes específicas. Sin ninguna marcada se guarda como «Todas»."></i></th><th>Situación <i class="fa fa-info-circle ar-info" data-toggle="tooltip" title="¿Qué está pasando? Describe el hecho, no la consecuencia. Ej. No hay buena iluminación en algunas de las instalaciones."></i></th><th>¿Por qué es un riesgo? <i class="fa fa-info-circle ar-info" data-toggle="tooltip" title="¿Por qué esa situación es un riesgo? Ej. Podrían entrar personas a robar sin ser vistas."></i></th><th>Riesgo <i class="fa fa-info-circle ar-info" data-toggle="tooltip" title="El evento que se quiere evitar. Ej. Robo a la empresa o al personal."></i></th><th>Fuente <i class="fa fa-info-circle ar-info" data-toggle="tooltip" title="¿El riesgo nace dentro de la empresa, fuera o ambos?"></i></th><th>¿Quién lo origina? <i class="fa fa-info-circle ar-info" data-toggle="tooltip" title="Quién o quiénes originan el riesgo. Ej. Personal de nuevo ingreso, transportistas subcontratados."></i></th><th>Estado</th><th></th></tr></thead>
          <tbody></tbody>
        </table></div>
        <div class="ar-acciones" style="margin-top:12px">
          <button type="button" class="btn btn-default" id="agregarFilasBoton"><i class="fa fa-plus"></i> Agregar 5 renglones</button>
          <a class="btn btn-default" href="importar.php"><i class="fa fa-upload"></i> Importar Excel (solo fase 1)</a>
          <button type="button" class="btn btn-success" id="guardarLoteBoton"><i class="fa fa-save"></i> Guardar riesgos identificados <i class="fa fa-arrow-right"></i></button>
        </div>
      </div>
      <div class="ar-card" id="pendientesCard" hidden>
        <h3><span id="pendientesTitulo"></span></h3>
        <div class="ar-sub">De talleres anteriores o importados.</div>
        <ul class="ar-lista" id="pendientesLista"></ul>
        <button type="button" class="btn btn-primary" id="pasarFase2Boton">Pasar a la fase 2: evaluar <i class="fa fa-arrow-right"></i></button>
      </div>

      <div id="faseDos" hidden>
        <div class="ar-card ar-vacio" id="faseDosVacio" hidden></div>
        <div id="faseDosContenido">
          <div class="ar-fila-progreso"><b id="avanceFase2"></b><span class="text-muted">Ideal para proyectar en pantalla durante el taller</span></div>
          <div class="ar-progreso"><div id="barraFase2"></div></div>
          <div class="ar-grid ar-g32">
            <div class="ar-card ar-proyeccion">
              <h2 id="proyItem"></h2>
              <p class="ar-situacion" id="proySubestandar"></p>
              <p class="ar-proy-k">Situación</p><p id="proySituacion" class="ar-proy-texto ar-proy-grande"></p>
              <p class="ar-proy-k">¿Por qué es un riesgo?</p><p id="proyPorque" class="ar-proy-texto"></p>
              <p class="ar-proy-k">Riesgo</p><p id="proyRiesgo" class="ar-proy-texto"><b></b></p>
              <p class="ar-proy-k">Fuente · ¿quién lo origina?</p><p id="proyFuente" class="ar-proy-texto"></p>
              <p class="ar-proy-k">Sedes</p><p id="proySedes" class="ar-proy-texto"></p>
              <p class="ar-proy-k">Departamento</p><p id="proyDepto" class="ar-proy-texto"></p>
            </div>
            <div class="ar-card">
              <div class="ar-callout" id="sinPermisoRiesgo" hidden>Este riesgo es solo de sedes que no puedes evaluar. Pásalo con «Omitir».</div>
              <form id="evaluarForm" class="ar-form-secciones" onsubmit="return false;">
                <div class="ar-bloque">
                  <h4>Controles y detección</h4>
                  <div class="ar-sub">Lo que la empresa ya tiene hoy contra este riesgo.</div>
                  <div class="row">
                    <div class="col-sm-6 form-group"><label for="controlesInput">Controles actuales <i class="fa fa-info-circle ar-info" data-toggle="tooltip" title="Lo que la empresa ya tiene para atacar este riesgo: procedimientos, políticas, ayudas visuales, equipo, capacitación."></i></label><input type="text" id="controlesInput" class="form-control" maxlength="4000" placeholder="Ej. Señalamientos, procedimiento, rondines"></div>
                    <div class="col-sm-3 form-group"><label for="eficaciaSelect">Eficacia <i class="fa fa-info-circle ar-info" data-toggle="tooltip" title="¿Qué tan bien funcionan hoy los controles actuales?"></i></label>
                      <select id="eficaciaSelect" class="form-control"><option value="Efectivo">Efectivo</option><option value="Parcial" selected>Parcial</option><option value="Inexistente">Inexistente</option></select></div>
                    <div class="col-sm-3 form-group"><label for="incidentesInput">Incidentes <i class="fa fa-info-circle ar-info" data-toggle="tooltip" title="Cuántas veces ocurrió este evento en los últimos 12 meses."></i></label><input type="number" id="incidentesInput" class="form-control" min="0" value="0"></div>
                  </div>
                  <div class="form-group"><label for="deteccionInput">¿Cómo detectamos este riesgo? <i class="fa fa-info-circle ar-info" data-toggle="tooltip" title="¿Cómo nos daríamos cuenta de que el riesgo se está presentando? Ej. recorridos, auditorías, CCTV, reportes del monitorista."></i></label><input type="text" id="deteccionInput" class="form-control" maxlength="300" placeholder="Ej. Recorridos, auditorías, CCTV"></div>
                </div>
                <div class="ar-bloque">
                  <h4>Evaluación</h4>
                  <div class="ar-sub">Responde las dos preguntas; el sistema clasifica y calcula el nivel.</div>
                  <div class="checkbox" id="mismoNivelGrupo"><label><input type="checkbox" id="mismoNivelCheck" checked> Mismo nivel de riesgo en todas las sedes</label></div>
                  <div class="ar-tabla-scroll"><table class="ar-tabla"><thead><tr><th>Sede</th><th>Probabilidad</th><th>Consecuencia</th><th>Nivel</th></tr></thead><tbody id="filasEvaluacion"></tbody></table></div>
                  <p style="margin-top:8px">Nivel resultante: <span id="nivelResultante">—</span></p>
                  <div class="row">
                    <div class="col-sm-4 form-group"><label for="metaSelect">Nivel meta <i class="fa fa-info-circle ar-info" data-toggle="tooltip" title="El nivel que se quiere alcanzar después de las acciones. Solo puede ser igual o menos grave que el actual."></i></label><select id="metaSelect" class="form-control"></select></div>
                    <div class="col-sm-4 form-group"><label for="tratamientoSelect">Tratamiento <i class="fa fa-info-circle ar-info" data-toggle="tooltip" title="Mitigar: reducir probabilidad o consecuencia. Evitar: dejar de hacer la actividad. Transferir: pasar el riesgo a un tercero (seguro, contrato). Aceptar: solo en niveles 5 y 6, con justificación."></i></label>
                      <select id="tratamientoSelect" class="form-control"><option>Mitigar</option><option>Evitar</option><option>Transferir</option><option>Aceptar</option></select></div>
                  </div>
                  <div class="form-group" id="justificacionAceptacionGrupo" hidden><label for="justificacionAceptacionInput">Justificación para aceptar el riesgo</label><textarea id="justificacionAceptacionInput" class="form-control" rows="2" maxlength="4000" placeholder="¿Por qué los controles actuales son suficientes?"></textarea></div>
                </div>
                <div class="ar-bloque" id="accionBloque">
                  <h4>Primera acción de tratamiento</h4>
                  <div class="ar-sub">Se pueden agregar más después desde Acciones.</div>
                  <div class="form-group"><label for="descripcionAccion">Acción recomendada</label><textarea class="form-control" id="descripcionAccion" rows="2" placeholder="Ej. Instalar control de préstamo de llaves"></textarea></div>
                  <div class="row">
                    <div class="col-sm-4 form-group"><label for="responsableSelect">Responsable</label><select class="form-control" id="responsableSelect"></select></div>
                    <div class="col-sm-4 form-group"><label for="apoyoSelect">Apoyo para la acción</label><select class="form-control" id="apoyoSelect"></select></div>
                    <div class="col-sm-4 form-group"><label for="fechaAccionInput">Fecha compromiso</label><input type="date" class="form-control" id="fechaAccionInput"><small class="text-muted" id="fechaSugeridaTexto"></small></div>
                  </div>
                  <div class="form-group" id="justificacionGrupo" hidden><label for="justificacionAccion">Justifica el plazo mayor al sugerido</label><textarea class="form-control" id="justificacionAccion" rows="2"></textarea></div>
                </div>
              </form>
              <div class="ar-acciones" style="margin-top:14px;justify-content:flex-end">
                <button type="button" class="btn btn-default" id="anteriorBoton"><i class="fa fa-arrow-left"></i> Anterior</button>
                <button type="button" class="btn btn-default" id="omitirBoton">Omitir</button>
                <button type="button" class="btn btn-success" id="guardarSiguienteBoton">Guardar y siguiente <i class="fa fa-arrow-right"></i></button>
              </div>
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
<script src="<?php echo getHandelAPI()?>/js/vistas/vista.js?v=20261110a"></script>

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
