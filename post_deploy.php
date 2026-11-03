<?php
// Endpoint post-deploy: verifica que el deploy dejó los archivos críticos.
// El token se compara contra un archivo fuera del webroot, subido manualmente
// una sola vez al servidor (nunca versionado en git).

$tokenArchivo = __DIR__ . '/../migrate_token.txt';
$tokenEsperado = file_exists($tokenArchivo) ? trim(file_get_contents($tokenArchivo)) : null;

if ($tokenEsperado === null || $tokenEsperado === '' || !hash_equals($tokenEsperado, (string)($_POST['token'] ?? ''))) {
    http_response_code(403);
    exit('Token inválido');
}

$resultados = array();
$error = false;
foreach (array('index.php', 'php/configuracion.php') as $requerido) {
    if (!file_exists(__DIR__ . '/' . $requerido)) {
        $resultados[] = "FALTA $requerido";
        $error = true;
    }
}
if (!$error) {
    $resultados[] = 'OK';
}

if ($error) {
    http_response_code(500);
}
echo implode("\n", $resultados);
