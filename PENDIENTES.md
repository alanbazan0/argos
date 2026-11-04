# ARGOS · pendientes y decisiones abiertas

Documento vivo: se actualiza en cada corte. Lo hecho está en el README y en los planes de Servixia (C-00742); aquí va **lo que falta o está a medias** y lo que depende de una decisión.

## Decisiones abiertas (especificación OEA, sección 9)

| # | Pregunta | Estado en el código |
|---|---|---|
| 1 | ¿Existe en apps-handel el rol «Administrador Händel» o se crea un permiso «Administrador ARGOS»? | Se usa el tipo de usuario Administrador (1). Si se crea un permiso aparte, cambiar `ControlAccesoArgos::esAdministrador` y `ArgosRiesgosRepositorio::TIPO_ADMINISTRADOR`. |
| 2 | ¿El consultor Händel entra a varias empresas con un solo usuario? | No hay selector de empresa; el Administrador trabaja en la suya. Pendiente con la barra superior completa. |
| 3 | Sede nueva en una empresa: ¿entra a los riesgos «Todas las sedes»? | Hoy `todas_sedes` copia las sedes al dar de alta; una sede nueva **no** se agrega sola. Falta implementar la herencia (evaluación general, sin seguimientos). |
| 4 | ¿El servidor tiene tareas programadas y correo? | Sin confirmar. Bloquea bandeja/avisos y los correos de CTPAT. |
| 5 | ¿Avisar «posible duplicado» al capturar un riesgo? | No implementado (propuesta: solo aviso). |
| 6 | Pesos de indicadores y 30 días de «sin actualizar» | Parámetros ya cargados en `arg_parametro`; validar con un cliente piloto. |
| 7 | ¿El PDF lleva logo del cliente? | Pendiente con reportes. |
| 8 | Retención de bitácora e historial | Sin borrado (se cumple: solo INSERT). |
| 9 | Migración de los Excel actuales (CMX 2024-2025) | Pendiente con importación. |
| — | **Sedes del Supervisor**: no existe «sedes asignadas» en apps-handel (cada usuario tiene una sola sede) | Suposición: su sede + las de su personal a cargo (`supervisor1/2/3_id`). Único lugar a cambiar: `ArgosRiesgosRepositorio::sedesDeEscritura`. |

## Pendiente por requerimiento

- **R-00091 Base:** `sistema_argos` por empresa (pestaña en el catálogo de empresas); permiso/alta del Administrador ARGOS (decisión 1); selector de empresa en la barra superior.
- **R-00107 Landing:** «Agendar demo» sigue enviando a Formspree (servicio externo); falta endpoint propio con correo; confirmar si se recorta CTPAT de los textos.
- **R-00108 Configuración:** solo lectura; falta editar nombres, videos, parámetros y guardar **nuevas versiones de calibración** (borrador → versión, con el aviso de cuántos riesgos cambian de nivel).
- **R-00109 Registro:** exportar a Excel; vista de mapa de calor; estados Cerrado/Vencido/En tratamiento (dependen de acciones); aviso de duplicados; agregar una sede nueva a «todas las sedes».
- **R-00110 Evaluación:** hecha (nivel en servidor, por sede o mismo nivel, meta, tratamiento, propuesta de revisar acciones). Falta: el **aviso por correo al responsable** cuando el nivel sube (depende de acciones y de la bandeja, R-00111/R-00112); el motivo «Revisión anual AAAA» (lo pone el flujo de revisiones); mini mapa de calor con la trayectoria por sede en el detalle; la vista previa del nivel en pantalla es aproximada cuando una sede ya tiene una evaluación propia más vieja que la general (el servidor siempre manda); fecha compromiso sugerida de la primera acción (R-12, con acciones).
- **R-00111 Acciones y bitácora:** hecho y probado en navegador (página `acciones.php`, alta con fecha sugerida y justificación, seguimiento por sede, publicar con enlace de evidencia, menciones, validar / pedir más evidencia, bloque de acciones en el detalle del riesgo). Falta: **avisos por correo** (asignación, mención, evidencia por validar; dependen de R-00112 y de la decisión 4); selector de ejercicio de revisión en el filtro; «Mis sedes» a la vez para el Supervisor (hoy elige sedes con casillas). Suposición: un riesgo solo se marca «Cerrado» con meta definida, nivel ≥ meta y al menos una acción, todas completadas.
- **R-00112 Mi bandeja y avisos:** hecha la **pantalla de inicio** (`bandeja.php`, `panel.php` redirige a ella), la campana con el total en todas las pantallas, acciones por urgencia (vencida, vence en 30 días, sin actualizar, en tiempo), evidencia por validar, menciones (marcar leída) y riesgos por evaluar en las sedes del usuario. **Falta:** los **correos** (asignación, mención, evidencia por validar, 15 días antes y el día del vencimiento, resumen semanal del lunes) y la tarea programada diaria/semanal (decisión 4: confirmar que el servidor tiene cron y correo); la tabla `arg_notificacion` existe pero aún no se usa; el aviso de «faltan 30 días para la revisión anual» (con revisiones). Suposición: a los Coordinadores/Administrador la bandeja solo les muestra acciones donde son responsable o apoyo; a Usuario y Supervisor, además, las de sus sedes.
- **R-00113 Tablero:** hecho (mapa de calor 5×6 con filtro de sede y periodo, comparación con +n/−n por celda, cambios de nivel subió/bajó/nuevo/evaluado, acciones por responsable). **Falta:** selector de periodo «Revisión AAAA» (depende de revisiones del Comité, R-00115; el servidor ya soporta cualquier fecha), gráfica de evolución por revisión, botón Exportar (reportes). Nota: «hoy» lo define el servidor; si el servidor y el navegador están en zonas horarias distintas, las fechas por omisión pueden diferir un día.
- **R-00116 Indicadores:** hecho (fórmulas R-25..R-28 en `ArgosIndicadores`, parámetros desde `arg_parametro`, semáforo por sede y por usuario, ranking con empates, tendencia mensual, botón «Guardar periodo» → `arg_kpi_mensual`). **Falta:** (1) cierre automático mensual por cron (decisión #4: confirmar si el servidor tiene cron); hoy la foto solo se guarda a mano; (2) los periodos pasados salen únicamente de la foto guardada (el avance de las acciones no tiene historial, no se puede reconstruir); (3) pantalla para editar los parámetros (pesos, restas, cortes) — se editan en `arg_parametro`; (4) gráfica de tendencia (hoy es lista con semáforo); (5) los usuarios sin sede o Supervisor ven todas las sedes de la empresa (como el tablero), pendiente confirmar el alcance.
- **R-00118 Tutoriales:** hecho (tarjetas por estándar y subestándar, buscador, reproductor incrustado youtube-nocookie, solo YouTube validado en servidor con `ArgosVideo`). **Falta:** (1) enlace «▶ Ver tutorial» dentro del alta de riesgo cuando el subestándar tiene video (el servidor ya entrega `video_id`); (2) capturar los enlaces de video (hoy `video_url` está vacío en todos; se cargarán desde la edición de estándares, R-00108, o por SQL); (3) tarjeta de introducción por estándar solo aparece si el estándar tiene video.
- **Todo el resto** (acciones, bandeja, tablero, taller, importación, revisiones, indicadores, reportes, tutoriales y el módulo CTPAT completo): ver los planes de IA 144–171 en Servixia.

## Trampas conocidas

- Ejecutar los SQL con el cliente en UTF-8 (`--default-character-set=utf8`).
- El `JsonMapper` de handel_api exige `@var tipo|null` en las propiedades del modelo si el navegador manda `null`.
- La lista temporal de cuentas con acceso está en **dos** lugares: `handel_api/php/clases/ControlAccesoArgos.php` y `argos/php/Aplicacion.php`.
- En `handel_api` otras sesiones dejan cambios sin commit: publicar solo archivos propios (`git add <archivos>`), nunca `git add -A` ni `git stash`.
