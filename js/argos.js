// Comportamiento común del armazón de ARGOS (menú lateral adaptable, ayudas). Sin lógica de negocio:
// cada pantalla trae su Vista y su Presentador (patrón MVP de handel_api).
$(function () {
  $('#menuToggle').on('click', function () {
    $('body').toggleClass('ar-menu-abierto');
  });
  $('.ar-main').on('click', function () {
    $('body').removeClass('ar-menu-abierto');
  });
  $('[data-toggle="tooltip"]').tooltip({container: 'body'});
  // Si la foto de perfil no carga, se muestran las iniciales del usuario.
  $('.ar-avatar img').each(function () {
    if (this.complete && this.naturalWidth === 0) { $(this).remove(); }
  }).on('error', function () { $(this).remove(); });
  // Campana: total de pendientes de Mi bandeja (acciones urgentes + lo que requiere respuesta).
  $('#campanaBoton').on('click', function () { window.location.href = 'bandeja.php'; });
  if (typeof ArgosBandejaRepositorio === 'function' && $('#campanaContador').length) {
    new ArgosBandejaRepositorio().consultar(null, function (r) {
      if (r && r.mensajeError === '' && r.valor.total > 0) {
        $('#campanaContador').text(r.valor.total > 99 ? '99+' : r.valor.total).show();
      }
      // Modo taller: contador morado con los riesgos identificados que siguen Sin evaluar
      if (r && r.mensajeError === '' && r.valor.sinEvaluar && r.valor.sinEvaluar.length > 0) {
        $("a.ar-nav[href='taller.php']").append("<span class='ar-ct ar-ct-sev' title='Riesgos sin evaluar'>" + r.valor.sinEvaluar.length + "</span>");
      }
    });
  }
  // Menú de CTPAT: selector de ejercicio («2026 · en curso») y contador de cuestionarios por contestar (como la maqueta)
  var cuest = $("a.ar-nav[href='ctpat_cuestionarios.php'], a.ar-nav[href='ctpat_resumen.php']");
  if (cuest.length && typeof HANDEL_API !== 'undefined') {
    var llamar = function (accion, criterios, fn) {
      // Empresa elegida en el selector de la barra (si no, el servidor usa la propia del usuario)
      var empresa = null;
      try { empresa = window.sessionStorage.getItem('argosEmpresaId'); } catch (e) {}
      criterios = criterios || {};
      if (empresa && !criterios.empresaId) { criterios.empresaId = parseInt(empresa, 10); }
      $.ajax({ url: HANDEL_API + '/php/repositorios/CtpatEjercicios.php', type: 'POST', data: { accion: accion, criteriosSeleccion: JSON.stringify(criterios || {}) },
        success: function (r) { if (r && r.mensajeError === '') { fn(r.valor); } } });
    };
    llamar('consultar', {}, function (v) {
      if (!v.ejercicios.length) { return; }
      var guardado = null;
      try { guardado = window.sessionStorage.getItem('ctpatEjercicioId'); } catch (e) {}
      var actual = v.ejercicios.filter(function (x) { return String(x.id) === String(guardado); })[0] || v.ejercicios.filter(function (x) { return x.id === v.enCurso; })[0] || v.ejercicios[0];
      var opciones = v.ejercicios.map(function (x) {
        return '<option value="' + x.id + '"' + (x.id === actual.id ? ' selected' : '') + '>' + x.anio + ' · ' + (x.estado === 'cerrado' ? 'cerrado' : 'en curso') + '</option>';
      }).join('');
      var encabezado = $('.ar-sidebar .ar-seccion').filter(function () { return $(this).text().toLowerCase().indexOf('ejercicio ctpat') >= 0; }).first();
      if (encabezado.length) {
        encabezado.after('<select id="ctpatEjercicioMenu" class="ar-menu-select" title="Ejercicio CTPAT">' + opciones + '</select>');
        $('#ctpatEjercicioMenu').on('change', function () {
          try { window.sessionStorage.setItem('ctpatEjercicioId', this.value); } catch (e) {}
          window.location.reload();
        });
      }
      llamar('resumen', { ejercicioId: actual.id }, function (r) {
        var faltan = r.detalle.socios.filter(function (x) { return x.estado !== 'ok'; }).length;
        if (faltan > 0 && r.ejercicio.estado !== 'mapeo') {
          $("a.ar-nav[href='ctpat_cuestionarios.php']").append("<span class='ar-ct' title='Cuestionarios por contestar'>" + faltan + "</span>");
        }
      });
    });
  }
});
