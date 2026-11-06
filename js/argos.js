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
});
