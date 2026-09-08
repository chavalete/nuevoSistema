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
$db->addSelect('df.tipo_entrega');
$db->addFrom('datos_facturas df');
$db->addFrom('INNER JOIN detalle_facturas def USING(factura_id)');                                               
$db->addFrom('INNER JOIN datos_clientes dc ON (df.cliente_id = dc.cliente_id)');
$db->addFrom('INNER JOIN datos_productos dp USING(producto_id)');
$db->addFrom('INNER JOIN datos_marcas dm USING(marca_id)');
$db->addFrom('LEFT JOIN datos_localidades dloc ON (dloc.localidad_id=dc.cliente_loca_id)');                             
$db->addFrom('LEFT JOIN datos_provincias dprov ON (dloc.localidad_prov_id = dprov.provincia_id)');
$db->addFrom('LEFT JOIN datos_facturas df2 ON df.de_factura_id = df2.factura_id');
$db->addGroup('dc.cliente_nombre,dc.cliente_calle,dc.cliente_altura,dc.cliente_piso, dc.cliente_depto,dc.cliente_cuit,CASE WHEN dc.cliente_resp_insc THEN \'RESPONSABLE INSCRIPTO\' ELSE \'Exento\' END, dloc.localidad_nombre,dprov.provincia_nombre,df.factura_id,df.factura_nro,to_char(df.factura_fecha::date,\'DD/MM/YYYY\'),df.factura_total_fact,df.factura_total_fact,df.factura_total_iva,dp.producto_nombre || \' \' || dp.producto_presentacion || \' - \' || dm.marca_nombre,def.importe_unitario,def.producto_id,def.importe_iva,df.de_factura_id, df2.factura_nro,df.descuento,dp,producto_nombre,df.factura_fecha, dc.cliente_direccion,df.observaciones, df.tipo_entrega');                  
$db->addOrderby('dp.producto_nombre');                   

if($_GET['crIdMovimiento']!=null){
    $db->addWhere('df.id_movimiento = \'' . $_GET['crIdMovimiento']. '\'');
}else{
    echo "No llego ID de factura";exit;
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
//$pdf->AddPage();                          

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


if($arrCabecera[0]['tipo_entrega']=='Envio'){
    $topeCopias=2;
}else{
    $topeCopias=1;
}
$copias=0;
while($copias<$topeCopias){
$pdf->AddPage();                          
//DATOS PROPIOS
$pdf->Image('limoneta2.png', 5, 5, 65, 20, 'png');
$pdf->SetFont('Arial','B',10);
$pdf->Text(5,30, utf8_decode("Razón Social: "));
$pdf->Text(5,34, utf8_decode("Domicilio Comercial: "));
$pdf->Text(5,42, utf8_decode("Teléfono: "));
$pdf->SetFont('Arial','',10);
$pdf->Text(30,30, utf8_decode("Limoneta Autoservicio Mayorista"));
$pdf->Text(40,34, utf8_decode(" Bufano 4206 - "));
$pdf->Text(5,38, utf8_decode("San Justo, Buenos Aires "));
$pdf->Text(23,42, utf8_decode("11-3012-7978"));




//FACTURA LETRA
//BORDER INFERIOR

//BORDER IZQUIERDO

//BORDER DERECHO

//BORDER QUE LIMITA
$pdf->Line(98.5, 5 ,98.5, 45);

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
$pdf->Line(3, 45 ,205 , 45);
//BORDER IZQUIERDO
$pdf->Line(3, 5 ,3, 290);
//BORDER DERECHO
$pdf->Line(205, 5 ,205, 290);
//FIN DATOS PROPIOS Y CUADRO

//DATOS DEL CLIENTE
//NOMBRE
$pdf->SetFont('Arial','B',10);
$pdf->Text(5,50, utf8_decode("Apellido y Nombre/Razón Social: "));
$pdf->Text(5,60, utf8_decode("Domicilio Comercial: "));


$pdf->SetFont('Arial','',10);

$pdf->Text(62,50, utf8_decode($arrCabecera[0]['cliente_nombre'])) ;
$pdf->Text(43,60, utf8_decode($arrCabecera[0]['cliente_direccion'] . " - " . $arrCabecera[0]['cliente_loca_nombre'] . " , " . $arrCabecera[0]['cliente_prov_nombre'])) ;
if($arrCabecera[0]['de_factura_id']>0){
    $pdf->Text(22,65, $arrCabecera[0][factura_origen]) ;
}else{
    $remitos=substr($remitos, 0, -2);
    $pdf->Text(20,65, $remitos) ;
}
$pdf->Line(3, 70 ,205 , 70);
$pedidos=substr($pedidos, 0, -1);

//FIN CABECERA
$pdf->SetFont('Arial','B',9);
$pdf->Text(5,75, "Item" );
$pdf->Text(20,75, "Producto" );
$pdf->Text(120,75, "Cantidad" );
$pdf->Text(145,75, "Precio Unit" );
$pdf->Text(178,75, "Subtotal" );


$alto = 80;
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
            $pdf->Line(115, 70 ,115, 270);
            $pdf->Cell(25, 3, utf8_decode($detalle['cantidad']), 0, 0, 'C', True); // en orden lo que informan estos parametros es: 
            //$pdf->Text(125,$alto, $detalle['cantidad'] );
            $pdf->Line(140, 70 ,140, 270);
            $pdf->Cell(30, 3, utf8_decode($detalle['importe_unitario']), 0, 0, 'C', True); // en orden lo que informan estos parametros es: 
            //$pdf->Text(150,$alto, $detalle['importe_unitario']);
            $pdf->Line(170, 70 ,170, 270);
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
        if($i==44 || $i==84 || $i==124){
            $pdf->SetFont('Arial','B',10);
            $pdf->Text(155,270, utf8_decode("Subtotal: ". $objFuncionesComunes->formatoMoneda($subtotal)));
            //BORDER SUPERIOR
            $pdf->Line(3, 255 ,205 , 255);
            //BORDER INFERIOR
            $pdf->Line(3, 290 ,205 , 290);

            
            $data = Barcode::fpdf($pdf, $black, $x, $y, $angle, $type, array('code'=>$arrCabecera[0]['factura_nro']), $width, $height);  
            //parte legible
            $pdf->SetFont('Arial','',8);    
            $pdf->Text(18,290, $codigoBarras);

            
            $pdf->AddPage();
            
            //DATOS PROPIOS
            $pdf->Image('limon.jpeg', 5, 5, 65, 25, 'jpg');
            $pdf->SetFont('Arial','B',10);
            $pdf->Text(5,34, utf8_decode("Razón Social: "));
            $pdf->Text(5,38, utf8_decode("Domicilio Comercial: ")); 
            $pdf->SetFont('Arial','',10);
            $pdf->Text(30,34, utf8_decode("Limon S.R.L"));
            $pdf->Text(40,38, utf8_decode("Juan Manuel de Rosas 2667 - "));
            $pdf->Text(5,42, utf8_decode("San Justo, Buenos Aires "));

//BORDER QUE LIMITA
$pdf->Line(98.5, 5 ,98.5, 45);

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
$pdf->Line(3, 45 ,205 , 45);
//BORDER IZQUIERDO
$pdf->Line(3, 5 ,3, 290);
//BORDER DERECHO
$pdf->Line(205, 5 ,205, 290);
//FIN DATOS PROPIOS Y CUADRO


//BORDER SUPERIOR
$pdf->Line(3, 5 ,205 , 5);
//BORDER INFERIOR
$pdf->Line(3, 45 ,205 , 45);
//BORDER IZQUIERDO
$pdf->Line(3, 5 ,3, 290);
//BORDER DERECHO
$pdf->Line(205, 5 ,205, 290);
//FIN DATOS PROPIOS Y CUADRO

//DATOS DEL CLIENTE
//NOMBRE
$pdf->SetFont('Arial','B',10);
$pdf->Text(5,50, utf8_decode("Apellido y Nombre/Razón Social: "));
$pdf->Text(5,55, utf8_decode("CUIT: "));
$pdf->Text(50,55, utf8_decode("Condición de Venta: "));
$pdf->Text(140,55, utf8_decode("IVA: "));
$pdf->Text(5,60, utf8_decode("Domicilio Comercial: "));

if($arrCabecera[0]['de_factura_id'] >0){
    $pdf->Text(5,65, utf8_decode("Factura/s: "));
}else{
    $pdf->Text(5,65, utf8_decode("Remito: "));
}
$pdf->SetFont('Arial','',10);

$pdf->Text(62,50, utf8_decode($arrCabecera[0]['cliente_nombre'])) ;
$pdf->Text(20,55, str_replace("-","",$arrCabecera[0]['cliente_cuit'])) ;
$pdf->Text(85,55, utf8_decode("Cuenta Corriente")) ;
$pdf->Text(147,55, utf8_decode($arrCabecera[0]['condicion_iva'])) ;
$pdf->Text(43,60, utf8_decode($arrCabecera[0]['cliente_calle'] . " " . $arrCabecera[0]['cliente_altura'] . " - " . $arrCabecera[0]['cliente_loca_nombre'] . " , " . $arrCabecera[0]['cliente_prov_nombre'])) ;
$pdf->Text(20,65, $remitos) ;
$pdf->Line(3, 70 ,205 , 70);



//FIN CABECERA
$pdf->SetFont('Arial','B',9);
$pdf->Text(160,78, utf8_decode("Transporte: $ " . $objFuncionesComunes->formatoMoneda($subtotal)));
if($arrCabecera[0]['factura_letra']=='A'){
    $pdf->Text(5,85, "Item" );
    $pdf->Text(20,85, "Producto" );
    $pdf->Text(115,85, "Cantidad" );
    $pdf->Text(135,85, "Precio Unit" );
    $pdf->Text(155,85, "Subtotal" );
    $pdf->Text(170,85, "IVA." );
    $pdf->Text(180,85, "Subtotal c/IVA." );
}else{
    $pdf->Text(5,85, "Item" );
    $pdf->Text(20,85, "Producto" );
    $pdf->Text(120,85, "Cantidad" );
    $pdf->Text(145,85, "Precio Unit" );
    $pdf->Text(178,85, "Subtotal" );
}

$alto = 90;
        
        }
        //exit;
}
if($arrCabecera[0]['condicion_iva_id']==4){
    $pdf->SetFont('Arial','B',8);
    $pdf->Text(4,250, utf8_decode("El crédito fiscal discriminado en el presente comprobante, sólo podrá ser computado a efectos del Régimen de Sostenimiento e Inclusión Fiscal"));
    $pdf->Text(4,253, utf8_decode("para Pequeños Contribuyentes de la Ley Nº 27.618"));
}
$pdf->SetFont('Arial','B',10);

$pdf->Text(10,265, utf8_decode("Bultos: " . $bultos));
if($arrCabecera[0]['observaciones']!=null){
    $pdf->Text(30,265, utf8_decode("Observaciones: " . $arrCabecera[0]['observaciones']));
}

$pdf->SetFont('Arial','B',12);
$pdf->Text(155,275, utf8_decode("Subtotal: " . $objFuncionesComunes->formatoMoneda($arrCabecera[0]['total_factura'])));
$pdf->Text(145,280, utf8_decode("Importe Total: " . $objFuncionesComunes->formatoMoneda($arrCabecera[0]['total_factura'])));

//BORDER SUPERIOR
$pdf->Line(3, 270 ,205 , 270);
//BORDER INFERIOR
$pdf->Line(3, 290 ,205 , 290);
/*
$codigoBarras=$arrCabecera[0]['cuit_ortodontia'] . str_pad($arrCabecera[0]['factura_tipo'], 3, "0", STR_PAD_LEFT) . str_pad($arrCabecera[0]['factura_puesto_venta'], 5, "0", STR_PAD_LEFT) . $arrCabecera[0]['factura_cae'] . str_replace("-","",$arrCabecera[0]['vencimiento_cae']);

$objFUncionesComunes = NEW FuncionesComunes();
$codigoBarras.=$objFUncionesComunes->calcularDigitoVerificadorCodigoBarra($codigoBarras);
//echo $codigoBarras;exit;
$data = Barcode::fpdf($pdf, $black, $x, $y, $angle, $type, array('code'=>$codigoBarras), $width, $height);  
//Barcode::fpdf('codigos/'.$codigoBarras.'.png', $codigoBarras, 20, 'horizontal', 'code128', true);
//new barCodeGenrator($code_number,0,'hello.gif'); 
//new barCodeGenrator("$codigoBarras",0,'hello.gif', 600, 150, true);		
*/
$data = Barcode::fpdf($pdf, $black, $x, $y, $angle, $type, array('code'=>$arrCabecera[0]['factura_nro']), $width, $height);  
//parte legible
$pdf->SetFont('Arial','',8);    
$pdf->Text(18,290, $codigoBarras);
/*
$pdf->SetFont('Arial','B',10);    
/*
if($i>24 && $i<48){
    $pdf->Text(92,253, utf8_decode("Pag 2/2"));
}
if($i>48 && $i<69){
    $pdf->Text(92,253, utf8_decode("Pag 3/3"));
}
if($i>=69){
        $pdf->Text(92,253, utf8_decode("Pag 4/4"));
}*/
//$pdf->Line(98.5, 255 ,98.5, 290);
//$pdf->Line(98, 273 ,205 , 273);
$copias++;
}


$pdf->Output();

    


    
