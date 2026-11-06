# Pruebas de aceptación (UAT) — opciones de OEA liberadas

Opciones abiertas a todos los usuarios con permiso de ARGOS: **Mi bandeja**, **Registro de riesgos**, **Acciones** y **Calibración y catálogos**.
Todo OEA está liberado (2026-11-06). Solo CTPAT sigue restringido a alanbazan@hotmail.com.

Probar con un usuario **Coordinador**, uno **Supervisor** y uno **Usuario de sede** de una empresa cliente (activar antes su permiso de ARGOS en la pantalla Usuarios).
El Administrador puede usar «Ver como» para revisar cada rol sin cambiar de sesión (solo consulta).

## Entrada y menú
- [ ] Al entrar a ARGOS aterriza en **Mi bandeja** y el menú muestra solo: Mi bandeja, Registro de riesgos, Acciones, Calibración y catálogos.
- [ ] El selector OEA / CTPAT se ve; CTPAT aparece apagado («próx.»).
- [ ] La campana muestra el número de pendientes y abre la bandeja.
- [ ] Un usuario sin permiso de ARGOS no entra.

## Registro de riesgos
- [ ] «Nuevo riesgo»: la lista de departamentos trae solo los de su empresa; las sedes solo las que puede escribir su rol.
- [ ] El item se asigna solo (subestándar + consecutivo) y no se reutiliza.
- [ ] Evaluar un riesgo (probabilidad × consecuencia) calcula el nivel de la calibración vigente; reevaluar guarda historial.
- [ ] Filtros (subestándar, departamento, sede, nivel, fuente, estado) y vista «Mapa de calor».
- [ ] Editar la identificación deja el cambio en «Cambios registrados».
- [ ] Coordinador/Administrador: Desactivar → desaparece; «Ver desactivados» → aparece atenuado → Reactivar lo regresa con su mismo item.
- [ ] Supervisor y Usuario de sede no ven Desactivar ni Reactivar.

## Acciones
- [ ] Crear acción desde un riesgo (responsable, fecha compromiso, sedes) — solo roles con permiso.
- [ ] Publicar avance por sede (el Usuario solo en su sede, el Supervisor en las suyas, el Coordinador en todas).
- [ ] Validar / pedir más evidencia (quien no es el responsable).
- [ ] El avance de la acción y el estado del riesgo se actualizan.

## Mi bandeja
- [ ] Acciones urgentes, vencidas y menciones del usuario; marcar mención como leída.

## Solo Administrador
- [ ] Selector de empresa en la barra superior; el Registro cambia de empresa.
- [ ] Ícono de historial (reloj): detalle del riesgo, acciones, Estándares, Parámetros y pantallas de CTPAT.
- [ ] «Ver como»: lista solo usuarios con permiso de ARGOS; mientras se usa, ninguna pantalla guarda.

## Tablero (liberado 2026-11-06)

- Con un Coordinador, un Supervisor y un Usuario reales: Tablero abre desde el menú y muestra el mapa de calor, los riesgos por nivel y las acciones de su empresa.
- El selector «Revisión AAAA» lista las revisiones aprobadas (si hay) y muestra el mapa a la fecha de aprobación.
- Supervisor y Usuario no ven otras empresas; solo el Coordinador (corporativo) y el Administrador tienen el selector de empresa.

## Modo taller (liberado 2026-11-06)

- Coordinador y Supervisor: capturar varios riesgos en una sesión; cada uno queda con su item, su evaluación y (si se piden) sus acciones, y aparece en el Registro.
- Un Usuario solo puede dar de alta riesgos y evaluar en su sede; intentar otra sede da error.
- Cada riesgo creado en el taller queda en el historial (visible para el Administrador) con origen «Alta (taller)».

## Importar, Indicadores, Revisiones y Tutoriales (liberados 2026-11-06)

- **Importar desde Excel:** un Coordinador sube el archivo; se revisan los renglones, se importan solo los válidos y cada riesgo queda con origen «Importación» e historial. Un Usuario solo importa a sedes que trabaja.
- **Indicadores:** Coordinador ve todas las sedes de la empresa y puede «Guardar periodo»; Supervisor y Usuario ven solo sus sedes (en el filtro, el ranking y la tendencia) y no pueden guardar el periodo.
- **Revisiones del Comité:** todos ven las revisiones y sus exportaciones; solo Administrador y Coordinador pueden iniciar y aprobar (los demás ven «Solo un Coordinador puede aprobarla»).
- **Tutoriales:** cada tarjeta abre su video incrustado.
- **Exportar (Registro, Acciones, Revisiones):** el menú Exportar descarga el Excel y abre el reporte PDF para cualquier usuario con permiso.
