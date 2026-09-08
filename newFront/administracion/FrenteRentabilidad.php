<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/includes/FuncionesComunes.php';

/**
 * Description of FrenteStock
 *
 * @author dMELMAC
 */

class FrenteRentabilidad
{
    private $_db;
    private $_colConsultasStock = Array();
    private $_objFuncionesComunes;

    public function __construct() {
        $this->_db = new FrenteAlmacenamiento();
	$this->_objFuncionesComunes = new FuncionesComunes();
    }
   public function setArrError ($error){
        $this->_arrError = $error;
    }
    public function getArrError (){
        return $this->_arrError;
    } 
    public function setTotalAcumulado($productoPventa){
        $this->_totalAcumulado = $this->getTotalAcumulado() + $productoPventa;        
    }

    public function setTotalParcial($total){
        $this->_totalParcial =  $total;
    }
    
    public function getTotalParcial(){
        return $this->_totalParcial;
    }
    public function getTotalAcumulado(){
        return $this->_totalAcumulado;
    }
    
    
        
    public function consultar ($arrParametros){

    //var_dump($arrParametros);
        
    
        $this->_db->addSelect("to_char(factura_fecha,'DD-MM-YYYY') AS fecha, sum(importe_detalle) as total_detalle,sum(de.producto_pcosto * cantidad) AS total_costo, factura_nro, cliente_nombre, sum(importe_detalle-(de.producto_pcosto*cantidad)) AS ganancia_neto, factura_id");
        $this->_db->addFrom('datos_facturas');
        $this->_db->addFrom('INNER JOIN detalle_facturas  de USING(factura_id)');
        $this->_db->addFrom('INNER JOIN datos_clientes USING(cliente_id)');
        $this->_db->addWhere('de.producto_pcosto >= 0 ');
        
        
        //**ANALIZO EL WHERE**//
        if($arrParametros['stringBuscar']){
            $this->_db->addWhere(' cantidad > 0 AND sucursal_id = ' . $arrParametros[stringBuscar]);
            $primerRegistro = 0;
            $ultimoRegistro = 11;
	    }else{
            //La cuenta loca para cargar solo lo que necestiamos mostrar
            $primerRegistro =   (($arrParametros['pagina'] * $arrParametros['porPagina'] ) - $arrParametros['porPagina'] + 1)-1;
            $ultimoRegistro =   ($primerRegistro + $arrParametros['porPagina']);
        }

        if($arrParametros['productos'] !=null ){
            $this->_db->addWhere('de.producto_id = \'' . $arrParametros['productos'] . '\' ');
        }
        if($arrParametros['marcas'] !=null ){
            $this->_db->addFrom('INNER JOIN datos_productos USING(producto_id)');
            $this->_db->addWhere("marca_id =  $arrParametros[marcas]");
        }
        if($arrParametros['subfamilias'] !=null ){
            $this->_db->addFrom('INNER JOIN relaciones_familias_subfamilias USING(producto_id)');
            $this->_db->addWhere('subfamilia_id = \'' . $arrParametros[subfamilias] . '\'');
	    }
	    if($arrParametros['familias'] !=null ){
            $this->_db->addFrom('INNER JOIN vw_familias_productos USING(producto_id)');
            $this->_db->addWhere('vw_familias_productos.familia_id = \'' . $arrParametros[familias] . '\'');
	    }
        if($arrParametros['vendedoresClientes'] !=null){
            $this->_db->addWhere('datos_facturas.vendedor_id  = \'' . $arrParametros['vendedoresClientes'] . '\' ');
        }
        if($arrParametros['clientes'] !=null){
            $this->_db->addWhere('cliente_id  = \'' . $arrParametros['clientes'] . '\' ');
        }
        if($arrParametros['fechaDesde'] !=null){
            $this->_db->addWhere('factura_fecha >= \'' . $this->_objFuncionesComunes->fechaFormatoDb($arrParametros[fechaDesde]) . '\'');
        }
        if($arrParametros['fechaHasta'] !=null){
            $this->_db->addWhere('factura_fecha <= \'' . $this->_objFuncionesComunes->fechaFormatoDb($arrParametros[fechaHasta]) . '\'');
        }
	$this->_db->addWhere('factura_cancelada=false');
    $this->_db->addWhere('rentabilidad=true');
        //**fin where **/
        //**INICIO ORDER BY**//
        //*seteo el orden*/
        $this->setOrden($arrParametros);
        $this->_db->AddOrderBy($this->_ordenColumnas[ordenarPor] ." ". $this->_ordenColumnas[ordenarOrden]);
        //**FIN ORDER BY**/
        //groupBy
        $this->_db->addGroup("to_char(factura_fecha,'DD-MM-YYYY') , factura_nro, cliente_nombre, factura_fecha, factura_id");
        
        //*INICIO LIMIT*//
        if($arrParametros['tipo']=='consultaStock'){
            //$this->_db->addLimit(100);
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
        for($i=0; $i<$primerRegistro; $i++){
	    
            $this->setTotalParcial($this->getTotalParcial() + $arr[$i]['ganancia_neto']);
        }
        for($i=$primerRegistro;$i<$ultimoRegistro;$i++){   
            $array['total']=$this->getTotalParcial()  + $arr[$i]['ganancia_neto'];;
            $this->setTotalParcial($this->getTotalParcial()  + $arr[$i]['ganancia_neto']);
            $array['facturaId'] = $arr[$i]['factura_id'];
            $array['fecha'] = $arr[$i]['fecha'];
            $array['cliente_nombre'] = $arr[$i]['cliente_nombre'];
            $array['factura_nro'] = $arr[$i]['factura_nro'];
            $array['total_detalle'] = $arr[$i]['total_detalle'];
            $array['total_costo'] = $arr[$i]['total_costo'];
            $array['utilidad_pesos_detalle'] = $arr[$i]['total_detalle'] - $array['total_costo'];
            $array['utilidad_pesos_porcentaje'] = round((float)(($array['utilidad_pesos_detalle']  / $array['total_detalle']) *100),2);
            
            
            $this->_colConsultasStock[]= $array;
        }
        
    }
    
    public function setOrden($parametros){
        if($parametros['ordenarPor'] != "" && $parametros['ordenarOrden'] != "")
        {
            $this->_ordenColumnas['ordenarPor']    =   $parametros['ordenarPor'];
            $this->_ordenColumnas['ordenarOrden']  =   $parametros['ordenarOrden'];
        }else
        {
            //definir default
            $this->_ordenColumnas['ordenarPor']    =   'factura_fecha';
            $this->_ordenColumnas['ordenarOrden']  =   'desc';
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
        $i = 1;
        setlocale(LC_MONETARY, 'en_US');
        foreach($this->_colConsultasStock AS $objConsulta){
                $arr[] = array(
                    //id si o si un solo string (sin espacios en blanco)
                    "id"=>  $objConsulta['facturaId'], //asi cuanta desde 1 en adelante
                    //define las herramientas
                    "herramientas"   =>  array(
                        "cancelable"    =>  false,
                        "editable"      =>  false,
                        "detalles"      =>  true,
                        "imprimir"      =>  false
                        ),
                    //las celdas propiamente dichas, en el orden en que estan declaradas las columnas
                    "cell" => array(
                                $objConsulta['fecha'],
                                $objConsulta['cliente_nombre'],
                                $objConsulta['factura_nro'],
                                $this->_objFuncionesComunes->formatoMoneda($objConsulta['total_detalle']),
                                $this->_objFuncionesComunes->formatoMoneda($objConsulta['total_costo']),
                                $this->_objFuncionesComunes->formatoMoneda($objConsulta['utilidad_pesos_detalle']),
                                "%" . $objConsulta['utilidad_pesos_porcentaje'],
                              ),
            );
            $i++;
        }
       return $arr;
    }
public function armarDetalles($id){
    
        $this->_db->addSelect("to_char(factura_fecha,'DD-MM-YYYY') AS fecha, producto_nombre || producto_presentacion AS producto,importe_detalle, importe_unitario, cantidad, de.producto_pcosto, factura_nro, cliente_nombre, importe_detalle - (de.producto_pcosto * cantidad) AS ganancia_neto, de.producto_comision");
        $this->_db->addFrom('datos_facturas');
        $this->_db->addFrom('INNER JOIN detalle_facturas  de USING(factura_id)');
        $this->_db->addFrom('INNER JOIN datos_productos USING(producto_id)');
        $this->_db->addFrom('INNER JOIN datos_clientes USING(cliente_id)');
        $this->_db->addWhere('de.producto_pcosto >= 0 ');
        $this->_db->addWhere("factura_id = $id ");
        $this->_db->AddOrderBy('producto_nombre');
        $this->_db->generarSelect();
        //echo $this->_db->getQry();exit;
        $resultado =  $this->_db->ejecutar();

        
        $propiedadesRemito =   array(
			array(

			    "display"   =>  'Fecha',
			    "name"      =>  'id_movimiento',
			    "editable"  =>  false,
			    "class"	=>'',
			    "value"     =>  $resultado[0]['fecha'],
			),
			array(
			    "display"   =>  'Cliente',
			    "name"      =>  'prove_id',
			    "editable"  =>  false,
			    "class"	=> 'autocomplete',
			    "value"     =>  $resultado[0]['cliente_nombre'],

			),
			array(
			    "display"   =>  'Remito Nro',
			    "name"      =>  'tipo_movimiento_id',
			    "editable"  =>  false,
			    "class"	=>  '',
			    "value"     =>  $resultado[0]['factura_nro'],

			),
		    );
        
        
        foreach($resultado AS $detalle){
             
	
            $arr []   =    array
                (
                //id si o si un solo string (sin espacios en blanco)
                "id"            =>  $detalle,
                //define las herramientas
                "herramientas"   =>  array(
                "editable"      =>  false,
                ),
                //las celdas propiamente dichas, en el orden en que estan declaradas las columnas
                "cell"          => array(
                    $detalle['producto'],
                    $this->_objFuncionesComunes->formatoMoneda($detalle['importe_unitario']),
                    $this->_objFuncionesComunes->formatoMoneda($detalle['producto_pcosto']),
                    $detalle['cantidad'],
                    $this->_objFuncionesComunes->formatoMoneda($detalle['importe_detalle']),
                    $this->_objFuncionesComunes->formatoMoneda($detalle['producto_pcosto'] * $detalle['cantidad']),
                    $this->_objFuncionesComunes->formatoMoneda($detalle['ganancia_neto']),
                    "% " . round((($detalle['ganancia_neto'] / $detalle['importe_detalle'])*100),2),
                    
                ),
		    );
        }
    
	$detalles=   array(
	    "modelo"    =>  array(
            array(
                "display"   =>  'Producto',
                "name"      =>  'peso_neto',
                "class"	=>  '',
                "editable"  =>  false,
            ),
            array(
                "display"   =>  'Precio Venta',
                "name"      =>  'peso_neto',
                "class"	=>  '',
                "editable"  =>  false,
            ),
            array(
                "display"   =>  'Costo',
                "name"      =>  'peso_neto',
                "class"	=>  '',
                "editable"  =>  false,
            ),
            array(
                "display"   =>  'Cant',
                "name"      =>  'peso_neto',
                "class"	=>  '',
                "editable"  =>  false,
            ),
            array(
                "display"   =>  'Total',
                "name"      =>  'peso_neto',
                "class"	=>  '',
                "editable"  =>  false,
            ),
            array(
                "display"   =>  'Total Costo',
                "name"      =>  'peso_neto',
                "class"	=>  '',
                "editable"  =>  false,
            ),
            array(
                "display"   =>  'Rent Abs',
                "name"      =>  'peso_neto',
                "class"	=>  '',
                "editable"  =>  false,
            ),
            array(
                "display"   =>  'Rent Rel',
                "name"      =>  'peso_neto',
                "class"	=>  '',
                "editable"  =>  false,
            ),
	    ),
	    "celdas"    =>  $arr
	);
	$array_devolver =   array(
	    "editable_cabecera" => false,
	    "propiedades"       =>  $propiedadesRemito,
	    "listadoDetalles"   =>  $detalles
        );
    return $array_devolver;
    }
    
}
?>
