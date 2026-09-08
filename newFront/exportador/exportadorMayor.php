<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/includes/FuncionesComunes.php';
ob_end_clean();                                                                                                                                                                  
require_once $_SERVER['DOCUMENT_ROOT'] .'/newFront/libreria/fpdf/fpdf.php';                                                                                                  


if($_GET){
    $arrParametros['facturas']=$_REQUEST['facturas'];
    $arrParametros['idCliente'] = $_REQUEST['idCliente'];
    $arrParametros['ordenNro'] = $_REQUEST['ordenNro'];
    $arrParametros['fechaDesde'] = $_REQUEST['fechaDesde'];
    $arrParametros['fechaHasta'] = $_REQUEST['fechaHasta'];   
    $arrParametros['idVendedor'] = $_REQUEST['idVendedor'];
    $arrParametros['relacionId'] = $_REQUEST['relacionId'];    
 

    $db = NEW FrenteAlmacenamiento();
    $objFuncionesComunes =  new FuncionesComunes();

    $db->addSelect('cta_cte_clientes.relacion_id');
    $db->addFrom('cta_cte_clientes');
    
    $db->addFrom('INNER JOIN datos_clientes USING(cliente_id)');
    $db->addFrom('INNER JOIN relaciones_clientes_cuentas USING(relacion_id)');
    $db->addFrom('LEFT JOIN datos_facturas USING(factura_id)');
    $db->addFrom('LEFT JOIN datos_cobros USING(cobro_id)');
    
    //$db->addWhere('factura_nro = \'0\' ');
    
        //**ANALIZO EL WHERE**//
    //var_dump($arrParametros);exit;
    if($arrParametros['idCliente'] !='undefined'){
        $db->addWhere('cta_cte_clientes.cliente_id  = \'' . $arrParametros['idCliente'] . '\' ');
    }
    if($arrParametros['relacionId'] !='undefined'){
        $db->addWhere('cta_cte_clientes.relacion_id = \'' . $arrParametros['relacionId'] . '\' ');
    }
    if($arrParametros['facturas'] !='undefined'){
        $db->addWhere('factura_id  = \'' . $arrParametros['facturas'] . '\' ');
    }
    if($arrParametros['ordenNro'] !='undefined'){
        $db->addWhere('orden_cobro_nro  = \'' . $arrParametros['ordenNro'] . '\' ');
    }
    if($arrParametros['fechaDesde'] !=null ){
        $db->addWhere('fecha >= \'' . $objFuncionesComunes->fechaFormatoDb($arrParametros['fechaDesde']) . '\'');
    }
    if($arrParametros['fechaHasta'] !=null ){
        $db->addWhere('fecha <= \'' . $objFuncionesComunes->fechaFormatoDb($arrParametros['fechaHasta']) . '\'');
    }
   if($arrParametros[idVendedor]!=''){
            $db->addWhere('vendedor_id = \'' . $arrParametros[idVendedor] . '\'');
	    }
    $db->addWhere('anulado = false');

    $db->addGroup('cta_cte_clientes.relacion_id,cliente_nombre');
    $db->addOrderBy('cliente_nombre ASC');
    $db->generarSelect();
    //echo $db->getQry();exit;
    //**fin where **/
    //**INICIO ORDER BY**//
    $db->AddOrderBy(" operacion_fecha_hora ASC ");
    
    $resultadoRelacion =  $db->ejecutar();

    $pdf = new FPDF ('P','mm',array(210,297));
    
    foreach($resultadoRelacion AS $dato){
        $pdf->AddPage();   

//echo "entr";
 
        $db->addSelect('to_char(fecha,\'DD/MM/YY\') AS fecha');
        $db->addSelect('cliente_nombre');
        $db->addSelect('factura_nro');
        $db->addSelect('factura_total_fact');
        $db->addSelect('importe_total_cobro');
        $db->addSelect('orden_cobro_nro');
        $db->addSelect('importe_deuda');
        $db->addSelect('relacion_nombre');
        $db->addFrom('cta_cte_clientes');
        $db->addFrom('INNER JOIN datos_clientes USING(cliente_id)');
        $db->addFrom('INNER JOIN relaciones_clientes_cuentas USING(relacion_id)');
        $db->addFrom('LEFT JOIN datos_facturas USING(factura_id)');
        $db->addFrom('LEFT JOIN datos_cobros USING(cobro_id)');
        
        $db->addWhere('cta_cte_clientes.relacion_id = \'' . $dato[relacion_id] . '\'');
    
	 if($arrParametros['fechaDesde'] !=null ){
        	$db->addWhere('fecha >= \'' . $objFuncionesComunes->fechaFormatoDb($arrParametros['fechaDesde']) . '\'');
	   }
	if($arrParametros['fechaHasta'] !=null ){
        	$db->addWhere('fecha <= \'' . $objFuncionesComunes->fechaFormatoDb($arrParametros['fechaHasta']) . '\'');
	   }


    
        $db->addWhere('anulado = false');
        
        $db->AddOrderBy(" operacion_fecha_hora ASC ");
        $db->generarSelect();
	//echo $db->getQry();
        $resultado =  $db->ejecutar();
        
        
        $pdf->Image('ortodontia_negro_azul.jpg', 135, 10, 65, 25, 'jpg');
            
            $pdf->SetFont('Arial','B',10);
            $pdf->Text(90,10, "MAYOR");
            $pdf->Line(10, 12 ,200 , 12);
            
            //$pdf->Rect(10, 30 ,150 , 12);
            
            $pdf->Text(12,16, "CLIENTE:");
            $pdf->Text(12,21, "CUENTA:");
            $pdf->Text(30,21, $resultado[0][relacion_nombre]);
            
            $today = getdate();
            
            $pdf->Text(12,26, "FECHA: " . $today['mday'] . "/" . $today['mon'] . "/" . $today['year']);
            
            if($arrParametros['idCliente']=='undefined' && $arrParametros['relacionId']=='undefined'){
                $pdf->Text(30,16, "TODOS");
            }else{
                $pdf->Text(30,16, $resultado[0][cliente_nombre]);
            }
            $pdf->Line(10, 30 ,200 , 30);
            
            $pdf->Text(10,34, "FECHA");
            $pdf->Text(30,34, "CLIENTE");
            $pdf->Text(80,34, "FACTURA");
            $pdf->Text(110,34, "IMPORTE");
            $pdf->Text(135,34, "ORDEN");
            $pdf->Text(155,34, "PAGO");
            $pdf->Text(180,34, "DEUDA");
            $alto = 40;
            $i=0;
        
               
            
            foreach($resultado AS $consulta){
                if($alto>241){
                    $pdf->AddPage();;
                    $alto= 10;
                    $pdf->Text(10,$alto, "FECHA");
                    $pdf->Text(30,$alto, "CLIENTE");
                    $pdf->Text(80,$alto, "REMITO");
                    $pdf->Text(110,$alto, "IMPORTE");
                    $pdf->Text(135,$alto, "ORDEN");
                    $pdf->Text(155,$alto, "PAGO");
                    $pdf->Text(180,$alto, "DEUDA");
                    $alto = 16;
                }
                $pdf->Text(10,$alto, $consulta['fecha']);
                $pdf->Text(25,$alto, $consulta['cliente_nombre']);
                $pdf->Text(78,$alto, $consulta['factura_nro']);
                $pdf->Text(110,$alto, $objFuncionesComunes->formatoMoneda($consulta['factura_total_fact']));
                $pdf->Text(140,$alto, $consulta['orden_cobro_nro']);
                $pdf->Text(155,$alto, $objFuncionesComunes->formatoMoneda($consulta['importe_total_cobro']));
                $pdf->Text(180,$alto, $objFuncionesComunes->formatoMoneda($consulta['importe_deuda']));
                $alto = $alto +5;
                $i++;
            }
        }
    
}
$pdf->Output();
