<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';


class ErrorANMAT
{
    private $_db_dM;
    private $_errorId;
    
    public function __construct($id) {
        $this->_db_dM= new FrenteAlmacenamiento('dmelmac');
        $this->_errorId = $id;
    }
    
    public function salvarError($errores){
        //var_dump($errores);EXIT;
        if(is_array($errores)){
            //echo "Error de envio \n";
            foreach($errores AS $error){
                $arrDatos[] =Array($this->_errorId,
                                    $error->_c_error,
                                    $error->_d_error
                            );
            }
        }else{
            //echo "Error de envio <br>\n";
            $arrDatos[] = Array($this->_errorId,
                            $errores->_c_error,
                            $errores->_d_error
                            );
        }
        
        $this->_db_dM->addFrom('relacion_error_id_error_anmat');

        $this->_db_dM->addCamposTabla('error_id');
        $this->_db_dM->addCamposTabla('error_id_anmat');
        $this->_db_dM->addCamposTabla('error_detalle_anamt');
        
        $this->_db_dM->addCamposValue('?');
        $this->_db_dM->addCamposValue('?');
        $this->_db_dM->addCamposValue('?');

        $this->_db_dM->generarInsert();
        //var_dump($arrDatos);exit;
        $rstdoInsertDato = $this->_db_dM->ejecutar($arrDatos);
        
    }

    
}

?>
