<?php
include 'php/configuracion.php';
session_start();
$url = isset($_GET['url']) ? $_GET['url'] : "";
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>ARGOS — Análisis y Gestión de Riesgos para OEA México y CTPAT | Handel</title>
<meta name="description" content="ARGOS consolida en una sola plataforma el análisis de riesgo OEA México (ISO 31010) y la Evaluación de Riesgo en 5 Pasos de CTPAT. Identifica, analiza, mitiga.">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="css/inicio_sesion.css?v=20261103">
<!--LOGIN-LIBS-START-->
  <!-- Bootstrap & librerías del login (mismas que CAVI) -->
  <link rel="stylesheet" href="<?php echo getHandelAPI()?>/lib/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo getHandelAPI()?>/lib/font-awesome-4.7.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="<?php echo getHandelAPI()?>/lib/iCheck/skins/square/blue.css">
  <link href="<?php echo getHandelAPI()?>/lib/toastr/toastr.min.css" rel="stylesheet">
  <link href="<?php echo getHandelAPI()?>/lib/sweetalert/sweetalert.css" rel="stylesheet">
<!--LOGIN-LIBS-END-->
  <link rel="shortcut icon" href="img/favicon.png"/>
</head>
<body data-aplicacionId="ARGOS" data-aplicacionVersion="html" data-url="<?php echo $url; ?>">

<!-- NAV -->
<nav>
  <div class="nav-in">
    <a href="#" class="nav-logo">
      <img src="img/logo_nav.png" alt="ARGOS — Análisis &amp; Gestión de riesgos" class="brand-img">
      <div class="nav-div"></div>
      <img src="https://handel-sce.com/wp-content/uploads/2025/10/Logo-Handel-principal.png" alt="Handel" class="nav-handel" onerror="this.style.display='none'">
    </a>
    <div class="nav-links">
      <a href="#metodologias">Metodologías</a>
      <a href="#como">Cómo funciona</a>
      <a href="#industrias">Industrias</a>
      <a href="#testimonios">Testimonios</a>
      <a href="#faq">Preguntas</a>
      <a href="#cta" class="btn-nav">Agendar demo</a>
    </div>
  </div>
</nav>

<!-- HERO -->
<section id="hero">
  <div class="hero-l">
    <svg class="hero-rings" viewBox="0 0 760 760" aria-hidden="true">
      <defs><linearGradient id="sweepGrad" x1="0" y1="0" x2="1" y2="0"><stop offset="0" stop-color="#7CC242" stop-opacity="0"/><stop offset="1" stop-color="#7CC242" stop-opacity=".16"/></linearGradient></defs>
      <circle cx="380" cy="380" r="370"/><circle cx="380" cy="380" r="290"/><circle cx="380" cy="380" r="210"/><circle cx="380" cy="380" r="130"/><circle cx="380" cy="380" r="50"/>
      <path class="sweep" d="M380 380 L380 10 A370 370 0 0 1 641.6 118.4 Z"/>
    </svg>
    <div class="hero-pain fade-up">¿Tu análisis de riesgo es <em>un documento que solo se abre antes de la auditoría?</em></div>
    <h1 class="hero-h1 fade-up est-1">
      Convierte tu análisis de riesgo en una <em>guía viva</em> para proteger tu cadena de suministro
    </h1>
    <div class="hero-tagline fade-up est-2">
      <span class="tag-step"><i class="est-3"></i>Identifica</span>
      <span class="tag-sep">→</span>
      <span class="tag-step"><i class="est-4"></i>Analiza</span>
      <span class="tag-sep">→</span>
      <span class="tag-step"><i class="est-5"></i>Mitiga</span>
    </div>
    <p class="hero-sub fade-up est-6">
      ARGOS consolida en una sola plataforma el análisis de riesgo de <strong class="est-7">OEA México (ISO 31010)</strong> y la <strong class="est-7">Evaluación de Riesgo en 5 Pasos de CTPAT</strong>: captura la información de tu empresa una sola vez, evalúa con la metodología de cada programa y da seguimiento a cada medida de mitigación hasta cerrarla.
    </p>
    <div class="btns fade-up est-8">
      <a href="#cta" class="btn-primary">Agendar una demo</a>
      <a href="#metodologias" class="btn-ghost">Ver metodologías</a>
    </div>
    <div class="hero-stats fade-up est-9">
      <div class="hst"><div class="hst-n">2<span>en</span>1</div><div class="hst-l">metodologías, una plataforma</div></div>
      <div class="hst"><div class="hst-n">5<span> pasos</span></div><div class="hst-l">CTPAT guiados</div></div>
      <div class="hst"><div class="hst-n"><span>ISO</span> 31010</div><div class="hst-l">base del análisis OEA</div></div>
      <div class="hst"><div class="hst-n">1<span>×</span></div><div class="hst-l">captura de información</div></div>
    </div>
  </div>
  <!-- LOGIN REAL (misma integración que CAVI; cambia data-aplicacionId a ARGOS) -->
  <div class="hero-r">
    <div class="panel-tag">Acceso a la plataforma</div>
    <div class="login-card">
      <div class="lc-logo est-10">
        <img class="est-11" src="img/login_logo.png" alt="ARGOS">
      </div>
      <p class="lc-title">Iniciar sesión</p>
      <p class="lc-sub">Accede a tu plataforma ARGOS</p>
      <form id="inicioSesionForm" action="#" method="post">
        <div class="fg">
          <label>Correo electrónico / Usuario</label>
          <div class="est-12">
            <input id="nombreUsuarioInput" name="nombreUsuarioInput"
                   class="fg-input est-13" placeholder="Correo electrónico o usuario"
                  >
            <span class="glyphicon glyphicon-envelope est-14"
                 ></span>
          </div>
        </div>
        <div class="fg">
          <label>Contraseña</label>
          <div class="est-12">
            <input id="contrasenaInput" name="contrasenaInput" type="password"
                   class="fg-input est-13" placeholder="••••••••"
                  >
            <span class="glyphicon glyphicon-lock est-14"
                 ></span>
          </div>
        </div>
        <div class="est-15">
          <a id="olvideContrasenaLink" href="#" class="forgot">¿Olvidaste tu contraseña?</a>
        </div>
        <button id="ingresarButton" type="submit" class="btn-login">Entrar a ARGOS</button>
      </form>
      <div class="lc-foot">¿Sin acceso? <a href="#cta">Solicita una demo</a></div>
    </div>
    <div class="powered">
      <span>Una herramienta de</span>
      <img src="https://handel-sce.com/wp-content/uploads/2025/10/Logo-Handel-principal.png" alt="Handel" onerror="this.style.display='none'">
    </div>
    <div class="est-16">
      <p class="est-17" id="fraseP"></p>
      <p class="est-18" id="autorP"></p>
    </div>
  </div>
</section>

<!-- FEATURES STRIP -->
<section id="features">
  <div class="feat-grid">
    <div class="feat-item">
      <div class="feat-icon">🔗</div>
      <div class="feat-title">Dos metodologías, una sola captura</div>
      <div class="feat-desc">Procesos, instalaciones, rutas y socios se registran una vez y alimentan OEA y CTPAT.</div>
      <div class="feat-badge">OEA + CTPAT</div>
    </div>
    <div class="feat-item">
      <div class="feat-icon">🌡️</div>
      <div class="feat-title">Matriz de riesgo automática</div>
      <div class="feat-desc">Probabilidad × consecuencia calculadas con tus criterios y clasificadas en bajo, medio y alto.</div>
    </div>
    <div class="feat-item">
      <div class="feat-icon">🛡️</div>
      <div class="feat-title">Planes de mitigación con seguimiento</div>
      <div class="feat-desc">Cada medida con responsable, fecha compromiso y evidencia de cierre.</div>
    </div>
    <div class="feat-item">
      <div class="feat-icon">🔄</div>
      <div class="feat-title">Actualización anual y por incidente</div>
      <div class="feat-desc">Recordatorios de revisión y registro de cambios para mantener el análisis vigente.</div>
      <div class="feat-badge">Requisito OEA</div>
    </div>
  </div>
</section>

<!-- FUNCIONALIDADES -->
<section id="valor">
  <div class="wrap">
    <div class="sec-ey fade-up"><span class="sec-ey-line"></span>Funcionalidades</div>
    <h2 class="sec-h2 fade-up est-19">Todo lo que piden los requisitos de análisis de riesgo, en un solo lugar</h2>
    <p class="sec-lead fade-up est-20">ARGOS está construido sobre lo que exigen el requisito 1.1 de OEA México y los Criterios Mínimos de Seguridad de CTPAT, no sobre una plantilla genérica de gestión de riesgos.</p>
    <div class="val-grid">
      <div class="val-card fade-up est-21"><span class="val-tag vt-oea">OEA</span><div class="val-icon">🧭</div><h3>Identificación por fuente de riesgo</h3><p>Ubicación de instalaciones, tipo de mercancía y país de origen, volumen, clientes, proveedores, rutas, contratación de personal, manejo de documentos y Tecnología de la Información.</p></div>
      <div class="val-card fade-up est-1"><span class="val-tag vt-oea">OEA</span><div class="val-icon">📐</div><h3>Probabilidad × consecuencia</h3><p>Escalas de valoración configuradas con los criterios de Handel. El nivel de riesgo se calcula solo y se ubica en el mapa de calor.</p></div>
      <div class="val-card fade-up est-22"><span class="val-tag vt-ctpat">CTPAT</span><div class="val-icon">🗺️</div><h3>Mapeo del flujo de carga y socios</h3><p>Representa cada etapa del movimiento de la mercancía desde el origen hasta el destino y quién interviene en ella.</p></div>
      <div class="val-card fade-up est-23"><span class="val-tag vt-ctpat">CTPAT</span><div class="val-icon">🌎</div><h3>Amenazas y vulnerabilidades</h3><p>Evalúa amenazas por país y región según tu modelo comercial, y vulnerabilidades contra los Criterios Mínimos de Seguridad de tu tipo de socio.</p></div>
      <div class="val-card fade-up est-24"><span class="val-tag vt-both">OEA + CTPAT</span><div class="val-icon">🤝</div><h3>Socios comerciales basados en riesgo</h3><p>El resultado del análisis alimenta la selección de nuevos socios y el monitoreo de los actuales, como lo pide OEA México.</p></div>
      <div class="val-card fade-up est-25"><span class="val-tag vt-both">OEA + CTPAT</span><div class="val-icon">👥</div><h3>Participación del Comité de Seguridad</h3><p>Registra quién elaboró, revisó y aprobó cada actualización: la evidencia de que el Comité participa en el análisis.</p></div>
    </div>
  </div>
</section>

<!-- METODOLOGÍAS -->
<section id="metodologias">
  <div class="wrap">
    <div class="sec-ey fade-up"><span class="sec-ey-line"></span>Metodologías</div>
    <h2 class="sec-h2 fade-up est-26">Cada programa con su metodología. Una sola plataforma para ambos.</h2>
    <p class="sec-lead fade-up est-20">OEA México y CTPAT piden lo mismo de fondo —identificar, evaluar y mitigar— pero cada uno con su propio método. ARGOS respeta ambos y te evita hacer el trabajo dos veces.</p>
    <div class="met-grid fade-up est-27">
      <div class="met-card">
        <div class="met-hd">
          <div class="met-logo"><img src="img/logo_oea.png" alt="OEA México — Operador Económico Autorizado"></div>
          <div><div class="met-name">OEA México</div><div class="met-full">Requisito 1.1 Análisis de riesgo · Metodología ISO 31010</div></div>
        </div>
        <p class="met-intro">Un proceso escrito para identificar, analizar y mitigar los riesgos de seguridad en la cadena de suministro y en las instalaciones, conforme al modelo de tu organización.</p>
        <ol class="met-flow mf-oea">
          <li><span><b>Identificación</b> de riesgos por fuente: instalaciones, mercancías, rutas, socios, personal, documentos y TI.</span></li>
          <li><span><b>Análisis</b> de probabilidad y consecuencia con escalas definidas.</span></li>
          <li><span><b>Evaluación</b> del nivel de riesgo en la matriz y priorización.</span></li>
          <li><span><b>Tratamiento</b>: medidas de seguridad con responsable y fecha.</span></li>
          <li><span><b>Revisión</b> al menos una vez al año, tras un incidente o ante cambios en la empresa.</span></li>
        </ol>
        <div class="met-note">El Comité de Seguridad participa en la elaboración y actualización del análisis.</div>
      </div>
      <div class="met-card">
        <div class="met-hd">
          <div class="met-logo"><img src="img/logo_ctpat.png" alt="CTPAT — Customs-Trade Partnership Against Terrorism"></div>
          <div><div class="met-name">CTPAT</div><div class="met-full">Evaluación general del riesgo (RA) · Five Step Risk Assessment</div></div>
        </div>
        <p class="met-intro">La evaluación general del riesgo tiene dos partes: una autoevaluación de las prácticas de seguridad contra los Criterios Mínimos de Seguridad, y la evaluación internacional del riesgo en cinco pasos.</p>
        <div class="met-part">Evaluación internacional — 5 pasos</div>
        <ol class="met-flow mf-ctpat">
          <li><span><b>Mapear</b> el flujo de la carga e identificar a los socios comerciales.</span></li>
          <li><span><b>Evaluar amenazas</b> geográficas según tu modelo comercial.</span></li>
          <li><span><b>Evaluar vulnerabilidades</b> frente a los Criterios Mínimos de Seguridad.</span></li>
          <li><span><b>Preparar un plan de acción</b> para mitigar lo encontrado.</span></li>
          <li><span><b>Documentar</b> cómo se realiza la evaluación de riesgo.</span></li>
        </ol>
        <div class="met-note">Niveles bajo, medio y alto, con el foco puesto en las áreas de mayor riesgo.</div>
      </div>
    </div>
    <div class="met-bridge fade-up est-6">
      <img src="img/argos_puente.png" alt="" aria-hidden="true">
      <div><h4>Información común, resultados por programa</h4><p>Lo que capturas para un programa —instalaciones, procesos, rutas, socios— queda disponible para el otro. Cada uno genera sus propios reportes con su propia metodología.</p></div>
    </div>
  </div>
</section>

<!-- COMPARATIVA -->
<section id="comparativa">
  <div class="comp-wrap">
    <div class="sec-ey fade-up est-28"><span class="sec-ey-line"></span>Comparativa</div>
    <h2 class="sec-h2 fade-up est-29">ARGOS vs. el análisis en hojas de cálculo</h2>
    <p class="sec-lead fade-up est-30">Tu competidor real no es otra plataforma: es el archivo de Excel que nadie quiere actualizar.</p>
    <div class="fade-up est-27">
      <table class="comp-table">
        <thead><tr><th>Criterio</th><th>Excel y documentos</th><th class="th-argos">ARGOS</th></tr></thead>
        <tbody>
          <tr><td>OEA y CTPAT</td><td><div class="td-bad">Dos archivos que se capturan por separado</div></td><td><div class="td-good">Una captura, dos metodologías</div></td></tr>
          <tr><td>Cálculo del riesgo</td><td><div class="td-bad">Fórmulas que se rompen o se copian mal</div></td><td><div class="td-good">Cálculo automático con criterios fijos</div></td></tr>
          <tr><td>Medidas de mitigación</td><td><div class="td-bad">Se quedan en papel</div></td><td><div class="td-good">Responsable, fecha y evidencia de cierre</div></td></tr>
          <tr><td>Actualización anual</td><td><div class="td-bad">Depende de que alguien se acuerde</div></td><td><div class="td-good">Recordatorios y registro de cambios</div></td></tr>
          <tr><td>Comité de Seguridad</td><td><div class="td-bad">Firmas sueltas y correos</div></td><td><div class="td-good">Participación y aprobación registradas</div></td></tr>
          <tr><td>Socios comerciales</td><td><div class="td-bad">Desconectados del análisis</div></td><td><div class="td-good">Selección y monitoreo basados en riesgo</div></td></tr>
          <tr><td>Auditorías y validaciones</td><td><div class="td-bad">Buscar versiones y anexos dispersos</div></td><td><div class="td-good">Reportes e historial listos al momento</div></td></tr>
        </tbody>
      </table>
    </div>
  </div>
</section>

<!-- BANNER: EL MITO -->
<div class="mid-banner">
  <div class="mid-banner-inner">
    <div class="myth fade-up">
      <img src="img/argos_puente.png" alt="" aria-hidden="true">
      <div>
        <h3>Argos, el guardián de los cien ojos, <em>nunca dejaba de vigilar.</em></h3>
        <p>Tu cadena de suministro tampoco debería quedarse sin vigilancia entre una auditoría y la siguiente. ARGOS mantiene tus riesgos a la vista todo el año.</p>
      </div>
    </div>
    <div class="fade-up est-31">
      <div class="mid-banner-stat"><div class="bn">365</div><div class="bl">días con tus riesgos a la vista</div></div>
      <div class="mid-banner-stat"><div class="bn">3</div><div class="bl">niveles: bajo, medio y alto</div></div>
    </div>
  </div>
</div>

<!-- CÓMO FUNCIONA -->
<section id="como">
  <div class="wrap">
    <div class="sec-ey fade-up"><span class="sec-ey-line"></span>Cómo funciona</div>
    <h2 class="sec-h2 fade-up est-19">Del perfil de tu empresa a un plan de mitigación con seguimiento</h2>
    <p class="sec-lead fade-up est-20">Cuatro etapas que siguen la lógica de ambos programas.</p>
    <div class="como-layout">
      <div class="steps fade-up est-20">
        <div class="step"><div class="step-n">1</div><div><div class="step-k">Perfil</div><div class="step-t">Describe tu operación una sola vez</div><div class="step-d">Instalaciones, procesos, mercancías, países de origen y destino, rutas y socios comerciales. Es la base de ambos análisis.</div></div></div>
        <div class="step"><div class="step-n t">2</div><div><div class="step-k">Identifica</div><div class="step-t">Riesgos, amenazas y vulnerabilidades</div><div class="step-d">Por fuente de riesgo para OEA y por etapa del flujo de la carga para CTPAT, con catálogos de referencia preparados por Handel.</div></div></div>
        <div class="step"><div class="step-n g">3</div><div><div class="step-k">Analiza</div><div class="step-t">Valora y prioriza</div><div class="step-d">Probabilidad × consecuencia para OEA, niveles bajo/medio/alto para CTPAT. ARGOS calcula, clasifica y muestra el mapa de calor.</div></div></div>
        <div class="step"><div class="step-n est-5">4</div><div><div class="step-k">Mitiga</div><div class="step-t">Plan de acción y seguimiento</div><div class="step-d">Asigna medidas, responsables y fechas. Adjunta la evidencia de cierre y programa la siguiente revisión anual.</div></div></div>
      </div>
      <div class="dash fade-up est-6" aria-label="Ejemplo ilustrativo del tablero de ARGOS">
        <div class="dash-hd"><div class="dash-t">Mapa de calor</div><div class="dash-tabs"><span class="on">OEA</span><span>CTPAT</span></div></div>
        <div class="dash-s">Ejemplo ilustrativo · Planta principal · Revisión 2026</div>
        <div class="hm" id="heatmap"></div>
        <div class="hm-lbl"><span>Consecuencia →</span><span></span></div>
        <div class="hm-leg"><span><i class="est-5"></i>Bajo</span><span><i class="est-32"></i>Medio</span><span><i class="est-33"></i>Alto</span><span class="est-34">↑ Probabilidad</span></div>
        <hr class="dash-div">
        <div class="est-35">Riesgos prioritarios</div>
        <div class="rk-row"><span class="rk-lv lv-a">Alto</span><div class="rk-txt">Contaminación de carga en patio de maniobras<small>Fuente: instalaciones · 3 medidas</small></div><span class="chip c-t">En curso</span></div>
        <div class="rk-row"><span class="rk-lv lv-a">Alto</span><div class="rk-txt">Transportista sin validación de seguridad<small>Fuente: socios comerciales · 2 medidas</small></div><span class="chip c-n">Pendiente</span></div>
        <div class="rk-row"><span class="rk-lv lv-m">Medio</span><div class="rk-txt">Acceso no autorizado a sistemas de TI<small>Fuente: tecnología · 2 medidas</small></div><span class="chip c-g">Cerrado</span></div>
      </div>
    </div>
  </div>
</section>

<!-- INDUSTRIAS -->
<section id="industrias">
  <div class="wrap">
    <div class="sec-ey fade-up"><span class="sec-ey-line"></span>¿Para quién es ARGOS?</div>
    <h2 class="sec-h2 fade-up est-19">Riesgos distintos para cada eslabón de la cadena</h2>
    <p class="sec-lead fade-up est-20">CTPAT pide considerar los requisitos específicos de tu función en la cadena de suministro. ARGOS parte del tipo de operación de tu empresa.</p>
    <div class="ind-grid">
      <div class="ind-card fade-up est-19">
        <div class="ind-img-wrap"><img class="ind-img" src="https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?w=600&q=75" alt="Manufactura" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1504328345606-18bbc8c9d7d1?w=600&q=75'"><div class="ind-img-overlay"></div><div class="ind-icon-float">🏭</div></div>
        <div class="ind-body"><div class="ind-title">Manufactura</div><div class="ind-desc">Plantas que importan insumos y exportan producto terminado, con múltiples proveedores y rutas.</div><ul class="ind-list"><li>Riesgos por proveedor y país de origen</li><li>Flujo de carga desde la línea hasta la frontera</li><li>Controles de acceso y manejo de materiales</li></ul></div>
      </div>
      <div class="ind-card fade-up est-20">
        <div class="ind-img-wrap"><img class="ind-img" src="https://images.unsplash.com/photo-1601584115197-04ecc0da31d7?w=600&q=75" alt="Transporte" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1494972308805-463bc619d34e?w=600&q=75'"><div class="ind-img-overlay"></div><div class="ind-icon-float">🚛</div></div>
        <div class="ind-body"><div class="ind-title">Transporte</div><div class="ind-desc">Empresas cuyo mayor riesgo está en ruta: paradas, cruces fronterizos y resguardo de unidades.</div><ul class="ind-list"><li>Evaluación de rutas y puntos de parada</li><li>Amenazas geográficas por corredor</li><li>Criterios de CTPAT para transportistas</li></ul></div>
      </div>
      <div class="ind-card fade-up est-27">
        <div class="ind-img-wrap"><img class="ind-img" src="https://images.unsplash.com/photo-1553413077-190dd305871c?w=600&q=75" alt="Bodegas" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?w=600&q=75'"><div class="ind-img-overlay"></div><div class="ind-icon-float">🏬</div></div>
        <div class="ind-body"><div class="ind-title">Bodegas y almacenes</div><div class="ind-desc">Recintos donde la mercancía de varios clientes se resguarda, consolida o desconsolida.</div><ul class="ind-list"><li>Seguridad física e instalaciones</li><li>Riesgos de manipulación y contaminación</li><li>Control de personal y visitantes</li></ul></div>
      </div>
      <div class="ind-card fade-up est-6">
        <div class="ind-img-wrap"><img class="ind-img" src="https://images.unsplash.com/photo-1578574577315-3fbeb0cecdc2?w=600&q=75" alt="Agencias Aduanales" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=600&q=75'"><div class="ind-img-overlay"></div><div class="ind-icon-float">📋</div></div>
        <div class="ind-body"><div class="ind-title">Agencias aduanales</div><div class="ind-desc">Agentes cuyo riesgo principal está en la información, los documentos y los clientes que representan.</div><ul class="ind-list"><li>Clasificación y manejo de documentos</li><li>Riesgos de Tecnología de la Información</li><li>Evaluación de clientes como socios</li></ul></div>
      </div>
      <div class="ind-card fade-up est-36">
        <div class="ind-img-wrap"><img class="ind-img" src="https://images.unsplash.com/photo-1500937386664-56d1dfef3854?w=600&q=75" alt="Empaques Agrícolas" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1464226184884-fa280b87c399?w=600&q=75'"><div class="ind-img-overlay"></div><div class="ind-icon-float">🥬</div></div>
        <div class="ind-body"><div class="ind-title">Empaques agrícolas</div><div class="ind-desc">Exportadores de producto fresco con temporadas de alta rotación de personal y carga.</div><ul class="ind-list"><li>Riesgos en empaque, almacenaje y despacho</li><li>Contratación de personal temporal</li><li>Integridad de contenedores y sellos</li></ul></div>
      </div>
      <div class="ind-card fade-up est-37">
        <div class="ind-img-wrap"><img class="ind-img" src="https://images.unsplash.com/photo-1494412651409-8963ce7935a7?w=600&q=75" alt="Líneas Navieras" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1519681393784-d120267933ba?w=600&q=75'"><div class="ind-img-overlay"></div><div class="ind-icon-float">🚢</div></div>
        <div class="ind-body"><div class="ind-title">Líneas navieras</div><div class="ind-desc">Operadores con cadenas de suministro extensas, donde el foco debe estar en las zonas de mayor riesgo.</div><ul class="ind-list"><li>Amenazas por puerto, país y región</li><li>Mapeo de terminales y agentes</li><li>Priorización de rutas de mayor riesgo</li></ul></div>
      </div>
    </div>
  </div>
</section>

<!-- ENTREGABLES -->
<section id="entregables">
  <div class="wrap">
    <div class="sec-ey fade-up"><span class="sec-ey-line"></span>Resultados</div>
    <h2 class="sec-h2 fade-up est-19">La evidencia que necesitas, siempre actualizada</h2>
    <p class="sec-lead fade-up est-20">ARGOS no se queda en el cálculo: genera los documentos que respaldan tu análisis ante una auditoría o validación.</p>
    <div class="rep-grid fade-up est-27">
      <div class="rep-card"><div class="rep-ic">🌡️</div><div class="rep-t">Matriz y mapa de calor</div><div class="rep-d">Todos los riesgos con su valoración, nivel y prioridad, por instalación o por proceso.</div></div>
      <div class="rep-card"><div class="rep-ic">🛡️</div><div class="rep-t">Plan de mitigación</div><div class="rep-d">Medidas, responsables, fechas y avance, con la evidencia de cada cierre.</div></div>
      <div class="rep-card"><div class="rep-ic">🌎</div><div class="rep-t">Evaluación CTPAT en 5 pasos</div><div class="rep-d">Flujo de carga, amenazas, vulnerabilidades, plan de acción y documentación del proceso.</div></div>
      <div class="rep-card"><div class="rep-ic">🗂️</div><div class="rep-t">Historial de revisiones</div><div class="rep-d">Qué cambió, cuándo, por qué y quién del Comité de Seguridad lo aprobó.</div></div>
    </div>
  </div>
</section>

<!-- TESTIMONIOS -->
<section id="testimonios">
  <div class="wrap">
    <div class="sec-ey fade-up"><span class="sec-ey-line"></span>Testimonios</div>
    <h2 class="sec-h2 fade-up est-19">Lo que dicen nuestros clientes sobre su análisis de riesgos con Handel</h2>
    <p class="sec-lead fade-up est-20">La experiencia de Handel en OEA México y CTPAT es la base de la metodología de ARGOS.</p>
    <div class="test-grid fade-up est-27">
      <div class="test-card">
        <span class="test-topic vt-ctpat">CTPAT</span>
        <div class="test-text">"Con la ayuda del equipo Handel y sus guías ahorramos tiempo para estructurar y entender el Análisis de riesgo de CTPAT, el seguimiento se facilita y la información es clara para todos."</div>
        <div class="test-footer"><div class="test-avatar est-38">H</div><div><div class="test-name">Héctor</div><div class="test-role">Coordinador de certificaciones</div></div></div>
      </div>
      <div class="test-card">
        <span class="test-topic vt-oea">OEA</span>
        <div class="test-text">"Con Handel y sus herramientas el Análisis de riesgo de OEA dejó de ser un documento con mucha dificultad para darle seguimiento para ser una guía de lo que debemos hacer."</div>
        <div class="test-footer"><div class="test-avatar est-39">A</div><div><div class="test-name">Adriana</div><div class="test-role">Directora de Operaciones</div></div></div>
      </div>
      <div class="test-card">
        <span class="test-topic vt-both">Análisis de riesgos</span>
        <div class="test-text">"Nos llevó meses entender lo que teníamos que hacer, en solo un curso de unas horas el equipo Handel nos explicó, estructuró y aclaró todas las dudas del análisis de riesgos."</div>
        <div class="test-footer"><div class="test-avatar est-40">D</div><div><div class="test-name">David</div><div class="test-role">Responsable de seguridad</div></div></div>
      </div>
    </div>
  </div>
</section>

<!-- FAQ -->
<section id="faq">
  <div class="wrap">
    <div class="sec-ey fade-up"><span class="sec-ey-line"></span>Preguntas frecuentes</div>
    <h2 class="sec-h2 fade-up est-19">Resolvemos tus dudas</h2>
    <div class="faq-layout fade-up est-22">
      <div class="faq-item">
        <div class="faq-q"><div class="faq-icon">?</div>¿Puedo usar ARGOS solo para OEA o solo para CTPAT?</div>
        <div class="faq-a">Sí. Puedes trabajar con una sola metodología. Si más adelante sumas el otro programa, la información de tu empresa ya estará capturada y solo agregas el análisis correspondiente.</div>
      </div>
      <div class="faq-item">
        <div class="faq-q"><div class="faq-icon">?</div>¿Cada cuánto debo actualizar mi análisis de riesgo?</div>
        <div class="faq-a">OEA México pide actualizarlo por lo menos una vez al año, y también después de un incidente de seguridad o cuando cambian las condiciones de la empresa. ARGOS te recuerda la revisión y guarda el historial de cada versión.</div>
      </div>
      <div class="faq-item">
        <div class="faq-q"><div class="faq-icon">?</div>¿Cómo participa el Comité de Seguridad?</div>
        <div class="faq-a">Los integrantes del Comité pueden revisar el análisis, comentar y aprobar cada actualización. Su participación queda registrada como evidencia para el programa.</div>
      </div>
      <div class="faq-item">
        <div class="faq-q"><div class="faq-icon">?</div>¿ARGOS me ayuda con la selección de socios comerciales?</div>
        <div class="faq-a">Sí. Los riesgos identificados en el análisis se convierten en criterios para seleccionar nuevos socios y monitorear a los actuales, como lo exige el requisito de OEA México.</div>
      </div>
    </div>
  </div>
</section>

<!-- CTA -->
<section id="cta">
  <div class="cta-inner">
    <div class="fade-up">
      <div class="sec-ey"><span class="sec-ey-line"></span>Contacto</div>
      <h2 class="cta-h2">¿Tu empresa está certificada en OEA México o CTPAT, o en proceso de estarlo?</h2>
      <p class="cta-sub">Agenda una demostración y te mostramos cómo se vería el análisis de riesgo de tu empresa en ARGOS: tus instalaciones, tus rutas y tus socios comerciales.</p>
      <div class="cta-btns">
        <a href="mailto:mail@handel-sce.com" class="btn-gold">Solicitar información</a>
        <a href="https://api.whatsapp.com/send/?phone=528714580814&text=Hola+Handel,+me+interesa+conocer+más+sobre+ARGOS" target="_blank" class="btn-wsp">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
          WhatsApp
        </a>
      </div>
      <div class="contact-info">
        <strong>Teléfono:</strong> +52 871 793 3069 · +52 871 458 0814 · +52 999 489 1139<br>
        <strong>Email:</strong> mail@handel-sce.com<br>
        <strong>Horario:</strong> Lunes a viernes, 8:30 a 18:00 hrs
      </div>
    </div>
    <div class="form-card fade-up est-22">
      <div class="form-card-t">Agendar una demo</div>
      <div class="form-card-s">Te mostramos ARGOS con un caso real de tu industria</div>
      <!-- Mismo endpoint de Formspree que CAVI; el asunto distingue las solicitudes de ARGOS -->
      <form id="demo-form" action="https://formspree.io/f/xwleqkew" method="POST">
        <input type="hidden" name="_subject" value="Nueva solicitud de demo ARGOS">
        <input type="hidden" name="plataforma" value="ARGOS">
        <div class="fg"><label>Nombre completo</label><input type="text" name="nombre" placeholder="Tu nombre" required></div>
        <div class="fg-row">
          <div class="fg"><label>Empresa</label><input type="text" name="empresa" placeholder="Nombre de tu empresa" required></div>
          <div class="fg"><label>Puesto</label><input type="text" name="puesto" placeholder="Tu puesto"></div>
        </div>
        <div class="fg"><label>Correo electrónico</label><input type="email" name="email" placeholder="correo@empresa.com" required></div>
        <div class="fg"><label>Teléfono</label><input type="tel" name="telefono" placeholder="+52 800 000 0000"></div>
        <div class="fg">
          <label>Análisis de riesgo de interés</label>
          <select name="certificacion">
            <option value="" disabled selected>Selecciona una opción</option>
            <option>OEA México (ISO 31010)</option><option>CTPAT (5 Step Risk Assessment)</option><option>Ambos</option><option>Aún no tengo certificación</option>
          </select>
        </div>
        <button type="submit" class="btn-submit">Agendar demo →</button>
      </form>
      <div class="form-success" id="form-success">
        <div class="fs-icon">✅</div>
        <h4>¡Solicitud enviada!</h4>
        <p>Nos pondremos en contacto contigo a la brevedad para coordinar tu demo de ARGOS.</p>
      </div>
    </div>
  </div>
</section>

<!-- FOOTER -->
<footer>
  <div class="foot-l">
    <img src="img/logo_footer.png" alt="ARGOS — Análisis &amp; Gestión de riesgos" class="foot-logo">
    <div class="foot-div"></div>
    <img src="https://handel-sce.com/wp-content/uploads/2025/10/Logo-Handel-principal.png" alt="Handel" class="foot-handel" onerror="this.style.display='none'">
  </div>
  <div class="foot-txt">©2026 Handel Consultoría. Todos los derechos reservados.</div>
  <div class="foot-chips">
    <span class="foot-chip">OEA México</span>
    <span class="foot-chip">CTPAT</span>
    <span class="foot-chip">ISO 31010</span>
  </div>
</footer>

<script src="js/inicio_sesion_landing.js?v=20261103"></script>

<!--LOGIN-SCRIPTS-START-->
<!-- Scripts del login real (mismos que CAVI) -->
<script src="<?php echo getHandelAPI()?>/lib/jquery-3.3.1/jquery.min.js"></script>
<script src="<?php echo getHandelAPI()?>/lib/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="<?php echo getHandelAPI()?>/lib/iCheck/icheck.min.js"></script>
<script src="<?php echo getHandelAPI()?>/lib/sweetalert/sweetalert.min.js"></script>
<script src="<?php echo getHandelAPI()?>/lib/toastr/toastr.min.js"></script>
<script src="<?php echo getHandelAPI()?>/lib/form-validation/jquery.validate.min.js"></script>
<script src="<?php echo getHandelAPI()?>/js/clases/configuracion.js"></script>
<script src="<?php echo getHandelAPI()?>/js/repositorios/repositorio.js"></script>
<script src="<?php echo getHandelAPI()?>/js/vistas/vista.js?v=20261028"></script>
<script src="<?php echo getHandelAPI()?>/js/repositorios/usuarios_repositorio.js"></script>
<script src="<?php echo getHandelAPI()?>/js/repositorios/frases_repositorio.js"></script>
<script src="<?php echo getHandelAPI()?>/js/presentadores/inicio_sesion_presentador.js"></script>
<script src="<?php echo getHandelAPI()?>/js/vistas/inicio_sesion_vista.js"></script>
<script src="js/inicio_sesion_login.js?v=20261103"></script>
<img class="est-41" src="img/loader.gif" id="indicador">
<!--LOGIN-SCRIPTS-END-->
</body>
</html>
