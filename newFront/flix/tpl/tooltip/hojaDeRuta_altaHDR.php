<div class='tooltip-text' style="height: 350px;">
    <div class="tooltip-title">
        <span class="title">Gestión de HDR</span>
    </div>
    <p>En ésta sección se podrá realizar la <b>alta</b> de HDR como así también <b>actualizar</b> el estado de los pedidos que se encuentran en condiciones de salir a distribución</p>
    <b>ALTA:</b> Para dar de alta una HDR deberá de ingresar aquellos pedidos en condiciones de salir a distribución. Para ello se leerá el código de barras asociado a su orden de pedido mediante el lector 2D. Una vez finalizada esta instancia se asignara un distribuidor y deberá de seleccionar la acción "ALTA".<br> 
    <b>ACTUALIZAR:</b>Esta accion debe realizarse para aquellos pedidos ya hayan salida en una HDR y se quiera actualizar su estado. Para ello deber realizarse la lectura de los códigos de barras de los pedidos y en el campo estado(Campo auto completado)  deberá ingresar el estado a asignar a los pedidos ingresados. Por ultimo deberá de seleccionar la acción "ACTUALIZAR".</p><br />
    
    <p><b>Descripción de datos del formulario:</b></p><br />
    
    <ul>
	<li><b>Pedidos:</b> Campo de ingreso mediante lector o  manual del código de barras del pedido.</li><br />
        <li><b>Distribuidor:</b> Campo de auto completado del distribuidor a asignar una HDR, solo deben completarse en caso de ALTA.</li><br />
        <li><b>Estados:</b> Campo de auto completado, estado a asignar a los pedidos ingresados, solo debe completarse en caso de ACTUALIZAR.</li><br />
        <li><b>Acción:</b> Campo seleccionable, tipo de acción a realizar (ALTA/ACTUALIZAR).</li><br />
        <li><b>Observaciones:</b> Campo de ingreso manual, comentarios/observaciones sobre la HDR.</li><br />
    </ul>
</div>