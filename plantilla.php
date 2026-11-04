<?php
/**
 * PLANTILLA DE PANTALLA DE ARGOS
 *
 * Copiar este archivo con el nombre de la pantalla (por ejemplo riesgos.php) y cambiar $titulo, $archivo y el
 * bloque <section class="ar-main-contenido">. El resto (cabecera, barra superior, menú, pie y scripts) se queda igual.
 * Muestra además todos los componentes de css/argos.css, para usarla como guía de estilos.
 *
 * Reglas (misma arquitectura que saha, sivah y cavi):
 *  - Sin estilos ni scripts dentro del HTML: los estilos van en css/argos.css y la lógica en la Vista y el Presentador.
 *  - La Vista, el Presentador y el Repositorio JS de cada pantalla viven en handel_api (js/vistas, js/presentadores,
 *    js/repositorios) y los endpoints PHP en handel_api/php/repositorios; aquí solo se cargan con getHandelAPI().
 *  - Las rutas a handel_api siempre con getHandelAPI(), nunca con una URL fija.
 *  - Al cambiar un script, subir el ?v= para romper la caché del navegador.
 */
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
$titulo = "Plantilla";
$archivo = "plantilla.php";
$modulo = "oea";   // oea | ctpat: define el menú lateral y el selector de la barra superior
$version = "20261116";
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

    <!-- Encabezado de la pantalla -->
    <div class="ar-page-hd">
      <div>
        <div class="ar-crumb">ARGOS › <?php echo $titulo; ?></div>
        <h1><?php echo $titulo; ?> <button type="button" class="ar-ayuda" data-toggle="tooltip" title="Guía rápida de esta pantalla"><i class="fa fa-info-circle"></i></button></h1>
        <p>Descripción corta de lo que se hace en esta pantalla.</p>
      </div>
      <div class="ar-acciones">
        <button type="button" class="btn btn-default"><i class="fa fa-download"></i> Exportar</button>
        <button type="button" class="btn btn-primary"><i class="fa fa-plus"></i> Agregar</button>
      </div>
    </div>

    <section class="ar-main-contenido">

      <!-- Indicadores rápidos -->
      <div class="ar-grid ar-g4">
        <div class="ar-card ar-kpi"><div class="ar-n">128</div><div class="ar-l">Riesgos activos</div><div class="ar-t">+4 este mes</div></div>
        <div class="ar-card ar-kpi"><div class="ar-n">17</div><div class="ar-l">Acciones vencidas</div><div class="ar-t">3 por validar</div></div>
        <div class="ar-card ar-kpi"><div class="ar-n">9</div><div class="ar-l">Sin evaluar</div><div class="ar-t">en tus sedes</div></div>
        <div class="ar-card ar-kpi"><div class="ar-n"><span class="ar-sem ar-sem-amarillo">78</span></div><div class="ar-l">Cumplimiento general</div><div class="ar-t">meta 85</div></div>
      </div>

      <div class="ar-callout ar-aviso">Aviso informativo: así se ve un mensaje de atención dentro de una pantalla.</div>

      <!-- Filtros + tabla -->
      <div class="ar-card">
        <h3>Tabla con filtros</h3>
        <div class="ar-sub">Los filtros se combinan; el nivel va con su color y el estado con su etiqueta.</div>
        <div class="ar-filtros">
          <input type="text" class="form-control" placeholder="Buscar situación o riesgo">
          <select class="form-control"><option>Todas las sedes</option></select>
          <select class="form-control"><option>Todos los niveles</option></select>
          <select class="form-control"><option>Todos los estados</option></select>
        </div>
        <div class="ar-tabla-scroll">
          <table class="ar-tabla">
            <thead><tr><th>Item</th><th>Riesgo</th><th>Sedes</th><th>Nivel</th><th>Estado</th><th></th></tr></thead>
            <tbody>
              <tr class="ar-grupo"><td colspan="6">1.1 · Evaluación de riesgos</td></tr>
              <tr>
                <td class="ar-item">1.1.1</td>
                <td>Robo de mercancía<span class="ar-situacion">No hay buena iluminación en el patio</span></td>
                <td><span class="ar-chip ar-todas">Todas</span></td>
                <td><span class="ar-lv ar-lv2">2</span></td>
                <td><span class="ar-st ar-st-en-tratamiento">En tratamiento</span></td>
                <td><button type="button" class="btn btn-default btn-xs" data-toggle="modal" data-target="#ejemploModal">Ver</button></td>
              </tr>
              <tr>
                <td class="ar-item">1.1.2</td>
                <td>Acceso no autorizado<span class="ar-situacion">Puerta lateral sin control</span></td>
                <td><span class="ar-chip">Monterrey</span><span class="ar-chip">Torreón</span></td>
                <td><span class="ar-lv ar-lv4">4*</span></td>
                <td><span class="ar-st ar-st-vencido">Vencido</span></td>
                <td><button type="button" class="btn btn-default btn-xs">Ver</button></td>
              </tr>
              <tr>
                <td class="ar-item">1.2.1</td>
                <td>Falla de cámaras<span class="ar-situacion">Sin respaldo de grabación</span></td>
                <td><span class="ar-chip">Nuevo Laredo</span></td>
                <td><span class="ar-lv ar-lv-sin">—</span></td>
                <td><span class="ar-st ar-st-sin-evaluar">Sin evaluar</span></td>
                <td><button type="button" class="btn btn-default btn-xs">Evaluar</button></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div class="ar-grid ar-g2">
        <!-- Mapa de calor -->
        <div class="ar-card">
          <h3>Mapa de calor</h3>
          <div class="ar-sub">Probabilidad × consecuencia. Cada celda cuenta los riesgos cuyo nivel vigente cae ahí.</div>
          <div class="ar-hm">
            <div class="ar-hm-eje"></div>
            <div class="ar-hm-eje">Insignif.</div><div class="ar-hm-eje">Menor</div><div class="ar-hm-eje">Medio</div>
            <div class="ar-hm-eje">Crítico</div><div class="ar-hm-eje">Muy crít.</div><div class="ar-hm-eje">Catastr.</div>
            <div class="ar-hm-eje">Frecuente</div>
            <div class="ar-hm-celda ar-lv5">3</div><div class="ar-hm-celda ar-lv5">1</div><div class="ar-hm-celda ar-lv4">2</div>
            <div class="ar-hm-celda ar-lv3">1</div><div class="ar-hm-celda ar-lv1">0</div><div class="ar-hm-celda ar-lv1 ar-vacia">0</div>
            <div class="ar-hm-eje">Ocasional</div>
            <div class="ar-hm-celda ar-lv6">4</div><div class="ar-hm-celda ar-lv5">2</div><div class="ar-hm-celda ar-lv4">5<span class="ar-hm-delta">+2</span></div>
            <div class="ar-hm-celda ar-lv3">3</div><div class="ar-hm-celda ar-lv2">1</div><div class="ar-hm-celda ar-lv1 ar-vacia">0</div>
          </div>
        </div>

        <!-- Semáforo y avance -->
        <div class="ar-card">
          <h3>Cumplimiento por sede</h3>
          <div class="ar-sub">Verde ≥ 85 · amarillo 70–84 · rojo &lt; 70.</div>
          <p>Monterrey <span class="ar-sem ar-sem-verde">91</span></p>
          <div class="ar-barra-avance"><span style="width:91%"></span></div>
          <p>Torreón <span class="ar-sem ar-sem-amarillo">76</span></p>
          <div class="ar-barra-avance"><span style="width:76%"></span></div>
          <p>Nuevo Laredo <span class="ar-sem ar-sem-rojo">58</span></p>
          <div class="ar-barra-avance"><span style="width:58%"></span></div>
        </div>
      </div>

      <!-- Estado vacío -->
      <div class="ar-card ar-vacio"><i class="fa fa-inbox"></i>Aquí aparece el mensaje cuando no hay resultados.</div>

    </section>

    <?php $menu->renderizarFooter(); ?>
  </main>
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

<!-- Repositorios, Presentador y Vista de ESTA pantalla (en handel_api). Ejemplo:
<script src="<?php echo getHandelAPI()?>/js/repositorios/argos_riesgos_repositorio.js"></script>
<script src="<?php echo getHandelAPI()?>/js/presentadores/argos_riesgos_presentador.js"></script>
<script src="<?php echo getHandelAPI()?>/js/vistas/argos_riesgos_vista.js"></script>
La vista termina con:  var vista = new ArgosRiesgosVista(); $(document).ready(function(){ vista.inicializar(); });
En esta plantilla se usa la Vista base solo para la sesión y "Cerrar sesión". -->
<script src="js/argos.js?v=<?php echo $version; ?>"></script>
<script src="js/plantilla_vista.js?v=<?php echo $version; ?>"></script>

<img src="img/cargando.svg" id="indicador" alt="Cargando">
</body>
</html>
