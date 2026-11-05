<?php
namespace php\clases;

use TipoUsuario;

require_once __DIR__ . "/TipoUsuario.php";

/**
 * Renderiza las piezas comunes de todas las pantallas de ARGOS (barra superior, menú lateral y pie),
 * igual que la clase Menu de saha, sivah y cavi: la página trae el esqueleto HTML y llama a estos métodos.
 *
 * Las opciones salen de $this->modulos. Una opción cuya página todavía no existe en la raíz del proyecto se
 * muestra apagada ("pronto"), así el menú puede crecer antes que las pantallas sin generar enlaces rotos.
 */
class Menu
{
    private $handelAPI;
    private $version = "0.1";

    /**
     * Los dos analisis de ARGOS son modulos independientes (una empresa puede tener uno, otro o ambos) y se cambian
     * con el selector de la barra superior. Cada modulo tiene su pagina de inicio y su menu lateral.
     * modulo => [titulo, pagina de inicio, secciones]; seccion => [ [titulo, archivo, icono Font Awesome 4, tipos de usuario que la ven (null = todos)] ]
     */
    private $modulos = array(
        "oea" => array(
            "titulo" => "OEA México",
            "inicio" => "panel.php",
            "secciones" => array(
                "Análisis OEA" => array(
                    array("Mi bandeja",           "bandeja.php",              "fa-inbox",              null),
                    array("Tablero",              "tablero.php",              "fa-th-large",           null),
                    array("Registro de riesgos",  "riesgos.php",              "fa-list-alt",           null),
                    array("Acciones",             "acciones.php",             "fa-tasks",              null),
                    array("Modo taller",          "taller.php",               "fa-pencil-square-o",    null),
                    array("Importar desde Excel", "importar.php",             "fa-file-excel-o",       null),
                ),
                "Seguimiento" => array(
                    array("Indicadores",          "indicadores.php",          "fa-line-chart",         null),
                    array("Revisiones del Comité","revisiones.php",           "fa-calendar-check-o",   null),
                    array("Tutoriales",           "tutoriales.php",           "fa-graduation-cap",     null),
                ),
                "Configuración" => array(
                    array("Calibración y catálogos", "configuracion_calibracion.php", "fa-sliders", null),
                ),
            ),
        ),
        "ctpat" => array(
            "titulo" => "CTPAT · 5 pasos",
            "inicio" => "ctpat_resumen.php",
            "secciones" => array(
                "Ejercicio CTPAT" => array(
                    array("Resumen",                  "ctpat_resumen.php",      "fa-dashboard",          null),
                    array("Evolución",                "ctpat_evolucion.php",    "fa-line-chart",         null),
                    array("1 · Cadenas y socios",     "ctpat_socios.php",       "fa-map-o",              null),
                    array("Cuestionarios",            "ctpat_cuestionarios.php","fa-envelope-o",         null),
                    array("2 · Amenazas",             "ctpat_amenazas.php",     "fa-globe",              null),
                    array("3 · Vulnerabilidades",     "ctpat_vulnerabilidades.php", "fa-search",         null),
                    array("4 · Plan de acción",       "ctpat_plan.php",         "fa-wrench",             null),
                    array("5 · Procedimiento",        "ctpat_procedimiento.php","fa-file-text-o",        null),
                ),
                "Configuración" => array(
                    array("Parámetros del análisis",  "ctpat_parametros.php",   "fa-cog",                array(TipoUsuario::ADMINISTRADOR)),
                    array("Tipos de socio y cuestionarios", "ctpat_tipos_cuestionarios.php", "fa-list-ol", array(TipoUsuario::ADMINISTRADOR)),
                    array("Acciones recomendadas",    "ctpat_acciones_recomendadas.php", "fa-check-square-o", array(TipoUsuario::ADMINISTRADOR)),
                    array("Plantillas de correo",     "ctpat_plantillas.php",   "fa-envelope",           array(TipoUsuario::ADMINISTRADOR)),
                    array("Reportes consolidados",    "ctpat_reportes.php",     "fa-paperclip",          array(TipoUsuario::ADMINISTRADOR)),
                    array("Biblioteca de amenazas",   "ctpat_biblioteca.php",   "fa-book",               array(TipoUsuario::ADMINISTRADOR)),
                    array("Guías de uso y videos",    "ctpat_guias.php",        "fa-film",               array(TipoUsuario::ADMINISTRADOR)),
                ),
            ),
        ),
    );

    private function existe($archivo)
    {
        return file_exists(dirname(__DIR__, 2) . "/" . $archivo);
    }

    public function setHandelAPI($handelAPI)
    {
        $this->handelAPI = $handelAPI;
    }

    /** Atributo data-usuario del <body>: handel_api (vista.js) lo lee para conocer al usuario de la sesión. */
    public function renderizarUsuarioJSON($usuario)
    {
        $json = "";
        if(isset($usuario))
        {
            $copia = clone $usuario;
            $copia->contrasena = "";
            $json = json_encode($copia, JSON_UNESCAPED_UNICODE);
        }
        echo "data-usuario=\"" . htmlspecialchars($json, ENT_QUOTES, 'UTF-8') . "\"";
    }

    public function renderizarBarraSuperior($usuario, $conMenu = true, $modulo = "oea")
    {
        $nombre = trim($usuario->nombre . " " . $usuario->apellido);
        $iniciales = mb_strtoupper(mb_substr($usuario->nombre, 0, 1) . mb_substr($usuario->apellido, 0, 1));
        $empresa = htmlspecialchars($usuario->empresa, ENT_QUOTES, 'UTF-8');
        $html  = "<header class='ar-topbar'>";
        if($conMenu)
            $html .= "<button type='button' class='ar-topbar-toggle' id='menuToggle' aria-label='Menú'><i class='fa fa-bars'></i></button>";
        $html .= "<a href='panel.php' class='ar-topbar-logo'><img src='img/argos-logo.png' alt='ARGOS'></a>";
        $html .= "<div class='ar-empresa'><i class='fa fa-building-o'></i> <span>$empresa</span></div>";
        if($conMenu)
            $html .= $this->renderizarSelectorModulo($modulo);
        $html .= "<div class='ar-topbar-fin'>";
        $html .= "<button type='button' class='ar-campana' id='campanaBoton' title='Pendientes'><i class='fa fa-bell-o'></i><span class='ar-contador' id='campanaContador' style='display:none'>0</span></button>";
        // Foto de perfil como en las demás apps (handel_api/php/fotos); si no carga, se quedan las iniciales.
        $foto = "";
        if(!empty($usuario->fotoPerfil))
            $foto = "<img id='imgFotoPefil3' src='" . htmlspecialchars($this->handelAPI . "/" . $usuario->fotoPerfil, ENT_QUOTES, 'UTF-8') . "?" . time() . "' alt=''>";
        $html .= "<div class='ar-usuario'><span class='ar-avatar'>" . htmlspecialchars($iniciales, ENT_QUOTES, 'UTF-8') . $foto . "</span>";
        $html .= "<span class='ar-nombre'>" . htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') . "</span>";
        $html .= "<button type='button' class='ar-salir' onclick='vista.cerrarSesion();' title='Cerrar sesión'><i class='fa fa-sign-out'></i></button></div>";
        $html .= "</div></header>";
        echo $html;
    }

    /** Selector OEA / CTPAT. El modulo cuya pagina de inicio aun no existe se muestra apagado ("próx."). */
    private function renderizarSelectorModulo($actual)
    {
        $html = "<div class='ar-modulos' role='tablist'>";
        foreach($this->modulos as $clave => $modulo)
        {
            $titulo = htmlspecialchars($modulo["titulo"], ENT_QUOTES, 'UTF-8');
            if($clave == $actual)
                $html .= "<span class='ar-mod ar-on' role='tab' aria-selected='true'>$titulo</span>";
            else if($this->existe($modulo["inicio"]))
                $html .= "<a class='ar-mod' role='tab' href='" . $modulo["inicio"] . "'>$titulo</a>";
            else
                $html .= "<span class='ar-mod ar-off' role='tab' title='En desarrollo'>$titulo<em>próx.</em></span>";
        }
        return $html . "</div>";
    }

    /** Menú lateral del modulo. $activa = nombre del archivo de la pantalla actual (por ejemplo "riesgos.php"). */
    /** Limita el menú a ciertas secciones (usuarios a los que solo se les ha liberado una parte); null = todas. */
    private $soloSecciones = null;

    public function setSoloSecciones($secciones)
    {
        $this->soloSecciones = $secciones;
    }

    /** Limita el menú a ciertas pantallas (por archivo); null = todas. Se combina con setSoloSecciones. */
    private $soloArchivos = null;

    public function setSoloArchivos($archivos)
    {
        $this->soloArchivos = $archivos;
    }

    public function renderizar($usuario, $activa = "", $modulo = "oea")
    {
        if(!isset($this->modulos[$modulo]))
            $modulo = "oea";
        $html = "";
        foreach($this->modulos[$modulo]["secciones"] as $seccion => $items)
        {
            if($this->soloSecciones !== null && !in_array($seccion, $this->soloSecciones, true))
                continue;
            $visibles = array();
            foreach($items as $item)
            {
                if($this->soloArchivos !== null && !in_array($item[1], $this->soloArchivos, true))
                    continue;
                if($item[3] === null || in_array($usuario->tipoUsuarioId, $item[3]))
                    $visibles[] = $item;
            }
            if(count($visibles) == 0)
                continue;
            $html .= "<div class='ar-seccion'>" . htmlspecialchars($seccion, ENT_QUOTES, 'UTF-8') . "</div>";
            foreach($visibles as $item)
                $html .= $this->renderizarOpcion($item[0], $item[1], $item[2], $item[1] == $activa);
        }
        echo $html;
    }

    private function renderizarOpcion($titulo, $archivo, $icono, $activa)
    {
        $titulo = htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8');
        if(!$this->existe($archivo))
            return "<span class='ar-nav ar-nav-off' title='En desarrollo'><i class='fa $icono'></i>$titulo<em>pronto</em></span>";
        $clase = $activa ? "ar-nav ar-activo" : "ar-nav";
        return "<a class='$clase' href='$archivo'><i class='fa $icono'></i>$titulo</a>";
    }

    public function renderizarFooter()
    {
        $ano = date("Y");
        echo "<footer class='ar-pie'><span>ARGOS · Análisis y gestión de riesgos · v" . $this->version . "</span>"
           . "<span>&copy; $ano <a href='http://handel-sce.net/'>Handel</a>. Todos los derechos reservados.</span></footer>";
    }
}
