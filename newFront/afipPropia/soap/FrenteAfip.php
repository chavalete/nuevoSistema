<?php
require_once 'AfipCliente.php';
class FrenteAfip
{
    private $_db;
    private $_token;
    private $_sign;
    private $_arrCredenciales;
    public function __construct() {
        ini_set("soap.wsdl_cache_enabled", "0");
    }
    public function getCredenciales(){
        return $this->_arrCredenciales;
    }
    public function validarCredenciales(){
	    //echo "kkega";
        $xml = simplexml_load_file('/var/www/html/newFront/afipPropia/TA.xml');
        //var_dump($xml);exit;
        foreach ($xml->header AS $data){
            date_default_timezone_set('America/Argentina/Buenos_Aires');
            $ahora=date("Y-m-d H:i:s");
            $fechaHoraVigencia = substr($data->expirationTime,0,10) ." ". substr($data->expirationTime,11,5);
            if($ahora> $fechaHoraVigencia){
                //echo  "\n Credenciales Vencidas";
                $this->generarCredenciales();
            }else{
                //echo "\n Cargamos credenciales";
                $this->cargarCredenciales();

            }
        }
    }
    public function generarCredenciales(){
        //echo  "\n Generando Credenciales";
        $salida = shell_exec('php /var/www/html/newFront/afipPropia/wsaa-corvision.php');
        //var_dump($salida);
        $this->cargarCredenciales();
    }
    public function cargarCredenciales(){
        $xml = simplexml_load_file('/var/www/html/newFront/afipPropia/TA.xml');
        foreach ($xml->credentials AS $data){
            $this->_arrCredenciales=
            array(
                'Token'=>"$data->token",
                'Sign'=>"$data->sign",
                'Cuit'=>20384567798
            );
        }
    }
    public function traerUltimoComprobante($parametros){
       // var_dump($parametros);exit;
        $p = new stdClass();
        $p->Auth = $this->getCredenciales();
        $p->PtoVta = $parametros['factura_puesto_venta'];
        $p->CbteTipo = $parametros['factura_tipo'];
        //print_r($p);exit;
        $intentos = 1;
        do{
            $this->_cliente= new AfipCliente();
            $this->setUltimaRespuesta($this->_cliente->getUltimoNro($p));
            //echo " Intento = " . $intentos++ . "/3 ";
        }while(is_array($this->_ultimaRespuesta) && $intentos < 4 );
        if(is_object($this->_ultimaRespuesta)){
            //echo "** respondio **";
            $this->setUltimoEnvio(array($this->_cliente->__getLastRequest()));
        }else{
            //echo "** No se obtuvo respuesta";
            $error[0] = new stdClass();
            $error[0]->return = new stdClass();
            $error[0]->return->errores = new stdClass();
            $error[0]->return->errores->_c_error = 999998;
            $error[0]->return->errores->_d_error = "No se obtuvo respuesta por parte del Servidor de AFIP.";
            $this->_ultimaRespuesta = $error;
        }
        foreach($this->getUltimaRespuesta() AS $respuesta){
            $nroFactura =  $respuesta->CbteNro + 1;
            return $nroFactura;
        }
    }
    public function solicitarCAE($parametros){
        //var_dump($parametros);exit;
        $f = new stdClass();
        $f->Auth = $this->getCredenciales();
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
            //echo "** respondio **";
            $this->setUltimoEnvio(array($this->_cliente->__getLastRequest()));
        }else{
            //echo "** No se obtuvo respuesta";
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
