<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
ob_end_clean();                                                                                                                                                                  
require_once $_SERVER['DOCUMENT_ROOT'] .'/newFront/libreria/fpdf/fpdf.php';                                                                                                  

                        


if($_GET){

	    $arrParametros['fechaDesde']=$_REQUEST['fechaDesde'];
	    $arrParametros['fechaHasta']=$_REQUEST['fechaHasta'];
	    $arrParametros['clientes']=$_REQUEST['clientes'];

	    $db = NEW FrenteAlmacenamiento();
	    
	    
	    $db->addSelect('to_char(movimiento_fecha_hora::date,\'DD-MM-YYYY\') AS fecha_movimiento, *');
	    $db->addSelect('to_char(movimiento_fecha_hora::date,\'MM-YYYY\') AS fecha_periodo');
	    $db->addFrom('datos_movimientos');
	    $db->addFrom('INNER JOIN datos_clientes USING (cliente_id)');
	    
	    
	    
	    if($arrParametros['clientes']!='undefined'){
		$db->addWhere('datos_movimientos.cliente_id = \'' . $arrParametros['clientes'] . '\'');
	    
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
	    $pdf->Line(10, 44 ,200 , 44);
	
	    if($arrParametros['clientes']=='undefined'){
		$pdf->Text(30,24, "TODOS");
		$pdf->Text(20,50, "CANTIDAD REMITOS");;
	    }else{
		$pdf->Text(30,24, $resultado[0][cliente_nombre]);
		$pdf->Text(20,50, "FECHA");
	    }
	    $pdf->Text(90,50, "CLIENTE");
	    $pdf->Text(150,50, "IMPORTE");
	    
	    
	    
	    $alto = 55;
	    $cantidadCodigos=0;
	    $total=0;
	    $totalCliente=0;
	    $cantRemitos=0;
	    $antCliente = null;
	    $cantidadCodigos=0;
	    $antCliente = $resultado[0][cliente_id];
	    
	    foreach($resultado AS $dato){
    
	    if($arrParametros['clientes']=='undefined'){
		if($antCliente == $dato[cliente_id]){
		    $totalCliente= $totalCliente + $dato[movimiento_total_fact]; 
		    $clienteNombre= $dato[cliente_nombre]; 
		}else{
		    $pdf->SetFont('Arial','',10);
		    $pdf->Text(30,$alto, $cantRemitos);;
		    $pdf->Text(80,$alto, $clienteNombre);;
		    $pdf->Text(150,$alto,"$". $totalCliente);
		    $cantRemitos=0;
		    $totalCliente=0;
		    $alto = $alto + 5;
		    $cantidadCodigos++;
		    $totalCliente= $totalCliente + $dato[movimiento_total_fact]; 
		    $clienteNombre= $dato[cliente_nombre];
		
		}
	    }else{
		$pdf->SetFont('Arial','',10);
		$pdf->Text(30,$alto, $dato['fecha_movimiento']);;
		$pdf->Text(80,$alto, $dato['cliente_nombre']);
		$pdf->Text(150,$alto,"$". $dato[movimiento_total_fact]);
		$alto = $alto + 5;
		$cantidadCodigos++;
	    
	    }
		$cantRemitos++;
		$antCliente = $dato[cliente_id];
		$total = $total + $dato['movimiento_total_fact'];
		if($cantidadCodigos==43){
		    $pdf->AddPage();;
		    $alto= 16;
		    $pdf->Text(20,10, "CANTIDAD REMITOS");;
		    $pdf->Text(90,10, "CLIENTE");;
		    $pdf->Text(150,10, "IMPORTE");;   
		    $alto = 16;
		    $cantidadCodigos=0;
    
		}
	    
	    }
	    if($arrParametros['clientes']=='undefined'){
		$pdf->Text(30,$alto, $cantRemitos);;
		$pdf->Text(80,$alto, $clienteNombre);;
		$pdf->Text(150,$alto, "$" . $totalCliente);
	    }
	    $pdf->Line(10, $alto+1 ,200 , $alto+1);
	    $pdf->Text(135,$alto+6, "TOTAL  $$total");   
	    //DESGLOCE
	    
	    
	    
	    
	    $db->addSelect('to_char(movimiento_fecha_hora,\'DD-MM-YYYY\') AS fecha,  tipo_producto_nombre, sum(detalle_total_fact) AS total, producto_kit');
	    $db->addFrom('datos_movimientos');
	    $db->addFrom('INNER JOIN detalle_movimientos USING (id_movimiento)');
	    $db->addFrom('INNER JOIN datos_clientes USING (cliente_id)');
	    $db->addFrom('INNER JOIN datos_productos ON detalle_movimientos.producto_id = datos_productos.producto_id');
	    $db->addFrom('INNER JOIN datos_tipos_productos USING(tipo_producto_id)');
	    
	    
	    if($arrParametros['clientes']!='undefined'){
		$db->addWhere('datos_movimientos.cliente_id = \'' . $arrParametros['clientes'] . '\'');
	    
	    }
	    
	    
	    $db->addWhere('movimiento_fecha_hora::date >= \'' . $fechaDesde. '\'');
	    $db->addWhere('movimiento_fecha_hora::date <= \'' . $fechaHasta . '\'');
	    $db->addWhere('datos_movimientos.tipo_movimiento_id  IN (2)');
	    $db->addWhere('anulado = false');
	    $db->addGroup(' tipo_producto_nombre, movimiento_fecha_hora, producto_kit');
	    $db->addOrderBy('tipo_producto_nombre');
	    $db->generarSelect();
	    $resultado = $db->ejecutar();
	    //echo $db->getQry();exit;
	    $total=0;
	    $unidades=0;
	    //var_dump($resultado);exit;
	    
	    
	    
	    $pdf->Text(20,$alto + 16, "DESGLOCE");;
	    $antTipoProducto = $resultado[0]['tipo_producto_nombre'];
	    
	    $alto = $alto + 21;
	    //echo $antCliente;exit;
	    foreach($resultado AS $movimiento){

		if($antTipoProducto == $movimiento['tipo_producto_nombre']){		
		    $total = $total + $movimiento['total'];
		    $unidades = $unidades + $movimiento['cantidad_unidades'];
		    $cliente = $movimiento['cliente_nombre'];
		    $tipo=$movimiento['tipo_producto_nombre'];
		    //echo $total;exit;
		}else{
		//echo $tipo;
		    $pdf->Text(20,$alto, $tipo);;
		    $pdf->Text(50,$alto,"$". $total);;
//		    fwrite($f,$linea);
		    $total=0;
		    $unidades=0;
		    $total = $total + $movimiento['total'];
		    $unidades = $unidades + $movimiento['cantidad_unidades'];
		    $cliente = $movimiento['cliente_nombre'];
		    $tipo=$movimiento['tipo_producto_nombre'];
		}
		$antCliente = $movimiento['cliente_nombre'];
		$antTipoProducto = $movimiento['tipo_producto_nombre'];
	    }
	    $pdf->Text(20,$alto+ 5, $tipo);;
	    $pdf->Text(50,$alto+5,  "$".$total);;


		//PRODUCTOS producto_kit
	    
	    
	    $db->addSelect('producto_nombre || producto_presentacion AS producto, sum(abs(cantidad)) AS cantidad');
	    $db->addFrom('datos_movimientos');
	    $db->addFrom('INNER JOIN detalle_movimientos USING (id_movimiento)');
	    $db->addFrom('INNER JOIN datos_productos ON detalle_movimientos.producto_id = datos_productos.producto_id');
	    
	    
	    if($arrParametros['clientes']!='undefined'){
		$db->addWhere('datos_movimientos.cliente_id = \'' . $arrParametros['clientes'] . '\'');
	    
	    }
	    
	    
	    $db->addWhere('movimiento_fecha_hora::date >= \'' . $fechaDesde. '\'');
	    $db->addWhere('movimiento_fecha_hora::date <= \'' . $fechaHasta . '\'');
	    $db->addWhere('datos_movimientos.tipo_movimiento_id  IN (2)');
	    $db->addWhere('anulado = false');
	    $db->addWhere('producto_kit = true');
	    $db->addGroup(' producto_presentacion, producto_nombre');
	    $db->addOrderBy('producto_nombre');
	    $db->generarSelect();
	    
	    
	    //echo $db->getQry();exit;
	    $resultado = $db->ejecutar();
	    
	    
	    $pdf->Text(20,$alto + 16, "KITS");;
	    
	    $alto = $alto + 21;
	    //echo $antCliente;exit;
	    foreach($resultado AS $kits){

		
		//echo $tipo;
		    $pdf->Text(20,$alto, $kits['producto']);;
		    $pdf->Text(100,$alto, $kits['cantidad']);;
		    
		    $alto = $alto + 8;
	    }


    
}
$pdf->Output();    
?>
