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
});
