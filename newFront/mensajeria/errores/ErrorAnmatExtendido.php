<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/mensajeria/errores/ErrorAnmat.php';


class ErrorAnmatExtendido extends ErrorAnmatWeb
{
    private $_db;    
    private $_arrError = Array();
    
    public function __construct(){
	    
	$this->_db = new FrenteAlmacenamiento();
    }
    
    public function setArrError($arr){
        $this->_arrError = $arr;
    }

    public function getArrError(){
        return $this->_arrError;
    }
    
    public function getDataDB($id){
	
	$this->_db->addSelect('error_id');
	$this->_db->addSelect('error_id_anmat');
	$this->_db->addSelect('error_detalle_anamt');

	$this->_db->addFrom('relacion_error_id_error_anmat');

	$this->_db->addWhere('error_id = \'' . $id . '\'');

	$this->_db->generarSelect();

	$arrError = $this->_db->ejecutar();

	//verificamos que el movimiento exista en la db
        if(count($arrError) == 0){
            $errorDetalle = 'El error enviado no existe';
            $errorArchivo = __FILE__;
            
        }
    }
    public function cargarme($datos){
	
	if(is_numeric($datos)){
	    $resultado = $this->getDataDB($datos);
	}else{
	    $resultado = $datos;
	}
    
	$this->setErrorId($resultado[error_id]);
	$this->setErrorIdAnmat($resultado[error_id_anmat]);
	$this->setErrorDesc($resultado[error_detalle_anamt]);

    }
    public function salvarme($errores){
        //genero el id unido de error
        $this->_db->addSelect('seq_error_transaccion_error_id');
        $this->_db->generarProximo();
        $arrErrorId = $this->_db->ejecutar();
        $this->setErrorId($arrErrorId[0]['nextval']);
        // fin
        if(is_array($errores)){
            //echo "Error de envio array \n";
            foreach($errores AS $error){
                $arrDatos[] =Array($this->getErrorId(),
                                    $error->_c_error,
                                    $error->_d_error
                            );
		//echo "$error->_d_error \n";
            }
        }else{
            //echo "Error de envio simple \n";
            $arrDatos[] = Array($this->getErrorId(),
                            $errores->_c_error,
                            $errores->_d_error
                            );
	    //echo "$errores->_d_error \n";
        }
        
        $this->_db->addFrom('relacion_error_id_error_anmat');

        $this->_db->addCamposTabla('error_id');
        $this->_db->addCamposTabla('error_id_anmat');
        $this->_db->addCamposTabla('error_detalle_anamt');
        
        $this->_db->addCamposValue('?');
        $this->_db->addCamposValue('?');
        $this->_db->addCamposValue('?');

        $this->_db->generarInsert();
        //var_dump($arrDatos);exit;
        $rstdoInsertDato = $this->_db->ejecutar($arrDatos);
        
    }

    public function actualizarme(){}
}