<?php
//ALMACENAMIENTO
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
require_once 'UsuariosHerramientas.php';
/**
 * Description of FrenteHerramientas
 *
 * @author lucas
 */

class FrenteHerramientas{
    private $_db;
    private $_colUsuariosHerramientas;
    private $_colUsuarios;
    
    //** NECESARIOS PARA EL FRENTE DE FRENTES//
    private $_ordenColumnas = Array();
    private $_total;
    private $_pagina;
    //private $_objFuncionesComunes;
    
    public function __construct() {
        $this->_db = new FrenteAlmacenamiento();
    }
    
    public function setColUsuariosHerramientas($objHerramienta){
    //var_dump($objHerramienta);exit;
        $this->_colUsuariosHerramientas[$objHerramienta->getHerramientasRelacionId()] = $objHerramienta;
    }
    public function getColUsuariosHerramientas(){
        return $this->_colUsuariosHerramientas;
    }
    public function setColUsuarios($objUsuario){
    //var_dump($objUsuario);exit;
        $this->_colUsuarios = $objUsuario;
    }
    public function getColUsuarios(){
        return $this->_colUsuarios;
    }
    
    public function cargarHerramientas($id){
    //var_dump($id);exit;
        $this->_colUsuariosHerramientas[$id] = NEW UsuariosHerramientas();
        $this->_colUsuariosHerramientas[$id]->cargarMe($id);
    }
    
    public function getIdUsuariosHerramientas(){
        $this->_db->addSelect('seq_usuarios_herramientas_herramientas_relacion_id');
        $this->_db->generarProximo();
        $rs =  $this->_db->ejecutar();
        return $rs[0]['nextval'];
    }
    

    public function cargarHerramientasLazzy($arrHerramientas){
        //echo $arrHerramientas;exit;
        //var_dump($arrHerramientas);exit;
        foreach($arrHerramientas AS $herramientas){
            $objHerramienta = new UsuariosHerramientas();
            $objHerramienta->cargarMe($herramientas);
            $this->setColUsuariosHerramientas($objHerramienta);
            $arrUsuarioId[]['usuario_id'] = $objHerramienta->getUsuarioId();
            }
        $objUsuario = new FrenteUsuarios();
        $objUsuario->cargarUsuarioLazzy($arrUsuarioId);
        $this->setColUsuarios($objUsuario->getColUsuarios());
        //var_dump($this);exit;
        //echo $this->_db->getQry();//exit;
    }
    
    public function buscarUsuariosHerramientas($arrParametros){
    //var_dump($arrParametros);exit;
    $this->_db->addSelect('herramienta_relacion_id');
    $this->_db->addFrom('usuarios_herramientas');
    //$this->_db->addFrom('');

	//**ANALIZO EL WHERE**//
    if($arrParametros['stringBuscar']  && $arrParametros[desdeAlta]==null){
	    $primerRegistro = 0;
	    $ultimoRegistro = 11;
	}else{
	    //La cuenta loca para cargar solo lo que necestiamos mostrar
            $primerRegistro  =   (($arrParametros['pagina'] * $arrParametros['porPagina'] ) - $arrParametros['porPagina'] + 1)-1;
	    $ultimoRegistro =   ($primerRegistro + $arrParametros['porPagina']);
	}
    if($arrParametros['herramientasRelacionId'] !=null && $arrParametros['desdeAlta']==null){
	    $this->_db->addWhere('herramientas_relacion_id = \'' . $arrParametros['herramientasRelacionId'] . '\'');
	}
	//where donde usuario_id = $_SESSIOn usuarioId
    //**fin where **/
	//**INICIO ORDER BY**//
	//*seteo el orden*/
	$this->setOrden($arrParametros);
	$this->_db->AddOrderBy($this->_ordenColumnas[ordenarPor] ." ". $this->_ordenColumnas[ordenarOrden]);
	//**FIN ORDER BY**/
	//*INICIO LIMIT*//
	if($arrParametros['desdeBusqueda']==false){
	    $this->_db->addLimit(500);
	}
	//*FIN LIMIT*//
	$this->_db->generarSelect();
	//echo $this->_db->getQry();exit;
	$resultado =  $this->_db->ejecutar();
	if(count($resultado)==0){
	    return;
	}
	//ahora seteamos el total y la pagina
	$this->setTotal(count($resultado));
	$this->setPagina($arrParametros['pagina']);
	if($this->getTotal() < $ultimoRegistro)	{
	    $ultimoRegistro = $this->getTotal();
	}
	for($i=$primerRegistro;$i<$ultimoRegistro;$i++)	{     
	    $arr[$i]= $resultado[$i]['herramienta_relacion_id'];
	}
	$this->cargarHerramientasLazzy($arr);
    }
    
    public function setOrden($parametros){
	
	if($parametros['ordenarPor'] != "" && $parametros['ordenarOrden'] != ""){
	    $this->_ordenColumnas['ordenarPor']    =   $parametros['ordenarPor'];
	    $this->_ordenColumnas['ordenarOrden']  =   $parametros['ordenarOrden'];
	}else{  //definir default
            $this->_ordenColumnas['ordenarPor']    =   'herramienta_relacion_id';
            $this->_ordenColumnas['ordenarOrden']  =   '';
         }
    }
    
    public function getOrden(){
	return $this->_ordenColumnas;
    }
    public function setTotal($total)
    {
	$this->_total = $total;
    }
    public function getTotal()
    {
	return $this->_total;
    }
    //pagina 1 por defecto amigo
    public function setPagina($pagina=1)
    {
	$this->_pagina = $pagina;
    }
    public function getPagina()
    {
	return $this->_pagina;
    }
    
    public function getDetalles(){
        //var_dump($this->_colUsuariosHerramientas);
        foreach($this->getColUsuariosHerramientas() AS $objHerramienta){
            $arr []   =    array
                (
                    //id si o si un solo string (sin espacios en blanco)
                    "id"            =>  $objHerramienta->getHerramientasRelacionId(),
                    //define las herramientas
                    "herramientas"   =>  array(
                        "cancelable"    =>  false,
                        "editable"      =>  false,
                        "detalles"      =>  false,
                    ),
                    //las celdas propiamente dichas, en el orden en que estan declaradas las columnas
                    "cell"          => array(
                        $this->_colUsuarios[$objHerramienta->getUsuarioId()]->getNombreCompleto(),
                    ),
                );
        }
        return $arr;
    }
    
    public function salvarUsuariosHerramientas($arrParametros){
        //var_dump($arrParametros);exit;
        $UsuariosHerramientas = NEW UsuariosHerramientas();
        $qry =  $UsuariosHerramientas->salvarMe($arrParametros);
        //var_dump($rs);exit;
        $this->_db->setQry($qry);
         //var_dump($this);exit;
        $rs = $this->_db->ejecutar();
        if($rs == true){
            $arrDevolver = array(
                "soyError" => false,
                "nivel" => 0,
                "mensaje" => "Herramienta creda : <br> " . $qry
            );
            //var_dump ($arrMensajeSec);exit;
            //echo json_encode($arrDevolver);
        }else{
            $arrDevolver = array(
                "soyError" => true,
                "nivel" => 1,
                "mensaje" =>"Error al crear la herramienta"
                );
        }
        echo json_encode($arrDevolver);
    }
    
    public function actualizarMe($arrParametros){
        //var_dump($arrParametros);exit;
        $UsuariosHerramientas = NEW UsuariosHerramientas();
        $rs =  $UsuariosHerramientas->actualizarMe($arrParametros);
        if($rs == true){
            $arrDevolver = array(
		    "soyError" => false,
		    "nivel" => 0,
		    "mensaje" =>"Actualizado"
		    );
        }else{
            $arrDevolver = array(
                "soyError" => true,
                "nivel" => 1,
                "mensaje" =>"Error en la actualizacion"
                );
        }
        echo json_encode($arrDevolver);
    } 
    
    public function getDetallesAutocompletar(){
    //echo "llllllll";exit;
	foreach($this->_colUsuariosHerramientas AS $herramienta){
	 $arr[]   =   array(
            'label' => $herramienta->getHerramientaRelacionId(),
            'value' => $herramienta->getHerramientaRelacionId()
            );
        }		
	return $arr;
    }

}






