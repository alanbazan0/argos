<?php
use php\clases\Menu;

include 'php/configuracion.php';
include 'php/Aplicacion.php';
include 'php/clases/Menu.php';

$aplicacion = new Aplicacion();
$usuario = $aplicacion->iniciar();
// El Registro de riesgos ya está liberado para todos con permiso; el resto del menú solo lo ve quien puede ver el menú completo.
if(!$aplicacion->puedeVerRegistro($usuario))
{
    header('Location: panel.php');
    exit;
}
$menu = new Menu();
$menu->setHandelAPI(getHandelAPI());
$menu->setSoloArchivos($aplicacion->puedeVerMenu($usuario) ? null : Aplicacion::archivosLiberados());
$titulo = "Registro de riesgos";
$archivo = "riesgos.php";
$modulo = "oea";   // oea | ctpat: define el menú lateral y el selector de la barra superior
$version = "20261147";
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
        <div class="ar-crumb">ARGOS › OEA México</div>
        <h1>Registro de riesgos</h1>
        <p>Organizado por estándar y subestándar OEA, más las categorías de riesgos adicionales. <span id="totalRiesgos"></span></p>
      </div>
      <div class="ar-acciones">
        <div class="btn-group ar-exportar" data-exportar></div>
        <a class="btn btn-default" href="importar.php"><i class="fa fa-upload"></i> Importar Excel (fase 1)</a>
        <button type="button" class="btn btn-primary" id="agregarButton"><i class="fa fa-plus"></i> Nuevo riesgo</button>
      </div>
    </div>

    <section class="ar-main-contenido">
      <div class="ar-card">
        <form id="filtrosForm" class="ar-filtros" onsubmit="return false;">
          <input type="text" class="form-control" id="textoFiltro" placeholder="Buscar situación, riesgo o item">
          <select class="form-control" id="subestandarFiltro" aria-label="Subestándar"></select>
          <select class="form-control" id="departamentoFiltro" aria-label="Departamento"></select>
          <select class="form-control" id="sedeFiltro" aria-label="Sede"></select>
          <select class="form-control" id="nivelFiltro" aria-label="Nivel">
            <option value="">Todos los niveles</option><option value="1">Nivel 1</option><option value="2">Nivel 2</option><option value="3">Nivel 3</option>
            <option value="4">Nivel 4</option><option value="5">Nivel 5</option><option value="6">Nivel 6</option>
          </select>
          <select class="form-control" id="fuenteFiltro" aria-label="Fuente"></select>
          <select class="form-control" id="estadoFiltro" aria-label="Estado">
            <option value="">Todos los estados</option><option>Sin evaluar</option><option>Abierto</option><option>En tratamiento</option>
            <option>Vencido</option><option>Cerrado</option><option>Aceptado</option>
          </select>
          <label class="checkbox-inline ar-inactivos"><input type="checkbox" id="inactivosCheck"> Ver desactivados</label>
          <span class="ar-sp"></span>
          <div class="btn-group ar-seg" id="vistaSeg"><button type="button" class="btn btn-default on" data-v="tabla"><i class="fa fa-th-list"></i> Tabla</button><button type="button" class="btn btn-default" data-v="mapa"><i class="fa fa-th"></i> Mapa de calor</button></div>
        </form>
        <div class="ar-tabla-scroll">
          <table class="ar-tabla" id="tablaRiesgos">
            <thead><tr><th>Item</th><th>Situación / Riesgo</th><th>Depto.</th><th>Sedes</th><th>Fuente</th><th>Probabilidad</th><th>Consecuencia</th><th>Nivel</th><th>Meta</th><th>Acciones</th><th>Estado</th></tr></thead>
            <tbody></tbody>
          </table>
        </div>
        <div id="mapaRiesgos" hidden><div id="mapaCalor"></div><div class="ar-callout"><b>Regla:</b> esta vista respeta los mismos filtros que la tabla. Sin filtro de sede, un riesgo con niveles distintos por sede se ubica en su nivel más grave; con filtro de sede, se usa el nivel de esa sede. Los riesgos sin evaluar no aparecen en el mapa.</div></div>
        <div class="ar-vacio" id="sinRiesgos" hidden><i class="fa fa-inbox"></i>No hay riesgos con esos filtros.</div>
        <div class="ar-callout"><b>Regla:</b> <sup>*</sup> = el nivel varía por sede; la tabla muestra el más grave. Al filtrar por sede se muestra el nivel de esa sede.</div>
        <div class="ar-callout"><b>Regla:</b> el <b>Item</b> se asigna solo: subestándar + consecutivo (1.1.1, 1.1.2…). Nunca se reutiliza un número, aunque el riesgo se cierre.</div>
      </div>
    </section>

    <?php $menu->renderizarFooter(); ?>
  </main>
</div>

<!-- Alta y edición de la identificación -->
<div class="modal fade" id="formularioModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title" id="formularioTitulo">Nuevo riesgo</h4>
      </div>
      <div class="modal-body">
        <form id="riesgoForm" onsubmit="return false;">
          <input type="hidden" id="riesgoId">
          <div class="ar-bloque">
            <h4>Ubicación</h4>
            <div id="ubicacionFija" class="ar-callout" hidden></div>
            <div id="ubicacionEdicion">
              <div class="row">
                <div class="col-sm-6 form-group"><label for="estandarSelect">Estándar <i class="fa fa-info-circle ar-info" data-toggle="tooltip" title="Estándar y subestándar de OEA México donde se presenta el riesgo. Si no corresponde a un estándar, usa las categorías adicionales (ruta, climatológicos, geográficos, legales)."></i></label><select id="estandarSelect" class="form-control"></select></div>
                <div class="col-sm-6 form-group"><label for="subestandarSelect">Subestándar</label><select id="subestandarSelect" class="form-control"></select><a href="tutoriales.php" target="_blank" rel="noopener" id="verTutorialLink" hidden style="font-size:12px"><i class="fa fa-play-circle"></i> Ver tutorial</a></div>
              </div>
              <div class="form-group"><label>Sedes <i class="fa fa-info-circle ar-info" data-toggle="tooltip" title="Sedes donde se presenta la situación. Con «Todas las sedes», cada sede reporta por separado el avance de las acciones."></i></label>
                <div><label class="checkbox-inline"><input type="checkbox" id="todasSedesCheck"> <b>Todas las sedes</b></label></div>
                <div id="sedesCheckboxes"></div>
              </div>
            </div>
            <div class="row">
              <div class="col-sm-6 form-group"><label for="departamentoSelect">Departamento <i class="fa fa-info-circle ar-info" data-toggle="tooltip" title="Departamento donde se presenta la situación. Viene del catálogo de departamentos de la empresa."></i></label><select id="departamentoSelect" class="form-control"></select></div>
              <div class="col-sm-6 form-group"><label for="fechaInput">Fecha de detección <i class="fa fa-info-circle ar-info" data-toggle="tooltip" title="Fecha en que se identificó el riesgo. Por defecto es hoy."></i></label><input type="date" id="fechaInput" class="form-control"></div>
            </div>
          </div>
          <div class="ar-bloque">
            <h4>Identificación</h4>
            <div class="form-group"><label for="situacionInput">Situación <i class="fa fa-info-circle ar-info" data-toggle="tooltip" title="Describe el hecho que observas tal como es hoy, sin juzgarlo. Ej. «No hay buena iluminación en el patio de remolques»."></i></label><textarea id="situacionInput" class="form-control" rows="2" maxlength="4000"></textarea></div>
            <div class="form-group"><label for="porqueInput">¿Por qué es un riesgo? <i class="fa fa-info-circle ar-info" data-toggle="tooltip" title="Explica qué podría pasar a partir de esa situación. Ej. «Podrían entrar personas sin ser vistas»."></i></label><textarea id="porqueInput" class="form-control" rows="2" maxlength="4000"></textarea></div>
            <div class="form-group"><label for="riesgoInput">Riesgo <i class="fa fa-info-circle ar-info" data-toggle="tooltip" title="El evento que se quiere evitar, en pocas palabras. Ej. «Robo de mercancía», «Contaminación de carga»."></i></label><textarea id="riesgoInput" class="form-control" rows="2" maxlength="4000"></textarea></div>
            <div class="row">
              <div class="col-sm-4 form-group"><label for="fuenteSelect">Fuente <i class="fa fa-info-circle ar-info" data-toggle="tooltip" title="Interno: lo origina la propia empresa (personal, procesos, instalaciones). Externo: lo origina alguien o algo fuera de la empresa (delincuencia, clima, socios). Interno y externo: los dos."></i></label><select id="fuenteSelect" class="form-control"></select></div>
              <div class="col-sm-8 form-group"><label for="origenInput">¿Quién o quiénes originan el riesgo? <i class="fa fa-info-circle ar-info" data-toggle="tooltip" title="¿Quién o quiénes originan el riesgo? Ej. «Personal de nuevo ingreso», «Transportistas subcontratados», «Delincuencia en la carretera 57»."></i></label><input type="text" id="origenInput" class="form-control" maxlength="300"></div>
            </div>
          </div>
          <div class="ar-bloque">
            <h4>Análisis</h4>
            <div class="form-group"><label for="controlesInput">Controles actuales <i class="fa fa-info-circle ar-info" data-toggle="tooltip" title="Lo que la empresa ya tiene para atacar este riesgo: procedimientos, políticas, ayudas visuales, equipo, capacitación."></i></label><textarea id="controlesInput" class="form-control" rows="2" maxlength="4000"></textarea></div>
            <div class="row">
              <div class="col-sm-4 form-group"><label for="eficaciaSelect">Eficacia de los controles <i class="fa fa-info-circle ar-info" data-toggle="tooltip" title="Efectivo: el control funciona y hay evidencia. Parcial: existe pero no siempre se cumple. Inexistente: no hay control."></i></label><select id="eficaciaSelect" class="form-control"></select></div>
              <div class="col-sm-5 form-group"><label for="deteccionInput">Detección <i class="fa fa-info-circle ar-info" data-toggle="tooltip" title="¿Cómo nos daríamos cuenta de que el riesgo se está presentando? Ej. recorridos, auditorías, CCTV, reportes del monitorista."></i></label><input type="text" id="deteccionInput" class="form-control" maxlength="300"></div>
              <div class="col-sm-3 form-group"><label for="incidentesInput">Incidentes 12 m <i class="fa fa-info-circle ar-info" data-toggle="tooltip" title="Número de veces que el evento ocurrió en los últimos 12 meses. Ayuda a justificar la probabilidad."></i></label><input type="number" min="0" id="incidentesInput" class="form-control"></div>
            </div>
          </div>
          <p class="text-muted">La evaluación (probabilidad, consecuencia y nivel) se registra aparte, desde el detalle del riesgo.</p>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" id="guardarButton">Guardar</button>
      </div>
    </div>
  </div>
</div>

<!-- Detalle del riesgo -->
<div class="modal fade" id="detalleModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title" id="detalleTitulo"></h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-7">
            <div class="ar-bloque"><h4>Identificación</h4><div id="detalleIdentificacion"></div></div>
          </div>
          <div class="col-md-5">
            <div class="ar-bloque"><h4>Acciones</h4><ul class="ar-lista" id="detalleAcciones"></ul><a class="btn btn-default btn-xs" id="nuevaAccionLink" href="#" hidden><i class="fa fa-plus"></i> Nueva acción</a></div>
            <div class="ar-bloque"><h4>Evaluaciones</h4><div id="detalleTrayectoria"></div><ul class="ar-lista" id="detalleEvaluaciones"></ul></div>
            <div class="ar-bloque"><h4>Cambios registrados</h4><ul class="ar-lista" id="detalleBitacora"></ul></div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" id="historialButton" hidden title="Historial de cambios"><i class="fa fa-history"></i> Historial</button>
        <button type="button" class="btn btn-default" id="evaluarButton"><i class="fa fa-line-chart"></i> Evaluar</button>
        <button type="button" class="btn btn-default" id="desactivarButton"><i class="fa fa-ban"></i> Desactivar</button>
        <button type="button" class="btn btn-success" id="reactivarButton" hidden><i class="fa fa-undo"></i> Reactivar</button>
        <button type="button" class="btn btn-default" id="editarButton"><i class="fa fa-pencil"></i> Editar</button>
        <button type="button" class="btn btn-primary" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<!-- Evaluación y reevaluación -->
<div class="modal fade" id="evaluacionModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title" id="evaluacionTitulo">Evaluar riesgo</h4>
      </div>
      <div class="modal-body">
        <form id="evaluacionForm" onsubmit="return false;">
          <div class="ar-callout">La evaluación anterior no se modifica: cada evaluación se agrega con su fecha. El nivel lo calcula el sistema con la calibración vigente.</div>
          <div class="ar-bloque">
            <h4>Evaluación</h4>
            <div class="row">
              <div class="col-sm-5 form-group"><label for="motivoSelect">Motivo</label>
                <select id="motivoSelect" class="form-control">
                  <option>Alta del riesgo</option><option>Alta (taller)</option><option>Importación</option><option>Seguimiento de acciones</option>
                  <option>Incidente</option><option>Cambio en las condiciones de la empresa</option>
                </select></div>
              <div class="col-sm-3 form-group"><label for="evaluacionFecha">Fecha</label><input type="date" id="evaluacionFecha" class="form-control"></div>
              <div class="col-sm-4 form-group"><label>Nivel resultante</label><div id="nivelResultante">—</div></div>
            </div>
            <div class="checkbox"><label><input type="checkbox" id="mismoNivelCheck" checked> Mismo nivel de riesgo en todas las sedes</label></div>
            <div class="ar-tabla-scroll">
              <table class="ar-tabla">
                <thead><tr><th>Sede</th><th><i class="fa fa-info-circle ar-info" data-toggle="tooltip" title="Elige la frase que mejor describe con qué frecuencia se presenta el evento. El sistema la clasifica en la escala de probabilidad."></i> Probabilidad</th><th><i class="fa fa-info-circle ar-info" data-toggle="tooltip" title="Elige la frase que mejor describe el daño si el evento ocurre. El sistema la clasifica en la escala de consecuencia."></i> Consecuencia</th><th>Nivel</th></tr></thead>
                <tbody id="filasEvaluacion"></tbody>
              </table>
            </div>
          </div>
          <div class="ar-bloque">
            <h4>Tratamiento</h4>
            <div class="row">
              <div class="col-sm-4 form-group"><label for="metaSelect">Nivel meta <i class="fa fa-info-circle ar-info" data-toggle="tooltip" title="El nivel que se quiere alcanzar después de las acciones. Solo puede ser igual o menos grave que el actual."></i></label><select id="metaSelect" class="form-control"></select></div>
              <div class="col-sm-4 form-group"><label for="tratamientoSelect">Tratamiento <i class="fa fa-info-circle ar-info" data-toggle="tooltip" title="Mitigar: reducir probabilidad o consecuencia. Evitar: dejar de hacer la actividad. Transferir: pasar el riesgo a un tercero (seguro, contrato). Aceptar: solo en niveles 5 y 6, con justificación."></i></label>
                <select id="tratamientoSelect" class="form-control"><option value="">Sin definir</option><option>Mitigar</option><option>Evitar</option><option>Transferir</option><option>Aceptar</option></select></div>
            </div>
            <div class="form-group" id="justificacionGrupo" hidden><label for="justificacionInput">Justificación de la aceptación</label><textarea id="justificacionInput" class="form-control" rows="2" maxlength="4000"></textarea></div>
          </div>
          <div class="form-group"><label for="comentarioEvaluacion">Comentario (queda en la bitácora)</label><textarea id="comentarioEvaluacion" class="form-control" rows="2" maxlength="4000"></textarea></div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" id="guardarEvaluacionButton">Guardar evaluación</button>
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
<script src="<?php echo getHandelAPI()?>/js/vistas/vista.js?v=20261028"></script>

<!-- Repositorio, Presentador y Vista de la pantalla (handel_api) -->
<script src="<?php echo getHandelAPI()?>/js/repositorios/argos_base_repositorio.js?v=<?php echo $version; ?>"></script>
<script src="<?php echo getHandelAPI()?>/js/repositorios/argos_bandeja_repositorio.js?v=<?php echo $version; ?>"></script>
<script src="<?php echo getHandelAPI()?>/js/repositorios/argos_configuracion_repositorio.js?v=<?php echo $version; ?>"></script>
<script src="<?php echo getHandelAPI()?>/js/repositorios/argos_evaluaciones_repositorio.js?v=<?php echo $version; ?>"></script>
<script src="<?php echo getHandelAPI()?>/js/repositorios/argos_acciones_repositorio.js?v=<?php echo $version; ?>"></script>
<script src="<?php echo getHandelAPI()?>/js/repositorios/argos_riesgos_repositorio.js?v=<?php echo $version; ?>"></script>
<script src="<?php echo getHandelAPI()?>/js/presentadores/argos_riesgos_presentador.js?v=<?php echo $version; ?>"></script>
<script src="<?php echo getHandelAPI()?>/js/vistas/argos_riesgos_vista.js?v=<?php echo $version; ?>"></script>
<script src="<?php echo getHandelAPI()?>/js/componentes/argos_exportacion.js?v=<?php echo $version; ?>"></script>
<script src="<?php echo getHandelAPI()?>/js/componentes/argos_historial.js?v=<?php echo $version; ?>"></script>
<script src="js/argos.js?v=<?php echo $version; ?>"></script>

<img src="img/cargando.svg" id="indicador" alt="Cargando">
</body>
</html>
