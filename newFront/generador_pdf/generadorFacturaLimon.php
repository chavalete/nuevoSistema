<?php                                                                                                                                                                            
//ALMACENAMIENTO                                                                                                                                                                 
require_once $_SERVER['DOCUMENT_ROOT'] .'/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';                                                                        
require_once 'php-barcode.php';                                                                                                                                                      
ob_end_clean();                                                                                                                                                                  
require_once $_SERVER['DOCUMENT_ROOT'] .'/newFront/libreria/fpdf/fpdf.php';                                                                                                  
require_once $_SERVER['DOCUMENT_ROOT'] .'/newFront/libreria/includes/FuncionesComunes.php'; 
require_once('barcode.inc.php'); 



$objFuncionesComunes =  new FuncionesComunes();
$db = NEW frenteAlmacenamiento();
$db->addSelect('dc.cliente_nombre');
$db->addSelect('dc.cliente_calle'); 
$db->addSelect('dc.cliente_altura');
$db->addSelect('dc.cliente_piso');  
$db->addSelect('dc.cliente_depto'); 
$db->addSelect('dc.cliente_cuit');         
$db->addSelect('dc.cliente_direccion');  
$db->addSelect('dc.horario_entrega');
$db->addSelect('dloc.localidad_nombre AS pedido_loca_nombre');                                                       
$db->addSelect('dprov.provincia_nombre pedido_prov_nombre');                                                         
$db->addSelect('df.factura_fecha AS factura_fecha_qr');
$db->addSelect('to_char(df.factura_fecha::date,\'DD/MM/YYYY\') AS factura_fecha');
$db->addSelect('dloc.localidad_nombre AS cliente_loca_nombre');                                                     
$db->addSelect('dprov.provincia_nombre AS cliente_prov_nombre');
$db->addSelect('df.factura_id');
$db->addSelect('df.factura_nro');
$db->addSelect('df.factura_total_fact AS total_factura');
$db->addSelect('df.factura_total_fact - df.factura_total_iva AS total_neto');
$db->addSelect('df.factura_total_iva AS total_iva');
$db->addSelect('dp.producto_nombre || \' \' || dp.producto_presentacion || \' - \' || dm.marca_nombre AS producto_nombre'); 
$db->addSelect('def.importe_unitario');
$db->addSelect('def.importe_iva');
$db->addSelect('sum(def.importe_detalle - importe_iva) AS detalle_neto');
$db->addSelect('sum(def.importe_detalle) AS importe_detalle');
$db->addSelect('df.de_factura_id');
$db->addSelect('30709384291 AS cuit_ortodontia');
$db->addSelect('sum(def.cantidad) AS cantidad');
$db->addSelect('def.producto_id');
$db->addSelect('df.descuento');
$db->addSelect('df2.factura_nro AS factura_origen');
$db->addSelect('df.observaciones');
$db->addSelect('dmov.obs');
$db->addSelect('dv.vendedor_nombre');
$db->addSelect('categoria_nombre');
$db->addFrom('datos_facturas df');
$db->addFrom('INNER JOIN detalle_facturas def USING(factura_id)');                                               
$db->addFrom('INNER JOIN datos_clientes dc ON (df.cliente_id = dc.cliente_id)');
$db->addFrom('INNER JOIN datos_movimientos dmov USING(id_movimiento)');
$db->addFrom('INNER JOIN datos_categorias USING(categoria_id)');
$db->addFrom('INNER JOIN datos_productos dp USING(producto_id)');
$db->addFrom('INNER JOIN datos_marcas dm USING(marca_id)');
$db->addFrom('INNER JOIN datos_vendedores dv ON dc.vendedor_id = dv.vendedor_id');
$db->addFrom('LEFT JOIN datos_localidades dloc ON (dloc.localidad_id=dc.cliente_loca_id)');                             
$db->addFrom('LEFT JOIN datos_provincias dprov ON (dloc.localidad_prov_id = dprov.provincia_id)');
$db->addFrom('LEFT JOIN datos_facturas df2 ON df.de_factura_id = df2.factura_id');
$db->addGroup('dc.cliente_nombre,dc.cliente_calle,dc.cliente_altura,dc.cliente_piso, dc.cliente_depto,dc.cliente_cuit,CASE WHEN dc.cliente_resp_insc THEN \'RESPONSABLE INSCRIPTO\' ELSE \'Exento\' END, dloc.localidad_nombre,dprov.provincia_nombre,df.factura_id,df.factura_nro,to_char(df.factura_fecha::date,\'DD/MM/YYYY\'),df.factura_total_fact,df.factura_total_fact,df.factura_total_iva,dp.producto_nombre || \' \' || dp.producto_presentacion || \' - \' || dm.marca_nombre,def.importe_unitario,def.producto_id,def.importe_iva,df.de_factura_id, df2.factura_nro,df.descuento,dp,producto_nombre,df.factura_fecha, dc.cliente_direccion,df.observaciones,dv.vendedor_nombre,categoria_nombre,dc.horario_entrega,dmov.obs');                   
$db->addOrderby('dp.producto_nombre');                   

if($_GET['crIdMovimiento']!=null){
    $db->addWhere('df.id_movimiento = \'' . $_GET['crIdMovimiento']. '\'');
}else{
    //echo "No llego ID de factura";exit;
	$db->addWhere('df.factura_id = \'' . $_GET['crIdFactura']. '\'');
}
$db->generarSelect();                                                                                                
//echo $db->getQry();exit;
$arrCabecera =  $db->ejecutar();
//var_dump($p); exit;
//echo $db->getQry();exit;                                                                                           
 if(count($arrCabecera) ==0){                                                                                        
    echo "<h2>Sin registros para {$_GET['crIdPedido']} ";                                                            
    exit;                                                                                                            
}                                                                                                                    
//LO MARCAMOS COMO IMPRESO
$qry="UPDATE datos_movimientos SET impreso=true WHERE id_movimiento = $_GET[crIdMovimiento];";
$db->setQry($qry);
$db->ejecutar();


$pdf = new FPDF ('P','mm',array(210,297));
$pdf->AddPage();                          

//CABECERA
$fontSize = 10;
$marge    = 10;   // between barcode and hri in pixel
$x        = 52;  // barcode center                  
$y        = 135;  // barcode center                   
$height   = 12;   // barcode height in 1D ; module size in 2D
$width    = 0.3;    // barcode height in 1D ; not use in 2D  
$angle    = 0;   // rotation in degrees                      
$black    = '000000'; // color in hexa                       
$type     = 'code128';                                                                          

//DATOS PROPIOS
$pdf->Image('limon.jpeg', 5, 5, 35, 15, 'jpg');
$pdf->SetFont('Arial','B',10);
$pdf->Text(5,24, utf8_decode("Razón Social: "));
$pdf->Text(5,28, utf8_decode("Domicilio: ")); 
$pdf->SetFont('Arial','',10);
$pdf->Text(30,24, utf8_decode("Autoservicio Mayorista Limón"));
$pdf->Text(23,28, utf8_decode("Juan Manuel de Rosas 2667 - "));
$pdf->Text(70,28, utf8_decode("San Justo, Buenos Aires "));


//BORDER QUE LIMITA
//$pdf->Line(98.5, 5 ,98.5, 35);

$pdf->SetFont('Arial','B',24);
//$pdf->Text(115,17, utf8_decode("FACTURA"));

$pdf->Text(115,17, utf8_decode("REMITO"));


$pdf->SetFont('Arial','B',10);
$pdf->Text(115,23, utf8_decode("Comp.Nro: " . $arrCabecera[0]['factura_nro']));


//FECHA
$pdf->Text(115,28, utf8_decode("Fecha de Emisión: ") . $arrCabecera[0]['factura_fecha']) ;
//DATOS FISCALES

//BORDER SUPERIOR
$pdf->Line(3, 5 ,205 , 5);
//BORDER INFERIOR
$pdf->Line(3, 30 ,205 , 30);
//BORDER IZQUIERDO
$pdf->Line(3, 5 ,3, 142);
//BORDER DERECHO
$pdf->Line(205, 5 ,205, 142);
//FIN DATOS PROPIOS Y CUADRO

//DATOS DEL CLIENTE
//NOMBRE
$pdf->SetFont('Arial','B',10);
$pdf->Text(4,35, utf8_decode("Apellido y Nombre/Razón Social: "));
$pdf->Text(4,40, utf8_decode("Domicilio Comercial: "));
$pdf->Text(150,35, utf8_decode("Categoria: "));
$pdf->Text(150,40, utf8_decode("Vendedor: "));
$pdf->Text(4,45, utf8_decode("Horario: "));
$pdf->SetFont('Arial','',10);

$pdf->Text(62,35, utf8_decode($arrCabecera[0]['cliente_nombre'])) ;
$pdf->SetFont('Arial','',9);
$pdf->Text(39,40, utf8_decode($arrCabecera[0]['cliente_direccion'] . " - " . $arrCabecera[0]['cliente_loca_nombre'])) ;
$pdf->SetFont('Arial','',10);
$pdf->Text(168,35, utf8_decode($arrCabecera[0]['categoria_nombre'])) ;
$pdf->Text(168,40, utf8_decode($arrCabecera[0]['vendedor_nombre'])) ;
$pdf->Text(20,45, utf8_decode($arrCabecera[0]['horario_entrega'])) ;

if($arrCabecera[0]['de_factura_id']>0){
    $pdf->Text(22,65, $arrCabecera[0][factura_origen]) ;
}else{
    $remitos=substr($remitos, 0, -2);
    $pdf->Text(20,65, $remitos) ;
}
$pdf->Line(3, 46 ,205 , 46);
$pedidos=substr($pedidos, 0, -1);

//FIN CABECERA
$pdf->SetFont('Arial','B',9);
$pdf->Text(5,51, "Item" );
$pdf->Text(20,51, "Producto" );
$pdf->Text(120,51, "Cantidad" );
$pdf->Text(145,51, "Precio Unit" );
$pdf->Text(178,51, "Subtotal" );


$alto = 53;
$i=1;
$bultos=0;
foreach ($arrCabecera as $detalle){
    
        $bultos = $bultos + $detalle['cantidad'];
if($par==false){
            $pdf->SetFont('Arial','',8);
            $pdf->SetXY(4,$alto);          // Primero establece Donde estará la esquina superior izquierda donde estará tu celda
            //$pdf->SetTextColor(255,255,255);  // Establece el color del texto (en este caso es blanco)
            $pdf->SetFillColor(10,255,255); // establece el color del fondo de la celda (en este caso es AZUL
            $pdf->Cell(15, 3, $i, 0, 0, '', True); // en orden lo que informan estos parametros es: 
            //$pdf->Text(6,$alto, $i) ;
            $pdf->Cell(96, 3, utf8_decode($detalle['producto_nombre']), 0, 0, '', True); // en orden lo que informan estos parametros es: 
            //$pdf->Text(15,$alto, utf8_decode($detalle['producto_nombre'])) ;
            $pdf->Line(115, 46 ,115, 128);
            $pdf->Cell(25, 3, utf8_decode($detalle['cantidad']), 0, 0, 'C', True); // en orden lo que informan estos parametros es: 
            //$pdf->Text(125,$alto, $detalle['cantidad'] );
            $pdf->Line(140, 46 ,140, 128);
            $pdf->Cell(30, 3, utf8_decode($detalle['importe_unitario']), 0, 0, 'C', True); // en orden lo que informan estos parametros es: 
            //$pdf->Text(150,$alto, $detalle['importe_unitario']);
            $pdf->Line(170, 46 ,170, 128);
            $pdf->Cell(35, 3, utf8_decode($detalle['importe_detalle']), 0, 0, 'C', True); // en orden lo que informan estos parametros es: 
            //$pdf->Text(180,$alto, $detalle['importe_detalle']);
            $par=true;
        }else{
            $pdf->SetFont('Arial','',8.1);

            $pdf->SetXY(4,$alto);          // Primero establece Donde estará la esquina superior izquierda donde estará tu celda
            //$pdf->SetTextColor(255,255,255);  // Establece el color del texto (en este caso es blanco)
            $pdf->SetFillColor(255,255,255); // establece el color del fondo de la celda (en este caso es AZUL
            $pdf->Cell(15, 3, $i, 0, 0, '', True); // en orden lo que informan estos parametros es: 
            //$pdf->Text(6,$alto, $i) ;
            $pdf->Cell(96, 3, utf8_decode($detalle['producto_nombre']), 0, 0, '', True); // en orden lo que informan estos parametros es: 
            //$pdf->Text(15,$alto, utf8_decode($detalle['producto_nombre'])) ;
            $pdf->Cell(25, 3, utf8_decode($detalle['cantidad']), 0, 0, 'C', True); // en orden lo que informan estos parametros es: 
            //$pdf->Text(125,$alto, $detalle['cantidad'] );
            $pdf->Cell(30, 3, utf8_decode($detalle['importe_unitario']), 0, 0, 'C', True); // en orden lo que informan estos parametros es: 
            //$pdf->Text(150,$alto, $detalle['importe_unitario']);
            $pdf->Cell(35, 3, utf8_decode($detalle['importe_detalle']), 0, 0, 'C', True); // en orden lo que informan estos parametros es: 
            //$pdf->Text(180,$alto, $detalle['importe_detalle']);
            $par=false;
        }        
        $subtotal=$subtotal + $detalle['importe_detalle'];
        //$alto = $alto + 3;
        $pdf->SetFont('Arial','',7);
        $alto = $alto + 4;
        $i++;
        
 
}
$pdf->SetFont('Arial','B',10);
//ar_dump($arrCabecera);exit;
$pdf->Text(5,125, utf8_decode("Bultos: " . $bultos));
if($arrCabecera[0]['obs']!=null){
$pdf->SetFont('Arial','B',8);
    $pdf->Text(30,125, utf8_decode("Obs: " . $arrCabecera[0]['obs']));
}
$pdf->SetFont('Arial','B',10);
$pdf->Text(164,135, utf8_decode("Subtotal: $ " . $arrCabecera[0]['total_factura']));
$pdf->Text(155,140, utf8_decode("Importe Total:  $ " . $arrCabecera[0]['total_factura']));

//BORDER SUPERIOR
$pdf->Line(3, 128 ,205 , 128);
//BORDER INFERIOR
$pdf->Line(3, 142 ,205 , 142);


$data = Barcode::fpdf($pdf, $black, $x, $y, $angle, $type, array('code'=>$arrCabecera[0]['factura_nro']), $width, $height);  

//copia si ya es un asco lo duplicado pero es lo q hay
//DATOS PROPIOS

$pdf->Image('limon.jpeg', 5, 146, 35, 15, 'jpg');
$pdf->SetFont('Arial','B',10);
$pdf->Text(5,165, utf8_decode("Razón Social: "));
$pdf->Text(5,169, utf8_decode("Domicilio: ")); 
$pdf->SetFont('Arial','',10);
$pdf->Text(30,165, utf8_decode("Autoservicio Mayorista Limón"));
$pdf->Text(23,169, utf8_decode("Juan Manuel de Rosas 2667 - "));
$pdf->Text(70,169, utf8_decode("San Justo, Buenos Aires "));


//BORDER QUE LIMITA
//$pdf->Line(98.5, 5 ,98.5, 35);

$pdf->SetFont('Arial','B',24);
//$pdf->Text(115,17, utf8_decode("FACTURA"));

$pdf->Text(115,155, utf8_decode("REMITO"));


$pdf->SetFont('Arial','B',10);
$pdf->Text(115,164, utf8_decode("Comp.Nro: " . $arrCabecera[0]['factura_nro']));


//FECHA
$pdf->Text(115,169, utf8_decode("Fecha de Emisión: ") . $arrCabecera[0]['factura_fecha']) ;
//DATOS FISCALES

//BORDER SUPERIOR
$pdf->Line(3, 145 ,205 , 145);
//BORDER INFERIOR
$pdf->Line(3, 170 ,205 , 170);

//BORDER IZQUIERDO
$pdf->Line(3, 145 ,3, 290);
//BORDER DERECHO
$pdf->Line(205, 145 ,205, 290);
//FIN DATOS PROPIOS Y CUADRO

//DATOS DEL CLIENTE
//NOMBRE

$pdf->SetFont('Arial','B',10);
$pdf->Text(4,174, utf8_decode("Apellido y Nombre/Razón Social: "));
$pdf->Text(4,178, utf8_decode("Domicilio Comercial: "));
$pdf->Text(150,174, utf8_decode("Categoria: "));
$pdf->Text(150,178, utf8_decode("Vendedor: "));
$pdf->Text(4,182, utf8_decode("Horario: "));
$pdf->SetFont('Arial','',10);

$pdf->Text(62,174, utf8_decode($arrCabecera[0]['cliente_nombre'])) ;
$pdf->SetFont('Arial','',9);
$pdf->Text(39,178, utf8_decode($arrCabecera[0]['cliente_direccion'] . " - " . $arrCabecera[0]['cliente_loca_nombre'])) ;
$pdf->SetFont('Arial','',10);
$pdf->Text(168,174, utf8_decode($arrCabecera[0]['categoria_nombre'])) ;
$pdf->Text(168,178, utf8_decode($arrCabecera[0]['vendedor_nombre'])) ;
$pdf->Text(20,182, utf8_decode($arrCabecera[0]['horario_entrega'])) ;

$pdf->Line(3, 183 ,205 , 183);
$pedidos=substr($pedidos, 0, -1);

//FIN CABECERA
$pdf->SetFont('Arial','B',9);
$pdf->Text(5,188, "Item" );
$pdf->Text(20,188, "Producto" );
$pdf->Text(120,188, "Cantidad" );
$pdf->Text(145,188, "Precio Unit" );
$pdf->Text(178,188, "Subtotal" );


$alto = 190;
$i=1;
$bultos=0;
$par=false;
foreach ($arrCabecera as $detalle){
    
        $bultos = $bultos + $detalle['cantidad'];
        if($par==false){
            $pdf->SetFont('Arial','',8);
            $pdf->SetXY(4,$alto);          // Primero establece Donde estará la esquina superior izquierda donde estará tu celda
            //$pdf->SetTextColor(255,255,255);  // Establece el color del texto (en este caso es blanco)
            $pdf->SetFillColor(10,255,255); // establece el color del fondo de la celda (en este caso es AZUL
            $pdf->Cell(15, 3, $i, 0, 0, '', True); // en orden lo que informan estos parametros es: 
            //$pdf->Text(6,$alto, $i) ;
            $pdf->Cell(96, 3, utf8_decode($detalle['producto_nombre']), 0, 0, '', True); // en orden lo que informan estos parametros es: 
            //$pdf->Text(15,$alto, utf8_decode($detalle['producto_nombre'])) ;
            $pdf->Line(115, 183 ,115, 270);
            $pdf->Cell(25, 3, utf8_decode($detalle['cantidad']), 0, 0, 'C', True); // en orden lo que informan estos parametros es: 
            //$pdf->Text(125,$alto, $detalle['cantidad'] );
            $pdf->Line(140, 183 ,140, 270);
            $pdf->Cell(30, 3, utf8_decode($detalle['importe_unitario']), 0, 0, 'C', True); // en orden lo que informan estos parametros es: 
            //$pdf->Text(150,$alto, $detalle['importe_unitario']);
            $pdf->Line(170, 183 ,170, 270);
            $pdf->Cell(35, 3, utf8_decode($detalle['importe_detalle']), 0, 0, 'C', True); // en orden lo que informan estos parametros es: 
            //$pdf->Text(180,$alto, $detalle['importe_detalle']);
            $par=true;
        }else{
            $pdf->SetFont('Arial','',8.1);

            $pdf->SetXY(4,$alto);          // Primero establece Donde estará la esquina superior izquierda donde estará tu celda
            //$pdf->SetTextColor(255,255,255);  // Establece el color del texto (en este caso es blanco)
            $pdf->SetFillColor(255,255,255); // establece el color del fondo de la celda (en este caso es AZUL
            $pdf->Cell(15, 3, $i, 0, 0, '', True); // en orden lo que informan estos parametros es: 
            //$pdf->Text(6,$alto, $i) ;
            $pdf->Cell(96, 3, utf8_decode($detalle['producto_nombre']), 0, 0, '', True); // en orden lo que informan estos parametros es: 
            //$pdf->Text(15,$alto, utf8_decode($detalle['producto_nombre'])) ;
            $pdf->Line(115, 183 ,115, 270);
            $pdf->Cell(25, 3, utf8_decode($detalle['cantidad']), 0, 0, 'C', True); // en orden lo que informan estos parametros es: 
            //$pdf->Text(125,$alto, $detalle['cantidad'] );
            $pdf->Line(140, 183 ,140, 270);
            $pdf->Cell(30, 3, utf8_decode($detalle['importe_unitario']), 0, 0, 'C', True); // en orden lo que informan estos parametros es: 
            //$pdf->Text(150,$alto, $detalle['importe_unitario']);
            $pdf->Line(170, 183 ,170, 270);
            $pdf->Cell(35, 3, utf8_decode($detalle['importe_detalle']), 0, 0, 'C', True); // en orden lo que informan estos parametros es: 
            //$pdf->Text(180,$alto, $detalle['importe_detalle']);
            $par=false;
        }
        
        $subtotal=$subtotal + $detalle['importe_detalle'];
        //$alto = $alto + 3;
        $pdf->SetFont('Arial','',7);
        $alto = $alto + 4;
        $i++;
        
 
}

$pdf->SetFont('Arial','B',10);

$pdf->Text(5,265, utf8_decode("Bultos: " . $bultos));
if($arrCabecera[0]['obs']!=null){
    $pdf->SetFont('Arial','B',8);
    $pdf->Text(30,265, utf8_decode("obs: " . $arrCabecera[0]['obs']));
}

$pdf->Text(164,280, utf8_decode("Subtotal: $ " . $arrCabecera[0]['total_factura']));
$pdf->Text(155,285, utf8_decode("Importe Total:  $ " . $arrCabecera[0]['total_factura']));

//BORDER SUPERIOR

$pdf->Line(3, 270 ,205 , 270);
//BORDER INFERIOR
$pdf->Line(3, 290 ,205 , 290);

//CABECERA
$fontSize = 10;
$marge    = 10;   // between barcode and hri in pixel
$x        = 52;  // barcode center                  
$y        = 280;  // barcode center                   
$height   = 12;   // barcode height in 1D ; module size in 2D
$width    = 0.3;    // barcode height in 1D ; not use in 2D  
$angle    = 0;   // rotation in degrees                      
$black    = '000000'; // color in hexa                       
$type     = 'code128';                                                                          



$data = Barcode::fpdf($pdf, $black, $x, $y, $angle, $type, array('code'=>$arrCabecera[0]['factura_nro']), $width, $height);  





$pdf->Output();

    

