<?php

class ParseINI {
    private static $file;
    private static $config = false;
    
    private static function loadINI(){
        if(self::$config == false){
            // Ruta calculada en base a la ubicación real de este archivo (class/ParseINI.php),
            // en vez de depender del nombre de la carpeta del sitio bajo DOCUMENT_ROOT.
            // Antes requería que el sitio viviera exactamente en {DOCUMENT_ROOT}/limonLocal/flix/.
            self::$file = __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'cfg'.DIRECTORY_SEPARATOR.'config.ini';
            self::$config = parse_ini_file(self::$file, true);
        }
    }
    
    public static function getConfig($seccion = false, $index = false){
        self::loadINI();
        
        if($seccion == false){
            return self::$config;
        }else if($seccion != false && $index != false){
            return self::$config[$seccion][$index];
        }else{
            return self::$config[$seccion];
        }
    }
    
    public static function getAllJson(){
        $params = array('accion', 'ordenarPor', 'ordenarOrden', 'pagina', 'porPagina', 'tipo');
        $rs = array();
        foreach(self::$config as $key => $value){
            if($key == 'common' || $key == 'menu' || $key == 'defaults_js'){
                continue;
            }
            for($i=0; $i<count($params); $i++){
                if(empty(self::$config[$key][$params[$i]])){
                    $rs[$key][$params[$i]] = false;
                }else{
                    $rs[$key][$params[$i]] = self::$config[$key][$params[$i]];
                }
            }
        }
        
        return json_encode($rs);
    }

    public static function split($seccion, $posicion = false){
        if($posicion != false){
            $temp_data = self::$config[$seccion][$posicion];
            self::$config[$seccion][$posicion] = array();

            for($i=0; $i<count($temp_data); $i++){
                $splited = explode('||', $temp_data[$i]);
                for($k=0; $k<count($splited); $k++){
                    if($k == 0){
                        self::$config[$seccion][$posicion][$i]['nombre'] = $splited[$k];
                    }else{
                        self::$config[$seccion][$posicion][$i][$splited[$k]] = true;
                    }
                }
            }
        }else{
            $temp_data = self::$config[$seccion];
            self::$config[$seccion] = array();            
            
            foreach($temp_data as $key => $value){
                
                for($i=0; $i<count($temp_data[$key]); $i++){
                    $splited = explode('||', $temp_data[$key][$i]);
                    for($k=0; $k<count($splited); $k++){
                        if($k == 0){
                            self::$config[$seccion][$key][$i]['seccion'] = $splited[$k];
                        }else{
                            self::$config[$seccion][$key][$i]['accion'] = $splited[$k];
                        }
                    }
                }                
                
            }
        }
    }
    
}
