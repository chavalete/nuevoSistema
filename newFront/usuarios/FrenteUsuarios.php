<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
require_once 'UsuarioExtendido.php';
require_once 'SeccionExtendido.php';
require_once 'SubseccionExtendido.php';
require_once 'BotoneraExtendido.php';

/**
 * Description of Usuarios
 * Para menejo de usuarios y en forma de frente
 * @author chava- lucas- Guillo
 */
class FrenteUsuarios 
{    
    private $_db;
    private $_colUsuario = Array();
    
    private $_colSecciones;
    private $_colSubsecciones;
    private $_colBotonera;
    
    private $_ordenColumnas = Array();
    private $_total;
    private $_pagina;
    
    public function __construct() {
        $this->_db = new FrenteAlmacenamiento();
    }
    public function setColUsuarios($objUsuario){
        $this->_colUsuario[$objUsuario->getUsuarioId()] = $objUsuario;
    }
    public function getColUsuarios(){
        return $this->_colUsuario;
    }
    public function setSecciones($obj){
    //var_dump($obj);exit;
        $this->_colSecciones[$obj->getUsuarioId()] = $obj;
    }
    public function getColSecciones(){
        return $this->_colSecciones;
    }

    public function setColSubsecciones($obj){
        $this->_colSubsecciones[$obj->getUsuarioId()] = $obj;
    }
    public function getColSubsecciones(){
	return $this->_colSubsecciones;
    }
    
    public function setColBotonera($obj){
        $this->_colBotonera[$obj->getUsuarioId()] = $obj;
    }
    public function getColBotonera(){
	return $this->_colBotonera;
    }
    
    public function cargarUsuario($id,$idSucursal){

        $this->_colUsuario[$id] = NEW UsuarioExtendido();
        $this->_colUsuario[$id]->cargarMe($id);
        //var_dump($this->_colUsuario[$id]);
        
        $this->_colSecciones[$id] = new SeccionExtendido();
        $this->_colSecciones[$id]->cargarPorUsuario($id,$idSucursal);
        //var_dump($this->_colSecciones[$id]->getArrSecciones());
        
        $this->_colSubsecciones[$id] = new SubseccionExtendido();
        $this->_colSubsecciones[$id]->cargarPorUsuario($id,$idSucursal);
        //var_dump($this->_colSubsecciones[$id]->getArrSubsecciones());
                
        $this->_colBotonera[$id] = new BotoneraExtendido();
        $this->_colBotonera[$id]->cargarPorUsuario($id,$idSucursal);
        
    }
    public function cargarUsuarioLazzy($arrIds){
        //echo "llega";
        //var_dump($arrIds);exit;
        if(is_array($arrIds)){
            foreach($arrIds as $id){
                if($id['usuario_id']!=null){
                    $ids.= $id['usuario_id'] . ",";
                }    
            }
            //echo $ids;exit;
            $ids=substr($ids, 0, -1);
        }else{
            $ids=$arrIds;
        }
        //$ids=substr($ids, 0, -1);
        $this->_db->addSelect('*');
        $this->_db->addSelect('to_char(usuario_fecha_alta,\'DD-MM-YYYY\') AS usuario_fecha_alta');
        $this->_db->addSelect('\'Activo\'  AS usuario_activo_desc');
        $this->_db->addSelect('\'Inactivo\'  AS usuario_no_activo_desc');
        $this->_db->addSelect('CASE WHEN usuario_activo THEN \'Activo\' ELSE \'Inactivo\' END AS usuario_activo_mensaje');
        $this->_db->addFrom('datos_usuarios');
        $this->_db->addWhere("usuario_id IN (" . $ids . ")" );
        //$this->_db->addWhere('modelo_activo = true');
        if($this->getOrdenUsuarios()!=NULL){
            $this->_db->AddOrderBy($this->_ordenColumnas[ordenarPor] ." ". $this->_ordenColumnas[ordenarOrden]);
        }
        $this->_db->generarSelect();
        //echo $ids; exit;
        //echo $this->_db->getQry();//exit;
        $resultado = $this->_db->ejecutar();
        foreach($resultado   AS $detalle){
            $usuario = NEW UsuarioExtendido();
            $usuario->cargarMe($detalle);
            //echo $usuario->getNombreUsuario();exit;
            $this->setColUsuarios($usuario);
        }
    }

    public function validarLogin($arrParametros){
        //var_dump($arrParametros);exit;
        $this->_db->addSelect('usuario_id');
        $this->_db->addFrom('datos_usuarios');
        $this->_db->addFrom('INNER JOIN usuarios_sucursales us USING (usuario_id)');
        $this->_db->addWhere('usuario_activo = true');
        $this->_db->addWhere('usuario_nombre = ?');
        $this->_db->addWhere('usuario_password = md5(?)');
        $this->_db->addWhere('us.sucursal_id = ?');
        $arrDatos = Array(Array($arrParametros[userName],$arrParametros[password],$arrParametros[sucursalId]));
        //generar qry
        $this->_db->generarSelect();   
        //echo $this->_db->getQry();exit;
        //asigamos el resulta que es un array a una variable
        $resultado = $this->_db->ejecutar($arrDatos);
        foreach($resultado as $rstdo){
                $this->cargarUsuario($rstdo['usuario_id'],$arrParametros[sucursalId]);
        }        
    }
    public function buscarUsuario($arrParametros){
        $this->_db->addSelect('*');
        $this->_db->addFrom('datos_usuarios');
        if($arrParametros['stringBuscar'])	{
            $this->_db->addWhere('usuario_nombre ILIKE  \'%' . $arrParametros[stringBuscar] .'%\'');
            $primerRegistro = 0;
            $ultimoRegistro = 11;
        }else{  
            //La cuenta loca para cargar solo lo que necestiamos mostrar
            $primerRegistro  =   (($arrParametros['pagina'] * $arrParametros['porPagina'] ) - $arrParametros['porPagina'] + 1)-1;
            $ultimoRegistro =   ($primerRegistro + $arrParametros['porPagina']);
        }
        if($arrParametros['usuarios'] !=null && $arrParametros['desdeAlta']==null){
            $this->_db->addWhere('usuario_id = \'' . $arrParametros['usuarios'] . '\'');
        }
        //**INICIO ORDER BY**//
        //*seteo el orden*/
        $this->setOrdenUsuarios($arrParametros);
        $this->_db->AddOrderBy($this->_ordenColumnas[ordenarPor] ." ". $this->_ordenColumnas[ordenarOrden]);
        //**FIN ORDER BY**//
        //**INICIO WHERE**//
        $this->_db->generarSelect();
        //echo $this->_db->getQry();//exit;
        $resultado =  $this->_db->ejecutar();
        //*FIN WHERE*//
        //*INICIO LIMIT*//
        if($arrParametros['tipo']=='usuarios '){
        //	 $this->_db->addLimit(100);
        }
        //*FIN LIMIT*//
        //ahora seteamos el total y la pagina
        $this->setTotal(count($resultado));
            if($this->getTotal() == 0){
                return ;
            }
        $this->setPagina($arrParametros['pagina']);
    
        if($this->getTotal() < $ultimoRegistro){
            $ultimoRegistro = $this->getTotal();
        }	
        //var_dump($resultado);exit;
        for($i=$primerRegistro;$i<$ultimoRegistro;$i++){   
            $arrIds[$i]['usuario_id'] = $resultado[$i]['usuario_id'];
        }
        $this->cargarUsuarioLazzy($arrIds);
    }
    public function setOrdenUsuarios($arrParametros){

	if($arrParametros['ordenarPor'] != "" && $arrParametros['ordenarOrden'] != ""){
	
	    $this->_ordenColumnas['ordenarPor']    =   $arrParametros['ordenarPor'];
	    $this->_ordenColumnas['ordenarOrden']  =   $arrParametros['ordenarOrden'];
	}else{
	    //definir default
	    $this->_ordenColumnas['ordenarPor']    =   'usuario_nombre';
	    $this->_ordenColumnas['ordenarOrden']  =   'asc';
        }
    }
    
    public function getOrdenUsuarios(){
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
        //var_dump($this->getColUsuarios());exit;
        foreach($this->getColUsuarios() AS $objUsuario){

	    $arr []   =    array
            (
                //id si o si un solo string (sin espacios en blanco)
                "id"            =>  $objUsuario->getUsuarioId(),
                //define las herramientas
                "herramientas"   =>  array(
                    "cancelable"    =>  false,
                    "editable"      =>  true,
                    "detalles"      =>  true,
                ),
                //las celdas propiamente dichas, en el orden en que estan declaradas las columnas
                "cell"          => array(
                    $objUsuario->getUsuarioId(),
                    $objUsuario->getNombreUsuario(),
                    $objUsuario->getNombreCompleto(),
                    //$objUsuario->getUsuarioBas(),
                    //$objUsuario->getPwd(),
                    Array
                        (
                        "value" => $objUsuario->getActivo(),
                        "label" =>$objUsuario->getUsuarioActivoMensaje(),
                        "select" => 
                            Array 
                            (
                            Array(
                            "value" =>true,
                            "label" =>$objUsuario->getUsuarioActivoDesc()

                            ),
                            Array

                            (
                            "value" =>false,
                            "label" =>$objUsuario->getUsuarioNoActivoDesc()

                            )		
                            )
                        ),
                    $objUsuario->getSubseccionInicio(),
                    ),
                );
            }		
        return $arr;
    }
    
    
    public function armarDetalles($id){
    //llamar al metodo q cargar el modelo completo
        $this->cargarUsuarioLazzy($id);
        foreach($this->_colUsuario AS $objUsuario){
            $propiedadesUsuario =   array(
                array(
                    "display"   =>  'Usuario',
                    "name"      =>  'usuario_nombre',
                    "editable"  =>  false,
                    "class"	=>'',
                    "value"     =>  $objUsuario->getNombreUsuario(),
                ),
                array(
                    "display"   =>  'Nombre completo',
                    "name"      =>  'usuario_nombre_completo',
                    "editable"  =>  false,
                    "class"	=>'',
                    "value"     =>  $objUsuario->getNombreCompleto(),
                ),
                array(
                    "display"   =>  'Activo',
                    "name"      =>  'modelo_activo',
                    "editable"  =>  false,
                    "class"	=>'',
                    "value"     =>  $objUsuario->getUsuarioActivoMensaje(),
                )
            );
        }
        $arr []   =    array
                (
                //id si o si un solo string (sin espacios en blanco)
                "id"            =>  $objUsuario->getUsuarioId(),
                //define las herramientas
                "herramientas"   =>  array(
                "editable"      =>  true,
                ),
                "bloqueado"     => false,	    
                //las celdas propiamente dichas, en el orden en que estan declaradas las columnas
                "cell"          => array(
                            $objUsuario->getPwd(),

                            ),
                );
            
        $detallesUsuario    =   array(
            
            "bloqueable"=> false,
            "modelo"    =>  array(
                    array(

                "display"   =>  'Contraseña',
                "name"      =>  'usuario_password',
                "editable"  =>  true,
                "class"	=>'',
                ),
                array(
                "display"   =>  'Herr.',
                "name"      =>  'herramienta',
                ),
            
            ),
            
            "celdas"    =>  $arr
        );
            $array_devolver =   array(
                "editable_cabecera" => false,
                "propiedades"       =>  $propiedadesUsuario,
                "listadoDetalles"   =>  $detallesUsuario
            );
                return $array_devolver;
            }
    
    
   
   public function getDetallesAutocompletar(){
    //var_dump($this->_colUsuario);exit;
    //echo "llega";exit;
	foreach($this->_colUsuario AS $objUsuario){
	 $arr[]   =   array(
            'label' =>  $objUsuario->getNombreUsuario(),
            'value' =>  $objUsuario->getUsuarioId()
            );
	}		
	return $arr;
    }
    
    public function registrarIngreso($id){
	$this->_db->addCamposUpdate('usuario_ultimo_login = now()::timestamp' );
        $this->_db->addFrom('datos_usuarios');
        $this->_db->addWhere('usuario_id =' . $id);
        //generar qry
        $this->_db->generarUpdate();
        //asigamos el resulta que es un array a una variable
        $this->_db->ejecutar();

    }
    
    public function getUsuarioById($id){
    
        $this->_db->addSelect('*');
        $this->_db->addFrom('datos_usuarios');
        $this->_db->addWhere('usuario_id = $id');
        //generar qry
        $this->_db->generarSelect();   
        //asigamos el resulta que es un array a una variable
        return  $this->_db->ejecutar('traer');
    }
    
    public function salvarMe($arrParametros){
        //var_dump($arrParametros);exit;
        $usuarioExtendido = NEW UsuarioExtendido();
        $arrQry['usuarios'] = $usuarioExtendido->salvarMe($arrParametros);
        
        $usuariosSecciones = NEW UsuariosSecciones();
        $arrQry['usuariosSecciones'] = $usuariosSecciones->salvarUsuarioNuevo($usuarioExtendido->getUsuarioId());
        
        $usuariosSubsecciones = NEW usuariosSubsecciones();
        $arrQry['usuariosSubsecciones'] = $usuariosSubsecciones->salvarUsuarioNuevo($usuarioExtendido->getUsuarioId());
        
        $usuariosBotonera = NEW usuariosBotonera();
        $arrQry['usuariosBotonera'] = $usuariosBotonera->salvarUsuarioNuevo($usuariosSubsecciones->getUsuarioId());
        
        $qryFinal = $arrQry['usuarios'] . $arrQry['usuariosSecciones'] . $arrQry['usuariosSubsecciones'] . $arrQry['usuariosBotonera'];
         
        $this->_db->setQry($qryFinal);
        $rs = $this->_db->ejecutarTransaccion();
        //echo $qryFinal;//exit;
        $this->setColUsuarios($usuarioExtendido);
        if($rs == true){
            $arrDevolver = array(
		    "soyError" => false,
		    "nivel" => 0,
		    "mensaje" =>"Usuario creado con éxito."
		    );
        }else{

            $arrDevolver = array(
                "soyError" => true,
                "nivel" => 1,
                "mensaje" =>"Error al salvar el usuario. Verificar"
                );
        }
        echo json_encode($arrDevolver);
    }
    public function actualizarMe($arrParametros){
        $usuarioExtendido = NEW UsuarioExtendido();
        $rs =  $usuarioExtendido->actualizarMe($arrParametros);
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
}
