<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
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
	    
	    
	    $db->addSelect('to_char(movimiento_fecha_hora::date,\'DD-MM-YYYY\') AS fecha_movimiento, *');
        if($arrParametros['bonificacion']=='100'){
               $db->addSelect('movimiento_total_fact AS movimiento_total_fact');
        }else{
            $db->addSelect('CASE WHEN descuento >  0  THEN ROUND(movimiento_total_fact - (movimiento_total_fact * descuento::integer / 100),2) ELSE movimiento_total_fact END AS movimiento_total_fact');
        }
	    $db->addSelect('to_char(movimiento_fecha_hora::date,\'MM-YYYY\') AS fecha_periodo');
	    $db->addFrom('datos_movimientos');
	    $db->addFrom('INNER JOIN datos_clientes USING (cliente_id)');
	    $db->addFrom('INNER JOIN relaciones_clientes_cuentas USING (relacion_id)');
	    $db->addFrom('INNER JOIN datos_cuentas USING (cuenta_id)');
	    $db->addWhere('datos_movimientos.cliente_id NOT IN (269,306) ');
	    $db->addWhere('datos_movimientos.tipo_movimiento_id  IN (2)');
	    $db->addWhere('anulado = false');
 
	    
	    
	    if($arrParametros['clientes']!='undefined'){
            $db->addWhere('datos_movimientos.cliente_id = \'' . $arrParametros['clientes'] . '\'');

		}
	    if($arrParametros['cuentaId']!='undefined'){
            $db->addWhere('relaciones_clientes_cuentas.cuenta_id= \'' . $arrParametros['cuentaId'] . '\'');

		}
		if($arrParametros['relacionId']!='undefined'){
            $db->addWhere('datos_movimientos.relacion_id = \'' . $arrParametros['relacionId'] . '\'');

		}
	    if($arrParametros['vendedores']!='undefined'){
            $db->addWhere('relaciones_clientes_cuentas.vendedor_id= \'' . $arrParametros['vendedores'] . '\'');
	    }
	    if($arrParametros['bonificacion']!=NULL){
            $db->addWhere('descuento= \'' . $arrParametros['bonificacion'] . '\'');
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
	    $pdf->Image('ortodontia_negro_azul.jpg', 135, 10, 65, 25, 'jpg');
	    
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
		$pdf->Text(20,45, "CANTIDAD REMITOS");;
	    }else{
		$pdf->Text(30,24, $resultado[0][relacion_nombre]);
		$pdf->Text(20,45, "FECHA");
		
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
    
	    if($arrParametros['clientes']=='undefined'){
		if($antCliente == $dato[cliente_id]){
		    $totalCliente= $totalCliente + $dato[movimiento_total_fact]; 
		    $clienteNombre= $dato[cliente_nombre]; 
		}else{
		    $pdf->SetFont('Arial','',10);
		    $pdf->Text(30,$alto, $cantRemitos);;
		    $pdf->Text(80,$alto, $clienteNombre);;
		    $pdf->Text(155,$alto,"$". $totalCliente);
		    $cantRemitos=0;
		    $totalCliente=0;
		    $alto = $alto + 5;
		    $cantidadCodigos++;
		    $totalCliente= $totalCliente + $dato[movimiento_total_fact]; 
		    $clienteNombre= $dato[cliente_nombre];
		
		}
	    }else{
		$pdf->SetFont('Arial','',10);
		$pdf->Text(18,$alto, $dato['fecha_movimiento']);;
		$pdf->Text(80,$alto, $dato['relacion_nombre']);
		$pdf->Text(160,$alto,"$". $dato[movimiento_total_fact]);		
		$alto = $alto + 5;
		$cantidadCodigos++;
	    
	    }
	    
		$cantRemitos++;
		$antCliente = $dato[cliente_id];
		$total = $total + $dato['movimiento_total_fact'];
		$totalBonificado = $totalBonificado + $dato['importe_total_bonificado'];
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
		
		$pdf->Line(10, 6 ,10, $alto+6);
		//linea borde fecha
		$pdf->Line(55, 6 ,55, $alto+1);
		//linea derecha
		$pdf->Line(200, 6 ,200, $alto+6);
		//liena derecha importe
		$pdf->Line(140, 6 ,140, $alto+6);
		$pdf->Line(10, $alto+1 ,200 , $alto+1);
		    }
		    $hoja1=true;
		    
		    $pdf->AddPage();;
		    $alto= 16;
		    $pdf->Text(20,10, "FECHA");;
		    $pdf->Text(90,10, "CLIENTE");;
		    $pdf->Text(150,10, "IMPORTE");;   
		    $pdf->Line(10, 12 ,200 , 12);
		    
		    $alto = 16;
		    $cantidadCodigos=0;
		    
    
		}
	    
	    }
	    if($arrParametros['clientes']=='undefined'){
		$pdf->Text(30,$alto, $cantRemitos);;
		$pdf->Text(80,$alto, $clienteNombre);;
		$pdf->Text(150,$alto, "$" . $totalCliente);
	    }
	    
	    if($hoja1!=true){
		//linea borde derecho
		$pdf->Line(10, 12 ,10, $alto+6);
		//linea borde fecha
		$pdf->Line(55, 40 ,55, $alto+1);
		//linea derecha
		$pdf->Line(200, 12 ,200, $alto+6);
		//liena derecha importe
		$pdf->Line(140, 40 ,140, $alto+6);
		$pdf->Line(10, $alto+1 ,200 , $alto+1);
	    }else{
	    //echo $cantidadCodigos;exit;
		//linea superior
		$pdf->Line(10, 6 ,200 , 6);
		
		$pdf->Line(10, 6 ,10, $alto+6);
		//linea borde fecha
		$pdf->Line(55, 6 ,55, $alto+1);
		//linea derecha
		$pdf->Line(200, 6 ,200, $alto+6);
		//liena derecha importe
		$pdf->Line(140, 6 ,140, $alto+6);
		$pdf->Line(10, $alto+1 ,200 , $alto+1);
	    
	    }
	    $pdf->SetFont('Arial','B',10);
	    $pdf->Text(145,$alto+5, "TOTAL:  $$total");
	    $pdf->Line(10, $alto+6 ,200 , $alto+6);
	    if($arrParametros['clientes']!= 'undefined'){
            if($totalBonificado > 0){
                $pdf->Line(10, 12 ,10, $alto+11);
                $pdf->Line(200, 12 ,200, $alto+11);
                $pdf->Line(140, 40 ,140, $alto+11);
                $pdf->Text(142,$alto+10, utf8_decode("Bonificación:  $ ") . $totalBonificado);   
                $pdf->Line(10, $alto+11 ,200 , $alto+11);
            }
        }
	    //DESGLOCE
	    if($cantidadCodigos>=37 && $cantidadCodigos<43){
		$pdf->AddPage();;
		$alto= 16;
	    
	    }
	    
	    
	    $db->addSelect('to_char(movimiento_fecha_hora,\'DD-MM-YYYY\') AS fecha,  tipo_producto_nombre, sum(detalle_total_fact) AS total_ant, producto_kit, datos_movimientos.descuento');
	    if($arrParametros['bonificacion']!=100){
            $db->addSelect('CASE WHEN datos_movimientos.descuento >  0 THEN sum(ROUND(detalle_total_fact - (detalle_total_fact * datos_movimientos.descuento::integer / 100),2)) ELSE sum(detalle_total_fact) END AS total');
        }else{
            $db->addSelect('sum(detalle_total_fact) AS total');
        }
	    $db->addFrom('datos_movimientos');
	    $db->addFrom('INNER JOIN detalle_movimientos USING (id_movimiento)');
	    $db->addFrom('INNER JOIN datos_clientes USING (cliente_id)');
	    $db->addFrom('INNER JOIN datos_productos ON detalle_movimientos.producto_id = datos_productos.producto_id');
	    $db->addFrom('INNER JOIN datos_tipos_productos USING(tipo_producto_id)');
	    
	    
	    if($arrParametros['clientes']!='undefined'){
		$db->addWhere('datos_movimientos.cliente_id = \'' . $arrParametros['clientes'] . '\'');
	    
	    }
	    if($arrParametros['vendedores']!='undefined'){
		$db->addWhere('datos_movimientos.vendedor_id= \'' . $arrParametros['vendedores'] . '\'');
	    }
	    
	    $db->addWhere('movimiento_fecha_hora::date >= \'' . $fechaDesde. '\'');
	    $db->addWhere('movimiento_fecha_hora::date <= \'' . $fechaHasta . '\'');
	    $db->addWhere('datos_movimientos.tipo_movimiento_id  IN (2)');
	    $db->addWhere('anulado = false');
	    $db->addWhere('datos_movimientos.cliente_id NOT IN (269,306)  ');

	    $db->addGroup(' tipo_producto_nombre, movimiento_fecha_hora, producto_kit, datos_movimientos.descuento, detalle_total_fact');
	    $db->addOrderBy('tipo_producto_nombre');
	    $db->generarSelect();
	    $resultado = $db->ejecutar();
	    //echo $db->getQry();exit;
	    $total=0;
	    $unidades=0;
	    //var_dump($resultado);exit;
	    
	    
	    $altoDetalle =  $alto + 16;
	    $pdf->Text(30,$altoDetalle, "DESGLOCE");;
	    $antTipoProducto = $resultado[0]['tipo_producto_nombre'];
	    
	    
	    
	    $alto = $alto + 21;
	    //echo $alto;
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
		    $pdf->Text(14,$alto, $tipo);;
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
	    $pdf->Text(13,$alto+ 5, $tipo);;
	    $pdf->Text(50,$alto+5,  "$".$total);;


		//PRODUCTOS producto_kit
	    
	    
	    $db->addSelect('producto_nombre || producto_presentacion AS producto, sum(abs(cantidad)) AS cantidad');
	    $db->addFrom('datos_movimientos');
	    $db->addFrom('INNER JOIN detalle_movimientos USING (id_movimiento)');
	    $db->addFrom('INNER JOIN datos_productos ON detalle_movimientos.producto_id = datos_productos.producto_id');
	    
	    
	    if($arrParametros['clientes']!='undefined'){
		$db->addWhere('datos_movimientos.cliente_id = \'' . $arrParametros['clientes'] . '\'');
	    
	    }
	    if($arrParametros['vendedores']!='undefined'){
		$db->addWhere('datos_movimientos.vendedor_id= \'' . $arrParametros['vendedores'] . '\'');
	    }
	    
	    $db->addWhere('movimiento_fecha_hora::date >= \'' . $fechaDesde. '\'');
	    $db->addWhere('movimiento_fecha_hora::date <= \'' . $fechaHasta . '\'');
	    $db->addWhere('datos_movimientos.tipo_movimiento_id  IN (2)');
	    $db->addWhere('anulado = false');
	    $db->addWhere('producto_kit = true');
	    $db->addWhere('datos_movimientos.cliente_id NOT IN (269,306)  ');

	    $db->addGroup(' producto_presentacion, producto_nombre');
	    $db->addOrderBy('producto_nombre');
	    $db->generarSelect();
	    
	    
	    //echo $db->getQry();exit;
	    $resultado = $db->ejecutar();
	    
	    if(count($resultado)>0){
		$pdf->Text(120,$altoDetalle, "KITS");
	        //echo $antCliente;exit;
		foreach($resultado AS $kits){

		    
		    //echo $tipo;
			$pdf->Text(80,$alto, $kits['producto'] . " - CANT: " . $kits['cantidad']);;
			//$pdf->Text(180,$alto, $kits['cantidad']);;
			
			$alto = $alto + 5;
		}
	    }

    
}
$pdf->Output();    
?>
