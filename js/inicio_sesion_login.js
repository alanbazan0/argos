  $(function() {
    $('input').iCheck({ checkboxClass:'icheckbox_square-blue', radioClass:'iradio_square-blue', increaseArea:'20%' });
    consultarFrases();
  });
  function consultarFrases() {
    var repositorio = new FrasesRepositorio(this);
    repositorio.consultar(this, function(resultado) {
      if(resultado.mensajeError === "") {
        var frases = resultado.valor;
        if(frases.length > 0) {
          var frase = frases[Math.floor(Math.random() * frases.length)];
          $("#fraseP").html(frase.texto);
          $("#autorP").html(frase.autor);
        }
      }
    });
  }
