# Pruebas de aceptación (UAT) — opciones de OEA liberadas

Opciones abiertas a todos los usuarios con permiso de ARGOS: **Mi bandeja**, **Registro de riesgos**, **Acciones** y **Calibración y catálogos**.
Todo lo demás (Taller, Importar, Indicadores, Revisiones, Tutoriales y CTPAT) sigue solo para alanbazan@hotmail.com.

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
