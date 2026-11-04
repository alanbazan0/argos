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
- **R-00111 Acciones y bitácora:** backend, estados (R-13/14/15), publicar con enlace de evidencia, menciones y validación **ya en producción** (endpoint `ArgosAcciones.php`, 20+7 pruebas); capa JS (repositorio, presentador, vista) escrita en handel_api y **sin probar en navegador**. **Falta:** la página `acciones.php` en argos (HTML con los ids que usa `argos_acciones_vista.js`: filtros, tabla, modal de detalle, modal de nueva acción), el bloque «Acciones» y los botones Nueva acción/Publicar en el detalle del riesgo, probar en navegador, y los avisos por correo (asignación, mención, evidencia por validar) que dependen de R-00112. Suposición: un riesgo solo se marca «Cerrado» con meta definida, nivel ≥ meta y al menos una acción, todas completadas.
- **Todo el resto** (acciones, bandeja, tablero, taller, importación, revisiones, indicadores, reportes, tutoriales y el módulo CTPAT completo): ver los planes de IA 144–171 en Servixia.

## Trampas conocidas

- Ejecutar los SQL con el cliente en UTF-8 (`--default-character-set=utf8`).
- El `JsonMapper` de handel_api exige `@var tipo|null` en las propiedades del modelo si el navegador manda `null`.
- La lista temporal de cuentas con acceso está en **dos** lugares: `handel_api/php/clases/ControlAccesoArgos.php` y `argos/php/Aplicacion.php`.
- En `handel_api` otras sesiones dejan cambios sin commit: publicar solo archivos propios (`git add <archivos>`), nunca `git add -A` ni `git stash`.
