/** Menú de CTPAT (js/argos.js): el contador y el selector de ejercicio usan la empresa elegida en el selector de la barra. */
const fs = require('fs');
const path = require('path');
const codigo = fs.readFileSync(path.join(__dirname, '../../js/argos.js'), 'utf8');

function cargar(empresa) {
  jest.resetModules();
  window.sessionStorage.clear();
  if (empresa) window.sessionStorage.setItem('argosEmpresaId', String(empresa));
  document.body.innerHTML = `<div class="ar-sidebar"><div class="ar-seccion">Ejercicio CTPAT</div><a class="ar-nav" href="ctpat_cuestionarios.php">Cuestionarios</a></div>`;
  window.$ = window.jQuery = global.$ = global.jQuery = require('jquery');
  window.$.fn.tooltip = function () { return this; };
  window.HANDEL_API = 'https://api.test';
  const enviadas = [];
  window.$.ajax = function (o) {
    const criterios = JSON.parse(o.data.criteriosSeleccion);
    enviadas.push({ accion: o.data.accion, criterios });
    if (o.data.accion === 'consultar') {
      o.success({ mensajeError: '', valor: { enCurso: 7, ejercicios: [{ id: 7, anio: 2026, estado: 'en curso' }] } });
    } else {
      o.success({ mensajeError: '', valor: { ejercicio: { estado: 'seguimiento' }, detalle: { socios: [{ estado: 'pendiente' }, { estado: 'ok' }, { estado: 'pendiente' }] } } });
    }
  };
  window.eval(codigo);
  return new Promise(r => setTimeout(() => r(enviadas), 20));
}

describe('menú CTPAT con empresa elegida', () => {
  test('manda la empresa elegida en las dos consultas', async () => {
    const enviadas = await cargar(5);
    expect(enviadas.map(e => e.accion)).toEqual(['consultar', 'resumen']);
    enviadas.forEach(e => expect(e.criterios.empresaId).toBe(5));
    expect(enviadas[1].criterios.ejercicioId).toBe(7);
  });

  test('sin empresa elegida no manda empresaId (usa la propia del usuario)', async () => {
    const enviadas = await cargar(null);
    enviadas.forEach(e => expect(e.criterios.empresaId).toBeUndefined());
  });

  test('muestra el contador de cuestionarios por contestar', async () => {
    await cargar(5);
    expect(document.querySelector('a.ar-nav .ar-ct').textContent).toBe('2');
  });
});
