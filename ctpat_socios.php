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
$titulo = "Cadenas y socios";
$archivo = "ctpat_socios.php";
$modulo = "ctpat";   // oea | ctpat: define el menú lateral y el selector de la barra superior
$version = "20261145";
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
        <div class="ar-crumb">ARGOS › CTPAT › Paso 1</div>
        <h1>Cadenas y socios</h1>
        <p>Primero registra los socios comerciales y después las cadenas de exportación hacia EUA con sus etapas (anexo A).</p>
      </div>
      <div class="ar-acciones"><select id="ejercicioSelect" class="form-control" aria-label="Ejercicio"></select></div>
    </div>

    <section class="ar-main-contenido">
      <ul class="nav nav-tabs ar-tabs" id="pestanas">
        <li class="active"><a href="#" data-p="socios">① Socios comerciales</a></li>
        <li><a href="#" data-p="cadenas">② Cadenas de exportación</a></li>
      </ul>

      <div id="panelSocios">
        <div class="ar-card">
          <div class="ar-acciones" style="margin-bottom:10px"><button type="button" class="btn btn-primary" id="nuevoSocioBoton" hidden><i class="fa fa-plus"></i> Agregar socio</button></div>
          <div class="ar-callout">Es <b>sensible</b> el socio que cumple al menos un criterio (maneja la carga o su información, entra a instalaciones o medios de transporte, subcontrata, manufactura o almacena). Solo los sensibles reciben cuestionario, aunque estén certificados.</div>
          <div class="ar-tabla-scroll"><table class="ar-tabla" id="tablaSocios"><thead><tr><th>Razón social</th><th>RFC / Tax ID</th><th>Tipo</th><th>Ubicación</th><th>Sensible</th><th>Cuestionario</th><th>Idioma</th><th></th></tr></thead><tbody></tbody></table></div>
          <div class="ar-vacio" id="sinSocios" hidden><i class="fa fa-inbox"></i>Aún no hay socios comerciales.</div>
        </div>
      </div>

      <div id="panelCadenas" hidden>
        <div class="ar-acciones" style="margin-bottom:10px">
          <button type="button" class="btn btn-primary" id="nuevaCadenaBoton" hidden><i class="fa fa-plus"></i> Nueva cadena</button>
          <button type="button" class="btn btn-default" id="verFlujoBoton"><i class="fa fa-share-alt"></i> Flujo</button>
          <button type="button" class="btn btn-default" id="verTablaBoton"><i class="fa fa-table"></i> Tabla</button>
        </div>
        <div id="cadenasLista"></div>
      </div>
    </section>

    <datalist id="zonasLista"></datalist>

    <div class="modal fade" id="socioModal" tabindex="-1" role="dialog"><div class="modal-dialog modal-lg" role="document"><div class="modal-content">
      <div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h4 class="modal-title" id="socioTitulo"></h4></div>
      <div class="modal-body">
        <div class="row">
          <div class="col-sm-8 form-group"><label for="razonSocial">Razón social</label><input type="text" class="form-control" id="razonSocial" maxlength="200"></div>
          <div class="col-sm-4 form-group"><label for="rfcTaxid">RFC o Tax ID</label><input type="text" class="form-control" id="rfcTaxid" maxlength="25"></div>
        </div>
        <div class="row">
          <div class="col-sm-6 form-group"><label for="tipoSocio">Tipo de socio</label><select id="tipoSocio" class="form-control"></select></div>
          <div class="col-sm-3 form-group"><label for="paisSocio">País</label><input type="text" class="form-control" id="paisSocio" maxlength="60"></div>
          <div class="col-sm-3 form-group"><label for="ciudadSocio">Ciudad</label><input type="text" class="form-control" id="ciudadSocio" maxlength="100"></div>
        </div>
        <div class="checkbox" id="cruzaGrupo" hidden><label><input type="checkbox" id="cruzaEua"> ¿Cruza la carga a Estados Unidos? (recibe el cuestionario de Transfer / cruce)</label></div>
        <div class="row">
          <div class="col-sm-4 form-group"><label for="certificacionSocio">Certificación</label><input type="text" class="form-control" id="certificacionSocio" maxlength="100" placeholder="CTPAT, OEA…"></div>
          <div class="col-sm-4 form-group"><label for="aniosSocio">Años de relación</label><input type="number" class="form-control" id="aniosSocio" min="0" max="100"></div>
          <div class="col-sm-4 form-group"><label for="seleccionadoPor">Lo selecciona</label><select id="seleccionadoPor" class="form-control"><option value="">—</option><option value="empresa">Nuestra empresa</option><option value="cliente">Cliente en EUA</option><option value="socio">Otro socio</option></select></div>
        </div>
        <div class="form-group"><label>Criterios de socio sensible</label><div id="criteriosSocio"></div></div>
        <div class="row">
          <div class="col-sm-4 form-group"><label for="contactoNombre">Contacto</label><input type="text" class="form-control" id="contactoNombre" maxlength="150"></div>
          <div class="col-sm-3 form-group"><label for="contactoPuesto">Puesto</label><input type="text" class="form-control" id="contactoPuesto" maxlength="100"></div>
          <div class="col-sm-3 form-group"><label for="contactoCorreo">Correo</label><input type="email" class="form-control" id="contactoCorreo" maxlength="150"></div>
          <div class="col-sm-2 form-group"><label for="contactoTel">Teléfono</label><input type="text" class="form-control" id="contactoTel" maxlength="40"></div>
        </div>
        <div class="form-group" style="max-width:200px"><label for="idiomaSocio">Idioma del cuestionario</label><select id="idiomaSocio" class="form-control"><option value="es">Español</option><option value="en">English</option></select></div>
      </div>
      <div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button><button type="button" class="btn btn-success" id="guardarSocioBoton">Guardar</button></div>
    </div></div></div>

    <div class="modal fade" id="cadenaModal" tabindex="-1" role="dialog"><div class="modal-dialog" role="document"><div class="modal-content">
      <div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h4 class="modal-title">Nueva cadena de exportación</h4></div>
      <div class="modal-body">
        <div class="form-group"><label for="cadenaNombre">Nombre</label><input type="text" class="form-control" id="cadenaNombre" maxlength="150"></div>
        <div class="row"><div class="col-sm-6 form-group"><label for="cadenaOrigen">Origen</label><input type="text" class="form-control" id="cadenaOrigen" maxlength="150"></div><div class="col-sm-6 form-group"><label for="cadenaDestino">Destino en EUA</label><input type="text" class="form-control" id="cadenaDestino" maxlength="150"></div></div>
        <div class="row"><div class="col-sm-5 form-group"><label for="cadenaModo">Modo</label><select id="cadenaModo" class="form-control"></select></div><div class="col-sm-7 form-group"><label for="cadenaCarga">Tipo de carga</label><input type="text" class="form-control" id="cadenaCarga" maxlength="150"></div></div>
        <div class="form-group"><label for="cadenaCopiar">Etapas</label><select id="cadenaCopiar" class="form-control"></select></div>
        <div class="ar-callout">Solo cadenas de exportación hacia Estados Unidos.</div>
      </div>
      <div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button><button type="button" class="btn btn-success" id="guardarCadenaBoton">Guardar</button></div>
    </div></div></div>

    <div class="modal fade" id="etapaModal" tabindex="-1" role="dialog"><div class="modal-dialog" role="document"><div class="modal-content">
      <div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h4 class="modal-title" id="etapaTitulo"></h4></div>
      <div class="modal-body">
        <div class="form-group"><label for="etapaSocio">Socio</label><select id="etapaSocio" class="form-control"></select></div>
        <div class="form-group"><label for="etapaProceso">Proceso</label><input type="text" class="form-control" id="etapaProceso" maxlength="200" placeholder="Manufactura, transporte, cruce…"></div>
        <div class="form-group"><label for="etapaMovimiento">Movimiento de la carga</label><input type="text" class="form-control" id="etapaMovimiento" maxlength="300"></div>
        <div class="form-group"><label for="etapaZona">Zona</label><input type="text" class="form-control" id="etapaZona" list="zonasLista" maxlength="150" placeholder="Nuevo Laredo, Tamps. o Saltillo – Nuevo Laredo"></div>
        <div class="row"><div class="col-sm-6 form-group"><label for="etapaDias">Días en reposo</label><input type="number" class="form-control" id="etapaDias" min="0" max="3650"></div><div class="col-sm-6 form-group"><label for="etapaModo">Modo</label><select id="etapaModo" class="form-control"></select></div></div>
        <div class="checkbox" id="etapaUltimaGrupo"><label><input type="checkbox" id="etapaUltima"> Es la última etapa de la cadena</label></div>
      </div>
      <div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button><button type="button" class="btn btn-success" id="guardarEtapaBoton">Guardar</button></div>
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
<script src="<?php echo getHandelAPI()?>/js/repositorios/ctpat_mapeo_repositorio.js?v=<?php echo $version; ?>"></script>
<script src="<?php echo getHandelAPI()?>/js/presentadores/ctpat_socios_presentador.js?v=<?php echo $version; ?>"></script>
<script src="<?php echo getHandelAPI()?>/js/componentes/argos_historial.js?v=<?php echo $version; ?>"></script>
<script src="<?php echo getHandelAPI()?>/js/vistas/ctpat_socios_vista.js?v=<?php echo $version; ?>"></script>
<script src="js/argos.js?v=<?php echo $version; ?>"></script>

<img src="img/cargando.svg" id="indicador" alt="Cargando">
</body>
</html>
