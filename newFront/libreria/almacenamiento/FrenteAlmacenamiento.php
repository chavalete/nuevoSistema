<?php
require_once 'miPDO.php';
require_once 'Sql.php';

class FrenteAlmacenamiento
{
    private $_db;  
    private $_sql;
    private $_base = null;
    private $_arrError = array();
        
    public function __construct($db=null) {
        if($db !=null){
            $this->_base = $db;
        }
        $this->_sql = new Sql();
    }

    public function setArrError($error){
        $this->_arrError = $error;
    }
    
    public function getArrError(){
        return $this->_arrError;
    }

    //PARA ARMAR SQL 
  	public function addWhere( $where ) {
	    $this->_sql->addWhere($where);
	} 

    	public function addSelect( $select ) {
            $this->_sql->addSelect($select);
	} 

	public function addFrom( $from ) {
	    $this->_sql->addFrom($from);
	}

	public function addFromTabla( $fromTabla ) {
	    $this->_sql->addFromTabla($fromTabla);
	}
	
	public function addOrderBy($order){
	    $this->_sql->addOrderBy($order);
	}

	public function addGroup($group){	
	    $this->_sql->addGroupBy($group);
	}

	public function addLimit($limit){	
	    $this->_sql->addLimit($limit);
	}

	public function addCamposTabla( $campos ) {
	    $this->_sql->addCamposTabla($campos);
	}

	public function addCamposValue( $campos ) {
	    $this->_sql->addCamposValue($campos);
	}
    
	public function addCamposUpdate( $campos ) {
	    $this->_sql->addCamposUpdate($campos);
	}
    
    //ARMADO DEL QRY
	public function generarSelect() {
	    $this->_sql->generarSelect();
	}

	public function generarInsert() {
            $this->_sql->generarInsert();
	}
	public function generarInsertMigracion() {
            $this->_sql->generarInsertMigracion();
	}
	public function generarSetVal(){
	    $this->_sql->generarSetVal();
	}
	public function generarUpdate() {
            $this->_sql->generarUpdate();
    	}
    
	public function generarDelete() {
            $this->_sql->generarDelete();
    	}

	public function generarCrearTabla(){
	    $this->_sql->generarCrearTabla();
	}

	public function generarProximo(){
            $this->_sql->generarProximo();
	}
	public function setearSecuencia(){
            $this->_sql->setearSecuencia();
	}
    
    //EJECUCION        
    public function ejecutar($arrayDatos = null){
        try{
            $this->_db = new miPDO($this->_base);
            $this->_db->beginTransaction();
            $qry = $this->_db->prepare($this->getQry());
            if(is_array($arrayDatos)){
                foreach($arrayDatos as $datos){
                    $qry->execute($datos);
                    //verificamos si se produce un error
                    $arrError = $this->_db->errorInfo();
                    if ($arrError[2] != NULL){
                        $arrError['detalle'] = substr($arrError['2'], 0, 103);
                        $arrError['archivo'] = __FILE__;
                        $this->setArrError($arrError);
                        $this->_db = null;
                        return;
                        
                    }
                } 
            }else{
                $qry->execute();
                //verificamos si se produce un error
                $arrError = $this->_db->errorInfo();
                if ($arrError[2] != NULL){
                    $arrError['detalle'] = substr($arrError['2'], 0, 103);
                    $arrError['archivo'] = __FILE__;
                    $this->setArrError($arrError);
                    $this->_db = null;
                    return;
                }
            }
            $this->_db->commit();
            $this->_db = null;
            
            if(substr($this->getQry(), 0 , 6) == 'SELECT') {
                return $qry->fetchAll(PDO::FETCH_ASSOC);
            }ELSE{
                return $qry->rowCount();
            }

            
        } catch (PDOException $e){
              $this->_db->rollBack();
              //print_r($e);exit;
              $arrError['detalle'] = str_pad(substr($e->getMessage(), 0, -186), 0);
              $arrError['archivo'] = __FILE__;
              $this->setArrError($arrError);
              $this->_db = null;
              //exit;              
        }
    }
    
    public function ejecutarTransaccion(){
        try{
            $this->_db = new miPDO($this->_base);
            $this->_db->beginTransaction();
            $this->_db->exec($this->getQry());
            $this->_db->commit();
            $error = $this->_db->errorInfo();
            //echo $error[0];
            if($error[0] != '00000'){
                $arrError['detalle'] = $error[2];
                $arrError['archivo'] = __FILE__;
                $this->setArrError($arrError);
                $this->_db = null;
                return;
            }
            $this->_db = null;
            
        } catch (PDOException $e){
              $this->_db->rollBack();
              //print_r($e);exit;
              $arrError['detalle'] = str_pad(substr($e->getMessage(), 0, -186), 0);
              $arrError['archivo'] = __FILE__;
              $this->setArrError($arrError);
              $this->_db = null;
              //exit;              
        }
    }    
     
    //cONTIENE EL SQL ARMADO
    public function getQry(){
        return $this->_sql->getQry();
    }
     
    public function setQry($qry){
        $this->_sql->setQry($qry);
    }
}

?>

