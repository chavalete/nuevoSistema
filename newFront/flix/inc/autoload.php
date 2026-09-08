<?php
/**
 * Function de autoinclusion de classes
 * @author  Ponzo, Nicolas
 * @param   <string> $className
 */
function __autoload($className){
    // Ruta calculada en base a la ubicación real de este archivo (inc/autoload.php),
    // en vez de depender del nombre de la carpeta del sitio bajo DOCUMENT_ROOT.
    // Antes requería que el sitio viviera exactamente en {DOCUMENT_ROOT}/limonLocal/flix/.
    $file = __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'class'.DIRECTORY_SEPARATOR.$className.'.php';

    if(file_exists($file)){
        require_once($file);
    }else{
        die('Ha ocurrido un error crítico. Contacte al administrador');
    }
}
