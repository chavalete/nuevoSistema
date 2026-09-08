<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/includes/FuncionesComunes.php';
/**
 * Description of FrenteStock
 *
 * @author dMELMAC
 */

class FrenteCheques
{
    private $_db;
    private $_colBancos = Array();
    private $_objFuncionesComunes;
    private $_bancoId;
    private $_bancoNombre;
    
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
    
    public function buscarCheques ($arrParametros){
	
        //**ANALIZO EL WHERE**//
        if($arrParametros['stringBuscar']){
            $this->_db->addWhere('cheque_nro ILIKE  \'%' . $arrParametros[stringBuscar] .'%\'');
            $primerRegistro = 0;
            $ultimoRegistro = 11;
	    }else{
            //La cuenta loca para cargar solo lo que necestiamos mostrar
            $primerRegistro =   (($arrParametros['pagina'] * $arrParametros['porPagina'] ) - $arrParametros['porPagina'] + 1)-1;
            $ultimoRegistro =   ($primerRegistro + $arrParametros['porPagina']);
        }
        $this->_db->addSelect('datos_cheques.observaciones, cheque_id, to_char(fecha_alta,\'DD-MM-YY\') AS fecha_alta, cliente_nombre, banco_nombre, cheque_nro, importe, to_char(fecha_acreditacion,\'DD-MM-YY\') AS fecha_acreditacion, descripcion, to_char(datos_cheques.imputacion_fecha_hora,\'DD-MM-YY HH24:MI\') AS imputacion_fecha_hora, estado_desc, forma_desc'); 
        $this->_db->addFrom('datos_cheques');
        $this->_db->addFrom('INNER JOIN datos_clientes USING(cliente_id)');
        $this->_db->addFrom('INNER JOIN datos_bancos USING(banco_id)');
	$this->_db->addFrom('INNER JOIN datos_estados USING(estado_id)');
	$this->_db->addFrom('INNER JOIN datos_formas_pagos USING(forma_pago_id)');
        $this->_db->addFrom('LEFT JOIN datos_imputaciones USING(imputacion_id)');
        
        if($arrParametros['clientes'] !=null ){
            $this->_db->addWhere('cliente_id = \'' . $arrParametros['clientes'] . '\' ');
        }
        if($arrParametros['cheques'] !=null ){
            $this->_db->addWhere('cheque_id = \'' . $arrParametros['cheques'] . '\' ');
        }
        if($arrParametros['bancos'] !=null ){
            $this->_db->addWhere('banco_id  = \'' . $arrParametros['bancos'] . '\' ');
        }
        if($arrParametros['imputaciones'] !=null ){
            $this->_db->addWhere('imputacion_id  = \'' . $arrParametros['imputaciones'] . '\' ');
        }
        if($arrParametros['estados'] !=null ){
            $this->_db->addWhere('estado_id  = \'' . $arrParametros['estados'] . '\' ');
	}
	if($arrParametros['formaId'] !=null ){
            $this->_db->addWhere('forma_pago_id  = \'' . $arrParametros['formaId'] . '\' ');
        }
        if($arrParametros['fechaDesde'] !=null && $arrParametros[desdeAlta]==null){
            $this->_db->addWhere('fecha_acreditacion >= \'' . $this->_objFuncionesComunes->fechaFormatoDb($arrParametros['fechaHasta']) . '\'');
        }
        if($arrParametros['fechaHasta'] !=null && $arrParametros[desdeAlta]==null){
            $this->_db->addWhere('fecha_acreditacion <= \'' . $this->_objFuncionesComunes->fechaFormatoDb($arrParametros['fechaHasta']) . '\'');
        }
        $this->_db->addWhere('anulado=false');
        $this->setOrdenCheques($arrParametros);
        $this->_db->AddOrderBy($this->_ordenColumnas[ordenarPor] ." ". $this->_ordenColumnas[ordenarOrden]);
        //**FIN ORDER BY**/
        //groupBy
        //$this->_db->addGroup('datos_productos.producto_id, lote_id, estanteria_id, almacen_id, sucursal_id, cantidad, valor_stock, costo_stock');
        
        //*INICIO LIMIT*//
        /*if($arrParametros['tipo']=='cheques'){
            $this->_db->addLimit(100);
        }*/
        //*FIN LIMIT*//
        $this->_db->generarSelect();
        echo $this->_db->getQry();
        $arr =  $this->_db->ejecutar();

        $this->setTotal(count($arr));
        $this->setPagina($arrParametros['pagina']);

        if($this->getTotal() < $ultimoRegistro){
            $ultimoRegistro = $this->getTotal();
        }
        for($i=$primerRegistro;$i<$ultimoRegistro;$i++){
            $array['chequeId'] = $arr[$i]['cheque_id'];
            $array['fechaAlta'] = $arr[$i]['fecha_alta'];
            $array['clienteNombre'] = $arr[$i]['cliente_nombre'];
            $array['banco'] = $arr[$i]['banco_nombre'];
	    $array['chequeNro'] = $arr[$i]['cheque_nro'];
	    $array['tipo'] = $arr[$i]['forma_desc'];
            $array['importe'] = $arr[$i]['importe'];
            $array['fechaAcred'] = $arr[$i]['fecha_acreditacion'];
            $array['imputacion'] = $arr[$i]['descripcion'];
            $array['imputacionFecha'] = $arr[$i]['imputacion_fecha_hora'];
            $array['observaciones'] = $arr[$i]['observaciones'];
            $array['estado'] = $arr[$i]['estado_desc'];
            $this->_colCheques[]= $array;
        }
        
    }
    
    public function setOrdenCheques($parametros){
        if($parametros['ordenarPor'] != "" && $parametros['ordenarOrden'] != "")
        {
            $this->_ordenColumnas['ordenarPor']    =   $parametros['ordenarPor'];
            $this->_ordenColumnas['ordenarOrden']  =   $parametros['ordenarOrden'];
        }else
        {
            //definir default
            $this->_ordenColumnas['ordenarPor']    =   'fecha_acreditacion::date';
            $this->_ordenColumnas['ordenarOrden']  =   'asc';
        }
    }
    public function getOrdenCheques(){
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
        foreach($this->_colCheques AS $objConsulta){
                $arr[] = array(
                    //id si o si un solo string (sin espacios en blanco)
                    "id"=>  $objConsulta['chequeId'],
                    //define las herramientas
                    "herramientas"   =>  array(
                        "cancelable"    =>  false,
                        "editable"      =>  true,
                        "detalles"      =>  false,
                        "imprimir"      =>  false
                        ),
                    //las celdas propiamente dichas, en el orden en que estan declaradas las columnas
                    "cell" => array(
                                $objConsulta['fechaAlta'],
                                $objConsulta['clienteNombre'],
                                $objConsulta['banco'],
				$objConsulta['chequeNro'],
				$objConsulta['tipo'],
                                money_format('%.2n', $objConsulta['importe']),
                                $objConsulta['fechaAcred'],
                                $objConsulta['imputacion'],
                                $objConsulta['imputacionFecha'],
                                $objConsulta['estado'],
                                $objConsulta['observaciones'],
                                
                              ),
            );
            $i++;
        }
       return $arr;
    }
    public function getDetallesAutocompletar(){

        foreach($this->_colCheques AS $cheque){
            $arr[]   =   array(
                    'label' =>  $cheque['banco'] . " Nro: ". $cheque['chequeNro'],
                    'value' => $cheque['chequeId']
                    );
        }		
	return $arr;
    }
    public function imputarCheques($arrParametros){
        $arrDetalle = explode ("||",$arrParametros[cheques]);
        array_pop($arrDetalle);
        
        foreach ($arrDetalle AS $detalle){    
            list($chequeId,$imputacionId,$estadoId, $observaciones) = explode("#",$detalle);
            
                if($imputacionId !=null){
                    $this->_db->addCamposUpdate('imputacion_id = \'' . $imputacionId .'\'');
                }
                if($estadoId!=null){
                    $this->_db->addCamposUpdate('estado_id = \'' . $estadoId .'\'');
                }
                if($observaciones!=null){
                    $this->_db->addCamposUpdate('observaciones = \'' . $observaciones .'\'');
                }
                $this->_db->addCamposUpdate('imputacion_usuario_id = \'' . $_SESSION['usuarioId'] .'\'');
                $this->_db->addCamposUpdate('imputacion_fecha_hora =  now()');
                $this->_db->addFrom('datos_cheques');
                $this->_db->addWhere('cheque_id = \'' . $chequeId .'\'');
            
                //generar qry
                $this->_db->generarUpdate();
                
                $qry.= $this->_db->getQry();
            
        }
        //echo $qry;exit;
        $this->_db->setQry($qry);
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
        echo json_encode($arrDevolver);exit;
    }
    public function actualizarMe($arrParametros){

    
        if($arrParametros['campos']['imputaciones']!='false'){
            $this->_db->addCamposUpdate('imputacion_id = \'' . $arrParametros['campos']['imputaciones'] .'\'');
        }
        if($arrParametros['campos']['estados']!='false'){
            $this->_db->addCamposUpdate('estado_id = \'' . $arrParametros['campos']['estados'] .'\'');
        }
        if($arrParametros['campos']['observaciones']!=null){
            $this->_db->addCamposUpdate('observaciones = \'' . $arrParametros['campos']['observaciones'] .'\'');
        }
            
        $this->_db->addCamposUpdate('imputacion_usuario_id = \'' . $_SESSION['usuarioId'] .'\'');
        $this->_db->addCamposUpdate('imputacion_fecha_hora =  now()');
        $this->_db->addFrom('datos_cheques');
        $this->_db->addWhere('cheque_id = \'' . $arrParametros['id'] .'\'');
    
        //generar qry
        $this->_db->generarUpdate();
        
        //echo $this->_db->getQry();
        
        
        $resultado =  $this->_db->ejecutar();
        if($resultado==1){
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
        echo json_encode($arrDevolver);exit;
        
    
    }
}
?>
