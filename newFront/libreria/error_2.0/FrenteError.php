<?php
//ALMACENAMIENTO
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';

//ERROR
require_once 'Error.php';

/**
 * Description of FrenteError
 * Manejador de Errores
 * @author dMELMAC
 */


class FrenteError {

    var $_colObjError = Array();
    var $_qry;
    
    
    private function setColObjError($obj){
        $this->_colObjError[] = $obj;
    }
    
    public function getColObjError(){
        return $this->_colObjError;
    }
    
     public function setQry($qry){
         $this->_qry = $qry;
     }

     public function getQry(){
         return $this->_qry;
     }
    
    public function cargarError(){
        foreach($this->getColObjError() AS $objError){
            $arrErrores[] = $objError->getDetalle();
        }
        $txtErrores=  implode('<br>', $arrErrores);
        $arrDevolver = array(
		      "soyError" => true,
		      "nivel" => 1,
		      "mensaje" => $txtErrores
		    );
	echo json_encode($arrDevolver);exit;
    }
    
    public function cargarErrorTabla(){
        foreach($this->getColObjError() AS $objError){
            $arrErrores[] = $objError->getDetalle();
        }
        $arrDevolver = array(
		      "soyError" => true,
		      "mensaje" => '',
                      "tabla" => $arrErrores[0]
		    );
//                    var_dump($arrDevolver);
        echo json_encode($arrDevolver);exit;
    }
    
    public function generarError($arrError){
        $obj = new Error2($arrError);
        $this->setColObjError($obj);
    }

}


