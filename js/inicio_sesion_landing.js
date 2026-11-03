  // Mapa de calor ilustrativo (5×5): nivel = probabilidad × consecuencia
  (function(){
    var hm=document.getElementById('heatmap'); if(!hm) return;
    var counts={'5-4':1,'4-5':1,'4-3':2,'3-4':1,'3-3':3,'2-4':1,'3-2':2,'2-2':2,'1-3':1,'2-1':1};
    var html='';
    for(var p=5;p>=1;p--){
      html+='<div class="hm-ax">'+p+'</div>';
      for(var c=1;c<=5;c++){
        var s=p*c, nivel=s>=15?'hm-alto':(s>=6?'hm-medio':'hm-bajo');
        var n=counts[p+'-'+c]||'';
        html+='<div class="hm-cell '+nivel+(n?'':' hm-vacio')+'">'+n+'</div>';
      }
    }
    html+='<div></div>'; for(var c2=1;c2<=5;c2++){ html+='<div class="hm-ax">'+c2+'</div>'; }
    hm.innerHTML=html;
  })();

  // Fade-up observer
  const obs = new IntersectionObserver(entries=>{
    entries.forEach(e=>{ if(e.isIntersecting){ e.target.classList.add('in'); obs.unobserve(e.target); } });
  },{threshold:.10});
  document.querySelectorAll('.fade-up').forEach(el=>obs.observe(el));
  setTimeout(()=>{ document.querySelectorAll('#hero .fade-up, #features .feat-item').forEach(el=>el.classList.add('in')); },80);

  // Formspree AJAX submit
  const form = document.getElementById('demo-form');
  const success = document.getElementById('form-success');
  if(form){
    form.addEventListener('submit', async function(e){
      e.preventDefault();
      const btn = form.querySelector('.btn-submit');
      btn.textContent = 'Enviando...';
      btn.disabled = true;
      try{
        const res = await fetch(form.action, { method:'POST', body:new FormData(form), headers:{'Accept':'application/json'} });
        if(res.ok){ form.style.display='none'; success.style.display='block'; }
        else { btn.textContent = 'Error al enviar — intenta de nuevo'; btn.disabled = false; }
      } catch(err){
        btn.textContent = 'Error de conexión — intenta de nuevo'; btn.disabled = false;
      }
    });
  }
