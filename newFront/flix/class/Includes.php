<?php

class Includes {
    private static $error = false;
    
    public static function get($seccion, $type){
        $type = strtolower($type);
        $seccion = strtolower($seccion);
        $htmlIncludes = '';
        
        switch($type){
            case 'css':
                $css = ParseINI::getConfig($seccion, $type);
                for($i=0; $i<count($css); $i++){
                    $htmlIncludes .= '<link href="css/'.$css[$i].'" rel="stylesheet" type="text/css"/>';
                }
                break;
            case 'js':
                $js = ParseINI::getConfig($seccion, $type);
                for($i=0; $i<count($js); $i++){
                    $htmlIncludes .= '<script type="text/javascript" src="js/'.$js[$i].'"></script>';
                }            
                break;
            default:
                self::$error = true;
                break;
        }
        
        if(!self::$error){
            return  $htmlIncludes;
        }
    }
    
}
