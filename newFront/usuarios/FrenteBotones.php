<?php
//ALMACENAMIENTO
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
require_once 'UsuariosBotonera.php';
/**
 * Description of FrenteBotones
 *
 * @author lucas
 */

class FrenteBotones{
    private $_db;
    private $_colUsuariosBotones;
//    private $_colUsuariosHerramientas;
    private $_colUsuarios;
    
    //** NECESARIOS PARA EL FRENTE DE FRENTES//
    private $_ordenColumnas = Array();
    private $_total;
    private $_pagina;
    //private $_objFuncionesComunes;
    
    public function __construct() {
        $this->_db = new FrenteAlmacenamiento();
    }
    
    public function setColUsuariosBotones($objBoton){
    //var_dump($objBoton);exit;
        $this->_colUsuariosBotones[$objBoton->getRelacionBotonId()] = $objBoton;
    }
    public function getColUsuariosBotones(){
        return $this->_colUsuariosBotones;
    }
    public function setColUsuarios($objUsuario){
    //var_dump($objUsuario);exit;
        $this->_colUsuarios = $objUsuario;
    }
    public function getColUsuarios(){
        return $this->_colUsuarios;
    }
    
    public function cargarBotones($id){
    //var_dump($id);exit;
        $this->_colUsuariosBotones[$id] = NEW UsuariosBotonera();
        $this->_colUsuariosBotones[$id]->cargarMe($id);
    }
    
    public function getIdUsuariosBotones(){
        $this->_db->addSelect('seq_usuarios_botonera_relacion_boton_id');
        $this->_db->generarProximo();
        $rs =  $this->_db->ejecutar();
        return $rs[0]['nextval'];
    }
    

    public function cargarBotonesLazzy($arrIds){
        //echo "llega";
        //var_dump($arrIds);exit;
        if(is_array($arrIds)){
            foreach($arrIds as $id){
                if($id!=null){
                    $ids.= $id . ",";
                }
            }
            //var_dump($ids);exit;
            $ids=substr($ids, 0, -1);
        }else{
            $ids=$arrIds;
        }
        //$ids=substr($ids, 0, -1);
        $this->_db->addSelect('*');
        $this->_db->addFrom('usuarios_botonera');
        $this->_db->addWhere("usuario_id IN (" . $ids . ")" );
        $this->_db->AddOrderBy('usuario_id');
        //$this->_db->addWhere('modelo_activo = true');
        $this->_db->generarSelect();
        //var_dump($this);exit;
        //echo $this->_db->getQry();exit;
        $resultado = $this->_db->ejecutar();
        foreach($resultado   AS $detalle){
            $objBoton = NEW UsuariosBotonera();
            $objBoton->cargarMe($detalle);
            $this->setColUsuariosBotones($objBoton);
            $arrUsuarioId[]['usuario_id'] = $objBoton->getUsuarioId();
        }
        $objUsuario = new FrenteUsuarios();
        $objUsuario->cargarUsuarioLazzy($arrUsuarioId);
        $this->setColUsuarios($objUsuario->getColUsuarios());
        //var_dump($this);exit;
        //echo $this->_db->getQry();//exit;
    }

    public function buscarUsuariosBotonera($arrParametros){
    //var_dump($arrParametros);exit;
    $this->_db->addSelect('relacion_boton_id');
    $this->_db->addFrom('usuarios_botonera');
    //$this->_db->addFrom('');

	//**ANALIZO EL WHERE**//
    if($arrParametros['stringBuscar']  && $arrParametros[desdeAlta]==null){
        $this->_db->addFrom('INNER JOIN datos_usuarios USING (usuario_id)');
        $this->_db->addWhere('usuario_nombre_completo ILIKE  \'%' . $arrParametros[stringBuscar] .'%\'');
	    $primerRegistro = 0;
	    $ultimoRegistro = 11;
	}else{
	    //La cuenta loca para cargar solo lo que necestiamos mostrar
            $primerRegistro  =   (($arrParametros['pagina'] * $arrParametros['porPagina'] ) - $arrParametros['porPagina'] + 1)-1;
	    $ultimoRegistro =   ($primerRegistro + $arrParametros['porPagina']);
	}
	if($arrParametros['usuariosBotonera'] !=null && $arrParametros['desdeAlta']==null){
	    $this->_db->addWhere('usuario_id= \'' . $arrParametros['usuariosBotonera'] . '\'');
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
	//echo $this->_db->getQry();//exit;
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
	    $arr[$i]= $resultado[$i]['relacion_boton_id'];
	}
	$this->cargarBotonesLazzy($arr);
    }
    
    public function setOrden($parametros){
	
	if($parametros['ordenarPor'] != "" && $parametros['ordenarOrden'] != ""){
	    $this->_ordenColumnas['ordenarPor']    =   $parametros['ordenarPor'];
	    $this->_ordenColumnas['ordenarOrden']  =   $parametros['ordenarOrden'];
	}else{  //definir default
            $this->_ordenColumnas['ordenarPor']    =   'relacion_boton_id';
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
        //var_dump($this->_colUsuariosBotones);
        foreach($this->getColUsuariosBotones() AS $objBoton){
            switch ($objBoton->getSucursalId()){
                case 2:
                    $sucursal ="MORENO";
                    break;
                case 3:
                    $sucursal ="ROSAS";
                    break;
                case 4:
                    $sucursal ="LIMONETA";
                    break;
            }
            $arr []   =    array
                (
                    //id si o si un solo string (sin espacios en blanco)
                    "id"            =>  $objBoton->getRelacionBotonId(),
                    //define las herramientas
                    "herramientas"   =>  array(
                        "cancelable"    =>  false,
                        "editable"      =>  false,
                        "detalles"      =>  true,
                    ),
                    //las celdas propiamente dichas, en el orden en que estan declaradas las columnas
                    "cell"          => array(
                        $this->_colUsuarios[$objBoton->getUsuarioId()]->getNombreCompleto() ." - ". $sucursal,
                    ),
                );
        }
        return $arr;
    }
    
    
     public function armarDetalles($id){
         //var_dump($id);exit;
        $this->_db->addSelect('bt.* , usuario_nombre_completo');
        $this->_db->addFrom('usuarios_botonera bt'); 
        $this->_db->addFrom('INNER JOIN datos_usuarios USING (usuario_id)'); 
        $this->_db->addWhere('relacion_boton_id =' . $id);
        $this->_db->generarSelect();
        $resultado =  $this->_db->ejecutar();
        //echo $this->_db->getQry();//exit;
        $propiedades =   array(
                array(
                    //$this->_colUsuariosSecciones[$objUsuario->getUsuarioId()]->getNombreCompleto(),
                    
                    "display"   =>  'Nombre de usuario',
                    "name"      =>  'usuario_nombre_completo',
                    "editable"  =>  false,
                    "class"	=>'',
                    "value"     =>  $resultado[0]['usuario_nombre_completo'],
                )
        );
        $relacionId = $resultado[0]['relacion_boton_id'];
        //var_dump($objSeccion);exit; 
        //var_dump($resultado[0]);exit;
        
        foreach($resultado[0] AS $key => $boton){
            if(is_bool($boton)){
            //var_dump($arrSecciones);exit;
                if($boton==false){
                    $boton='No';
                }else{
                    $boton='Si';
                }
                //echo "nombre " . $key . "  valor " . $subseccion . "<br>";
                $arr []   =    array
                (
                //id si o si un solo string (sin espacios en blanco)
                "id"            =>  $key . "#boton#" . $relacionId,
                //define las herramientas
                "herramientas"   =>  array(
                "editable"      =>  true,
                                    ),                
                //las celdas propiamente dichas, en el orden en que estan declaradas las columnas
                "cell"  => array(
                            $key,
                            $boton,
                        ),
                    );
            }
        }

            $detalles =   array(
            "modelo"    =>  array(
                array(
                    "display"   =>  'Boton',
                    "name"      =>  'relacion_boton_id',
                    "editable"  =>  false,
                    "class"	=>'',
                    
                ),
                array(
                    "display"   =>  'Habilitado',
                    "name"      =>  'habilitado',
                    "editable"  =>  true,
                    "class"	=>'',
                ),
                array(
                    "display"   =>  'Herr.',
                    "name"      =>  'herramienta',
                    "editable"  =>  false,
                )
            ),
            "celdas"    =>  $arr
            );
        
        $array_devolver =   array(
	    "propiedades"       =>  $propiedades,
	    "listadoDetalles"   =>  $detalles
        );
        return $array_devolver;
    }
    
    public function salvarUsuariosBotones($arrParametros){
        //var_dump($arrParametros);exit;
        $UsuariosBotonera = NEW UsuariosBotonera();
        $qry =  $UsuariosBotonera->salvarMe($arrParametros);
        //var_dump($rs);exit;
        $this->_db->setQry($qry);
         //var_dump($this);exit;
        $rs = $this->_db->ejecutar();
        if($rs == true){
            $arrDevolver = array(
                "soyError" => false,
                "nivel" => 0,
                "mensaje" => "Boton credo : <br> " . $qry
            );
            //var_dump ($arrMensajeSec);exit;
            //echo json_encode($arrDevolver);
        }else{
            $arrDevolver = array(
                "soyError" => true,
                "nivel" => 1,
                "mensaje" =>"Error al crear el boton"
                );
        }
        echo json_encode($arrDevolver);
    }
    
    public function actualizarMe($arrParametros){
        //var_dump($arrParametros);exit;
        $UsuariosBotonera = NEW UsuariosBotonera();
        $qry =  $UsuariosBotonera->actualizarMe($arrParametros);
        $this->_db->setQry($qry);
        $rs = $this->_db->ejecutar();
        if($rs > 0){
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
    
    public function actualizarBotones($parametros){
        //echo "llega";exit;
        //var_dump($parametros);exit;
        $arrParametros = explode("#" , $parametros['id']);
        $arrParametros[] = $parametros['campos']['habilitado']; 
        //var_dump($arrParametros[1]);exit;
        switch($arrParametros[1]){
            case "boton":
                //echo "lega";exit;
                $this->actualizarMe($arrParametros);
                break;
        }
        //var_dump($arrParametros);exit;
    
    }
    public function getDetallesAutocompletar(){
    //echo "llllllll";exit;
	foreach($this->_colUsuarios AS $objUsuario){
	 $arr[]   =   array(
            'label' => $objUsuario->getNombreCompleto(),
            'value' => $objUsuario->getUsuarioId()
            );
        }		
	return $arr;
    }

}





