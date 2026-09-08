<?php

class Sql
{
	private $_qry;

	private $_colCamposUpdate = Array();
    
	private $_colWhere = Array();
	
	private $_colFromTabla = Array();

	private $_colFrom = Array();

	private $_colCamposTabla = Array();

	private $_colCamposValue = Array();

	private $_colOrder = Array();
	
	private $_colGroup = Array();

	private $_colLimit = Array();

	public function addWhere( $where ) {
		$this->_colWhere[] = $where;
	}

	public function addSelect( $select ) {
		$this->_colCamposTabla[] = $select;
	}

	public function addFrom( $from ) {
		$this->_colFrom[] = $from;
	}

	public function addFromTabla( $fromTabla ) {
		$this->_colFromTabla[] = $fromTabla;
	}
	
	public function addCamposTabla( $campos ) {
		$this->_colCamposTabla[] = $campos;
	}

	public function addCamposValue( $campos ) {
		$this->_colCamposValue[] = $campos;	
	}

        public function addCamposUpdate( $campos ) {
		$this->_colCamposUpdate[] = $campos;	
	}
    
	public function addOrderBy($campos){
		$this->_colOrder[] = $campos;
	}

	public function addGroupBy($campos){
		$this->_colGroup[] = $campos;
	}

	public function addLimit( $limit ) {
		$this->_colLimit[] = $limit;
	}

	public function generarSelect() {
	
		$campos = implode(", ",$this->_colCamposTabla);
		$from = implode(" ", array_unique($this->_colFrom));
		$where = implode(" AND ", array_unique($this->_colWhere));
		$order = '';
		$group = '';
		$limit = '';
		if(count($this->_colLimit) > 0){
			$limit = " LIMIT ";
			$limit .= implode(" ", array_unique($this->_colLimit));
		}
		if(count($this->_colOrder) > 0){
			$order = " ORDER BY ";
			$order .= implode(" ", array_unique($this->_colOrder));
		}
		if(count($this->_colGroup)>0){
			$group = " GROUP BY ";
			$group.= implode("", array_unique($this->_colGroup));
		}
	        if($where !=''){
        	    $where = "WHERE " . $where;
	        }
		$this->_qry = "SELECT " . $campos . " FROM " . $from . " " . $where . $group . $order . $limit .";";
		//echo "<h3>" . $this->_qry . "</h3>";

	        //limpiamos los array para poder volver a cargar otra consulta
        	$this->_colWhere = Array();
        	$this->_colFrom = Array();
        	$this->_colCamposTabla = Array();
	        $this->_colOrder = Array();
		$this->_colLimit = Array();
		$this->_colGroup = Array();
	}

	public function generarInsert() {
		$tabla = implode(" ",$this->_colFrom);
		$campos = implode(", ",$this->_colCamposTabla);
		$valores = implode(",",$this->_colCamposValue);

		$this->_qry = "INSERT INTO " . $tabla . "(" . $campos . ") Values (" . $valores . ");";
		//echo "<h2>" . $this->_qry . "</h2>"; exit;
		//limpiamos los array para poder volver a cargar otra consulta
        	$this->_colCamposValue = Array();
	        $this->_colFrom = Array();
        	$this->_colCamposTabla = Array();
	}
        public function generarInsertMigracion() {
		$tabla = implode(" ",$this->_colFrom);
		$campos = implode(", ",$this->_colCamposTabla);
		$valores = implode(",",$this->_colCamposValue);

		$this->_qry = "INSERT INTO " . $tabla . "(" . $valores . ");";
		//echo "<h2>" . $this->_qry . "</h2>"; exit;
		//limpiamos los array para poder volver a cargar otra consulta
        	$this->_colCamposValue = Array();
	        $this->_colFrom = Array();
        	$this->_colCamposTabla = Array();
	}
	public function generarSetVal() {
		$campos = implode(", ",$this->_colCamposTabla);
		
		
		$this->_qry = "SELECT * FROM " . $campos . ";";
		//echo "<h3>" . $this->_qry . "</h3>";

	        //limpiamos los array para poder volver a cargar otra consulta
        	$this->_colWhere = Array();
        	$this->_colFrom = Array();
        	$this->_colCamposTabla = Array();
	        $this->_colOrder = Array();
		$this->_colLimit = Array();
		$this->_colGroup = Array();
	}
	public function generarUpdate() {
            $from = implode(" ", array_unique($this->_colFrom));
            $update = implode(",", array_unique($this->_colCamposUpdate));
            $where = implode(" AND ", array_unique($this->_colWhere));
            $fromTabla = implode(" ", array_unique($this->_colFromTabla));
	    
	    if($fromTabla !=null){
		$fromTabla = " FROM  $fromTabla";
	    }
          
            $this->_qry = "UPDATE " . $from . " SET " . $update .  $fromTabla . " WHERE " . $where .";";
            //echo "<h2>" . $this->_qry . "</h2>"; exit;
            //limpiamos los array para poder volver a cargar otra consulta
            $this->_colWhere = Array();
            $this->_colFrom = Array();
            $this->_colCamposUpdate = Array();
	}

	public function generarDelete() {
	    $from = implode(" ", array_unique($this->_colFrom));
	    $where = implode(" AND ", array_unique($this->_colWhere));
	    $this->_qry = "DELETE FROM " . $from  .  ' WHERE ' . $where . ";";
	    $this->_colWhere = Array();
	    $this->_colFrom = Array();
    }
	

    public function generarProximo(){
        $select = implode(",", array_unique($this->_colCamposTabla));
        $this->_qry = "SELECT  nextval('" . $select . "');";
        //echo "<h2>" . $this->_qry . "</h2>"; exit;
        $this->_colCamposTabla = Array();
    }
	public function generarCrearTabla() {
	
		$campos = implode(", ",$this->_colCamposTabla);
		$nombreTabla = implode(" ", array_unique($this->_colFrom));
		$where = implode(" AND ", array_unique($this->_colWhere));
		$fromTabla = implode(" ", array_unique($this->_colFromTabla));
	    
		if($fromTabla !=null){
		    $fromTabla = " FROM  $fromTabla";
		}
          
	        if($where !=''){
        	    $where = "WHERE " . $where;
	        }
		$this->_qry = "CREATE TABLE  " . $nombreTabla . " AS SELECT   " . $campos. $fromTabla . " " . $where . $group . $order . $limit .";";
		//echo "<h3>" . $this->_qry . "</h3>";

	        //limpiamos los array para poder volver a cargar otra consulta
        	$this->_colWhere = Array();
        	$this->_colFrom = Array();
        	$this->_colCamposTabla = Array();
	        $this->_colOrder = Array();
		$this->_colLimit = Array();
		$this->_colGroup = Array();
	}
    public function setearSecuencia(){
        $select = implode(",", array_unique($this->_colCamposTabla));
        $valores = implode(",",$this->_colCamposValue);
        $this->_qry = "SELECT  setval('" . $select . "',$valores);";
        //echo "<h2>" . $this->_qry . "</h2>"; exit;
        $this->_colCamposTabla = Array();
        $this->_colCamposValue= Array();
    }

    public function getQry(){
            return $this->_qry;
    }
        
    public function setQry($qry){
        $this->_qry = $qry;
    }

}

