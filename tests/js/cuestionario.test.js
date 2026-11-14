/** Cuestionario público (js/cuestionario.js): popups, marcado de preguntas faltantes y envío. */
const fs = require('fs');
const path = require('path');
const TOKEN = 'a'.repeat(40);
const codigo = fs.readFileSync(path.join(__dirname, '../../js/cuestionario.js'), 'utf8');

const datosBase = () => JSON.parse(JSON.stringify(DATOS_BASE));
const DATOS_BASE = {
  socio: 'Transfers del Norte', idioma: 'es', soloLectura: false, estado: 'pendiente', respuestas: {},
  estandares: { 1: 'Visión', 2: 'Riesgo' },
  preguntas: [
    { id: 11, estandar: 1, texto: 'P1' }, { id: 12, estandar: 1, texto: 'P2' },
    { id: 21, estandar: 2, texto: 'P3' }, { id: 22, estandar: 2, texto: 'P4' }
  ]
};

let llamadas;

function cargar(respuestaPorAccion, opciones) {
  opciones = opciones || {};
  jest.resetModules();
  document.body.innerHTML = `
    <div id="idiomaBloque"><button data-idioma="es"></button><button data-idioma="en"></button></div>
    <h1 id="titulo"></h1><p id="intro"></p><div id="mensaje"></div><div id="preguntas"></div>
    <div id="firmaBloque" hidden><h3 id="firmaTitulo"></h3><p id="firmaTexto"></p><label id="firmaNombreEt"></label><input id="firmaNombre"><label id="firmaPuestoEt"></label><input id="firmaPuesto"><button id="enviarBoton"></button></div>
    <div class="ar-publico-pie"><button id="guardarBoton"></button><span id="avanceTexto"></span><span id="estadoGuardado"></span></div>`;
  window.history.pushState({}, '', '/cuestionario.php?t=' + TOKEN);
  window.$ = window.jQuery = global.$ = global.jQuery = require('jquery');
  window.HANDEL_API = 'https://api.test';
  window.RETARDO_AUTOGUARDADO = opciones.retardo || 600000;   // por omisión no se guarda solo durante la prueba
  window.ESPERA_REINTENTO = opciones.espera || 600000;
  window.HTMLElement.prototype.scrollIntoView = jest.fn();
  window.scrollTo = jest.fn();
  llamadas = [];
  window.$.ajax = function (o) {
    llamadas.push(o.data);
    let r = respuestaPorAccion[o.data.accion];
    if (typeof r === 'function') { r = r(o.data); }
    if (r === 'fallo') { o.error({ status: 0 }, 'error', ''); } else { o.success(r); }
  };
  window.eval(codigo);
  window.$.ready;
}

function contestar(id, valor) {
  const radio = document.querySelector(`.ar-resp[data-id='${id}'] input[value='${valor}']`);
  radio.checked = true;
  window.$(radio).trigger('change');
}

const listo = () => new Promise(r => setTimeout(r, 20));

describe('cuestionario público', () => {
  test('dibuja las preguntas agrupadas por estándar', async () => {
    cargar({ obtener: { mensajeError: '', valor: datosBase() } });
    await listo();
    expect(document.querySelectorAll('.ar-pregunta')).toHaveLength(4);
    expect(document.querySelectorAll('.ar-pub-est')).toHaveLength(2);
  });

  test('al enviar con preguntas sin contestar marca las faltantes, la sección y no manda nada', async () => {
    cargar({ obtener: { mensajeError: '', valor: datosBase() } });
    await listo();
    contestar(11, 'si'); contestar(12, 'no'); contestar(21, 'na');   // falta la 22
    window.$('#enviarBoton').trigger('click');
    const faltantes = document.querySelectorAll('.ar-pregunta.ar-falta');
    expect(faltantes).toHaveLength(1);
    expect(faltantes[0].querySelector('.ar-resp').getAttribute('data-id')).toBe('22');
    const secciones = document.querySelectorAll('.ar-pub-est.ar-falta-est');
    expect(secciones).toHaveLength(1);
    expect(secciones[0].textContent).toContain('1 sin contestar');
    expect(document.getElementById('avisoVentana').textContent).toContain('Faltan 1 preguntas por contestar');
    expect(llamadas.filter(l => l.accion === 'guardar' || l.accion === 'firmar')).toHaveLength(0);
  });

  test('al cerrar el aviso baja a la primera pregunta que falta y al contestarla pierde el marcado', async () => {
    cargar({ obtener: { mensajeError: '', valor: datosBase() } });
    await listo();
    window.$('#enviarBoton').trigger('click');
    expect(document.querySelectorAll('.ar-pregunta.ar-falta')).toHaveLength(4);
    window.$('#avisoCerrar').trigger('click');
    expect(window.HTMLElement.prototype.scrollIntoView).toHaveBeenCalled();
    expect(document.getElementById('avisoVentana')).toBeNull();
    contestar(11, 'si');
    expect(document.querySelector(".ar-resp[data-id='11']").closest('.ar-pregunta').classList.contains('ar-falta')).toBe(false);
  });

  test('con todo contestado guarda y luego firma, y muestra el folio en un aviso', async () => {
    cargar({ obtener: { mensajeError: '', valor: datosBase() }, guardar: { mensajeError: '', valor: {} }, firmar: { mensajeError: '', valor: { folio: 'CTP-2026-00009' } } });
    await listo();
    [11, 12, 21, 22].forEach((id, i) => contestar(id, ['si', 'no', 'na', 'si'][i]));
    document.getElementById('firmaNombre').value = 'Eduardo Massú';
    document.getElementById('firmaPuesto').value = 'Especialista';
    window.$('#enviarBoton').trigger('click');
    const acciones = llamadas.map(l => l.accion);
    expect(acciones).toEqual(['obtener', 'guardar', 'firmar']);
    expect(JSON.parse(llamadas[1].respuestas)).toEqual({ 11: 'si', 12: 'no', 21: 'na', 22: 'si' });
    expect(document.getElementById('avisoVentana').textContent).toContain('CTP-2026-00009');
  });

  test('si la red falla muestra el aviso emergente con el detalle técnico', async () => {
    cargar({ obtener: { mensajeError: '', valor: datosBase() }, guardar: 'fallo' });
    await listo();
    contestar(11, 'si');
    window.$('#guardarBoton').trigger('click');
    const aviso = document.getElementById('avisoVentana');
    expect(aviso.className).toContain('ar-aviso-fondo');
    expect(aviso.textContent).toContain('No se pudo enviar tu información');
    expect(aviso.textContent).toContain('Detalle técnico: guardar · HTTP 0 · error');
  });

  test('un error del servidor al guardar también sale en el aviso emergente', async () => {
    cargar({ obtener: { mensajeError: '', valor: datosBase() }, guardar: { mensajeError: 'Este cuestionario ya no se puede modificar.', valor: null } });
    await listo();
    window.$('#guardarBoton').trigger('click');
    expect(document.getElementById('avisoVentana').textContent).toContain('ya no se puede modificar');
  });

  test('el aviso de guardado correcto aparece y se cierra con el botón', async () => {
    cargar({ obtener: { mensajeError: '', valor: datosBase() }, guardar: { mensajeError: '', valor: {} } });
    await listo();
    window.$('#guardarBoton').trigger('click');
    expect(document.getElementById('avisoVentana').textContent).toContain('Avance guardado.');
    window.$('#avisoCerrar').trigger('click');
    expect(document.getElementById('avisoVentana')).toBeNull();
  });

  test('la pregunta contestada se sombrea de verde (sin importar la respuesta) y las pendientes no', async () => {
    const d = datosBase(); d.respuestas = { 11: 'no' };                      // una ya guardada antes
    cargar({ obtener: { mensajeError: '', valor: d } });
    await listo();
    const caja = id => document.querySelector(`.ar-resp[data-id='${id}']`).closest('.ar-pregunta');
    expect(caja(11).classList.contains('ar-contestada')).toBe(true);
    expect(caja(12).classList.contains('ar-contestada')).toBe(false);
    contestar(12, 'na');
    expect(caja(12).classList.contains('ar-contestada')).toBe(true);
    contestar(21, 'si');
    expect(document.querySelectorAll('.ar-pregunta.ar-contestada')).toHaveLength(3);
    expect(caja(22).classList.contains('ar-contestada')).toBe(false);
  });

  describe('guardado automático', () => {
    const OK = { mensajeError: '', valor: {} };
    const guardados = () => llamadas.filter(l => l.accion === 'guardar').map(l => JSON.parse(l.respuestas));
    const esperar = ms => new Promise(r => setTimeout(r, ms));

    test('tras una pausa guarda solo las respuestas nuevas, no las 100 de golpe', async () => {
      cargar({ obtener: { mensajeError: '', valor: datosBase() }, guardar: OK }, { retardo: 15 });
      await listo();
      contestar(11, 'si'); contestar(12, 'no');
      expect(guardados()).toHaveLength(0);                                  // aún dentro de la pausa
      await esperar(60);
      expect(guardados()).toEqual([{ 11: 'si', 12: 'no' }]);
      contestar(21, 'na');
      await esperar(60);
      expect(guardados()).toEqual([{ 11: 'si', 12: 'no' }, { 21: 'na' }]);  // solo lo nuevo
      expect(document.getElementById('estadoGuardado').textContent).toContain('Guardado automáticamente');
    });

    test('varios clics seguidos mandan una sola petición', async () => {
      cargar({ obtener: { mensajeError: '', valor: datosBase() }, guardar: OK }, { retardo: 30 });
      await listo();
      contestar(11, 'si'); contestar(11, 'no'); contestar(12, 'na');
      await esperar(90);
      expect(guardados()).toEqual([{ 11: 'no', 12: 'na' }]);                // la última elección de cada pregunta
    });

    test('si falla la red avisa, conserva lo pendiente y reintenta hasta lograrlo', async () => {
      let intento = 0;
      cargar({ obtener: { mensajeError: '', valor: datosBase() }, guardar: () => (++intento < 3 ? 'fallo' : OK) }, { retardo: 10, espera: 15 });
      await listo();
      contestar(11, 'si');
      await esperar(40);
      expect(document.getElementById('estadoGuardado').textContent).toContain('reintentando');
      await esperar(200);
      expect(intento).toBe(3);
      expect(guardados().every(g => g[11] === 'si')).toBe(true);            // siempre la misma respuesta pendiente
      expect(document.getElementById('estadoGuardado').textContent).toContain('Guardado automáticamente');
    });

    test('si el servidor la rechaza muestra el aviso una vez y no reintenta', async () => {
      cargar({ obtener: { mensajeError: '', valor: datosBase() }, guardar: { mensajeError: 'Este cuestionario ya no se puede modificar.', valor: null } }, { retardo: 10, espera: 10 });
      await listo();
      contestar(11, 'si');
      await esperar(100);
      expect(guardados()).toHaveLength(1);
      expect(document.getElementById('avisoVentana').textContent).toContain('ya no se puede modificar');
    });

    test('al enviar solo manda lo que falta por guardar y luego firma', async () => {
      cargar({ obtener: { mensajeError: '', valor: datosBase() }, guardar: OK, firmar: { mensajeError: '', valor: { folio: 'CTP-1' } } }, { retardo: 10 });
      await listo();
      contestar(11, 'si'); contestar(12, 'no');
      await esperar(60);                                                    // ya se guardaron solas
      contestar(21, 'na'); contestar(22, 'si');
      document.getElementById('firmaNombre').value = 'Ana'; document.getElementById('firmaPuesto').value = 'Gerente';
      window.$('#enviarBoton').trigger('click');
      expect(guardados()).toEqual([{ 11: 'si', 12: 'no' }, { 21: 'na', 22: 'si' }]);
      expect(llamadas.map(l => l.accion).slice(-1)).toEqual(['firmar']);
    });
  });
});
