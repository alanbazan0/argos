/**
 * Cuestionario público del socio comercial (CTPAT, R-00125). Sin cuenta: el enlace trae el token (?t=...). Guarda y continúa con el mismo enlace,
 * cambia de idioma ES/EN y al final firma una declaración de veracidad (nombre y puesto). No se piden archivos.
 */
var T = {
	es: { titulo: "Cuestionario de seguridad de la cadena de suministro", intro: "Contesta cada pregunta con Sí, No o No aplica. Puedes guardar y continuar después con este mismo enlace.", si: "Sí", no: "No", na: "No aplica",
		debe: "Debe", deberia: "Debería", guardar: "Guardar y continuar", guardado: "Avance guardado.", avance: "contestadas", firmaTitulo: "Declaración de veracidad",
		firmaTexto: "Declaro que las respuestas de este cuestionario son verdaderas y completas a mi leal saber y entender.", nombre: "Nombre", puesto: "Puesto", enviar: "Firmar y enviar",
		gracias: "Gracias. Tu cuestionario fue enviado. Folio: ", leido: "Este cuestionario ya fue enviado o está cerrado; solo se puede consultar.", error: "No se pudo abrir el enlace.", faltan: "Faltan preguntas por contestar.", estandar: "Estándar" },
	en: { titulo: "Supply chain security questionnaire", intro: "Answer each question with Yes, No or Not applicable. You can save and continue later with this same link.", si: "Yes", no: "No", na: "Not applicable",
		debe: "Must", deberia: "Should", guardar: "Save and continue", guardado: "Progress saved.", avance: "answered", firmaTitulo: "Declaration of truthfulness",
		firmaTexto: "I declare that the answers in this questionnaire are true and complete to the best of my knowledge.", nombre: "Name", puesto: "Position", enviar: "Sign and submit",
		gracias: "Thank you. Your questionnaire was submitted. Reference: ", leido: "This questionnaire was already submitted or is closed; it is read-only.", error: "The link could not be opened.", faltan: "Some questions are unanswered.", estandar: "Standard" }
};
var datos = null, idioma = "es", respuestas = {}, token = (/[?&]t=([a-f0-9]{40})/.exec(window.location.search) || [])[1];

function api(accion, extra, ok)
{
	$.ajax({ url: HANDEL_API + "/php/repositorios/CtpatPublico.php", type: "POST", data: $.extend({ accion: accion, token: token }, extra),
		success: function(r){ ok(r); }, error: function(){ ok({ mensajeError: T[idioma].error }); } });
}
function esc(t){ return $("<div>").text(t == null ? "" : t).html(); }

function dibujar()
{
	var t = T[idioma], h = "", ultimo = null;
	$("#titulo").text(datos.socio + " · " + t.titulo);
	$("#intro").text(t.intro);
	for(var i = 0; i < datos.preguntas.length; i++)
	{
		var p = datos.preguntas[i];
		if(p.estandar != ultimo)
		{
			ultimo = p.estandar;
			h += "<h3 class='ar-pub-est'>" + t.estandar + " " + p.estandar + " · " + esc(datos.estandares[p.estandar] || "") + "</h3>";
		}
		h += "<div class='ar-pregunta'><div>" + esc(p.texto) + " <span class='ar-chip'>" + (p.tipo == "debe" ? t.debe : t.deberia) + "</span></div><div class='ar-resp' data-id='" + p.id + "'>";
		["si", "no", "na"].forEach(function(v){ h += "<label class='radio-inline'><input type='radio' name='p" + p.id + "' value='" + v + "'" + (respuestas[p.id] == v ? " checked" : "") + (datos.soloLectura ? " disabled" : "") + "> " + t[v] + "</label>"; });
		h += "</div></div>";
	}
	$("#preguntas").html(h);
	$("#guardarBoton").text(t.guardar).toggle(!datos.soloLectura);
	$("#firmaTitulo").text(t.firmaTitulo); $("#firmaTexto").text(t.firmaTexto); $("#firmaNombreEt").text(t.nombre); $("#firmaPuestoEt").text(t.puesto); $("#enviarBoton").text(t.enviar);
	$("#firmaBloque").toggle(!datos.soloLectura);
	if(datos.soloLectura)
		$("#mensaje").html("<div class='ar-callout'>" + t.leido + (datos.folio ? " (" + esc(datos.folio) + ")" : "") + "</div>");
	actualizarAvance();
}

function actualizarAvance()
{
	$("#avanceTexto").text(Object.keys(respuestas).length + " / " + datos.preguntas.length + " " + T[idioma].avance);
}

function guardar(alTerminar)
{
	api("guardar", { respuestas: JSON.stringify(respuestas), idioma: idioma }, function(r)
	{
		if(r.mensajeError){ $("#mensaje").html("<div class='alert alert-danger'>" + esc(r.mensajeError) + "</div>"); return; }
		if(alTerminar) alTerminar(); else $("#mensaje").html("<div class='alert alert-success'>" + T[idioma].guardado + "</div>");
	});
}

$(function()
{
	if(!token){ $("#titulo").text(T.es.error); return; }
	$("#preguntas").on("change", "input[type=radio]", function(){ respuestas[$(this).closest(".ar-resp").attr("data-id")] = this.value; actualizarAvance(); });
	$("#idiomaBloque").on("click", "button", function(){ idioma = $(this).attr("data-idioma"); if(datos){ dibujar(); } });
	$("#guardarBoton").on("click", function(){ guardar(null); });
	$("#enviarBoton").on("click", function()
	{
		guardar(function()
		{
			api("firmar", { nombre: $("#firmaNombre").val(), puesto: $("#firmaPuesto").val() }, function(r)
			{
				if(r.mensajeError){ $("#mensaje").html("<div class='alert alert-danger'>" + esc(r.mensajeError) + "</div>"); return; }
				$("#preguntas, #firmaBloque, .ar-publico-pie").hide();
				$("#mensaje").html("<div class='alert alert-success'>" + T[idioma].gracias + "<b>" + esc(r.valor.folio) + "</b></div>");
			});
		});
	});
	api("obtener", {}, function(r)
	{
		if(r.mensajeError){ $("#titulo").text(r.mensajeError); return; }
		datos = r.valor; idioma = datos.idioma == "en" ? "en" : "es"; respuestas = datos.respuestas || {};
		dibujar();
	});
});
