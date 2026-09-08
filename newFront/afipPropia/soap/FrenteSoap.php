<?php
require_once 'AfipCliente.php';

class FrenteSoap
{
    private $_cliente;
    private $_ultimaRespuesta;
    private $_ultimoEnvio;
    
    public function __construct() {
            ini_set("soap.wsdl_cache_enabled", "0");
            //clearstatcache();
            //$this->_cliente= new AfipCliente();
    }
    public function traerUltimoComprobante($parametros){
        //var_dump($parametros);exit;
        $p = new stdClass();
        $p->Auth = $parametros['Auth'];
        $p->PtoVta = $parametros['PtoVta'];
        $p->CbteTipo = $parametros['CbteTipo'];
        $intentos = 1;
        do{
            $this->_cliente= new AfipCliente();
            $this->setUltimaRespuesta($this->_cliente->getUltimoNro($p));
            echo " Intento = " . $intentos++ . "/3 ";
        }while(is_array($this->_ultimaRespuesta) && $intentos < 4 );
        if(is_object($this->_ultimaRespuesta)){
            echo "** respondio **";
            $this->setUltimoEnvio(array($this->_cliente->__getLastRequest()));
        }else{
            echo "** No se obtuvo respuesta";
            $error[0] = new stdClass();
            $error[0]->return = new stdClass();
            $error[0]->return->errores = new stdClass();
            $error[0]->return->errores->_c_error = 999998;
            $error[0]->return->errores->_d_error = "No se obtuvo respuesta por parte del Servidor de AFIP.";
            $this->_ultimaRespuesta = $error;
        }
    }
    public function solicitarCAE($parametros){
        //var_dump($parametros);exit;
        $f = new stdClass();
        $f->Auth = $parametros['Auth'];
        $f->FeCAEReq['FeCabReq'] = $parametros['FeCAEReq'];
        $f->FeCAEReq['FeDetReq']['FECAEDetRequest'] = $parametros['FECAEDetRequest'];
        //print_r($f);exit;
        do{
            $this->_cliente= new AfipCliente();
            $this->setUltimaRespuesta($this->_cliente->pedirCAE($f));
            /*echo "<pre>";
            print_r($this->_cliente->__getLastRequest());
            echo "</pre>";exit;*/
            //echo " Intento = " . $intentos++ . "/3 ";
        }while(is_array($this->_ultimaRespuesta) && $intentos < 4 );
        if(is_object($this->_ultimaRespuesta)){
            echo "** respondio **";
            $this->setUltimoEnvio(array($this->_cliente->__getLastRequest()));
        }else{
            echo "** No se obtuvo respuesta";
            $error[0] = new stdClass();
            $error[0]->return = new stdClass();
            $error[0]->return->errores = new stdClass();
            $error[0]->return->errores->_c_error = 999998;
            $error[0]->return->errores->_d_error = "No se obtuvo respuesta por parte del Servidor de AFIP.";
            $this->_ultimaRespuesta = $error;
        }
    }
    public function setUltimaRespuesta($ultimaRespuesta){
        unset($this->_ultimaRespuesta);
        $this->_ultimaRespuesta = $ultimaRespuesta;
    }
    
    public function getUltimaRespuesta(){
        return $this->_ultimaRespuesta;
    }
    
    public function setUltimoEnvio($ultimoEnvio){
        unset($this->_ultimoEnvio);
        $this->_ultimoEnvio = $ultimoEnvio;
    }
    
    public function getUltimoEnvio(){
        return $this->_ultimoEnvio;
    }

    public function getUsuario(){
        return $this->_usuario->getUsuario();
    }
    
    public function getPassword(){
        return $this->_usuario->getPassword();
    }
    
}
