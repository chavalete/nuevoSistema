<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/actores/PacienteExtendido.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
/*
FRENTE PARA CARGAR LO QUE EL FRENTE requiera en cuanto a estetica y datos
*/

class FrentePacientes
{
    private $_colPacientes = Array();	    
    private $_ordenColumnas = Array();
    private $_total;
    private $_pagina;
    private $_objFuncionesComunes;
    
    public function __construct(){
	$this->_db = NEW FrenteAlmacenamiento('dmelmac');
	$this->_objFuncionesComunes = new FuncionesComunes();
    }
    public function cargarPaciente($id){
	
	$this->_colPacientes[$id] = NEW PacienteExtendido();
	$this->_colPacientes[$id]->cargarMe($id);
    }
    public function setColObjPaciente($objPaciente){
    
	$this->_colPacientes[$objPaciente->getPacienteId()] = $objPaciente;
    }
    public function getColObjPaciente(){
	return $this->_colPacientes;
    }
    public function cargarPacientesLazzy($arrIds){
    
	foreach($arrIds as $id){
	    if($id['paciente_id']!=NULL){
		$ids.= "'" . $id['paciente_id'] . "',";
	    }
	}
	
	$ids=substr($ids, 0, -1);
	$this->_db->addSelect('*');
	$this->_db->addSelect('paciente_activo AS paciente_activo');
	$this->_db->addSelect('\'Activo\'  AS paciente_activo_desc');
	$this->_db->addSelect('\'Inactivo\'  AS paciente_no_activo_desc');
	$this->_db->addSelect('CASE WHEN paciente_activo THEN \'Activo\' ELSE \'Inactivo\' END AS paciente_activo_mensaje');
        $this->_db->addFrom('datos_pacientes');
        $this->_db->addWhere("paciente_id IN (" . $ids . ")" );
	$this->_db->generarSelect();

	//echo $this->_db->getQry();
	
        $resultado = $this->_db->ejecutar();
        
        
        $creados=0;
        
	if(count($resultado)>0){
	    foreach($resultado AS $detalle){
		$objPaciente = NEW PacienteExtendido();
		$objPaciente->cargarMe($detalle);
		$this->setColObjPaciente($objPaciente);
		$creados++;
	    }
	}
	if($creados<count($arrIds)){
	    for($i=$creados;$i<count($arrIds);$i++){
		$objPaciente = NEW PacienteExtendido();
		$this->setColObjPaciente($objPaciente);
	    }
	}
	
    }
    public function buscarPacientes($arrParametros){

	$this->_db->addSelect('paciente_id');

	$this->_db->addFrom('datos_pacientes');

	//**ANALIZO EL WHERE**//

	if($arrParametros['stringBuscar']){

	    $this->_db->addWhere('paciente_nombre ILIKE  \'%' . $arrParametros[stringBuscar] .'%\'');
	    $primerRegistro = 0;
	    $ultimoRegistro = 11;
	    
	}else{
	    //La cuenta loca para cargar solo lo que necestiamos mostrar
	    $primerRegistro  =   (($arrParametros['pagina'] * $arrParametros['porPagina'] ) - $arrParametros['porPagina'] + 1)-1;
	    $ultimoRegistro =   ($primerRegistro + $arrParametros['porPagina']) -1;
	}

	//**where faso = true**/
	if($arrParametros['pacientes'] !=null && $arrParametros['desdeAlta']==null){

	    $this->_db->addWhere('paciente_id = \'' . $arrParametros['pacientes'] . '\'');
	}
	if($arrParametros['nroAfiliado'] !=null && $arrParametros['desdeAlta']==null){

	    $this->_db->addWhere('nro_afiliado = \'' . $arrParametros['nroAfiliado'] . '\'');
	}
	if($arrParametros['obrasSociales'] !=null && $arrParametros['desdeAlta']==null){

	    $this->_db->addWhere('obra_social_id = \'' . $arrParametros['obrasSociales'] . '\'');
	}
	
	
	//**fin where **/

	//**INICIO ORDER BY**//
	//*seteo el orden*/
	$this->setOrdenPacientes($arrParametros);
	$this->_db->AddOrderBy($this->_ordenColumnas[ordenarPor] ." ". $this->_ordenColumnas[ordenarOrden]);
	//**FIN ORDER BY**/
	
	//*INICIO LIMIT*//
	if($arrParametros['tipo']=='pacientes'){

	    $this->_db->addLimit(100);
	}
	//*FIN LIMIT*//
	$this->_db->generarSelect();

	$arr =  $this->_db->ejecutar();    
	
	//ahora seteamos el total y la pagina
	$this->setTotal(count($arr));
	$this->setPagina($arrParametros['pagina']);
	
	if($this->getTotal() < $ultimoRegistro){
	    $ultimoRegistro = $this->getTotal();
	}	

	for($i=$primerRegistro;$i<$ultimoRegistro;$i++){
	    
	    $arrIds[$i]['paciente_id'] = $arr[$i]['paciente_id'];
	}
	$this->cargarPacientesLazzy($arrIds);
    }
    public function setOrdenPacientes($arrParametros){
	if($arrParametros['ordenarPor'] != "" && $arrParametros['ordenarOrden'] != ""){
	    $this->_ordenColumnas['ordenarPor']    =   $arrParametros['ordenarPor'];
	    $this->_ordenColumnas['ordenarOrden']  =   $arrParametros['ordenarOrden'];
	}else{
	    //definir default
	    $this->_ordenColumnas['ordenarPor']    =   'paciente_nombre';
	    $this->_ordenColumnas['ordenarOrden']  =   'asc';
	}
    }
    public function getOrdenPacientes(){
	return $this->_ordenColumnas;
    }

    public function setTotal($total){
	$this->_total = $total;
    }
    public function getTotal(){
	return $this->_total;
    }
    //pagina 1 por defecto amigo
    public function setPagina($pagina=1){
	$this->_pagina = $pagina;
    }
    public function getPagina(){
	return $this->_pagina;
    }

    public function getDetalles(){

	foreach($this->_colPacientes AS $paciente){
	    $arr []   =    array
            (
                //id si o si un solo string (sin espacios en blanco)
                "id"            =>  $paciente->getPacienteId(),
                //define las herramientas
                "herramientas"   =>  array(
                    "cancelable"    =>  false,
                    "editable"      =>  true,
                    "detalles"      =>  false,
                ),
                //las celdas propiamente dichas, en el orden en que estan declaradas las columnas
                "cell"          => array(
                    $paciente->getPacienteNombre(),
                    $paciente->getNroAfiliado(),
                    $paciente->getPacienteTelefono(),
                    //$paciente->getObjObraSocial()->getObraSocialNombre(),
		    $paciente->getObs(),
                    Array
			(
			"value" => $paciente->getPacienteActivo(),
			"label" =>$paciente->getPacienteActivoMensaje(),
			"select" => 
			    Array 
			    (
				Array(
				"value" =>true,
				"label" =>$paciente->getPacienteActivoDesc()

				),
				Array

				(
				"value" =>false,
				"label" =>$paciente->getPacienteNoActivoDesc()

				)		
			    )
			)
                ),
            );
	}		
	return $arr;
    }
    public function getDetallesAutocompletar(){
	foreach($this->_colPacientes AS $paciente){
	 $arr[]   =   array(
            'label' =>  $paciente->getPacienteNombre(),
            'value' =>  $paciente->getPacienteId()
            );
	}		
	return $arr;
    }
    public function salvarMe($arrParametros){

	$pacienteExtendido = NEW PacienteExtendido();
	$pacienteExtendido->salvarMe($arrParametros);
    }

    public function actualizarMe($arrParametros){

	$pacienteExtendido = NEW PacienteExtendido();
	$pacienteExtendido->actualizarMe($arrParametros);
    }

}
?>
