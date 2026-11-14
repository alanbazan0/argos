# argos

Frontend web de **ARGOS** (análisis y gestión de riesgos: módulo OEA México con ISO 31010 y, en una fase posterior, CTPAT), servido en https://argos.apps-handel.com. Misma arquitectura que `saha`, `sivah`, `cavi` y `10y7-web`: páginas PHP en la raíz que cargan librerías, capa de datos y lógica de pantalla desde [`handel_api`](https://github.com/alanbazan0/handel_api) mediante `getHandelAPI()`.

## Arquitectura

| Capa | Dónde vive |
|---|---|
| Página (esqueleto HTML + armado de la sesión y del menú) | este repo, `*.php` en la raíz |
| Estilos y script del armazón | este repo, `css/argos.css` y `js/argos.js` |
| Vista, Presentador y Repositorio JS de cada pantalla (patrón MVP) | `handel_api/js/vistas`, `js/presentadores`, `js/repositorios` |
| Endpoints, repositorios PHP y modelos | `handel_api/php/` |
| Base de datos | tablas `arg_*` (OEA) y `ctpat_*` (CTPAT), con migraciones SQL idempotentes en `handel_api/migraciones/` |
| Librerías de terceros (Bootstrap 3, jQuery, Font Awesome 4.7, DataTables, toastr, SweetAlert…) | `handel_api/lib/` |

Convenciones de código (las mismas que el módulo de Sellos de `handel_api`; ante la duda, copiar el patrón de ahí):

- **PHP:** repositorios que extienden `ArgosBaseRepositorio` (sentencias preparadas con `filas()`/`ejecutar()`, ids con `siguienteId()` dentro de una transacción, alcance de empresa y sede con `resolverAlcance()`: la empresa y las sedes salen de la sesión, nunca del navegador). Endpoints con el esqueleto de `ArgosConfiguracion.php`: sesión de servidor, `ControlAccesoArgos` (permiso y lista de orígenes), `Cache-Control: no-store`, escrituras solo por POST, `catch (\Throwable)` con `error_log` y mensaje claro, `CodigoError::SESION_CADUCADA`.
- **Base de datos:** migraciones idempotentes en `handel_api/migraciones/`, sin `AUTO_INCREMENT`, evaluaciones y bitácora solo con INSERT; ejecutarlas con el cliente en UTF-8.
- **JS:** Repositorio (extiende `ArgosBaseRepositorio`), Presentador y Vista por pantalla en `handel_api/js`; la Vista extiende `Vista`, escapa el texto que inserta en el HTML y pasa el `codigoError` a `mostrarMensajeError`.
- **Pruebas:** unitarias para las reglas puras (`tests/Unit`), de integración para repositorios contra la base **local** (`tests/Integration`) y de endpoints (`tests/Endpoints`), creadas junto con el código.

Reglas:

- **Si falta una librería, se agrega en `handel_api/lib/` junto con las demás**, nunca dentro de este repo (aquí no hay `lib/`, `vendor/` ni AdminLTE).
- Las rutas a `handel_api` siempre con `getHandelAPI()` (`php/configuracion.php`); nunca una URL fija.
- Sin estilos ni scripts dentro del HTML: los estilos van en `css/argos.css` y la lógica en la Vista y el Presentador.
- Al modificar un script o estilo, subir el `?v=` en la página que lo carga (caché del navegador).
- Cualquier cambio en `handel_api` afecta a todas las apps: correr su suite de pruebas contra la base **local** antes de publicar, y ejecutar las migraciones en producción **antes** de publicar el código que las usa.

## Estructura

```
index.php               redirige a panel.php
inicio_sesion.php       landing + inicio de sesión (Vista y Presentador de handel_api)
panel.php               inicio del módulo OEA (hoy un panel vacío)
ctpat_resumen.php       inicio del módulo CTPAT (provisional)
plantilla.php           plantilla de pantalla y guía de estilos (solo la ve quien puede ver el menú)
php/configuracion.php   getHandelAPI(): host de la API según el entorno
php/Aplicacion.php      sesión, permiso de acceso a ARGOS y liberación gradual del menú
php/clases/Menu.php     barra superior, menú lateral y pie (mismas piezas que Menu de saha/sivah/cavi)
php/clases/TipoUsuario.php   niveles de usuario de apps-handel
css/argos.css           identidad visual y componentes de las pantallas (prefijo ar-)
css/inicio_sesion.css   estilos de la landing
js/argos.js             comportamiento del armazón (menú adaptable, ayudas)
js/inicio_sesion_landing.js   interacciones de la landing
img/                    logotipos y recursos propios
post_deploy.php         endpoint post-deploy (verifica archivos críticos)
```

## Cómo crear una pantalla nueva

1. Copiar `plantilla.php` con el nombre de la pantalla (por ejemplo `riesgos.php`) y cambiar `$titulo` y `$archivo`.
1b. Definir `$modulo` (`oea` o `ctpat`): decide el menú lateral y la opción activa del selector OEA / CTPAT de la barra superior.
2. Reemplazar el contenido de `<section class="ar-main-contenido">` usando los componentes de `css/argos.css` (la plantilla los muestra todos: tarjetas e indicadores, tabla con filtros, niveles de riesgo 1 a 6, estados, mapa de calor, semáforo, ventana emergente, estado vacío). Si hace falta un estilo nuevo, se agrega a `css/argos.css` con el prefijo `ar-`.
3. Crear en `handel_api` el endpoint PHP, el Repositorio, el Presentador y la Vista JS de la pantalla, y cargarlos al final de la página (el bloque comentado de `plantilla.php` muestra cómo). La Vista termina con `var vista = new MiVista(); $(document).ready(function(){ vista.inicializar(); });`.
4. Dar de alta la opción en el arreglo `$modulos` de `php/clases/Menu.php` (módulo, sección, título, archivo, ícono y tipos de usuario que la ven) (título, archivo, ícono y tipos de usuario que la ven). Las opciones cuya página aún no existe se muestran apagadas con la etiqueta «pronto».
5. Quitar la restricción `puedeVerMenu` de la página cuando la pantalla se libere.

## Acceso y liberación gradual

- Para entrar a ARGOS se necesita el permiso **«Acceso a ARGOS»** (`usuarios.permiso_argos`, se asigna en la pantalla de Usuarios) y que la empresa tenga habilitado el sistema; el Administrador (tipo 1) entra sin el permiso.
- Mientras el menú esté en desarrollo, **solo ve el menú** (y la plantilla) la lista de cuentas de `Aplicacion::puedeVerMenu()`; los demás usuarios con permiso entran al panel sin menú. Para liberar a más personas se agrega su correo a esa lista, y al abrirlo a todos se quita la regla.
- La sesión se comparte con `api.apps-handel.com` (cookie para `.apps-handel.com` y sesiones en `/tmp`); `Aplicacion::compartirSesion()` lo fija desde el código porque el `.user.ini` del servidor no lo trae.

## Deploy

Cada push a `master` dispara `.github/workflows/deploy.yml`: sube por FTP a `/argos.apps-handel.com/`, llama a `post_deploy.php` y verifica que `inicio_sesion.php` no referencie `localhost`. El subdominio debe tener PHP 8.5 asignado en cPanel (MultiPHP Manager).

Secretos en GitHub: `FTP_SERVER`, `FTP_USER`, `FTP_PASS`, `APP_URL` y `MIGRATE_TOKEN`. Deploy manual: copiar `.env.deploy.example` a `.env.deploy`, completarlo y correr `bash deploy.sh`.

`.htaccess`, `.user.ini` y `php.ini` no se despliegan: la versión de PHP y su configuración las administra cPanel.

## Pruebas de JavaScript

`tests/js/` (Jest + jsdom, `npm install` y luego `npm test`): cubren el comportamiento de navegador que PHPUnit no alcanza. Hoy: el cuestionario público de CTPAT (`cuestionario.test.js`: popups de aviso, marcado de preguntas faltantes, guardar y firmar) y el contador del menú de CTPAT con la empresa elegida (`menu_ctpat.test.js`). `package.json`, `node_modules/` y `tests/` no se despliegan. Las pruebas de PHP viven en `handel_api` (PHPUnit).

## Reglas que ya nos costaron un error

- **Empresa elegida:** toda llamada a `handel_api` que dependa de la empresa debe llevar el `empresaId` del selector de la barra (`sessionStorage.argosEmpresaId`). Los repositorios JS que heredan de `ArgosBaseRepositorio` lo agregan solos a `criteriosSeleccion`/`criterios`/`datos`; las llamadas `$.ajax` directas (menú de CTPAT en `js/argos.js`, vista previa y plantilla de la importación) lo agregan a mano. Sin eso, un Administrador ve los datos de su propia empresa (Handel) con otra elegida.
- **Cuestionario público (`cuestionario.php`):** los avisos salen en ventana emergente (`aviso()`), con «Detalle técnico» si falla la red (`HTTP 0` = el navegador no recibió respuesta). Antes de enviar, `marcarFaltantes()` marca en rojo las preguntas y secciones sin contestar. La página no se cachea (`Cache-Control: no-store`) y muestra su versión junto al avance.

## Desarrollo local

`php -d short_open_tag=On -S localhost:PORT` desde esta carpeta. Con `localhost`, `getHandelAPI()` apunta a `http://localhost:7500/handel_api` (la copia local de `handel_api`). Las fuentes de Font Awesome pueden marcar un error de CORS en local; en producción no ocurre.
