// Solo para la plantilla: instancia la Vista base de handel_api (sesión y cerrar sesión).
// Las pantallas reales cargan su propia Vista desde handel_api y no usan este archivo.
var vista = new Vista();
$(document).ready(function () {
  vista.inicializar();
});
