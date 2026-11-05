// «Ver como» (solo Administrador Handel, solo consulta): elegir empresa y usuario para ver ARGOS como esa persona.
// JavaScript sin dependencias: se carga desde el pie de cada pantalla (Menu::renderizarFooter). El servidor valida todo otra vez.
(function () {
  var script = document.currentScript;
  var API = script ? script.getAttribute('data-api') : '';
  var URL = API + '/php/repositorios/ArgosVerComo.php';

  function llamar(datos, ok) {
    var cuerpo = Object.keys(datos).map(function (k) { return encodeURIComponent(k) + '=' + encodeURIComponent(datos[k]); }).join('&');
    var xhr = new XMLHttpRequest();
    xhr.open('POST', URL, true);
    xhr.withCredentials = true;
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function () { try { ok(JSON.parse(xhr.responseText)); } catch (e) { ok({ mensajeError: 'Respuesta inesperada del servidor.' }); } };
    xhr.onerror = function () { ok({ mensajeError: 'No se pudo conectar con el servidor.' }); };
    xhr.send(cuerpo);
  }
  function esc(t) { var d = document.createElement('div'); d.textContent = t == null ? '' : t; return d.innerHTML; }
  function opciones(filas, texto) { return filas.map(function (f) { return '<option value="' + f.id + '">' + esc(texto(f)) + '</option>'; }).join(''); }

  document.addEventListener('DOMContentLoaded', function () {
    var detener = document.getElementById('verComoDetener');
    if (detener) {
      detener.addEventListener('click', function () { llamar({ accion: 'detener' }, function () { window.location.href = 'panel.php'; }); });
    }
    var abrir = document.getElementById('verComoAbrir');
    var caja = document.getElementById('verComoCaja');
    if (!abrir || !caja) { return; }
    abrir.addEventListener('click', function () {
      if (document.getElementById('verComoPanel')) { return; }
      var panel = document.createElement('div');
      panel.id = 'verComoPanel';
      panel.className = 'ar-vercomo-panel';
      panel.innerHTML = '<label>Empresa <select id="verComoEmpresa"><option>Cargando…</option></select></label>' +
        '<label>Usuario <select id="verComoUsuario"><option value="">Elige una empresa</option></select></label>' +
        '<div class="ar-vercomo-nota">Solo consulta: no se puede guardar nada mientras ves como otro usuario.</div>' +
        '<div class="ar-vercomo-acc"><button type="button" class="btn btn-success btn-sm" id="verComoIr" disabled>Ver como este usuario</button> ' +
        '<button type="button" class="btn btn-default btn-sm" id="verComoCerrar">Cancelar</button></div>';
      caja.appendChild(panel);
      var selEmpresa = document.getElementById('verComoEmpresa'), selUsuario = document.getElementById('verComoUsuario'), ir = document.getElementById('verComoIr');
      document.getElementById('verComoCerrar').addEventListener('click', function () { caja.removeChild(panel); });
      function cargarUsuarios() {
        selUsuario.innerHTML = '<option value="">Cargando…</option>'; ir.disabled = true;
        llamar({ accion: 'consultarUsuarios', empresaId: selEmpresa.value }, function (r) {
          if (r.mensajeError) { selUsuario.innerHTML = '<option value="">' + esc(r.mensajeError) + '</option>'; return; }
          selUsuario.innerHTML = r.valor.length ? opciones(r.valor, function (u) { return u.nombre + ' · ' + u.tipo + (u.sede ? ' · ' + u.sede : ''); }) : '<option value="">Sin usuarios activos</option>';
          ir.disabled = r.valor.length === 0;
        });
      }
      llamar({ accion: 'consultarEmpresas' }, function (r) {
        if (r.mensajeError) { selEmpresa.innerHTML = '<option>' + esc(r.mensajeError) + '</option>'; return; }
        selEmpresa.innerHTML = opciones(r.valor, function (e) { return e.nombre; });
        cargarUsuarios();
      });
      selEmpresa.addEventListener('change', cargarUsuarios);
      ir.addEventListener('click', function () {
        llamar({ accion: 'iniciar', usuarioId: selUsuario.value }, function (r) {
          if (r.mensajeError) { alert(r.mensajeError); return; }
          window.location.href = 'panel.php';
        });
      });
    });
  });
})();
