<?php
//ALMACENAMIENTO
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
require_once 'UsuariosSecciones.php';
require_once 'UsuariosSubsecciones.php';
/**
 * Description of FrentePermisos
 *
 * @author lucas
 */

 
 

 
class FrentePermisos{
    
    private $_db;
    private $_colUsuariosSecciones;
    private $_colUsuariosSubsecciones;
    private $_colUsuarios = Array();
    
    //** NECESARIOS PARA EL FRENTE DE FRENTES//
    private $_ordenColumnas = Array();
    private $_total;
    private $_pagina;
    //private $_objFuncionesComunes;
    public function __construct() {
        $this->_db = new FrenteAlmacenamiento();
    }
    
    public function setColUsuariosSecciones($objSeccion){
    //var_dump($objSeccion);exit;
        $this->_colUsuariosSecciones[$objSeccion->getRelacionSeccionId()] = $objSeccion;
    }
    public function getColUsuariosSecciones(){
        return $this->_colUsuariosSecciones;
    }
    public function setColUsuariosSubsecciones($objSubseccion){
    //var_dump($objSubseccion);exit;
        $this->_colUsuariosSubsecciones[] = $objSubseccion;
    }
    public function getColUsuariosSubsecciones(){
        return $this->_colUsuariosSubsecciones;
    }
    
    public function setColUsuarios($objUsuario){
    //var_dump($objUsuario);exit;
        $this->_colUsuarios = $objUsuario;
    }
    public function getColUsuarios(){
        return $this->_colUsuarios;
    }
    
    public function cargarUsuarios($id){
    //var_dump($id);exit;
        $this->_colUsuariosSecciones[$id] = NEW UsuariosSecciones();
        $this->_colUsuariosSecciones[$id]->cargarMe($id);
    }
    
    public function getIdUsuariosSeccion(){
        $this->_db->addSelect('seq_usuarios_secciones_relacion_seccion_id');
        $this->_db->generarProximo();
        $rs =  $this->_db->ejecutar();
        return $rs[0]['nextval'];
    }
    
    public function getIdUsuariosSubseccion(){
        $this->_db->addSelect('seq_usuarios_subseccion_relacion_seccion_id');
        $this->_db->generarProximo();
        $rs =  $this->_db->ejecutar();
        return $rs[0]['nextval'];
    }
    
    public function cargarSeccionesLazzy($arrIds){
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
        $this->_db->addFrom('usuarios_secciones');
        $this->_db->addWhere("usuario_id IN (" . $ids . ")" );
        $this->_db->AddOrderBy('usuario_id');;
        $this->_db->generarSelect();
        //var_dump($this);exit;
        //echo $this->_db->getQry();exit;
        $resultado = $this->_db->ejecutar();
        foreach($resultado   AS $detalle){
            $objSeccion = NEW UsuariosSecciones();
            $objSeccion->cargarMe($detalle);
            $this->setColUsuariosSecciones($objSeccion);
            $arrUsuarioId[]['usuario_id'] = $objSeccion->getUsuarioId();
        }
        $objUsuario = new FrenteUsuarios();
        $objUsuario->cargarUsuarioLazzy($arrUsuarioId);
        $this->setColUsuarios($objUsuario->getColUsuarios());
        //var_dump($this->_colUsuarios);exit;
        //echo $this->_db->getQry();//exit;
    }
    
    public function cargarSubseccionesLazzy($arrIds){
    //echo "entra";exit;
        foreach($arrIds as $id){
            $ids.= $id['usuario_id'] . ",";
        }
        $ids=substr($ids, 0, -1);
        $this->_db->addSelect('*');       
        $this->_db->addFrom('usuarios_subsecciones');
        $this->_db->addWhere("usuario_id IN (" . $ids . ")" );
        $this->_db->generarSelect();
        //echo $this->_db->getQry();exit;
        $resultado = $this->_db->ejecutar();
        foreach($this->getColUsuariosSubsecciones() AS $objSubseccion){
            foreach($resultado AS $detalle){
                $objSubseccion= NEW UsuariosSubsecciones();
                $objSubseccion->cargarMe($detalle);
                $this->_colUsuarios[$objSubseccion->getUsuarioId()]->setColUsuariosSubsecciones($objSubseccion);
                $arrUsuarioId[]['usuario_id'] = $objSeccion->getUsuarioId();
            }
            $objUsuario = new FrenteUsuarios();
            $objUsuario->cargarUsuarioLazzy($arrUsuarioId);
            $this->setColUsuarios($objUsuario->getColUsuarios());
            //var_dump($this);exit;
            //echo $this->_db->getQry();//exit;
        }
    }
    
    public function cargarUsuariosCompleto($id){
        //echo "eeeeeee";exit;
        //var_dump($id);exit;
        $this->_db->addSelect('*');
        $this->_db->addFrom('usuarios_secciones'); 
        $this->_db->addFrom('INNER JOIN usuarios_subsecciones USING (usuario_id)'); 
        $this->_db->addWhere('usuario_id =' . $id);
        $this->_db->generarSelect();
        //echo $id; exit;
        //echo $this->_db->getQry();exit;
        $resultado =  $this->_db->ejecutar();
        //var_dump($objSeccion);exit;
        //echo $this->_db->getQry();//exit;
        foreach($resultado AS $usuario){
            //var_dump($usuario);exit;
            //echo $this->_db->getQry();//exit;
            $objSeccion = new UsuariosSecciones();
            $objSeccion->cargarMe($usuario);
            $this->setColUsuariosSecciones($objSeccion);
            $objSubseccion = new UsuariosSubsecciones();
            $objSubseccion->cargarMe($usuario);
            $this->setColUsuariosSubsecciones($objSubseccion);
            //var_dump($objSubseccion);exit;
            $arrUsuarioId[]['usuario_id'] = $objSeccion->getUsuarioId();
        }
        $objUsuario = new FrenteUsuarios();
        $objUsuario->cargarUsuarioLazzy($arrUsuarioId);
        $this->setColUsuarios($objUsuario->getColUsuarios());
        
    }
    
    public function buscarUsuariosSecciones($arrParametros){
    //var_dump($arrParametros);exit;
    $this->_db->addSelect('usuario_id');
    $this->_db->addFrom('usuarios_secciones');
    $this->_db->addFrom('INNER JOIN datos_sucursales USING(sucursal_id)');
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
    if($arrParametros['usuariosPermisos'] !=null && $arrParametros['desdeAlta']==null){
	    $this->_db->addWhere('usuario_id= \'' . $arrParametros['usuariosPermisos'] . '\'');
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
	    $arr[$i]= $resultado[$i]['usuario_id'];
	}
	$this->cargarSeccionesLazzy($arr);
    }
    
    public function setOrden($parametros){
	
	if($parametros['ordenarPor'] != "" && $parametros['ordenarOrden'] != ""){
	    $this->_ordenColumnas['ordenarPor']    =   $parametros['ordenarPor'];
	    $this->_ordenColumnas['ordenarOrden']  =   $parametros['ordenarOrden'];
	}else{  //definir default
            $this->_ordenColumnas['ordenarPor']    =   'usuario_id';
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
        //var_dump($this->_colUsuariosSecciones);
        foreach($this->getColUsuariosSecciones() AS $objSeccion){
            switch ($objSeccion->getSucursalId()){
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
                    "id"            =>  $objSeccion->getRelacionSeccionId(),
                    //define las herramientas
                    "herramientas"   =>  array(
                        "cancelable"    =>  false,
                        "editable"      =>  false,
                        "detalles"      =>  true,

                    ),
                    //las celdas propiamente dichas, en el orden en que estan declaradas las columnas
                    "cell"          => array(
                        $this->_colUsuarios[$objSeccion->getUsuarioId()]->getNombreCompleto() . " - " . $sucursal,
                        
                    ),
                );
        }
        return $arr;
    }

    public function armarDetalles($id){
         //var_dump($id);exit;
        $this->_db->addSelect('us.* , usuario_nombre_completo');
        $this->_db->addFrom('usuarios_secciones us'); 
        $this->_db->addFrom('INNER JOIN datos_usuarios USING (usuario_id)'); 
        $this->_db->addWhere('relacion_seccion_id =' . $id);
        $this->_db->generarSelect();
        $resultadoSeccion =  $this->_db->ejecutar();
        
        $propiedades =   array(
                array(
                    //$this->_colUsuariosSecciones[$objUsuario->getUsuarioId()]->getNombreCompleto(),
                    
                    "display"   =>  'Nombre de usuario',
                    "name"      =>  'usuario_nombre_completo',
                    "editable"  =>  false,
                    "class"	=>'',
                    "value"     =>  $resultadoSeccion[0]['usuario_nombre_completo'],
                )
        );
        $relacionId = $resultadoSeccion[0]['relacion_seccion_id'];
        
        $arr []   =    array
                (
                //id si o si un solo string (sin espacios en blanco)
                "id"            =>  "none",
                //define las herramientas
                "herramientas"   =>  array(
                "editable"      =>  false,
                                    ),                
                //las celdas propiamente dichas, en el orden en que estan declaradas las columnas
                "cell"  => array(
                            "------------SECCIONES------------",
                            "",
                        ),
                    );
        //fin separador
        
        foreach($resultadoSeccion[0] AS $key => $seccion){
            if(is_bool($seccion)){
            //var_dump($arrSecciones);exit;
                if($seccion==false){
                    $seccion='No';
                }else{
                    $seccion='Si';
                }
                //echo "nombre " . $key . "  valor " . $subseccion . "<br>";
                $arr []   =    array
                (
                //id si o si un solo string (sin espacios en blanco)
                "id"            =>  $key . "#seccion#" . $relacionId,
                //define las herramientas
                "herramientas"   =>  array(
                "editable"      =>  true,
                                    ),                
                //las celdas propiamente dichas, en el orden en que estan declaradas las columnas
                "cell"  => array(
                            $key,
                            $seccion,
                        ),
                    );
            }
        }
        //separador 
        $arr []   =    array
                (
                //id si o si un solo string (sin espacios en blanco)
                "id"            =>  "none",
                //define las herramientas
                "herramientas"   =>  array(
                "editable"      =>  false,
                                    ),                
                //las celdas propiamente dichas, en el orden en que estan declaradas las columnas
                "cell"  => array(
                            "------------SUBSECCIONES------------",
                            "",
                        ),
                    );
        //fin separador
        //echo "llega";exit;
        
        $this->_db->addSelect('usu.* , usuario_nombre_completo');
        $this->_db->addFrom('usuarios_subsecciones usu'); 
        $this->_db->addFrom('INNER JOIN datos_usuarios USING (usuario_id)'); 
        $this->_db->addWhere('relacion_subseccion_id =' . $id);
        $this->_db->generarSelect();
        //echo $this->_db->getQry();exit;
        $resultadoSubseccion =  $this->_db->ejecutar();
        
        foreach($resultadoSubseccion[0] AS $key => $subseccion){   
            //var_dump($key);exit;
            if(is_bool($subseccion)){
                if($subseccion==false){
                    $subseccion='No';
                }else{
                    $subseccion='Si';
                }
                //echo "nombre " . $key . "  valor " . $subseccion . "<br>";
                $arr []   =    array
                (
                //id si o si un solo string (sin espacios en blanco)
                "id"            =>  $key . "#subseccion#" . $relacionId,
                //define las herramientas
                "herramientas"   =>  array(
                "editable"      =>  true,
                                    ),                
                //las celdas propiamente dichas, en el orden en que estan declaradas las columnas
                "cell"  => array(
                            $key,
                            $subseccion,
                        ),
                );
            }
        }
        
            $detalles =   array(
            "modelo"    =>  array(
                array(
                    "display"   =>  'Secciones/Subsecciones',
                    "name"      =>  'relacion_seccion_id',
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

    public function salvarUsuariosSecciones($arrParametros){
        //echo "llega";exit;
        //var_dump($arrParametros);exit;
        $usuariosSecciones= NEW UsuariosSecciones();
        $qry =  $usuariosSecciones->salvarMe($arrParametros);
        $this->_db->setQry($qry);
        //echo $qry;exit;
        $rs = $this->_db->ejecutar();
        //var_dump($rs);
        if($rs == true){
        //var_dump($rs);
            $arrDevolver = array(
                "soyError" => false,
                "nivel" => 0,
                "mensaje" =>"Seccion creada : <br> " . $qry
            );
            }else{
            $arrDevolver = array(
                "soyError" => true,
                "nivel" => 1,
                "mensaje" =>"Error al crear la seccion"
            );
        }
        return $arrDevolver;
    }
    
    public function salvarUsuariosSubsecciones($arrParametros){
        //var_dump($arrParametros);exit;
        $UsuariosSubsecciones= NEW UsuariosSubsecciones();
        $qry =  $UsuariosSubsecciones->salvarMe($arrParametros);
        $this->_db->setQry($qry);
         //var_dump($this);exit;
        $rs = $this->_db->ejecutar();
        if($rs == true){
            $arrDevolver = array(
                "soyError" => false,
                "nivel" => 0,
                "mensaje" =>"Subseccion creada: <br> " . $qry
            );
        }else{
             $arrDevolver = array(
                "soyError" => true,
                "nivel" => 1,
                "mensaje" =>"Error al crear la subseccion"
             );
            }
            return $arrDevolver;
    }
    
    public function salvarUsuariosPermisos($arrParametros){
    //var_dump($arrParametros);
    if($arrParametros['seccion'] != ""){
        $arrMensajeSec = $this->salvarUsuariosSecciones($arrParametros);
    }
    if($arrParametros['subseccion'] != ""){
        $arrMensajeSub = $this->salvarUsuariosSubsecciones($arrParametros);
    }
    
    $arrDevolver = array(
    "soyError" => false,
    "nivel" => 0,
    "mensaje" => $arrMensajeSec['mensaje'] . "<br>" . $arrMensajeSub['mensaje'] . "<br/>"  
    );
    //var_dump ($arrMensajeSec);exit;
    echo json_encode($arrDevolver);
    }
    
     public function actualizarGeneral($parametros){
        //echo "llega";exit;
        //var_dump($parametros);exit;
        $arrParametros = explode("#" , $parametros['id']);
        $arrParametros[] = $parametros['campos']['habilitado']; 
        //var_dump($arrParametros[1]);exit;
        switch($arrParametros[1]){
            case "seccion":
                //echo "lega";exit;
                $this->actualizarSecciones($arrParametros);
                break;
            case "subseccion":
                //echo "llega";exit;
                $this->actualizarSubsecciones($arrParametros);
                break;
        }
        //var_dump($arrParametros);exit;
    }
    
    
    public function actualizarSecciones($arrParametros){
        //var_dump($arrParametros);//exit;
        //echo "llega";exit;
        $UsuariosSecciones= NEW UsuariosSecciones();
        $qry =  $UsuariosSecciones->actualizarMe($arrParametros);
        $this->_db->setQry($qry);
        $rs = $this->_db->ejecutar();
        //var_dump($rs);
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
    
    public function actualizarSubsecciones($parametros){ 
        //echo"llega";exit;
        //var_dump($parametros);exit;
        $UsuariosSubsecciones = NEW UsuariosSubsecciones();
        $qry =  $UsuariosSubsecciones->actualizarMe($parametros);
        //echo $qry;exit;
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
                "mensaje" =>"Error en la actualizacion" . print_r($this->_db->getArrError())
                );
        }
        echo json_encode($arrDevolver);
        
    }
    
    public function getDetallesAutocompletar(){
    //echo "llllllll";exit;
    //var_dump($this->_colUsuarios);exit;
	foreach($this->_colUsuarios AS $objUsuario){
	 $arr[]   =   array(
            'label' => $objUsuario->getNombreCompleto(),
            'value' => $objUsuario->getUsuarioId()
            );
        }		
	return $arr;
    }

}




