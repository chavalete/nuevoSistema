<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
require_once 'ProductoExtendido.php';
require_once 'LoteExtendido.php';
require_once 'PresentacionExtendido.php';

/**
 * Description of FrenteStock
 *
 * @author dMELMAC
 */

class FrenteStockConsolidado
{
    private $_db;
    private $_colConsultasStock = Array();
    private $_objFuncionesComunes;

    public function __construct() {
        $this->_db = new FrenteAlmacenamiento();
        $this->_dbReparto = new FrenteAlmacenamiento('reparto');
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

        $this->_db->addSelect("datos_productos.producto_nombre,datos_productos.producto_presentacion, sum(cantidad) AS cantidad, producto_pcosto, CASE WHEN sucursal_id = 2 THEN 'Moreno' ELSE CASE WHEN sucursal_id = 3 THEN 'ROSAS' ELSE 'LIMONETA' END END AS sucursal_id, stock_completo.producto_id");
        $this->_db->addFrom('datos_productos');
        $this->_db->addFrom('INNER JOIN stock_completo ON (datos_productos.producto_id = stock_completo.producto_id)');
        $this->_db->addFrom('INNER JOIN datos_marcas USING(marca_id)');
        $this->_db->addWhere('cantidad >= 0 ');
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
        if($arrParametros['marcas'] !=null){
            $this->_db->addWhere('marca_id = \'' . $arrParametros['marcas'] . '\' ');
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
        //$this->_db->addWhere("stock_completo.producto_id=1116");
        //**fin where **/
        //**INICIO ORDER BY**//
        //*seteo el orden*/
        $this->setOrdenStock($arrParametros);
        $this->_db->AddOrderBy($this->_ordenColumnas[ordenarPor] ." ". $this->_ordenColumnas[ordenarOrden]);
        //**FIN ORDER BY**/
        //groupBy
        $this->_db->addGroup('datos_productos.producto_nombre, datos_productos.producto_presentacion,  marca_nombre, producto_pcosto,sucursal_id,stock_completo.producto_id');
        
        //*INICIO LIMIT*//
        if($arrParametros['tipo']=='consultaStock'){
            //$this->_db->addLimit(100);
        }
        //*FIN LIMIT*//
        $this->_db->generarSelect();
        //echo $this->_db->getQry();
        $arr =  $this->_db->ejecutar();

	  $arrFinal =  $this->armarMatriz($arr);
        $this->setTotal(count($arrFinal));
	$this->setPagina($arrParametros['pagina']);

	if($this->getTotal() < $ultimoRegistro){

	    $ultimoRegistro = $this->getTotal();
	}

        for($i=$primerRegistro;$i<$ultimoRegistro;$i++){   

	    //$array['producto'] = $arr[$i]['producto_nombre'] . " - " . $arr[$i]['producto_presentacion'];
            if($_SESSION['usuarioId']==10){
                $array['producto'] = $arrFinal[$i]['producto_nombre'] . " - " . $arrFinal[$i]['producto_presentacion'] . " - Costo :" . $arrFinal[$i]['producto_pcosto'];
            }else{
                $array['producto'] = $arrFinal[$i]['producto_nombre'] . " - " . $arrFinal[$i]['producto_presentacion'];
            }
            $array['rosas'] = $arrFinal[$i]['ROSAS'];
            $array['moreno'] = $arrFinal[$i]['Moreno'];
            $array['limoneta'] = $arrFinal[$i]['LIMONETA'];
            $array['productoId'] = $arrFinal[$i]['producto_id'];
            $array['reparto'] = $this->getStockReparto($arrFinal[$i]['producto_id']);
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
                                $objConsulta['rosas'],
                                $objConsulta['moreno'],
                                $objConsulta['limoneta'],
                                $objConsulta['reparto'],

                              ),
            );
            $i++;
        }
       return $arr;
    }
   public function armarMatriz($arrParametros){
       //var_dump($arrParametros);
        // 1. Identificamos todas las sucursales únicas para inicializar los valores por defecto
        $sucursales_ids = array_unique(array_column($arrParametros, 'sucursal_id'));
        sort($sucursales_ids);

        $matriz_asociativa = [];

        foreach ($arrParametros as $fila) {
            $p_id = $fila['producto_id'];
            $suc_id = $fila['sucursal_id'];

            // Si es la primera vez que procesamos este producto_id
            if (!isset($matriz_asociativa[$p_id])) {
            // Inicializamos los datos básicos del producto
            $matriz_asociativa[$p_id] = [
            'producto_id'     => $p_id,
            'producto_nombre' => $fila['producto_nombre'],
            'producto_presentacion' => $fila['producto_presentacion']
            ];

            // Creamos las columnas de las sucursales al mismo nivel con valor 0
                foreach ($sucursales_ids as $id) {
                    // Nota: Uso "sucursal_$id" para que la llave sea un texto claro,
                    // pero si prefieres solo el número, puedes cambiarlo por: $matriz_asociativa[$p_id][$id] = 0;
                    $matriz_asociativa[$p_id][$id] = 0;
                    }
            }

            // Asignamos la cantidad directamente al nivel principal de la fila
            $matriz_asociativa[$p_id][$suc_id] = $fila['cantidad'];
        }

        // 2. Quitamos las llaves asociativas de los productos para que quede una lista indexada (0, 1, 2...)
        $matriz_final = array_values($matriz_asociativa);

        // Ver el resultado estructurado
        /*echo "<pre>";
        print_r($matriz_final);
        echo "</pre>";*/

        return $matriz_final;
    }
    public function getStockReparto($productoId){
        $qry="SELECT gtin FROM presentaciones_productos WHERE producto_id =$productoId;";
        //echo $qry;
        $this->_db->setQry($qry);
        $resultado = $this->_db->ejecutar();
        if(count($resultado)>0){
            foreach($resultado AS $producto){
                $qry="SELECT sum(cantidad) AS cantidad FROM stock_completo INNER JOIN datos_productos USING(producto_id) WHERE producto_gtin='{$producto['gtin']}';";
                $this->_dbReparto->setQry($qry);
                $resultado = $this->_dbReparto->ejecutar();
                if($resultado[0][cantidad]>0){
                    return $resultado[0][cantidad];
                }else{
                    return 0;
                }
            }
        }else{
            return 0;
        }
    }
    public function getStockRepartoDetalle($productoId){
        $qry="SELECT gtin FROM presentaciones_productos WHERE producto_id =$productoId;";
        //echo $qry;
        $this->_db->setQry($qry);
        $resultado = $this->_db->ejecutar();
        if(count($resultado)>0){
            foreach($resultado AS $producto){
                $qry="SELECT producto_nombre || producto_presentacion AS producto,cantidad, to_char(lote_vencimiento,'DD-MM-YYYY') AS lote_vencimiento FROM stock_completo INNER JOIN datos_productos USING(producto_id) INNER JOIN datos_lotes USING(lote_id) WHERE datos_productos.producto_gtin = '{$producto['gtin']}' AND cantidad>0 ORDER BY lote_vencimiento ASC;";
                $this->_dbReparto->setQry($qry);
                $resultado = $this->_dbReparto->ejecutar();
                if($resultado[0][cantidad]>0){
                    return $resultado;
                }else{
                    return 0;
                }
            }
        }else{
            return 0;
        }
    }
    public function armarDetalles($id){
        $qry="SELECT producto_nombre || producto_presentacion AS producto,cantidad, to_char(lote_vencimiento,'DD-MM-YYYY') AS lote_vencimiento, stock_completo.sucursal_id, CASE WHEN stock_completo.sucursal_id = 2 THEN 'MORENO' ELSE CASE WHEN stock_completo.sucursal_id = 3 THEN 'ROSAS' ELSE 'LIMONETA' END END AS sucursal_nombre  FROM stock_completo INNER JOIN datos_productos USING(producto_id) INNER JOIN datos_lotes USING(lote_id) WHERE stock_completo.producto_id = $id AND cantidad>0 ORDER BY stock_completo.sucursal_id,lote_vencimiento ASC;";
        //echo $qry;exit;
        $this->_db->setQry($qry);
        $resultado = $this->_db->ejecutar();
        $antSucu=null;
        foreach($resultado AS $detalle){
            if($antSucu!=$detalle['sucursal_id']){

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
			    $detalle['sucursal_nombre'],
			    "-------",
			    "-------"
                ),
		    );
            }
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
            $antSucu = $detalle['sucursal_id'];
        }
        //REPARTO
        $detalleReparto = $this->getStockRepartoDetalle($id);
        foreach($detalleReparto AS $reparto){
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
			    "REPARTO",
			    "-------",
			    "-------"
                ),
		    );

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
			    $reparto[lote_vencimiento],
			    $reparto[cantidad]
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
