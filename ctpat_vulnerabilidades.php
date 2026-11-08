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
$titulo = "Vulnerabilidades";
$archivo = "ctpat_vulnerabilidades.php";
$modulo = "ctpat";   // oea | ctpat: define el menú lateral y el selector de la barra superior
$version = "20261176";
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
        <p>Vulnerabilidad del socio = su estándar con la calificación más alta (anexo D). Riesgo de la etapa = amenaza de su zona × vulnerabilidad de su socio.</p>
      </div>
      <div class="ar-acciones"><button type="button" class="btn btn-default" id="exportarBoton"><i class="fa fa-file-excel-o"></i> Excel</button><select id="ejercicioSelect" class="form-control" aria-label="Ejercicio"></select></div>
    </div>

    <section class="ar-main-contenido">
      <div class="ar-card">
        <h3>Vulnerabilidad por socio</h3>
        <div class="ar-sub">Calificación 1 cumple todo · 2 falla una Debe no crítica o una Debería · 3 falla una Debe crítica. Sin respuestas a la fecha límite: 3. El asterisco marca un ajuste del consultor.</div>
        <div class="ar-tabla-scroll"><table class="ar-tabla" id="tablaSocios"><thead><tr><th>Socio</th><th>1</th><th>2</th><th>3</th><th>4</th><th>5</th><th>6</th><th>7</th><th>8</th><th>9</th><th>10</th><th>11</th><th>12</th><th>Vuln.</th><th>Validación</th><th></th></tr></thead><tbody></tbody></table></div>
        <div class="ar-vacio" id="sinSocios" hidden><i class="fa fa-inbox"></i>Aún no hay socios en las cadenas.</div>
      </div>
      <div class="ar-grid ar-g32">
        <div class="ar-card"><h3>Matriz de riesgo 3 × 3</h3><div class="ar-sub">Número de etapas por celda. 1-2 bajo · 3-4 medio · 6-9 alto.</div><div class="ar-hm ar-hm-riesgo" id="matrizRiesgo"></div></div>
        <div class="ar-card"><h3>Etapas de la celda</h3><ul class="ar-lista" id="etapasCelda"></ul></div>
      </div>
      <h3>Riesgo por cadena</h3>
      <div id="cadenasRiesgo"></div>
    </section>

    <div class="modal fade" id="revisionModal" tabindex="-1" role="dialog"><div class="modal-dialog modal-lg" role="document"><div class="modal-content">
      <div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h4 class="modal-title" id="revisionTitulo"></h4></div>
      <div class="modal-body">
        <h4>Método de verificación</h4>
        <label class="checkbox-inline"><input type="checkbox" class="metodo-vuln" value="cuestionario"> Cuestionario</label>
        <label class="checkbox-inline"><input type="checkbox" class="metodo-vuln" value="certificacion"> Certificación CTPAT/OEA verificada</label>
        <label class="checkbox-inline"><input type="checkbox" class="metodo-vuln" value="visita"> Visita en sitio</label>
        <label class="checkbox-inline"><input type="checkbox" class="metodo-vuln" value="evidencia"> Evidencia documental</label>
        <div class="form-group" style="margin-top:10px"><label for="notaRevision">Nota</label><textarea class="form-control" id="notaRevision" rows="2" maxlength="2000"></textarea></div>
        <button type="button" class="btn btn-default" id="guardarRevisionBoton">Guardar método y nota</button>
        <hr>
        <h4>Ajustar la calificación de un estándar</h4>
        <div class="row"><div class="col-sm-8 form-group"><label for="ajusteEstandar">Estándar</label><select id="ajusteEstandar" class="form-control"></select></div>
        <div class="col-sm-4 form-group"><label for="ajusteCalificacion">Nueva calificación</label><select id="ajusteCalificacion" class="form-control"><option value="1">1</option><option value="2">2</option><option value="3">3</option></select></div></div>
        <div class="form-group"><label for="ajusteJustificacion">Justificación (obligatoria)</label><textarea class="form-control" id="ajusteJustificacion" rows="2" maxlength="2000"></textarea></div>
        <div class="form-group"><label for="ajusteBuenaPractica">Buena práctica (opcional)</label><textarea class="form-control" id="ajusteBuenaPractica" rows="2" maxlength="2000"></textarea></div>
        <button type="button" class="btn btn-success" id="guardarAjusteBoton">Aplicar ajuste</button>
        <div class="ar-callout">Cambiar la revisión o ajustar un estándar quita la validación: hay que validar de nuevo.</div>
      </div>
      <div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button></div>
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
