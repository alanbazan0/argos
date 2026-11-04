/**
 * Seguimiento público de acciones del socio comercial (CTPAT, C-22). Sin cuenta: el enlace trae el token (?t=...). El socio marca avance
 * (En proceso o Terminada) y agrega enlaces de evidencia; nunca verifica. El enlace sigue activo mientras haya acciones sin verificar.
 */
var T = {
	es: { titulo: "Seguimiento de tus acciones de seguridad", intro: "Estas son las acciones acordadas. Marca tu avance y agrega enlaces a las evidencias.", enProceso: "Marcar en proceso", terminada: "Marcar terminada", evidencia: "Enlace de evidencia (https://)", desc: "Descripción", agregar: "Agregar evidencia", limite: "Fecha límite", esperada: "Evidencia esperada", estandar: "Estándar", estatus: "Estatus", guardado: "Guardado.", error: "No se pudo abrir el enlace." },
	en: { titulo: "Follow-up of your security actions", intro: "These are the agreed actions. Mark your progress and add links to the evidence.", enProceso: "Mark in progress", terminada: "Mark completed", evidencia: "Evidence link (https://)", desc: "Description", agregar: "Add evidence", limite: "Due date", esperada: "Expected evidence", estandar: "Standard", estatus: "Status", guardado: "Saved.", error: "The link could not be opened." }
};
var idioma = "es", datos = null, token = (/[?&]t=([a-f0-9]{40})/.exec(window.location.search) || [])[1];

function api(accion, extra, ok)
{
	$.ajax({ url: HANDEL_API + "/php/repositorios/CtpatPublico.php", type: "POST", data: $.extend({ accion: accion, token: token }, extra), success: ok, error: function(){ ok({ mensajeError: T[idioma].error }); } });
}
function esc(t){ return $("<div>").text(t == null ? "" : t).html(); }

function dibujar()
{
	var t = T[idioma], h = "";
	$("#titulo").text(datos.socio + " · " + t.titulo);
	$("#intro").text(t.intro);
	datos.acciones.forEach(function(a)
	{
		h += "<div class='ar-card'><h3>" + t.estandar + " " + a.estandar + " <span class='ar-chip'>" + esc(a.estatus) + "</span></h3><p style='white-space:pre-line'>" + esc(a.accion) + "</p>" +
			"<div class='ar-sub'>" + t.limite + ": " + esc(a.fechaLimite) + " · " + t.esperada + ": " + esc(a.evidenciaEsperada || "—") + "</div><ul class='ar-lista'>" +
			a.evidencias.map(function(e){ return "<li><a href='" + esc(e.url) + "' target='_blank' rel='noopener noreferrer'>" + esc(e.descripcion || e.url) + "</a></li>"; }).join("") + "</ul>";
		if(!a.soloLectura)
			h += "<div class='ar-acc-socio' data-id='" + a.id + "'><div class='row'><div class='col-sm-6 form-group'><input type='url' class='form-control ev-url' placeholder='" + t.evidencia + "'></div><div class='col-sm-4 form-group'><input type='text' class='form-control ev-desc' maxlength='300' placeholder='" + t.desc + "'></div>" +
				"<div class='col-sm-2 form-group'><button type='button' class='btn btn-default btn-block ev-agregar'>" + t.agregar + "</button></div></div>" +
				(a.estado == "Abierta" ? "<button type='button' class='btn btn-default btn-sm av' data-estado='En proceso'>" + t.enProceso + "</button> " : "") + (a.estado != "Terminada" ? "<button type='button' class='btn btn-success btn-sm av' data-estado='Terminada'>" + t.terminada + "</button>" : "") + "</div>";
		h += "</div>";
	});
	$("#acciones").html(h);
}

function cargar()
{
	api("planObtener", {}, function(r)
	{
		if(r.mensajeError){ $("#titulo").text(r.mensajeError); $("#acciones, #intro").html(""); return; }
		datos = r.valor; dibujar();
	});
}

$(function()
{
	if(!token){ $("#titulo").text(T.es.error); return; }
	$("#idiomaBloque").on("click", "button", function(){ idioma = $(this).attr("data-idioma"); if(datos) dibujar(); });
	$("#acciones").on("click", ".ev-agregar", function()
	{
		var c = $(this).closest(".ar-acc-socio");
		api("planAvance", { accionId: c.attr("data-id"), url: c.find(".ev-url").val(), descripcion: c.find(".ev-desc").val() }, function(r){ if(r.mensajeError) $("#mensaje").html("<div class='alert alert-danger'>" + esc(r.mensajeError) + "</div>"); else { $("#mensaje").html("<div class='alert alert-success'>" + T[idioma].guardado + "</div>"); cargar(); } });
	});
	$("#acciones").on("click", ".av", function()
	{
		var c = $(this).closest(".ar-acc-socio");
		api("planAvance", { accionId: c.attr("data-id"), estado: $(this).attr("data-estado") }, function(r){ if(r.mensajeError) $("#mensaje").html("<div class='alert alert-danger'>" + esc(r.mensajeError) + "</div>"); else { $("#mensaje").html("<div class='alert alert-success'>" + T[idioma].guardado + "</div>"); cargar(); } });
	});
	cargar();
});
