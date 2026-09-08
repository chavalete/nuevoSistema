<?php

class miPDO extends PDO
{
    public function __construct ($db = null, $file='almacenamiento.ini'){
        try{
            if (!$settings = parse_ini_file($file, TRUE)) throw new exception('Error al abrir -> ' . $file . '.');
            if ($db == NULL){
                $db= 'dmelmac';
            }
            $dns = $settings[$db]['driver'] . 
                   ':host=' . $settings[$db]['host'] . 
                   ((!empty($settings[$db]['port'])) ? (';port=' . $settings[$db]['port']) : '') .
                   ';dbname=' . $settings[$db]['dbname'];
            
            parent::__construct($dns, $settings[$db]['username'], $settings[$db]['password']);
            
        } catch (PDOException $e){
            print "**Error ** : " . $e->getMessage() . "\n";
            //print "Archivo : " . $e->getFile() . "\n";
            //print "Linea : " . $e->getLine() . "\n **Error Fin**";
            die();
        }
    }
}
