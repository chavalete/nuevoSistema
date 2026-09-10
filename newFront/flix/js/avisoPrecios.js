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
