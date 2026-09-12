// Aviso de precios actualizados — interacción. No usa loadFlix ni
// callService (esos disparan la maquinaria genérica de listados); este
// aviso solo abre/cierra su panel y confirma contra un endpoint propio.
function avisoPreciosAbrir(){
    document.getElementById('avisoPreciosScrim').classList.add('open');
    document.getElementById('avisoPreciosBox').classList.add('open');
}
function avisoPreciosCerrar(){
    document.getElementById('avisoPreciosScrim').classList.remove('open');
    document.getElementById('avisoPreciosBox').classList.remove('open');
}
function avisoPreciosConfirmar(){
    var ids = [];
    document.querySelectorAll('#avisoPreciosBox .aviso-precios-row').forEach(function(row){
        ids.push(row.getAttribute('data-producto-id'));
    });
    if(!ids.length){ return; }

    var btn = document.getElementById('avisoPreciosConfirmBtn');
    btn.disabled = true;
    btn.textContent = 'Confirmando...';

    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'inc/avisoPrecios_confirmar.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function(){
        btn.disabled = false;
        btn.textContent = 'Confirmar que vi los precios nuevos';
        var ok = false;
        try{ ok = JSON.parse(xhr.responseText).soyError === false; }catch(e){}
        if(ok){
            avisoPreciosCerrar();
            document.getElementById('avisoPreciosBanner').classList.add('hidden');
            var toast = document.getElementById('avisoPreciosToast');
            toast.classList.add('show');
            setTimeout(function(){ toast.classList.remove('show'); }, 2600);
        }else{
            alert('No se pudo confirmar, probá de nuevo.');
        }
    };
    xhr.onerror = function(){
        btn.disabled = false;
        btn.textContent = 'Confirmar que vi los precios nuevos';
        alert('No se pudo confirmar, probá de nuevo.');
    };
    xhr.send('productoIds=' + encodeURIComponent(ids.join(',')));
}

// El sistema navega entre secciones por AJAX (loadFlix(), en flix.js,
// pega contra tpl/flix/flix.php) sin recargar la página completa, así
// que el header — y con él este aviso — nunca se vuelve a pedir solo.
// Reconsultamos después de cada cambio de sección para que se
// mantenga al día sin depender de un F5. Mismo patrón que ya usa
// topbarTitle.js para sincronizar el título de la sección.
function avisoPreciosCheck(){
    var wrap = document.getElementById('avisoPreciosWrap');
    if(!wrap){ return; }
    $.get('inc/avisoPrecios_check.php', function(html){
        wrap.innerHTML = html;
    });
}

$(document).ajaxComplete(function(event, xhr, settings){
    if(!settings || settings.url !== 'tpl/flix/flix.php'){ return; }
    avisoPreciosCheck();
});

// Polling cada 1 segundo (AVISO_PRECIOS_TTL_SEGUNDOS en tpl/avisoPrecios.php
// tiene que estar en 1 también, sino el cache de sesión igual frena la
// consulta a la base). Pensado para ver el aviso aparecer sin tener que
// navegar. Si esto queda así en producción, el server recibe un hit por
// segundo por cada pestaña abierta — subir el intervalo antes de dejarlo.
setInterval(avisoPreciosCheck, 1000);
