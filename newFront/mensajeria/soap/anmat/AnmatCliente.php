<?php

class anmatCliente extends SoapClient
{
    private $_wsdl;
    private $_opciones = Array();
    private $_header;
    

    public function __construct($header)     {
        //server de desarrollo
        // $this->_wsdl = NULL;
        /*$this->_wsdl = 'https://servicios.pami.org.ar/trazamed.WebService?wsdl';
        $this->_opciones['location'] = 'https://servicios.pami.org.ar/trazamed.WebService';
        $this->_opciones['trace'] = true;
        $this->_header = $header;
        
        */
	/*server de produccion*/
	$this->_wsdl = 'https://trazabilidad.pami.org.ar:9050/trazamed.WebService?wsdl';
        $this->_opciones['location'] = 'https://trazabilidad.pami.org.ar:9050/trazamed.WebService';
        $this->_opciones['trace'] = true;
        $this->_header = $header;
	
        SoapClient::SoapClient($this->_wsdl,$this->_opciones);
        
        $this->__setSoapHeaders($this->_header);
        
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
    public function enviarAlertaTransacc($parametros){
        try{
            $resultado = $this->sendAlertaTransacc($parametros);
            return $resultado;
        }catch(Exception $e){
            $arrError['error'] = TRUE;
            $arrError[]= $e->getMessage();
            $arrError[]= $e->getFile();
            return $arrError;
        }                
    }
}


