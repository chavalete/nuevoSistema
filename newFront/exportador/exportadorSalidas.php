<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/includes/FuncionesComunes.php';
ob_end_clean();                                                                                                                                                                  
require_once $_SERVER['DOCUMENT_ROOT'] .'/newFront/libreria/fpdf/fpdf.php';                                                                                                  

                        


if($_GET){  
        //var_dump($_GET);

	    $arrParametros['fechaDesde']=$_REQUEST['fechaDesde'];
	    $arrParametros['fechaHasta']=$_REQUEST['fechaHasta'];
	    $arrParametros['clientes']=$_REQUEST['clientes'];
        $arrParametros['vendedores']=$_REQUEST['vendedores'];
        $arrParametros['relacionId']=$_REQUEST['relacionId'];
        $arrParametros['cuentaId']=$_REQUEST['cuentaId'];
        $arrParametros['bonificacion']=$_REQUEST['bonificacion'];

	    $db = NEW FrenteAlmacenamiento();
	    $objFuncionesComunes = NEW FuncionesComunes();
	    
	    
	    $db->addSelect('to_char(movimiento_fecha_hora::date,\'DD-MM-YYYY\') AS fecha_movimiento, *');
        $db->addSelect('CASE WHEN descuento >  0  THEN ROUND(movimiento_total_fact - (movimiento_total_fact * descuento::integer / 100),2) ELSE movimiento_total_fact END AS movimiento_total_fact, cliente_direccion, remito_nro');
        
	    $db->addSelect('to_char(movimiento_fecha_hora::date,\'MM-YYYY\') AS fecha_periodo');
	    $db->addFrom('datos_movimientos');
	    $db->addFrom('INNER JOIN datos_clientes USING (cliente_id)');
	    $db->addWhere('datos_movimientos.tipo_movimiento_id  IN (2,17)');
	    $db->addWhere('anulado = false');
 
	    
	    
	    if($arrParametros['clientes']!='undefined'){
            $db->addWhere('datos_movimientos.cliente_id = \'' . $arrParametros['clientes'] . '\'');

		}
	    if($arrParametros['vendedores']!='undefined'){
            $db->addWhere('datos_movimientos.vendedor_id= \'' . $arrParametros['vendedores'] . '\'');
	    }
	    $ano= substr($arrParametros['fechaDesde'],6,4);
	    $mes = substr($arrParametros['fechaDesde'],3,2);
	    $dia = substr($arrParametros['fechaDesde'],0,2);
	    $fechaDesde = $ano."-".$mes ."-".$dia;
	    $db->addWhere('movimiento_fecha_hora::date >= \'' . $fechaDesde . '\'');
	    
	    $ano= substr($arrParametros['fechaHasta'],6,4);
	    $mes = substr($arrParametros['fechaHasta'],3,2);
	    $dia = substr($arrParametros['fechaHasta'],0,2);
	    $fechaHasta = $ano."-".$mes ."-".$dia;
	    
	    $db->addWhere('movimiento_fecha_hora::date <= \'' . $fechaHasta . '\'');
	    $db->addWhere('anulado=false');
	    //$db->addGroup('cliente_nombre, count, total');
	    $db->addOrderBy('cliente_nombre, movimiento_fecha_hora ASC');
	    $db->generarSelect();
	    $resultado = $db->ejecutar();
	    //echo $db->getQry();exit;
	    
	    
	    $pdf = new FPDF ('P','mm',array(210,297));
	    $pdf->AddPage();
	    
	    //$pdf->images('ortodontia_negro_azul.jpg',$x=null, $y=null, $w=0, $h=0, $type='', $link='');
	    $pdf->Image('../generador_pdf/limon.jpeg', 120, 12, 65, 25, 'jpg');
	    
	    $pdf->SetFont('Arial','B',10);
	    $pdf->Text(90,10, "INFORME GENERAL");
	    $pdf->Line(10, 12 ,200 , 12);
	    
	    //$pdf->Rect(10, 30 ,150 , 12);
	    
	    $pdf->Text(12,18, "VENTAS");;   
	    $pdf->Text(12,24, "CLIENTE:");
	    
	    $pdf->Text(12,30, "DESDE:");
	    $pdf->Text(30,30, $arrParametros['fechaDesde']);
	    $pdf->Text(12,36, "HASTA:");
	    $pdf->Text(30,36, $arrParametros['fechaHasta']);
	    $pdf->Line(10, 40 ,200 , 40);
	
	    if($arrParametros['clientes']=='undefined'){
		$pdf->Text(30,24, "TODOS");
		$pdf->Text(20,45, "REMITO");;
	    }else{
		$pdf->Text(30,24, $resultado[0][cliente_direccion]);
		$pdf->Text(20,45, "REMITO");
		
	    }
	    $pdf->Text(90,45, "CLIENTE");
	    
	    $pdf->Text(160,45, "IMPORTE");
	    $pdf->Line(10, 48 ,200 , 48);
	    
	    //$pdf->Line(10, 280 ,200 , 280);
	    
	    $alto = 53;
	    $cantidadCodigos=0;
	    $total=0;
	    $totalCliente=0;	    
	    $cantRemitos=0;
	    $antCliente = null;
	    $cantidadCodigos=0;
	    $antCliente = $resultado[0][cliente_id];
	    
	    foreach($resultado AS $dato){
    
	    
		$pdf->SetFont('Arial','',9);
		$pdf->Text(18,$alto, $dato['remito_nro']);;		
		$pdf->Text(60,$alto, utf8_decode($dato['cliente_nombre']));
		$pdf->Text(160,$alto,$objFuncionesComunes->formatoMoneda($dato[movimiento_total_fact]));		
		$alto = $alto + 5;
		$cantidadCodigos++;
	    
		$total = $total + $dato['movimiento_total_fact'];
		
		//echo $totalBonificado;
		if($cantidadCodigos==43){
		
		    if($hoja1!=true){
			$pdf->Line(10, 12 ,10, $alto+1);
			//linea borde fecha
			$pdf->Line(55, 40 ,55, $alto+1);
			//linea derecha
			$pdf->Line(200, 12 ,200, $alto+1);
			//liena derecha importe
			$pdf->Line(140, 40 ,140, $alto+1);
			$pdf->Line(10, $alto+1 ,200 , $alto+1);
		    }else{
		    $pdf->Line(10, 6 ,200 , 6);
		
            $pdf->Line(10, 6 ,10, $alto);
            //linea borde fecha
            $pdf->Line(55, 6 ,55, $alto+1);
            //linea derecha
            $pdf->Line(200, 6 ,200, $alto);
            //liena derecha importe
            $pdf->Line(140, 6 ,140, $alto);
            $pdf->Line(10, $alto+1 ,200 , $alto+1);
		    }
		    $hoja1=true;
		    
		    $pdf->AddPage();;
		    $alto= 16;
		    $pdf->Text(20,10, "REMITO");;
		    $pdf->Text(90,10, "CLIENTE");;
		    $pdf->Text(150,10, "IMPORTE");;   
		    $pdf->Line(10, 12 ,200 , 12);
		    
		    $alto = 16;
		    $cantidadCodigos=0;
		    
    
		}
	    
	    }
	    $pdf->Line(10, 12 ,10, $alto+1);
			//linea borde fecha
			$pdf->Line(55, 40 ,55, $alto+1);
			//linea derecha
			$pdf->Line(200, 12 ,200, $alto+1);
			//liena derecha importe
			$pdf->Line(140, 40 ,140, $alto+1);
			$pdf->Line(10, $alto+1 ,200 , $alto+1);
	    $pdf->Text(150,$alto+5,  "Total :" . $objFuncionesComunes->formatoMoneda($total));
}
$pdf->Output();    
?>
