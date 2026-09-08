<?php
require_once 'ImportadorRemesa.php';

if(!$_FILES){
	echo "
		<form action='formularioRemesa.php' method='post' enctype='multipart/form-data'>
			<input type='file' name='nombre_archivo_cliente'/><br />
			<input type='submit' name='enviar' value='Enviar' />
		</form>
";

}else{
	//var_dump($_FILES);
	if (!copy($_FILES['archivo']['tmp_name'], 'archivosRemesa/' . $_FILES['archivo']['name'])) {
		echo "Error al copiar $archivo...\n";
	}
	$imp = new ImportadorRemesa('archivosRemesa/' . $_FILES['archivo']['name']);
	$imp->cargarRemesa();
	if(count($imp->getArrError()) > 0 ){
	echo "<pre>";
	print_r($imp->getArrError());
	echo "</pre>";
	exit;
	}
	$imp->salvarRemesa();
	if(count($imp->getArrError()) > 0 ){
	echo "<pre>";
	print_r($imp->getArrError());
	echo "</pre>";
	exit;
	}
	echo "Listo Geton!";
}
