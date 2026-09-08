<?php
require_once 'anmat/AnmatCliente.php';
require_once 'anmat/AnmatHeader.php';
require_once 'anmat/AnmatUsuario.php';

class FrenteSoap
{
    private $_header;
    private $_cliente;
    private $_usuario;
    private $_ultimaRespuesta;
    private $_ultimoEnvio;
    
    public function __construct($conQuien, $codigoSucursal) {
        if($conQuien === 'anmat'){
            //cargamos las header del transporte
            $this->_header = new anmatHeader();
            //instanciamos el cliente anmat para poder hablar y escuchar
            //$this->_cliente = new anmatCliente($this->_header);
            //instanciamos el usuario para el sistema anmat
            $this->_usuario = new anmatUsuario($codigoSucursal);
        }
    }

    public function enviarMedicamentos($ColMedDTO){
        $p = new stdClass();
        $p->arg0 = $ColMedDTO;
        $p->arg1 = $this->_usuario->getUsuario();
        $p->arg2 = $this->_usuario->getPassword();
        //echo "**envio - user : $p->arg1 **"; 
        $intentos = 1;
        do{
            $this->_cliente = new anmatCliente($this->_header);
            $this->setUltimaRespuesta($this->_cliente->enviarMedicamentos($p));
            //print_r($this->getUltimaRespuesta());
            //echo " Intento = " . $intentos++ . "/3 ";
        }while(is_array($this->_ultimaRespuesta[0]) && $intentos < 4 );
        if(is_object($this->_ultimaRespuesta[0])){
            //echo "** respondio **";
            $this->setUltimoEnvio(array($this->_cliente->__getLastRequest()));
        }else{
            //echo "** No se obtuvo respuesta";
            $error[0] = new stdClass();
            $error[0]->return = new stdClass();
            $error[0]->return->errores = new stdClass();
            $error[0]->return->errores->_c_error = 999998;
            $error[0]->return->errores->_d_error = "No se obtuvo respuesta por parte del Servidor de ANMAT.";
            $this->_ultimaRespuesta = $error;
        }
    }
    
    public function enviarMedicamentosDHSerie($ColMedDTODHSerie){
        $p = new stdClass();
        $p->arg0 = $ColMedDTODHSerie;
        $p->arg1 = $this->_usuario->getUsuario();
        $p->arg2 = $this->_usuario->getPassword();
	$this->_cliente = new anmatCliente($this->_header);
        $this->setUltimaRespuesta($this->_cliente->enviarMedicamentosDHSerie($p));
        $this->setUltimoEnvio(array($this->_cliente->__getLastRequest()));
    }
    
    public function enviarCancelacTransacc($cancTrans){
        $p = new stdClass();
        $p->arg0 = $cancTrans;
        $p->arg1 = $this->_usuario->getUsuario();
        $p->arg2 = $this->_usuario->getPassword();
        $intentos = 1;
        do{
            $this->_cliente = new anmatCliente($this->_header);
            $this->setUltimaRespuesta($this->_cliente->enviarCancelacTransacc($p));
        }while(is_array($this->_ultimaRespuesta[0]) && $intentos < 4 );
        if(is_object($this->_ultimaRespuesta[0])){
            //echo "** respondio **";
            $this->setUltimoEnvio(array($this->_cliente->__getLastRequest()));
        }else{
            //echo "** No se obtuvo respuesta";
            $error[0] = new stdClass();
            $error[0]->return = new stdClass();
            $error[0]->return->errores = new stdClass();
            $error[0]->return->errores->_c_error = 999998;
            $error[0]->return->errores->_d_error = "No se obtuvo respuesta por parte del Servidor de ANMAT.";
            $this->_ultimaRespuesta = $error;
        }
    }
    
    public function enviarConfirmacionTransacc($confTrans){
        $p = new stdClass();
        $p->arg0 = $this->_usuario->getUsuario();
        $p->arg1 = $this->_usuario->getPassword();
        $p->arg2 = $confTrans;
        $intentos = 1;
        do{
            $this->_cliente = new anmatCliente($this->_header);
            $this->setUltimaRespuesta($this->_cliente->enviarConfirmacionTransacc($p));
        }while(is_array($this->_ultimaRespuesta[0]) && $intentos < 4 );
        if(is_object($this->_ultimaRespuesta[0])){
            //echo "** respondio **";
            $this->setUltimoEnvio(array($this->_cliente->__getLastRequest()));
        }else{
            //echo "** No se obtuvo respuesta";
            $error[0] = new stdClass();
            $error[0]->return = new stdClass();
            $error[0]->return->errores = new stdClass();
            $error[0]->return->errores->_c_error = 999998;
            $error[0]->return->errores->_d_error = "No se obtuvo respuesta por parte del Servidor de ANMAT.";
            $this->_ultimaRespuesta = $error;
        }
    }
    public function enviarAlertaTransacc($confTrans){
    
        $p = new stdClass();
        $p->arg0 = $this->_usuario->getUsuario();
        $p->arg1 = $this->_usuario->getPassword();
        $p->arg2 = $confTrans;
        $intentos = 1;
        do{
            $this->_cliente = new anmatCliente($this->_header);
            $this->setUltimaRespuesta($this->_cliente->enviarAlertaTransacc($p));
        }while(is_array($this->_ultimaRespuesta[0]) && $intentos < 4 );
        if(is_object($this->_ultimaRespuesta[0])){
            //echo "** respondio **";
            $this->setUltimoEnvio(array($this->_cliente->__getLastRequest()));
        }else{
            //echo "** No se obtuvo respuesta";
            $error[0] = new stdClass();
            $error[0]->return = new stdClass();
            $error[0]->return->errores = new stdClass();
            $error[0]->return->errores->_c_error = 999998;
            $error[0]->return->errores->_d_error = "No se obtuvo respuesta por parte del Servidor de ANMAT.";
            $this->_ultimaRespuesta = $error;
        }
    }
    
    public function TraerTransaccionesNoConfirmadas($parametros){
        $p = new stdClass();
        $p->arg0 = $this->_usuario->getUsuario();
        $p->arg1 = $this->_usuario->getPassword();
        
        $p->arg2 = $parametros['idTransaccionGlobal'];
        $p->arg3 = $parametros['glnInformador'];
        $p->arg4 = $parametros['glnOrigen'];
        $p->arg5 = $parametros['glnDestino'];
        $p->arg6 = $parametros['gtin'];
        $p->arg7 = $parametros['idEvento'];
        $p->arg8 = $parametros['fechaOperacionDesde'];
        $p->arg9 = $parametros['fechaOperacionHasta'];
        $p->arg10 = $parametros['fechaTransaccionDesde'];
        $p->arg11 = $parametros['fechaTransaccionHasta'];
        $p->arg12 = $parametros['fechaVencimientoDesde'];
        $p->arg13 = $parametros['fechaVencimientoHasta'];
        $p->arg14 = $parametros['nroRemito'];
        $p->arg15 = $parametros['nroFactura'];
        $p->arg16 = $parametros['estado'];

        $intentos = 1;
        do{   
            $this->_cliente = new anmatCliente($this->_header);
            $this->setUltimaRespuesta($this->_cliente->TraerTransaccionesNoConfirmadas($p));
        }while(is_array($this->_ultimaRespuesta[0]) && $intentos < 4 );
        if(is_object($this->_ultimaRespuesta[0])){
            //echo "** respondio **";
            $this->setUltimoEnvio(array($this->_cliente->__getLastRequest()));
        }else{
            //echo "** No se obtuvo respuesta";
            $error[0] = new stdClass();
            $error[0]->return = new stdClass();
            $error[0]->return->errores = new stdClass();
            $error[0]->return->errores->_c_error = 999998;
            $error[0]->return->errores->_d_error = "No se obtuvo respuesta por parte del Servidor de ANMAT.";
            $this->_ultimaRespuesta = $error;
        }        
    }
    
    public function setUltimaRespuesta($ultimaRespuesta){
        unset($this->_ultimaRespuesta);
        $this->_ultimaRespuesta = Array($ultimaRespuesta);
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
