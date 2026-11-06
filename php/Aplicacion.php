<?php

class Aplicacion
{
    /**
     * Devuelve el usuario de la sesión o redirige a inicio_sesion.php (con ?url= para volver a la página pedida).
     * El acceso a ARGOS lo concede usuarios.permiso_argos; el Administrador (tipo 1) entra siempre.
     */
    public function iniciar()
    {
        self::compartirSesion();
        session_start();
        $usuario = isset($_SESSION['usuario']) ? $_SESSION['usuario'] : null;
        // «Ver como»: el Administrador puede ver ARGOS como otro usuario (solo consulta; lo elige en la barra superior).
        if(self::esAdministradorReal($usuario) && isset($_SESSION['argosVerComo']) && is_object($_SESSION['argosVerComo']))
        {
            $usuario = clone $_SESSION['argosVerComo'];
            $usuario->verComo = true;
        }
        if($usuario == null || !$this->tienePermiso($usuario))
        {
            $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            header('Location: inicio_sesion.php?url=' . urlencode($url));
            exit;
        }
        return $usuario;
    }
    
    /** Sesión real de un Administrador Handel (la única que puede usar «Ver como»). */
    public static function esAdministradorReal($usuario)
    {
        return is_object($usuario) && isset($usuario->tipoUsuarioId) && (int)$usuario->tipoUsuarioId === 1;
    }

    /**
     * TEMPORAL: el menú de ARGOS está en desarrollo y se libera de forma parcial; por ahora solo lo ve esta cuenta.
     * Los demás usuarios con permiso entran al panel pero sin menú. Ampliar la lista (o quitar la regla) al liberarlo.
     */
    public function puedeVerMenu($usuario)
    {
        return in_array(strtolower(trim($usuario->nombreUsuario)), array("alanbazan@hotmail.com"));
    }
    
    /**
     * Opciones ya liberadas para todos los usuarios con permiso (por ahora solo Configuración: calibración, estándares y parámetros).
     * El resto del menú sigue en puedeVerMenu y se libera poco a poco.
     */
    public function puedeVerConfiguracion($usuario)
    {
        return $this->tienePermiso($usuario);
    }

    /** Registro de riesgos (R-00109) liberado para todos los usuarios con permiso. */
    public function puedeVerRegistro($usuario)
    {
        return $this->tienePermiso($usuario);
    }

    /** Pantallas liberadas para todos con permiso (el resto del menú sigue en puedeVerMenu). */
    public static function archivosLiberados()
    {
        return array("bandeja.php", "tablero.php", "riesgos.php", "taller.php", "acciones.php", "configuracion_calibracion.php");
    }

    /**
     * El login se hace contra handel_api (api.apps-handel.com), que guarda la sesión en /tmp con cookie para todo
     * .apps-handel.com. Argos debe leer la misma sesión; si el .user.ini del servidor no lo trae, se fija aquí.
     * Solo en producción (en local el dominio no coincide).
     */
    public static function compartirSesion()
    {
        $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
        if(substr($host, -15) === 'apps-handel.com')
        {
            ini_set('session.cookie_domain', '.apps-handel.com');
            ini_set('session.save_path', '/tmp');
        }
    }
    
    public function tienePermiso($usuario)
    {
        return $usuario->tipoUsuarioId == 1 || $usuario->permisoARGOS == 1;
    }
}
