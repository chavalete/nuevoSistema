<?php
header('Content-type: application/json');

$folder = '..'.DIRECTORY_SEPARATOR.'tmp';
$nombre = $_FILES['archivo']['name'];
$tmp    = $_FILES['archivo']['tmp_name'];
$moveTo = $folder.DIRECTORY_SEPARATOR.$nombre;

if(!move_uploaded_file($tmp, $moveTo)) {
    $json = array(  'error' => true, 
                    'msg'   => "Ocurrio un error al subir el archivo.");
}else{
    $json = array(  'error' => false,
                    'file'  => $nombre);
}

echo json_encode($json);