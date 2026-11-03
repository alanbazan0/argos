<?php

class Aplicacion
{
    /**
     * Devuelve el usuario de la sesión o redirige a inicio_sesion.php (con ?url= para volver a la página pedida).
     * El acceso a ARGOS lo concede usuarios.permiso_argos; el Administrador (tipo 1) entra siempre.
     */
    public function iniciar()
    {
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
    
    public function tienePermiso($usuario)
    {
        return $usuario->tipoUsuarioId == 1 || $usuario->permisoARGOS == 1;
    }
}
