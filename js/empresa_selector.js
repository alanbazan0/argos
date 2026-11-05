// Selector de empresa de la barra superior (Administrador, Coordinador y Supervisor; como en la maqueta). El nombre de la empresa
// pasa a ser una lista con las empresas a las que el usuario tiene acceso (el servidor valida cada consulta). La elección se guarda
// en sessionStorage ('argosEmpresaId'); las pantallas que dependen de la empresa la leen y se actualizan con
// window.argosAlCambiarEmpresa (si no la definen, la página se recarga). JavaScript sin dependencias, cargado desde el pie.
(function () {
  var script = document.currentScript;
  var API = script ? script.getAttribute('data-api') : '';

  function esc(t) { var d = document.createElement('div'); d.textContent = t == null ? '' : t; return d.innerHTML; }

  document.addEventListener('DOMContentLoaded', function () {
    var chip = document.querySelector('.ar-empresa span');
    var usuario = null;
    try { usuario = JSON.parse(document.body.getAttribute('data-usuario')); } catch (e) {}
    // Mientras se ve como otro usuario se muestra su empresa y no se cambia
    if (!chip || !usuario || [1, 2, 5].indexOf(parseInt(usuario.tipoUsuarioId, 10)) < 0 || document.getElementById('verComoAviso')) { return; }

    var xhr = new XMLHttpRequest();
    xhr.open('POST', API + '/php/repositorios/ArgosRiesgos.php', true);
    xhr.withCredentials = true;
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function () {
      var r;
      try { r = JSON.parse(xhr.responseText); } catch (e) { return; }
      if (r.mensajeError !== '' || !r.valor || r.valor.length < 2) { return; }   // sin otra empresa que elegir se queda el nombre fijo
      var elegida = null;
      try { elegida = window.sessionStorage.getItem('argosEmpresaId'); } catch (e) {}
      if (!elegida || !r.valor.some(function (e) { return String(e.id) === String(elegida); })) { elegida = String(usuario.empresaId); }
      var html = r.valor.map(function (e) { return '<option value="' + e.id + '"' + (String(e.id) === String(elegida) ? ' selected' : '') + '>' + esc(e.nombre) + '</option>'; }).join('');
      chip.outerHTML = '<select id="empresaSelect" class="ar-empresa-select" title="Empresa">' + html + '</select>';
      document.getElementById('empresaSelect').addEventListener('change', function () {
        try { window.sessionStorage.setItem('argosEmpresaId', this.value); } catch (e) {}
        if (typeof window.argosAlCambiarEmpresa === 'function') { window.argosAlCambiarEmpresa(); } else { window.location.reload(); }
      });
    };
    xhr.send('accion=consultarEmpresas');
  });
})();
