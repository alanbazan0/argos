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
        if($usuario == null || !$this->tienePermiso($usuario))
        {
            $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            header('Location: inicio_sesion.php?url=' . urlencode($url));
            exit;
        }
        return $usuario;
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
        // TEMPORAL: mientras ARGOS está en construcción solo entra esta cuenta, aunque otros tengan permiso o sean Administrador
        // (la sesión puede venir de otra app Handel). Quitar junto con el bloque equivalente de Usuarios.php en handel_api.
        if(!in_array(strtolower(trim($usuario->nombreUsuario)), array("alanbazan@hotmail.com")))
            return false;
        return $usuario->tipoUsuarioId == 1 || $usuario->permisoARGOS == 1;
    }
}
