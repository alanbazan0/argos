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
$titulo = "Vulnerabilidades";
$archivo = "ctpat_vulnerabilidades.php";
$modulo = "ctpat";   // oea | ctpat: define el menú lateral y el selector de la barra superior
$version = "20261206";
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
        <div class="ar-crumb">ARGOS › CTPAT › Paso 3</div>
        <h1>Vulnerabilidades y riesgo</h1>
        <p>Debilidades de cada socio frente a los criterios mínimos de seguridad (anexo D), combinadas con la amenaza de la zona</p>
      </div>
      <div class="ar-acciones"><button type="button" class="btn btn-default" id="exportarBoton">⬇ Exportar</button><select id="ejercicioSelect" class="form-control" aria-label="Ejercicio"></select></div>
    </div>

    <section class="ar-main-contenido">
      <div class="ar-grid ar-g4 ar-grid-kpis" id="kpisVuln"></div>
      <div class="ar-tabm" id="pestanasVuln">
        <button type="button" class="ar-on" data-p="soc">Vulnerabilidad por socio <span class="ar-tabm-n" id="tabSocN">0</span></button>
        <button type="button" data-p="cad">Riesgo por cadena <span class="ar-tabm-n" id="tabCadN">0</span></button>
        <button type="button" data-p="mat">Matriz de riesgo <span class="ar-tabm-n" id="tabMatN">0</span></button>
      </div>
      <div id="panelSoc">
        <div class="ar-card"><div class="ar-tabla-scroll"><table class="ar-tabla ar-amz" id="tablaSocios"><thead></thead><tbody></tbody></table></div>
          <div class="ar-vacio" id="sinSocios" hidden><i class="fa fa-inbox"></i>Aún no hay socios en las cadenas.</div>
          <div class="ar-situacion" id="leyendaEstandares" style="margin-top:8px"></div></div>
      </div>
      <div id="panelCad" style="display:none"><div id="cadenasRiesgo"></div></div>
      <div id="panelMat" style="display:none">
        <div class="ar-grid ar-g32">
          <div class="ar-card"><h3>Etapas por amenaza y vulnerabilidad</h3><div class="ar-situacion" style="margin-bottom:8px">Vulnerabilidad del socio →  ·  Amenaza de la zona ↑</div><div class="ar-hm ar-hm-riesgo" id="matrizRiesgo"></div><div class="ar-situacion" id="leyendaMatriz" style="margin-top:8px"></div></div>
          <div class="ar-card"><h3 id="tituloCelda">Etapas de riesgo alto</h3><ul class="ar-lista" id="etapasCelda"></ul></div>
        </div>
      </div>
    </section>

    <div class="modal fade" id="revisionModal" tabindex="-1" role="dialog"><div class="modal-dialog modal-lg" role="document"><div class="modal-content">
      <div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h4 class="modal-title" id="revisionTitulo"></h4></div>
      <div class="modal-body">
        <div id="revisionResumen"></div>
        <div class="ar-form-secciones">
        <div class="ar-bloque"><h4>Método de verificación</h4>
        <div class="ar-sedes-box">
          <label class="checkbox-inline"><input type="checkbox" class="metodo-vuln" value="cuestionario"> Cuestionario</label>
          <label class="checkbox-inline"><input type="checkbox" class="metodo-vuln" value="certificacion"> Certificación CTPAT/OEA verificada</label>
          <label class="checkbox-inline"><input type="checkbox" class="metodo-vuln" value="visita"> Visita en sitio</label>
          <label class="checkbox-inline"><input type="checkbox" class="metodo-vuln" value="evidencia"> Evidencia documental</label>
        </div></div>
        <div class="ar-bloque"><h4>Calificación por estándar</h4>
        <div class="ar-tabla-scroll"><table class="ar-tabla" id="tablaEstandares"><thead><tr><th>Estándar</th><th style="text-align:center">Cumpl.</th><th style="text-align:center">Cuestionario</th><th>Final</th><th>Vulnerabilidades identificadas</th></tr></thead><tbody></tbody></table></div></div>
        </div>
        <div class="form-group" style="margin-top:12px"><label for="notaRevision">Notas de la revisión</label><textarea class="form-control" id="notaRevision" rows="2" maxlength="2000" placeholder="Ej. Se revisaron las respuestas con el responsable del socio"></textarea></div>
        <div id="revisionError" class="text-danger"></div>
      </div>
      <div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button><button type="button" class="btn btn-default" id="guardarRevisionBoton">Guardar</button><button type="button" class="btn btn-success" id="guardarValidarBoton">✓ Guardar y validar</button></div>
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
<script src="<?php echo getHandelAPI()?>/js/repositorios/ctpat_vulnerabilidades_repositorio.js?v=<?php echo $version; ?>"></script>
<script src="<?php echo getHandelAPI()?>/js/presentadores/ctpat_vulnerabilidades_presentador.js?v=<?php echo $version; ?>"></script>
<script src="<?php echo getHandelAPI()?>/js/componentes/argos_historial.js?v=<?php echo $version; ?>"></script>
<script src="<?php echo getHandelAPI()?>/js/vistas/ctpat_vulnerabilidades_vista.js?v=<?php echo $version; ?>"></script>
<script src="js/argos.js?v=<?php echo $version; ?>"></script>

<img src="img/cargando.svg" id="indicador" alt="Cargando">
</body>
</html>
