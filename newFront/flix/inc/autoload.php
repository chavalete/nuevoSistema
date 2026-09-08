<?php
/**
 * Function de autoinclusion de classes
 * @author  Ponzo, Nicolas
 * @param   <string> $className
 */
function __autoload($className){
    $file = $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'limonLocal'.DIRECTORY_SEPARATOR.'flix'.DIRECTORY_SEPARATOR.'class'.DIRECTORY_SEPARATOR.$className.'.php';

    if(file_exists($file)){
        require_once($file);
    }else{
        die('Ha ocurrido un error crítico. Contacte al administrador');
    }
}
