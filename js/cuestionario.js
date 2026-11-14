/**
 * Cuestionario público del socio comercial (CTPAT, R-00125). Sin cuenta: el enlace trae el token (?t=...). Guarda y continúa con el mismo enlace,
 * cambia de idioma ES/EN y al final firma una declaración de veracidad (nombre y puesto). No se piden archivos.
 */
var T = {
	es: { titulo: "Cuestionario de seguridad de la cadena de suministro", intro: "Contesta cada pregunta con Sí, No o No aplica. Puedes guardar y continuar después con este mismo enlace.", si: "Sí", no: "No", na: "No aplica",
		debe: "Debe", deberia: "Debería", guardar: "Guardar y continuar", guardado: "Avance guardado.", sinGuardar: "Cambios sin guardar…", guardando: "Guardando…", autoGuardado: "Guardado automáticamente", reintentando: "No se pudo guardar; reintentando…", avance: "contestadas", firmaTitulo: "Declaración de veracidad",
		firmaTexto: "Declaro que las respuestas de este cuestionario son verdaderas y completas a mi leal saber y entender.", nombre: "Nombre", puesto: "Puesto", enviar: "Firmar y enviar",
		gracias: "Gracias. Tu cuestionario fue enviado. Folio: ", leido: "Este cuestionario ya fue enviado o está cerrado; solo se puede consultar.", error: "No se pudo abrir el enlace.", errorEnvio: "No se pudo enviar tu información. Revisa tu conexión a internet y vuelve a intentarlo en unos minutos: tus respuestas siguen en esta pantalla y no se pierden. Si el problema continúa, escribe a la persona que te envió este cuestionario.", faltan: "Faltan preguntas por contestar.", estandar: "Estándar" },
	en: { titulo: "Supply chain security questionnaire", intro: "Answer each question with Yes, No or Not applicable. You can save and continue later with this same link.", si: "Yes", no: "No", na: "Not applicable",
		debe: "Must", deberia: "Should", guardar: "Save and continue", guardado: "Progress saved.", sinGuardar: "Unsaved changes…", guardando: "Saving…", autoGuardado: "Saved automatically", reintentando: "Could not save; retrying…", avance: "answered", firmaTitulo: "Declaration of truthfulness",
		firmaTexto: "I declare that the answers in this questionnaire are true and complete to the best of my knowledge.", nombre: "Name", puesto: "Position", enviar: "Sign and submit",
		gracias: "Thank you. Your questionnaire was submitted. Reference: ", leido: "This questionnaire was already submitted or is closed; it is read-only.", error: "The link could not be opened.", errorEnvio: "We could not submit your information. Check your internet connection and try again in a few minutes: your answers are still on this screen and are not lost. If the problem continues, contact the person who sent you this questionnaire.", faltan: "Some questions are unanswered.", estandar: "Standard" }
};
var datos = null, idioma = "es", respuestas = {}, token = (/[?&]t=([a-f0-9]{40})/.exec(window.location.search) || [])[1];

function api(accion, extra, ok)
{
	$.ajax({ url: HANDEL_API + "/php/repositorios/CtpatPublico.php", type: "POST", data: $.extend({ accion: accion, token: token }, extra),
		success: function(r){ ok(r); }, error: function(x, estado, causa){ ok({ mensajeError: accion == "obtener" ? T[idioma].error : T[idioma].errorEnvio, detalle: accion + " · HTTP " + x.status + " · " + estado + (causa ? " · " + causa : "") }); } });
}
// Aviso en ventana emergente (visible aunque la persona esté al final de un cuestionario largo)
function aviso(tipo, texto, detalle)
{
	$("#avisoVentana").remove();
	var cierre = idioma == "en" ? "Close" : "Cerrar";
	$("body").append("<div id='avisoVentana' class='ar-aviso-fondo' role='alertdialog' aria-modal='true'><div class='ar-aviso ar-aviso-" + tipo + "'><p>" + texto + "</p>"
		+ (detalle ? "<small>" + (idioma == "en" ? "Technical detail: " : "Detalle técnico: ") + esc(detalle) + "</small>" : "")
		+ "<button type='button' class='btn btn-success' id='avisoCerrar'>" + cierre + "</button></div></div>");
	$("#avisoCerrar").on("click", function(){ $("#avisoVentana").remove(); }).focus();
}
function esc(t){ return $("<div>").text(t == null ? "" : t).html(); }

// Marca en rojo las preguntas sin contestar y su sección; lleva a la primera. Devuelve true si no falta ninguna.
function marcarFaltantes()
{
	$(".ar-pregunta").removeClass("ar-falta"); $(".ar-pub-est").removeClass("ar-falta-est").find(".ar-falta-n").remove();
	var faltan = 0, primera = null, porSeccion = {};
	$(".ar-resp").each(function()
	{
		var id = $(this).attr("data-id");
		if(respuestas[id]) return;
		faltan++;
		var caja = $(this).closest(".ar-pregunta").addClass("ar-falta");
		if(!primera) primera = caja;
		var est = caja.prevAll(".ar-pub-est").first();
		est.addClass("ar-falta-est");
		porSeccion[est.index()] = (porSeccion[est.index()] || 0) + 1;
		est.data("n", porSeccion[est.index()]);
	});
	$(".ar-falta-est").each(function(){ $(this).append(" <span class='ar-falta-n'>(" + $(this).data("n") + (idioma == "en" ? " missing" : " sin contestar") + ")</span>"); });
	if(!faltan) return true;
	aviso("error", (idioma == "en" ? "There are " + faltan + " unanswered questions. They are highlighted in red." : "Faltan " + faltan + " preguntas por contestar. Están marcadas en rojo."));
	$("#avisoCerrar").one("click", function(){ primera[0].scrollIntoView({ behavior: "smooth", block: "center" }); });
	return false;
}

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
		h += "<div class='ar-pregunta" + (respuestas[p.id] ? " ar-contestada" : "") + "'><div>" + esc(p.texto) + "</div><div class='ar-resp' data-id='" + p.id + "'>";
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

// Guardado automático: cada respuesta nueva se manda al servidor tras una pausa corta (solo las que cambiaron, no las 100 de golpe).
var pendientes = {}, temporizador = null, guardando = false, reintentos = 0, retardo = window.RETARDO_AUTOGUARDADO || 1500;

function estadoGuardado(texto, clase){ $("#estadoGuardado").text(texto || "").attr("class", "ar-estado-guardado " + (clase || "")); }
function hayPendientes(){ return Object.keys(pendientes).length > 0; }

// Manda las respuestas pendientes; las que lleguen bien dejan de estar pendientes (salvo que el usuario las haya cambiado mientras tanto).
function enviarPendientes(fin)
{
	clearTimeout(temporizador); temporizador = null;
	var lote = $.extend({}, pendientes);
	api("guardar", { respuestas: JSON.stringify(lote), idioma: idioma }, function(r)
	{
		if(!r.mensajeError) Object.keys(lote).forEach(function(k){ if(pendientes[k] === lote[k]) delete pendientes[k]; });
		fin(r);
	});
}

function programarAutoguardado()
{
	clearTimeout(temporizador);
	temporizador = setTimeout(autoguardar, retardo);
	estadoGuardado(T[idioma].sinGuardar, "ar-eg-pend");
}

function autoguardar()
{
	if(guardando){ temporizador = setTimeout(autoguardar, retardo); return; }
	if(!hayPendientes()) return;
	guardando = true;
	estadoGuardado(T[idioma].guardando, "ar-eg-pend");
	enviarPendientes(function(r)
	{
		guardando = false;
		if(r.mensajeError)
		{
			estadoGuardado(T[idioma].reintentando, "ar-eg-error");
			if(r.detalle) temporizador = setTimeout(autoguardar, Math.min(30000, (window.ESPERA_REINTENTO || 3000) * ++reintentos));   // falla de red: reintenta con espera creciente
			else aviso("error", esc(r.mensajeError));                                                      // el servidor lo rechazó: no tiene caso repetirlo
			return;
		}
		reintentos = 0;
		if(hayPendientes()) programarAutoguardado();
		else estadoGuardado(T[idioma].autoGuardado + " · " + new Date().toTimeString().slice(0, 5), "ar-eg-ok");
	});
}

function guardar(alTerminar)
{
	enviarPendientes(function(r)
	{
		if(r.mensajeError){ aviso("error", esc(r.mensajeError), r.detalle); return; }
		estadoGuardado(T[idioma].autoGuardado + " · " + new Date().toTimeString().slice(0, 5), "ar-eg-ok");
		if(alTerminar) alTerminar(); else aviso("ok", esc(T[idioma].guardado));
	});
}

$(window).on("beforeunload", function(){ if(hayPendientes()) return "pendientes"; });

$(function()
{
	if(!token){ $("#titulo").text(T.es.error); return; }
	$("#preguntas").on("change", "input[type=radio]", function(){ var id = $(this).closest(".ar-resp").attr("data-id"); respuestas[id] = this.value; pendientes[id] = this.value; programarAutoguardado(); $(this).closest(".ar-pregunta").removeClass("ar-falta").addClass("ar-contestada"); actualizarAvance(); });
	$("#idiomaBloque").on("click", "button", function(){ idioma = $(this).attr("data-idioma"); if(datos){ dibujar(); } });
	$("#guardarBoton").on("click", function(){ guardar(null); });
	$("#enviarBoton").on("click", function()
	{
		if(!marcarFaltantes()) return;
		guardar(function()
		{
			api("firmar", { nombre: $("#firmaNombre").val(), puesto: $("#firmaPuesto").val() }, function(r)
			{
				if(r.mensajeError){ aviso("error", esc(r.mensajeError), r.detalle); return; }
				$("#preguntas, #firmaBloque, .ar-publico-pie").hide();
				$("#mensaje").html("<div class='alert alert-success'>" + T[idioma].gracias + "<b>" + esc(r.valor.folio) + "</b></div>");
				window.scrollTo(0, 0);
				aviso("ok", T[idioma].gracias + "<b>" + esc(r.valor.folio) + "</b>");
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
