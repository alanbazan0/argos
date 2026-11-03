<?php
// TEMPORAL: diagnóstico del inicio de sesión (solo muestra datos de la sesión del propio visitante). Borrar después.
session_start();
header('Content-Type: text/plain; charset=utf-8');
echo "cookie PHPSESSID: " . (isset($_COOKIE['PHPSESSID']) ? 'si' : 'no') . "\n";
echo "cookie_domain: " . ini_get('session.cookie_domain') . "\n";
echo "save_path: " . ini_get('session.save_path') . "\n";
echo "php: " . PHP_VERSION . "\n";
$u = isset($_SESSION['usuario']) ? $_SESSION['usuario'] : null;
echo "sesion con usuario: " . ($u ? 'si' : 'no') . "\n";
if ($u) {
    echo "tipo: " . $u->tipoUsuarioId . " permisoARGOS: " . ($u->permisoARGOS ?? 'null') . "\n";
    echo "nombreUsuario: " . $u->nombreUsuario . "\n";
}
