<?php
namespace php\clases;

use TipoUsuario;

require_once __DIR__ . "/TipoUsuario.php";

/**
 * Renderiza las piezas comunes de todas las pantallas de ARGOS (barra superior, menú lateral y pie),
 * igual que la clase Menu de saha, sivah y cavi: la página trae el esqueleto HTML y llama a estos métodos.
 *
 * Las opciones salen de $this->opciones. Una opción cuya página todavía no existe en la raíz del proyecto se
 * muestra apagada ("pronto"), así el menú puede crecer antes que las pantallas sin generar enlaces rotos.
 */
class Menu
{
    private $handelAPI;
    private $version = "0.1";

    /** sección => [ [titulo, archivo, ícono Font Awesome 4, tipos de usuario que la ven (null = todos)] ] */
    private $opciones = array(
        "Análisis" => array(
            array("Mi bandeja",           "bandeja.php",              "fa-inbox",              null),
            array("Tablero",              "tablero.php",              "fa-th-large",           null),
            array("Registro de riesgos",  "riesgos.php",              "fa-list-alt",           null),
            array("Acciones",             "acciones.php",             "fa-tasks",              null),
            array("Modo taller",          "taller.php",               "fa-pencil-square-o",    null),
        ),
        "Seguimiento" => array(
            array("Indicadores",          "indicadores.php",          "fa-line-chart",         null),
            array("Revisiones del Comité","revisiones.php",           "fa-calendar-check-o",   null),
            array("Tutoriales",           "tutoriales.php",           "fa-graduation-cap",     null),
        ),
        "Configuración" => array(
            array("Calibración",          "configuracion_calibracion.php", "fa-sliders",       null),
            array("Estándares",           "configuracion_estandares.php",  "fa-sitemap",       array(TipoUsuario::ADMINISTRADOR)),
        ),
    );

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

    public function renderizarBarraSuperior($usuario, $conMenu = true)
    {
        $nombre = trim($usuario->nombre . " " . $usuario->apellido);
        $iniciales = mb_strtoupper(mb_substr($usuario->nombre, 0, 1) . mb_substr($usuario->apellido, 0, 1));
        $empresa = htmlspecialchars($usuario->empresa, ENT_QUOTES, 'UTF-8');
        $html  = "<header class='ar-topbar'>";
        if($conMenu)
            $html .= "<button type='button' class='ar-topbar-toggle' id='menuToggle' aria-label='Menú'><i class='fa fa-bars'></i></button>";
        $html .= "<a href='panel.php' class='ar-topbar-logo'><img src='img/argos-logo.png' alt='ARGOS'></a>";
        $html .= "<div class='ar-empresa'><i class='fa fa-building-o'></i> <span>$empresa</span></div>";
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

    /** Menú lateral. $activa = nombre del archivo de la pantalla actual (por ejemplo "riesgos.php"). */
    public function renderizar($usuario, $activa = "")
    {
        $html = "";
        foreach($this->opciones as $seccion => $items)
        {
            $visibles = array();
            foreach($items as $item)
            {
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
        if(!file_exists(dirname(__DIR__, 2) . "/" . $archivo))
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
