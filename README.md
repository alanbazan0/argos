# argos

Frontend web de ARGOS (análisis de riesgo OEA), servido en `argos.apps-handel.com`. Misma arquitectura que `saha`, `sivah` y `10y7-web`: páginas PHP en la raíz que cargan librerías y la capa de datos compartidas desde [`handel_api`](https://github.com/alanbazan0/handel_api) mediante `getHandelAPI()` (`php/configuracion.php`).

## Estructura

```
*.php                   páginas server-rendered
php/configuracion.php   getHandelAPI(): host de la API según el entorno (no fijar la URL a mano)
css/, js/               recursos propios de la app
post_deploy.php         endpoint post-deploy (verifica archivos críticos)
```

## Deploy

Cada push a `master` dispara `.github/workflows/deploy.yml`: sube por FTP a `/argos.apps-handel.com/`, llama a `post_deploy.php` y verifica que `index.php` no referencie `localhost`.

Secretos en GitHub: `FTP_SERVER`, `FTP_USER`, `FTP_PASS`, `APP_URL` y `MIGRATE_TOKEN`. Deploy manual: copiar `.env.deploy.example` a `.env.deploy`, completarlo y correr `bash deploy.sh`.

`.htaccess`, `.user.ini` y `php.ini` no se despliegan: la versión de PHP la administra cPanel.
