<?php
//require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/administracion/DatoPromocionExtendido.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
/*
FRENTE PARA CARGAR LO QUE EL FRENTE requiera en cuanto a estetica y datos
*/

class FrenteCombos
{
    private $_colCombos = Array();
    private $_ordenColumnas = Array();
    private $_total;
    private $_pagina;
    private $_objFuncionesComunes;


    public function __construct(){
        $this->_objFuncionesComunes = new FuncionesComunes();
        $this->_db = NEW FrenteAlmacenamiento('dmelmac');
    }

    public function buscar($arrParametros) {

        $this->_db->addSelect("to_char(combo_fecha,'DD-MM-YYYY') AS combo_fecha");
        $this->_db->addSelect("*");
        $this->_db->addSelect('dpc.activo AS producto_activo');
        $this->_db->addSelect('\'Activo\'  AS producto_activo_desc');
        $this->_db->addSelect('\'Inactivo\'  AS producto_no_activo_desc');
        $this->_db->addSelect('CASE WHEN dpc.activo THEN \'Activo\' ELSE \'Inactivo\' END AS producto_activo_mensaje');
        $this->_db->addFrom('datos_productos_combos dpc');
        $this->_db->addFrom('INNER JOIN  datos_productos dp ON dpc.combo_id=dp.producto_id');

        if($arrParametros['stringBuscar']){
            $this->_db->addWhere('producto_nombre ILIKE  \'%' . $arrParametros[stringBuscar] .'%\'');
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
        //$this->_db->addWhere('dpc.activo = true');


        //$this->_db->addGroup('factura_id, factura_nro');
        $this->setOrden($arrParametros);
        $this->_db->AddOrderBy($this->_ordenColumnas[ordenarPor] ." ". $this->_ordenColumnas[ordenarOrden]);
        //$this->_db->AddOrderBy('substring(factura_nro, position(\'-\' in factura_nro)+1)::integer asc');

        if($arrParametros['tipo']=='combos'){
            $this->_db->addLimit(100);
        }
        $this->_db->generarSelect();

        //echo $this->_db->getQry();

        $arr =  $this->_db->ejecutar();

        //ahora seteamos el total y la pagina
        $this->setTotal(count($arr));
        $this->setPagina($arrParametros['pagina']);

        if($this->getTotal() < $ultimoRegistro){
            $ultimoRegistro = $this->getTotal();
        }
        for($i=$primerRegistro;$i<$ultimoRegistro;$i++){
            $array['id'] = $arr[$i]['id'];
            $array['fecha'] = $arr[$i]['combo_fecha'];
            $array['producto'] = $arr[$i]['producto_nombre'] . " - " . $arr[$i]['producto_presentacion'];
            $array['obs'] = $arr[$i]['obs'];
            $array['activo'] = $arr[$i]['producto_activo'];
            $array['activoSi'] = $arr[$i]['producto_activo_desc'];
            $array['activoNo'] = $arr[$i]['producto_no_activo_desc'];
            $array['mensaje'] = $arr[$i]['producto_activo_mensaje'];
            $this->_colCombos[]= $array;
        }
    }
    public function setOrden($arrParametros){

        if($arrParametros['ordenarPor'] != "" && $arrParametros['ordenarOrden'] != ""){
            $this->_ordenColumnas['ordenarPor']    =   $arrParametros['ordenarPor'];
            $this->_ordenColumnas['ordenarOrden']  =   $arrParametros['ordenarOrden'];
        }else{
            //definir default
            //$this->_ordenColumnas['ordenarPor']    = 'substring(factura_nro, 7)';
            $this->_ordenColumnas['ordenarPor']    =   'producto_nombre';
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

	foreach($this->_colCombos AS  $objConsulta){
	    $arr []   =    array
            (
                //id si o si un solo string (sin espacios en blanco)
                "id"            =>  $objConsulta['id'],
                //define las herramientas
                "herramientas"   =>  array(
                    "cancelable"    =>  true,
                    "editable"      =>  true,
                    "detalles"      =>  true,
                    "imprimir"      =>  false,
                ),
                //las celdas propiamente dichas, en el orden en que estan declaradas las columnas
                "cell"          => array(
                    $objConsulta['fecha'],
                    $objConsulta['producto'],
                    $objConsulta['obs'],
                    Array
                    (
                    "value" =>$objConsulta['activo'],
                    "label" =>$objConsulta['mensaje'],
                    "select" =>
                        Array
                        (
                        Array(
                        "value" =>true,
                        "label" =>$objConsulta['activoSi']

                        ),
                        Array

                        (
                        "value" =>false,
                        "label" =>$objConsulta['activoNo']

                        )
                        )
                    )

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
    public function armarDetalles($id){

        $qry="SELECT producto_nombre || ' - ' || producto_presentacion AS producto, cantidad, detalle_id FROM detalle_productos_combos INNER JOIN datos_productos USING(producto_id) WHERE id = $id;";
        //echo $qry;
        $this->_db->setQry($qry);

        $resultado =  $this->_db->ejecutar();



        foreach($resultado AS $detalle){

            $arr []   =    array
                (
                //id si o si un solo string (sin espacios en blanco)
                "id"            =>  $detalle['detalle_id'],
                //define las herramientas
                "herramientas"   =>  array(
                "editable"      =>  true,
                ),
                //las celdas propiamente dichas, en el orden en que estan declaradas las columnas
                "cell"          => array(
                    $detalle['producto'],
                    $detalle['cantidad'],
                ),
		    );
        }

	$detalles=   array(
	    "modelo"    =>  array(
            array(

                "display"   =>  'Producto',
                "name"      =>  'productos',
                "class"	=>  'autocomplete',
                "editable"  =>  false,
            ),
            array(
                "display"   =>  'Cantidad',
                "name"      =>  'cantidad',
                "class"	=>  '',
                "editable"  =>  true,
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
	    "propiedades"       =>  $propiedades,
	    "listadoDetalles"   =>  $detalles
        );
    return $array_devolver;
    }
    public function actualizarCabecera($arrParametros){
        if($arrParametros['campos']['activo'] == 1){
            $this->_db->addCamposUpdate('activo = true');
        }else{
            $this->_db->addCamposUpdate('activo = false');
        }
        if($arrParametros['campos']['obs']!=NULL){
            $this->_db->addCamposUpdate('obs= \'' . $arrParametros['campos']['obs'] .'\'');
        }
        $this->_db->addFrom('datos_productos_combos');
        $this->_db->addWhere('combo_id = \'' . $arrParametros['id'] .'\'');

        //generar qry
        $this->_db->generarUpdate();
        //echo $this->_db->getQry();exit;
        //asigamos el resulta que es un array a una variable
        $resultado = $this->_db->ejecutar();

        if(count($resultado)> 0){
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
    public function actualizarDetalle($arrParametros){
        if(is_numeric($arrParametros['campos']['cantidad'])){
            $this->_db->addCamposUpdate('cantidad= \'' . $arrParametros['campos']['cantidad'] .'\'');
        }
        $this->_db->addFrom('detalle_productos_combos');
        $this->_db->addWhere('detalle_id = \'' . $arrParametros['id'] .'\'');

        //generar qry
        $this->_db->generarUpdate();
        //echo $this->_db->getQry();
        //asigamos el resulta que es un array a una variable
        $resultado = $this->_db->ejecutar();

        if(count($resultado)> 0){
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
    public function crearCombos($arrParametros){
        $arrDetalle = explode ("||",$arrParametros[productos]);
        array_pop($arrDetalle);

        $this->_db->addCamposTabla('datos_productos_combos_id_seq');
        $this->_db->generarProximo();

        $id = $this->_db->ejecutar();

        $Id = $id[0]['nextval'];

        foreach ($arrDetalle AS $detalle){

            list($productoId,$cantidad) = explode("#",$detalle);

            $this->_db->addCamposTabla('id');
            $this->_db->addCamposValue('\'' . $id[0]['nextval'] .'\'');
            $this->_db->addCamposTabla('combo_id');
            $this->_db->addCamposValue('\'' . $arrParametros['comboId'] .'\'');
            $this->_db->addCamposTabla('producto_id');
            $this->_db->addCamposValue('\'' . $productoId .'\'');
            $this->_db->addCamposTabla('cantidad');
            $this->_db->addCamposValue('\'' . $cantidad .'\'');

            $this->_db->addFrom('detalle_productos_combos');

            $this->_db->generarInsert();

            $qryCombo.=$this->_db->getQry();

        }
        $this->_db->addCamposTabla('id');
        $this->_db->addCamposValue('\'' . $id[0]['nextval'] .'\'');
        $this->_db->addCamposTabla('combo_id');
        $this->_db->addCamposValue('\'' . $arrParametros['comboId'] .'\'');
        $this->_db->addCamposTabla('obs');
        $this->_db->addCamposValue('\'' . $arrParametros['obs'] .'\'');
        $this->_db->addCamposTabla('combo_usuario_id');
        $this->_db->addCamposValue('\'' . $_SESSION['usuarioId'] .'\'');

        $this->_db->addFrom('datos_productos_combos');

        $this->_db->generarInsert();

        $qryCombo.=$this->_db->getQry();
        //echo $qryCombo;
        $this->_db->setQry($qryCombo);
        $this->_db->ejecutarTransaccion();
        if (count($this->_db->getArrError()) > 0){
            $arrDevolver = array(
                                "soyError" => true,
                                "nivel" => 0,
                                "alertados" => $arrAlertador,
                                "mensaje" => "Error al salvar!"
                                );
        }else{
            $arrDevolver = array(
                                "soyError" => false,
                                "nivel" => 0,
                                "alertados" => $arrAlertador,
                                "mensaje" => "Combo creado!"
                                );
        }
        echo json_encode($arrDevolver);exit;
    }
    public function cancelar($id){
        $qry="UPDATE datos_productos_combos SET activo=false, cancelado=true,cancelado_fecha_hora=now(), cancelado_usuario_id={$_SESSION['usuarioId']} WHERE id=$id;";
        //echo $qry;
        $this->_db->setQry($qry);
        $resultado = $this->_db->ejecutar();
        if ($resultado !== false && $resultado > 0) {
            $arrDevolver = array(
                "soyError" => false,
                "nivel" => 0,
                "mensaje" =>"Cancelado"
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
?>
