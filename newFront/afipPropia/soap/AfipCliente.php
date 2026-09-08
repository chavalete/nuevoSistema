<?php

class afipCliente extends SoapClient
{
    private $_wsdl;
    private $_opciones = Array();
    private $_header;
    

    public function __construct()     {
        clearstatcache();
	ini_set("soap.wsdl_cache_enabled", "0");
	ini_set('soap.wsdl_cache_ttl',0);
        //clearstatcache();
	//server de desarrollo
	/*
         $this->_wsdl = NULL;
        $this->_wsdl = 'https://wswhomo.afip.gov.ar/wsfev1/service.asmx?WSDL';
        $this->_opciones['location'] = 'https://wswhomo.afip.gov.ar/wsfev1/service.asmx';
        $this->_opciones['trace'] = true;
        //$this->_header = $header;
	 */
        

	/*server de produccion*/
    	$this->_wsdl = 'https://servicios1.afip.gov.ar/wsfev1/service.asmx?WSDL';
        //$this->_opciones['location'] = 'https://servicios1.afip.gov.ar/wsfev1/service.asmx;
        $this->_opciones['trace'] = true;
        //$this->_header = $header;*/
	

        SoapClient::SoapClient($this->_wsdl,$this->_opciones);
        
        //$this->__setSoapHeaders($this->_header);
        
    }
    public function getUltimoNro($parametros){
        try{
            //var_dump($parametros);exit;
            $resultado = $this->FECompUltimoAutorizado($parametros);
            //var_dump($resultado);exit;
            return $resultado;
        }catch(Exception $e){
            //var_dump($e);
            $arrError['error'] = TRUE;
            $arrError[]= $e->getMessage();
            $arrError[]= $e->getFile();
            return $arrError;
        }
    }
    public function pedirCAE($parametros){
        try{
            //var_dump($parametros);exit;
            $resultado = $this->FECAESolicitar($parametros);
            //print_r($resultado);exit;
            return $resultado;
        }catch(Exception $e){
            //var_dump($e);
            $arrError['error'] = TRUE;
            $arrError[]= $e->getMessage();
            $arrError[]= $e->getFile();
            return $arrError;
        }
    }
    public function enviarMedicamentos($parametros){
        try{
        $resultado = $this->sendMedicamentos($parametros);
        return $resultado;
        }catch(Exception $e){
            $arrError['error'] = TRUE;
            $arrError[]= $e->getMessage();
            $arrError[]= $e->getFile();
            return $arrError;
        }
    }

    public function enviarMedicamentosDHSerie($parametros){
        try{
        $resultado = $this->sendMedicamentosDHSerie($parametros);
        return $resultado;
        }catch(Exception $e){
            $arrError['error'] = TRUE;
            $arrError[]= $e->getMessage();
            $arrError[]= $e->getFile();
            return $arrError;
        }
    }
    
    public function enviarCancelacTransacc($parametros){
        try{
        $resultado = $this->sendCancelacTransacc($parametros);
        return $resultado;
        }catch(Exception $e){
            $arrError['error'] = TRUE;
            $arrError[]= $e->getMessage();
            $arrError[]= $e->getFile();
            return $arrError;
        }
    }          
    
    public function TraerTransaccionesNoConfirmadas($parametros){
        try{
            $resultado = $this->getTransaccionesNoConfirmadas($parametros);
            return $resultado;
        }catch(Exception $e){
            $arrError['error'] = TRUE;
            $arrError[]= $e->getMessage();
            $arrError[]= $e->getFile();
            return $arrError;
        }                
    }
    
    public function enviarConfirmacionTransacc($parametros){
        try{
            $resultado = $this->sendConfirmaTransacc($parametros);
            return $resultado;
        }catch(Exception $e){
            $arrError['error'] = TRUE;
            $arrError[]= $e->getMessage();
            $arrError[]= $e->getFile();
            return $arrError;
        }                
    }

    
    public function grabarAsignarNroAutorizacion($parametros){
        try {
            $resultado = $this->sendAsignarNroAutorizacion($parametros);
            return $resultado;
        } catch (Exception $e) {
            $arrError['error'] = TRUE;
            $arrError[]= $e->getMessage();
            $arrError[]= $e->getFile();
            return $arrError;
        }
    }
}


