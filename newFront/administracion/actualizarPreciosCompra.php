<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';


/**
 * Description of FrenteStock
 *
 * @author dMELMAC
 */

class actualizarPrecios
{
    private $_db;
    private $_colConsultasCtaCte= Array();
    private $_objFuncionesComunes;

    public function __construct() {
        $this->_db = new FrenteAlmacenamiento();
	//$this->_objFuncionesComunes = new FuncionesComunes();
    }
    public function actualizar_precios(){
    
	$this->_db->addSelect('id_movimiento, \'1\' AS tipoOperacion');
        $this->_db->addFrom('datos_movimientos');
        $this->_db->addWhere('tipo_movimiento_id = 1');
        $this->_db->AddOrderBy('id_movimiento ASC');
        $this->_db->generarSelect();
        //echo $this->_db->getQry();
        $resultado =  $this->_db->ejecutar();
        
        foreach($resultado AS $movimiento){
        
         echo "SELECT fn_actualizar_stock_compras({$movimiento['id_movimiento']},1);";
         echo "<br>";
        
        }
    
    
    }
    public function actualizarPreciosHistoricos(){
    
	$this->_db->addSelect('producto_id,lista_id, id_movimiento');
        $this->_db->addFrom('datos_movimientos');
        $this->_db->addFrom('INNER JOIN detalle_movimientos USING(id_movimiento)');
        $this->_db->addFrom('INNER JOIN datos_clientes USING(cliente_id)');
        $this->_db->addWhere('tipo_movimiento_id = 2');
        $this->_db->addWhere('anulado = false');
        $this->_db->AddOrderBy('cliente_id,id_movimiento ASC');
        $this->_db->generarSelect();
        //echo $this->_db->getQry();exit;
        $resultado =  $this->_db->ejecutar();
        
        
        foreach($resultado AS $movimiento){
        
	    $this->_db->addSelect('producto_pventa'); 
	    $this->_db->addFrom('lista_precios_'. $movimiento['lista_id']);
	    $this->_db->addWhere('producto_id = \'' . $movimiento['producto_id']. '\'');                                                 
	    $this->_db->generarSelect();    

	    //echo $this->_db->getQry();exit;
	    $arrPrecio =  $this->_db->ejecutar();                                                                                     
	    
	    $this->_db->addCamposUpdate('detalle_total_fact = abs(cantidad) *  ' . $arrPrecio[0][producto_pventa]);
	    $this->_db->addCamposUpdate('producto_pventa = ' . $arrPrecio[0][producto_pventa]);
	    $this->_db->addFrom('detalle_movimientos');
	    $this->_db->addWhere('id_movimiento = \'' . $movimiento[id_movimiento] .'\'');
	    $this->_db->addWhere('producto_id = \'' . $movimiento[producto_id] .'\'');
	    $this->_db->generarUpdate();    
	    
	    $this->_db->ejecutar();                                                                                     
	    
	    
        }
        foreach($resultado AS $movimiento){
        
	    $this->_db->addSelect('sum(detalle_total_fact) AS total_movimiento'); 
	    $this->_db->addFrom('detalle_movimientos');
	    $this->_db->addWhere('id_movimiento = \'' . $movimiento['id_movimiento']. '\'');                                                 
	    $this->_db->generarSelect();    

	    //echo $this->_db->getQry();exit;
	    $arrPrecio =  $this->_db->ejecutar();
	    
	   
	    $this->_db->addCamposUpdate('movimiento_total_fact = ' . $arrPrecio[0][total_movimiento]);
	    $this->_db->addFrom('datos_movimientos');
	    $this->_db->addWhere('id_movimiento = \'' . $movimiento[id_movimiento] .'\'');
	    $this->_db->generarUpdate();    
	    
	    //echo $this->_db->getQry();
	    $this->_db->ejecutar();     
	    
	    echo "<br>";
	    echo $movimiento['id_movimiento'];
        
        }
    
    
    }
    
    
    
    public function actualizarRemitoId(){
    
	$this->_db->addSelect('remito_nro, prove_id, id_movimiento');
        $this->_db->addFrom('datos_movimientos');
        $this->_db->addWhere('tipo_movimiento_id = 1');
        $this->_db->AddOrderBy('id_movimiento ASC');
        $this->_db->generarSelect();
        //echo $this->_db->getQry();
        $resultado =  $this->_db->ejecutar();
        
        foreach ($resultado AS $movimiento){
	    
	    $this->_db->addSelect('remito_id');
	    $this->_db->addFrom('datos_remitos');
	    $this->_db->addWhere('remito_nro = \'' .$movimiento[remito_nro] .'\'');
	    $this->_db->addWhere('prove_id = \'' .$movimiento[prove_id] .'\'');
	    $this->_db->generarSelect();
	    //echo $this->_db->getQry();exit;
	    $resultadoRemito =  $this->_db->ejecutar();
	    
	    if(count($resultadoRemito)== 0) {
	    
		echo "no se encontro remito";exit;
	    }else{
	    
		$this->_db->addCamposUpdate('remito_id = \'' . $resultadoRemito[0][remito_id] .'\'');
		$this->_db->addFrom('datos_movimientos');
		$this->_db->addWhere('id_movimiento = \'' . $movimiento[id_movimiento] .'\'');
		
		$this->_db->generarUpdate();
		
		//echo $this->_db->getQry();exit;
		$this->_db->ejecutar();
		
		
	    }
        
        }
    
    }
    public function consultarMovimientosStock ($arrParametros){

        $this->_db->addSelect('*');
        $this->_db->addFrom('cta_cte_stock');
	$this->_db->addFrom('LEFT JOIN datos_proveedores USING(prove_id)');
	$this->_db->addFrom('LEFT JOIN datos_clientes USING(cliente_id)');
	//$this->_db->addFrom('INNER JOIN datos_proveedores USING(prove_id)');
        //**ANALIZO EL WHERE**//
        if($arrParametros['stringBuscar']){
            $this->_db->addWhere(' cantidad > 0 AND sucursal_id = ' . $arrParametros[stringBuscar]);
            $primerRegistro = 0;
            $ultimoRegistro = 11;
	    }else{
            //La cuenta loca para cargar solo lo que necestiamos mostrar
            $primerRegistro =   (($arrParametros['pagina'] * $arrParametros['porPagina'] ) - $arrParametros['porPagina'] + 1)-1;
            $ultimoRegistro =   ($primerRegistro + $arrParametros['porPagina']) -1;
        }

        if($arrParametros['productos'] !=null ){
            $this->_db->addWhere('datos_productos.producto_id = \'' . $arrParametros['productos'] . '\' ');
        }
        if($arrParametros['productosNombre'] !=null ){
            $this->_db->addWhere('datos_productos.producto_id = \'' . $arrParametros['productosNombre'] . '\' ');
        }
	if($arrParametros['valor'] !=null ){
            $this->_db->addWhere('codigo_referencia = \'' . $arrParametros['valor'] . '\' ');
        }
	if($arrParametros['value'] !=null ){
            $this->_db->addWhere('codigo_referencia = \'' . $arrParametros['value'] . '\' ');
        }
        if($arrParametros['estanterias'] !=null){
            $this->_db->addWhere('estanteria_id = \'' . $arrParametros['estanterias'] . '\' ');
        }
        if($arrParametros['almacenes'] !=null){
            $this->_db->addWhere('almacen_id = \'' . $arrParametros['almacenes'] . '\' ');
        }
        if($arrParametros['sucursales'] !=null){
            $this->_db->addWhere('sucursal_id = \'' . $arrParametros['sucursales'] . '\' ');
        }
	if($arrParametros['proveedores'] !=null){
            $this->_db->addWhere('prove_id = \'' . $arrParametros['proveedores'] . '\' ');
        }
        if($arrParametros['lotes'] !=null){
            $this->_db->addWhere('lote = \'' . $arrParametros['lotes'] . '\' ');
        }
	if($arrParametros['codigoReferencia'] !=null){
            $this->_db->addWhere('codigo_referencia = \'' . $arrParametros[codigoReferencia] . '\'');
        }
	if($arrParametros['fechaHasta'] !=null){
            $this->_db->addWhere('lote_vencimiento <= \'' . $arrParametros[fechaHasta] . '\'');
        }
        if($arrParametros['productoFamilia'] !=null){
            $this->_db->addWhere('producto_nombre ILIKE  \'%' . $arrParametros[productoFamilia] .'%\'');
        }
        //**fin where **/
        //**INICIO ORDER BY**//
        //*seteo el orden*/
        $this->setOrdenMovimientosStock($arrParametros);
        $this->_db->AddOrderBy($this->_ordenColumnas[ordenarPor] ." ". $this->_ordenColumnas[ordenarOrden]);
        //**FIN ORDER BY**/
        //groupBy
        
        //*INICIO LIMIT*//
        if($arrParametros['tipo']=='consultarStockCompras'){
            $this->_db->addLimit(100);
        }
        //*FIN LIMIT*//
        $this->_db->generarSelect();
        //echo $this->_db->getQry();
        $arr =  $this->_db->ejecutar();

	$this->setTotal(count($arr));
	$this->setPagina($arrParametros['pagina']);

	if($this->getTotal() < $ultimoRegistro){

	    $ultimoRegistro = $this->getTotal();
	}

        for($i=$primerRegistro;$i<$ultimoRegistro;$i++){   
            $array['fecha'] = $arr[$i]['fecha'];
            
            if($arr[$i]['prove_razon_social'] !=null){
		$array['actor'] = $arr[$i]['prove_razon_social'];
		$array['nroRemito'] = $arr[$i]['remito_nro'];
	    }else{
		$array['actor'] = $arr[$i]['cliente_nombre'];
		$array['nroRemitoSalida'] = $arr[$i]['remito_nro'];
	    }
            $array['ingresoKilos'] = $arr[$i]['cantidad_ingresada'];
            $array['egresoKilos'] = $arr[$i]['cantidad_facturada'];
            $array['precioCompra'] = $arr[$i]['precio_compra'];
            $array['precioVenta'] = $arr[$i]['precio_venta'];
            $array['entrada'] = $arr[$i]['precio_compra'] * $arr[$i]['cantidad_ingresada'];
            $array['salida'] = $arr[$i]['precio_venta'] * $arr[$i]['cantidad_facturada'];
            $this->_colConsultasCtaCte[]= $array;
        }
        
    }
    
    public function setOrdenMovimientosStock($parametros){
        if($parametros['ordenarPor'] != "" && $parametros['ordenarOrden'] != "")
        {
            $this->_ordenColumnas['ordenarPor']    =   $parametros['ordenarPor'];
            $this->_ordenColumnas['ordenarOrden']  =   $parametros['ordenarOrden'];
        }else
        {
            //definir default
            $this->_ordenColumnas['ordenarPor']    =   'fecha';
            $this->_ordenColumnas['ordenarOrden']  =   'asc';
        }
    }

    public function getMovimientosStock(){
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
        $i = 1;
        foreach($this->_colConsultasCtaCte AS $objConsulta){
                $arr[] = array(
                    //id si o si un solo string (sin espacios en blanco)
                    "id"=>  $i, //asi cuanta desde 1 en adelante
                    //define las herramientas
                    "herramientas"   =>  array(
                        "cancelable"    =>  false,
                        "editable"      =>  false,
                        "detalles"      =>  false,
                        "imprimir"      =>  false
                        ),
                    //las celdas propiamente dichas, en el orden en que estan declaradas las columnas
                    "cell" => array(
			    $objConsulta['actor'],
			    $objConsulta['fecha'],
			    $objConsulta['nroRemito'] ,
			    $objConsulta['nroRemitoSalida'] ,
			    $objConsulta['ingresoKilos'],
			    $objConsulta['egresoKilos'],
			    "$" . $objConsulta['precioCompra'],
			    "$" . $objConsulta['precioVenta'],
			    "$" . $objConsulta['entrada'],
			    "$" . $objConsulta['salida']
                              ),
            );
            $i++;
        }
       return $arr;
    }
    
}
$actualizar = NEW actualizarPrecios();
//$actualizar->actualizar_precios();
$actualizar->actualizarPreciosHistoricos();
?>
