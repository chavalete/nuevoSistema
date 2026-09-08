<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
require_once 'ProductoExtendido.php';
require_once 'PresentacionExtendido.php';

/**
 * Description of FrenteStock
 *
 * @author dMELMAC
 */

class FrenteStock
{
    private $_db;
    private $_colConsultasStock = Array();
    private $_objFuncionesComunes;

    public function __construct() {
        $this->_db = new FrenteAlmacenamiento();
	$this->_objFuncionesComunes = new FuncionesComunes();
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


    public function cargarObjPresentacionesProductos($relacionId){
        $objPresentacionProductos= new PresentacionesProductosExtendido();
        $objPresentacionProductos->cargarMe($relacionId);
        return $objPresentacionProductos;
    }

    public function consultarStock ($arrParametros){

        $this->_db->addSelect('datos_productos.producto_nombre,datos_productos.producto_presentacion, sum(cantidad) AS cantidad,producto_pcosto, datos_productos.producto_id');
        $this->_db->addFrom('datos_productos');
        $this->_db->addFrom('INNER JOIN stock_completo ON (datos_productos.producto_id = stock_completo.producto_id)');
        $this->_db->addFrom('INNER JOIN datos_marcas USING(marca_id)');
        //$this->_db->addWhere('cantidad >= 0 ');
        //**ANALIZO EL WHERE**//
        if($arrParametros['stringBuscar']){
            $this->_db->addWhere(' cantidad > 0 AND sucursal_id = ' . $arrParametros[stringBuscar]);
            $primerRegistro = 0;
            $ultimoRegistro = 11;
	    }else{
            //La cuenta loca para cargar solo lo que necestiamos mostrar
            $primerRegistro =   (($arrParametros['pagina'] * $arrParametros['porPagina'] ) - $arrParametros['porPagina'] + 1)-1;
            $ultimoRegistro =   ($primerRegistro + $arrParametros['porPagina']) ;
        }

        if($arrParametros['productos'] !=null ){
            $this->_db->addWhere('datos_productos.producto_id = \'' . $arrParametros['productos'] . '\' ');
        }
        /*if($arrParametros['subfamilias'] !=null ){
            $this->_db->addFrom('INNER JOIN relaciones_familias_subfamilias ON datos_productos.producto_id = relaciones_familias_subfamilias.producto_id');
            $this->_db->addWhere('subfamilia_id = \'' . $arrParametros['subfamilias'] . '\' ');
            $this->_db->addWhere('relacion_activa = true');
        }
        if($arrParametros['familias'] !=null ){
            $this->_db->addFrom('INNER JOIN relaciones_familias_subfamilias ON datos_productos.producto_id = relaciones_familias_subfamilias.producto_id');
            $this->_db->addWhere('relaciones_familias_subfamilias.familia_id = \'' . $arrParametros['familias'] . '\' ');
            $this->_db->addWhere('relacion_activa = true');
	}*/
	// Filtro por subfamilias y/o familias usando EXISTS para evitar duplicar el stock
        if ($arrParametros['subfamilias'] != null && $arrParametros['familias'] != null) {
            $this->_db->addWhere("EXISTS (
                SELECT 1 FROM relaciones_familias_subfamilias rfs
                WHERE rfs.producto_id = datos_productos.producto_id
                  AND rfs.subfamilia_id = '{$arrParametros['subfamilias']}'
                  AND rfs.familia_id = '{$arrParametros['familias']}'
                  AND rfs.relacion_activa = true
            )");
        } elseif ($arrParametros['subfamilias'] != null) {
            $this->_db->addWhere("EXISTS (
                SELECT 1 FROM relaciones_familias_subfamilias rfs
                WHERE rfs.producto_id = datos_productos.producto_id
                  AND rfs.subfamilia_id = '{$arrParametros['subfamilias']}'
                  AND rfs.relacion_activa = true
            )");
        } elseif ($arrParametros['familias'] != null) {
            $this->_db->addWhere("EXISTS (
                SELECT 1 FROM relaciones_familias_subfamilias rfs
                WHERE rfs.producto_id = datos_productos.producto_id
                  AND rfs.familia_id = '{$arrParametros['familias']}'
                  AND rfs.relacion_activa = true
            )");
        }
        if($arrParametros['productosNombre'] !=null ){
            $this->_db->addWhere('datos_productos.producto_id = \'' . $arrParametros['productosNombre'] . '\' ');
        }
        if($arrParametros['proveedores'] !=null){
            $this->_db->addWhere('prove_id = \'' . $arrParametros['proveedores'] . '\' ');
        }
        if($arrParametros['marcas'] !=null){
            $this->_db->addWhere('marca_id = \'' . $arrParametros['marcas'] . '\' ');
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
        $this->_db->addWhere("sucursal_id={$_SESSION[sucursalId]}");
        //**fin where **/
        //**INICIO ORDER BY**//
        //*seteo el orden*/
        $this->setOrdenStock($arrParametros);
        $this->_db->AddOrderBy($this->_ordenColumnas[ordenarPor] ." ". $this->_ordenColumnas[ordenarOrden]);
        //**FIN ORDER BY**/
        //groupBy
        $this->_db->addGroup('datos_productos.producto_nombre, datos_productos.producto_presentacion, marca_nombre,producto_pcosto,datos_productos.producto_id');

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

            $this->setTotalParcial($this->getTotalParcial() + $arr[$i]['cantidad']);
        }
        for($i=$primerRegistro;$i<$ultimoRegistro;$i++){
            $array['total']=$this->getTotalParcial()  + $arr[$i]['cantidad'];;
            $this->setTotalParcial($this->getTotalParcial()  + $arr[$i]['cantidad']);
            //$array['producto'] = $arr[$i]['producto_nombre'] . " - " . $arr[$i]['producto_presentacion'];
	    if($_SESSION['usuarioId']==11){
                $array['producto'] = $arr[$i]['producto_nombre'] . " - " . $arr[$i]['producto_presentacion'] . " - Costo :" . $arr[$i]['producto_pcosto'];
            }else{
                $array['producto'] = $arr[$i]['producto_nombre'] . " - " . $arr[$i]['producto_presentacion'];
            }
            $array['cantidad'] = $arr[$i]['cantidad'];
            $array['productoId'] = $arr[$i]['producto_id'];
            $this->_colConsultasStock[]= $array;
        }

    }

    public function setOrdenStock($parametros){
        if($parametros['ordenarPor'] != "" && $parametros['ordenarOrden'] != "")
        {
            $this->_ordenColumnas['ordenarPor']    =   $parametros['ordenarPor'];
            $this->_ordenColumnas['ordenarOrden']  =   $parametros['ordenarOrden'];
        }else
        {
            //definir default
            $this->_ordenColumnas['ordenarPor']    =   'marca_nombre, producto_nombre';
            $this->_ordenColumnas['ordenarOrden']  =   '';
        }
    }

    public function getOrdenStock(){
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
                    "id"=>  $objConsulta['productoId'], //asi cuanta desde 1 en adelante
                    //define las herramientas
                    "herramientas"   =>  array(
                        "cancelable"    =>  false,
                        "editable"      =>  false,
                        "detalles"      =>  true,
                        "imprimir"      =>  false
                        ),
                    //las celdas propiamente dichas, en el orden en que estan declaradas las columnas
                    "cell" => array(
                                $objConsulta['producto'],
                                $objConsulta['cantidad']
                              ),
            );
            $i++;
        }
       return $arr;
    }
    public function armarDetalles($id){
        $qry="SELECT producto_nombre || producto_presentacion AS producto,cantidad, to_char(lote_vencimiento,'DD-MM-YYYY') AS lote_vencimiento FROM stock_completo INNER JOIN datos_productos USING(producto_id) INNER JOIN datos_lotes USING(lote_id) WHERE stock_completo.producto_id = $id AND cantidad>0 AND stock_completo.sucursal_id={$_SESSION['sucursalId']} ORDER BY lote_vencimiento;";
        //echo $qry;exit;
        $this->_db->setQry($qry);
        $resultado = $this->_db->ejecutar();
        foreach($resultado AS $detalle){
            $arr []   =    array
		    (
			//id si o si un solo string (sin espacios en blanco)
			"id"            =>  $detalle[detalle_id],
			//define las herramientas
			"herramientas"   =>  array(
			 "editable"      =>  false,
			  ),
			 "bloqueado"     => false,
			//las celdas propiamente dichas, en el orden en que estan declaradas las columnas
			"cell"          => array(
			    $detalle[producto],
			    $detalle[lote_vencimiento],
			    $detalle[cantidad]
                ),
		    );
        }
        $detallesTransaccion    =   array(
	    "modelo"    =>  array(
            array(

                "display"   =>  'Producto',
                "name"      =>  'cantidad',
                "class"	=>  '',
                "editable"  =>  false,
            ),
            array(
                "display"   =>  'Vencimiento',
                "name"      =>  'producto_nombre',
                "class"	=>  'autocomplete',
                "editable"  =>  false,
            ),
                    array(
                "display"   =>  'Cantidad',
                "name"      =>  'producto_gtin',
                "class"	=>  '',
                "editable"  =>  false,
            ),

            ),
            "celdas"    =>  $arr
        );

        $array_devolver =   array(
            "propiedades"       =>  $propiedadesTransaccion,
            "listadoDetalles"   =>  $detallesTransaccion,
            "imprimir"		=>  false
        );

            return $array_devolver;
    }

}
?>
