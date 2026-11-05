// «Ver como» (solo Administrador Handel, solo consulta), como en la maqueta: «Ver como: [usuario]» en la barra superior.
// La lista son los usuarios de la empresa elegida (selector de empresa de la barra superior; por defecto, la del Administrador).
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

  document.addEventListener('DOMContentLoaded', function () {
    var detener = document.getElementById('verComoDetener');
    if (detener) {
      detener.addEventListener('click', function () { llamar({ accion: 'detener' }, function () { window.location.href = 'panel.php'; }); });
    }
    var caja = document.getElementById('verComoCaja');
    if (!caja) { return; }
    caja.innerHTML = '<label for="verComoSelect">Ver como:</label><select id="verComoSelect" title="Solo consulta"><option value="">Cargando…</option></select>';
    var select = document.getElementById('verComoSelect');

    function empresaElegida(estado) {
      var elegida = null;
      try { elegida = window.sessionStorage.getItem('argosEmpresaId'); } catch (e) {}
      if (estado.activo) { return estado.usuario.empresaId; }   // viendo como otro: su empresa
      if (elegida) { return elegida; }
      try { return JSON.parse(document.body.getAttribute('data-usuario')).empresaId; } catch (e) { return null; }
    }

    function cargar() {
      llamar({ accion: 'estado' }, function (e) {
        if (e.mensajeError) { select.innerHTML = '<option value="">' + esc(e.mensajeError) + '</option>'; return; }
        var estado = e.valor;
        llamar({ accion: 'consultarUsuarios', empresaId: empresaElegida(estado) }, function (r) {
          if (r.mensajeError) { select.innerHTML = '<option value="">' + esc(r.mensajeError) + '</option>'; return; }
          var html = '<option value="">Yo (Administrador)</option>';
          r.valor.forEach(function (u) {
            html += '<option value="' + u.id + '"' + (estado.activo && estado.usuario.id == u.id ? ' selected' : '') + '>' + esc(u.nombre + ' · ' + u.tipo + (u.sede ? ' · ' + u.sede : '')) + '</option>';
          });
          select.innerHTML = html;
        });
      });
    }
    cargar();

    select.addEventListener('change', function () {
      var datos = select.value === '' ? { accion: 'detener' } : { accion: 'iniciar', usuarioId: select.value };
      llamar(datos, function (r) {
        if (r.mensajeError) { alert(r.mensajeError); cargar(); return; }
        window.location.href = 'panel.php';
      });
    });
    // Si el Administrador cambia de empresa en la barra superior, la lista de usuarios sigue a esa empresa
    document.addEventListener('change', function (ev) { if (ev.target && ev.target.id === 'empresaSelect') { cargar(); } });
  });
})();
