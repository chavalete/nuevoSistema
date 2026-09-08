<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/administracion/DatoPromocionExtendido.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
/*
FRENTE PARA CARGAR LO QUE EL FRENTE requiera en cuanto a estetica y datos
*/

class FrentePromociones
{
    private $_colPromociones = Array();	    
    private $_ordenColumnas = Array();
    private $_total;
    private $_pagina;
    private $_objFuncionesComunes;
    
    
    public function __construct(){
        $this->_objFuncionesComunes = new FuncionesComunes();
        $this->_db = NEW FrenteAlmacenamiento('dmelmac');
    }
    public function cargarPromocion($id){
        $this->_colPromociones[$id] = NEW DatoPromocionExtendido();
        $this->_colPromociones[$id]->cargarMe($id);
    }
    public function setColObjPromociones($objPromocion){
        $this->_colPromociones[$objPromocion->getPromocionId()] = $objPromocion;
    }
    public function getColObjPromociones(){
        return $this->_colPromociones;
    }
    public function cargarLosDatosLazzy($arrIds){
    	
    	if(is_array($arrIds)){
            foreach($arrIds as $id){
                $ids.= "'" . $id['promocion_id'] . "',";
            }
            $ids=substr($ids, 0, -1);
            $this->_db->addWhere("promocion_id IN (" . $ids . ")" );
        }else{
            $this->_db->addWhere("promocion_id IN (" . $arrIds . ")" );
        }

        $ids=substr($ids, 0, -1);
        $this->_db->addSelect('*');
        $this->_db->addSelect('to_char(promocion_fecha,\'DD-MM-YY\') as promocion_fecha');
        $this->_db->addSelect('CASE WHEN promocion_activa THEN \'SI\' ELSE \'NO\' END AS promocion_activa');
        $this->_db->addSelect('to_char(fecha_vencimiento,\'DD-MM-YY\') as fecha_vencimiento');
        $this->_db->addSelect('to_char(fecha_inicio,\'DD-MM-YY\') as fecha_inicio');
        $this->_db->addSelect('subfamilia_nombre');
        $this->_db->addSelect("producto_nombre || ' -  ' || producto_presentacion AS producto_nombre");
        $this->_db->addFrom('datos_promociones');
        $this->_db->addFrom('LEFT JOIN datos_subfamilias ON datos_promociones.promocion_subfamilia_id = datos_subfamilias.subfamilia_id');
	$this->_db->addFrom('LEFT JOIN datos_productos ON datos_promociones.producto_id = datos_productos.producto_id');
	$this->_db->addWhere('fecha_vencimiento>=now()::date');
        $this->_db->AddOrderBy('promocion_id DESC');
        
        $this->_db->generarSelect();
        //echo $this->_db->getQry();exit;
        
        $resultado = $this->_db->ejecutar();
        

        foreach($resultado AS $detalle){
            $objPromocion = NEW DatoPromocionExtendido();
            $objPromocion->cargarMe($detalle);
            $this->setColObjPromociones($objPromocion);
        }
    }
    
    
    
    public function buscarPromociones($arrParametros) {

        $this->_db->addSelect('promocion_id');
        
        $this->_db->addFrom('datos_promociones');
        //$this->_db->addFrom('INNER JOIN  detalle_facturas USING (factura_id)');

        if($arrParametros['stringBuscar']){
            $this->_db->addWhere('promocion_nombre ILIKE  \'%' . $arrParametros[stringBuscar] .'%\'');
            $primerRegistro = 0;
            $ultimoRegistro = 11;
            
        }else{
            //La cuenta loca para cargar solo lo que necestiamos mostrar
            $primerRegistro  =   (($arrParametros['pagina'] * $arrParametros['porPagina'] ) - $arrParametros['porPagina'] + 1)-1;
            $ultimoRegistro =   ($primerRegistro + $arrParametros['porPagina']);
        }
	
        if($arrParametros['promociones'] !=null && $arrParametros['desdeAlta'] == null){
            
            $this->_db->addWhere('promocion_id = \''  . $arrParametros['promociones'] . '\'');
        }
        if($arrParametros['facturas'] !=null && $arrParametros['desdeAlta'] == null){
            
            $this->_db->addWhere('factura_nro = \''  . $arrParametros['facturas'] . '\'');
        }
        if($arrParametros['facturaPaga'] !=null){
            $this->_db->addWhere('factura_paga = \''  . $arrParametros['facturaPaga'] . '\'');
        }
        if($arrParametros['fechaDesde'] !=null && $arrParametros[desdeAlta]==null){
            $this->_db->addWhere('factura_fecha >= \'' . $this->_objFuncionesComunes->fechaFormatoDb($arrParametros['fechaHasta']) . '\'');
        }
        if($arrParametros['fechaHasta'] !=null && $arrParametros[desdeAlta]==null){
            $this->_db->addWhere('factura_fecha <= \'' . $this->_objFuncionesComunes->fechaFormatoDb($arrParametros['fechaHasta']) . '\'');
        }
	$this->_db->addWhere('promocion_cancelada = false');
        $this->_db->addWhere('fecha_vencimiento>=now()::date');

	
        //$this->_db->addGroup('factura_id, factura_nro');
        $this->setOrden($arrParametros);
        $this->_db->AddOrderBy($this->_ordenColumnas[ordenarPor] ." ". $this->_ordenColumnas[ordenarOrden]);
        //$this->_db->AddOrderBy('substring(factura_nro, position(\'-\' in factura_nro)+1)::integer asc');
	
        if($arrParametros['tipo']=='promociones'){
            $this->_db->addLimit(100);
        }
        $this->_db->generarSelect();
        
        //echo $this->_db->getQry();exit;

        $arr =  $this->_db->ejecutar();
	
        //ahora seteamos el total y la pagina
        $this->setTotal(count($arr));
        $this->setPagina($arrParametros['pagina']);
	
        if($this->getTotal() < $ultimoRegistro){    
            $ultimoRegistro = $this->getTotal();
        }	
        for($i=$primerRegistro;$i<$ultimoRegistro;$i++){   
            $arrIds[$i]['promocion_id'] = $arr[$i]['promocion_id'];
        }
        $this->cargarLosDatosLazzy($arrIds);
    }
    public function setOrden($arrParametros){

        if($arrParametros['ordenarPor'] != "" && $arrParametros['ordenarOrden'] != ""){
            $this->_ordenColumnas['ordenarPor']    =   $arrParametros['ordenarPor'];
            $this->_ordenColumnas['ordenarOrden']  =   $arrParametros['ordenarOrden'];
        }else{
            //definir default
            //$this->_ordenColumnas['ordenarPor']    = 'substring(factura_nro, 7)';
            $this->_ordenColumnas['ordenarPor']    =   'promocion_nombre';
            $this->_ordenColumnas['ordenarOrden']  =   'asc';
        }
    }
    public function getOrden(){
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

	foreach($this->_colPromociones AS  $objPromocion){
	    $arr []   =    array
            (
                //id si o si un solo string (sin espacios en blanco)
                "id"            =>  $objPromocion->getPromocionId(),
                //define las herramientas
                "herramientas"   =>  array(
                    "cancelable"    =>  true,
                    "editable"      =>  false,
                    "detalles"      =>  false,
                    "imprimir"      =>  false,
                ),
                //las celdas propiamente dichas, en el orden en que estan declaradas las columnas
                "cell"          => array(
                    $objPromocion->getPromocionFecha(),
                    $objPromocion->getPromocionNombre(),
                    $objPromocion->getPromocionCantidad(),
                    $objPromocion->getPromocionPrecio(),
                    $objPromocion->getSubfamiliaNombre(),
                    $objPromocion->getProductoNombre(),
                    $objPromocion->getObservaciones(),
                    $objPromocion->getFechaInicio(),
                    $objPromocion->getPromocionVencimiento(),
                    $objPromocion->getPorcentajeDesc(),
                    $objPromocion->getPromocionActiva(),
                ),
            );
	    $total++;
	}
	return $arr;
    }
    public function getDetallesAutocompletar(){
        foreach($this->_colPromociones AS $promcion){
            $arr[]   =   array(
                'label' =>  $promcion->getPromocionNombre(),
                'value' =>  $factura->getPromocionId()
                );
        }		
        return $arr;
    }
    public function getNroFactura($letra){
    
        $qry="SELECT last_value + 1 AS nro,'" .   $letra['stringBuscar'] . "' AS letra FROM seq_datos_factura_nro_" . $letra['stringBuscar'] .";";
        $this->_db->setQry($qry);
        
        $resultado = $this->_db->ejecutar();
        
        foreach($resultado AS $factura){
            $arr[]   =   array(
                'label' =>  $factura['letra'] . "0002-" . str_pad($factura['nro'],8,"0",STR_PAD_LEFT),
                'value' =>  $factura['letra']
                );
        }		
        return $arr;
    }
    public function armarDetalles($id){

        $qry="SELECT promocion_precio, producto_nombre || ' - ' || producto_presentacion AS producto FROM datos_promociones INNER JOIN relaciones_familias_subfamilias ON promocion_subfamilia_id = subfamilia_id INNER JOIN datos_productos USING(producto_id) WHERE promocion_id = $id;";
    
        $this->_db->setQry($qry);
        
        $resultado =  $this->_db->ejecutar();
        
        
        
        foreach($resultado AS $detalle){
	
            $arr []   =    array
                (
                //id si o si un solo string (sin espacios en blanco)
                "id"            =>  1,
                //define las herramientas
                "herramientas"   =>  array(
                "editable"      =>  false,
                ),
                //las celdas propiamente dichas, en el orden en que estan declaradas las columnas
                "cell"          => array(
                    $detalle['producto'],
                    $detalle['promocion_precio'],
                ),
		    );
        }
    
	$detalles=   array(
	    "modelo"    =>  array(
            array(

                "display"   =>  'Producto',
                "name"      =>  'peso_bruto',
                "class"	=>  '',
                "editable"  =>  false,
            ),
            array(
                "display"   =>  'Precio',
                "name"      =>  'tara',
                "class"	=>  '',
                "editable"  =>  false,
            ),
	    ),
	    "celdas"    =>  $arr
	);
	$array_devolver =   array(
	    "editable_cabecera" => false,
	    "propiedades"       =>  $propiedades,
	    "listadoDetalles"   =>  $detalles
        );
    return $array_devolver;
    }
    
    
	
		
	public function salvarMe($parametros){
	
        $promocionExtendido = NEW DatoPromocionExtendido();
        $promocionExtendido->salvarMe($parametros);
    }
    public function cancelarPromocion($id){
	    
        $this->_db->addCamposUpdate('promocion_cancelada = true');
        $this->_db->addCamposUpdate('promocion_inactiva_fecha_hora = now()');
        $this->_db->addCamposUpdate('promocion_inactiva_usuario_id = \'' . $_SESSION['usuarioId'] .'\'');
        $this->_db->addFrom('datos_promociones');
        $this->_db->addWhere('promocion_id = \'' . $id .'\'');
        
        $this->_db->generarUpdate();
        
        $this->_db->ejecutar();
            
        if(count($this->_db->getArrError()) > 0){
            $arrDevolver = array(
                    "soyError" => true,
                    "nivel" => 1,
                    "alertados" => $arrAlertador,
                    "mensaje" => "Error al cancelar!!!!"
                    );
        }else{
            
            $arrDevolver = array(
                    "soyError" => false,
                    "nivel" => 1,
                    "alertados" => $arrAlertador,
                    "mensaje" => "Promocion cancelada!!!!"
                    );
        }  
        echo json_encode($arrDevolver);exit;
	
    }
    
    
    public function actualizarMe($arrParametros){

	$compraExtendido = NEW CompraExtendido();
	$compraExtendido->actualizarMe($arrParametros);
    }
}
?>
