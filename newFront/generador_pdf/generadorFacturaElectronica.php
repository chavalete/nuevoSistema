<?php                                                                                                                                                                            
//ALMACENAMIENTO                                                                                                                                                                 
require_once $_SERVER['DOCUMENT_ROOT'] .'/trazabilidadCorvision/libreria/almacenamiento/FrenteAlmacenamiento.php';                                                                        
require_once 'php-barcode.php';                                                                                                                                                      
require_once 'demoQR.php';
ob_end_clean();                                                                                                                                                                  
require_once $_SERVER['DOCUMENT_ROOT'] .'/trazabilidadCorvision/libreria/fpdf/fpdf.php';                                                                                                  
require_once $_SERVER['DOCUMENT_ROOT'] .'/trazabilidadCorvision/libreria/includes/FuncionesComunes.php'; 
require_once('barcode.inc.php'); 

//var_dump($arrNC);exit;
$objFuncionesComunes =  new FuncionesComunes();
$db = NEW frenteAlmacenamiento();
$db->addSelect('dc.cliente_nombre');
$db->addSelect('dc.cliente_calle'); 
$db->addSelect('dc.cliente_altura');
$db->addSelect('dc.cliente_piso');  
$db->addSelect('dc.cliente_depto'); 
$db->addSelect('dc.cliente_cuit');         
$db->addSelect('dc.condicion_iva_id');
$db->addSelect('descripcion AS condicion_iva');
$db->addSelect('dloc.localidad_nombre AS pedido_loca_nombre');                                                       
$db->addSelect('dprov.provincia_nombre pedido_prov_nombre');                                                         
$db->addSelect('df.factura_fecha AS factura_fecha_qr');
$db->addSelect('to_char(df.factura_fecha::date,\'DD/MM/YYYY\') AS factura_fecha');
$db->addSelect('to_char(df.vencimiento_cae::date,\'DD/MM/YYYY\') AS vencimiento_cae_cast');                   
$db->addSelect('dloc.localidad_nombre AS cliente_loca_nombre');                                                     
$db->addSelect('dprov.provincia_nombre AS cliente_prov_nombre');
$db->addSelect('df.factura_id');
$db->addSelect('df.factura_letra');
$db->addSelect('df.factura_nro');
$db->addSelect('df.factura_nro_afip');
$db->addSelect('df.factura_total_fact AS total_factura');
$db->addSelect('df.factura_total_fact - df.factura_total_iva AS total_neto');
$db->addSelect('df.factura_total_iva AS total_iva');
$db->addSelect('dp.producto_nombre || \' \' || dp.producto_presentacion || \' - \' || dm.marca_nombre AS producto_nombre'); 
$db->addSelect('def.producto_pventa');
$db->addSelect('def.importe_iva');
$db->addSelect("CASE WHEN def.producto_pventa <> def.importe_unitario THEN def.importe_unitario ELSE '0' END AS importe_bonificado ");
$db->addSelect('def.descuento AS desc_detalle');
$db->addSelect('sum(def.importe_detalle - importe_iva) AS detalle_neto');
$db->addSelect('sum(def.importe_detalle) AS importe_detalle');
$db->addSelect('df.factura_puesto_venta');
$db->addSelect('df.factura_tipo');
$db->addSelect('df.factura_cae');
$db->addSelect('df.vencimiento_cae');
$db->addSelect('df.de_factura_id');
//$db->addSelect('30709384291 AS cuit_ortodontia');
$db->addSelect("'20137401534' AS cuit_ortodontia");
$db->addSelect('sum(def.cantidad) AS cantidad');
$db->addSelect('def.producto_id');
$db->addSelect('df.descuento');
$db->addSelect('df2.factura_nro AS factura_origen');
$db->addSelect('def.remito_nro');
$db->addSelect('dp.producto_iva');
$db->addFrom('datos_facturas df');
$db->addFrom('INNER JOIN detalle_facturas def USING(factura_id)');                                               
$db->addFrom('INNER JOIN datos_clientes dc ON (df.cliente_id = dc.cliente_id)');
$db->addFrom('INNER JOIN datos_productos dp USING(producto_id)');
$db->addFrom('INNER JOIN datos_marcas dm USING(marca_id)');
$db->addFrom('INNER JOIN datos_condiciones_iva USING(condicion_iva_id)');
$db->addFrom('LEFT JOIN datos_localidades dloc ON (dloc.localidad_id=dc.cliente_loca_id)');                             
$db->addFrom('LEFT JOIN datos_provincias dprov ON (dloc.localidad_prov_id = dprov.provincia_id)');
$db->addFrom('LEFT JOIN datos_facturas df2 ON df.de_factura_id = df2.factura_id');
$db->addGroup('dc.cliente_nombre,dc.cliente_calle,dc.cliente_altura,dc.cliente_piso, dc.cliente_depto,dc.cliente_cuit,CASE WHEN dc.cliente_resp_insc THEN \'RESPONSABLE INSCRIPTO\' ELSE \'Exento\' END, dloc.localidad_nombre,dprov.provincia_nombre,df.factura_id,df.factura_letra,df.factura_nro,to_char(df.factura_fecha::date,\'DD/MM/YYYY\'),df.factura_total_fact,df.factura_total_fact,df.factura_total_iva,dp.producto_nombre || \' \' || dp.producto_presentacion || \' - \' || dm.marca_nombre,def.importe_unitario,df.factura_puesto_venta,def.producto_id,df.factura_cae, df.vencimiento_cae, df.factura_tipo,to_char(df.vencimiento_cae::date,\'DD/MM/YYYY\'), descripcion,def.importe_iva,df.de_factura_id, df2.factura_nro,df.descuento,dp,producto_nombre,def.remito_nro,df.factura_fecha,df.factura_nro_afip,dc.condicion_iva_id,dp.producto_iva,def.producto_pventa,CASE WHEN def.producto_pventa <> def.importe_unitario THEN def.importe_unitario ELSE \'0\' END, def.descuento');
$db->addOrderby('dp.producto_nombre ');

if($_GET['crPedidoId']!=null){
    $db->addWhere('df.pedido_id = \'' . $_GET['crPedidoId']. '\'');
}
if($_GET['crIdFactura']!=null){
    $db->addWhere('df.factura_id = \'' . $_GET['crIdFactura']. '\'');
}
if($_GET['crIdMovimiento']!=NULL){
    $db->addWhere('df.id_movimiento = \'' . $_GET['crIdMovimiento']. '\'');
}
$db->addWhere('df.factura_cancelada=false');
$db->generarSelect();                                                                                                
//echo $db->getQry();exit;
$arrCabecera =  $db->ejecutar();
//var_dump($p); exit;
//echo $db->getQry();exit;                                                                                           
 if(count($arrCabecera) ==0){                                                                                        
    echo "<h2>Sin registros para {$_GET['crIdPedido']} ";                                                            
    exit;                                                                                                            
}                                                                                                                    

if($_GET['crPedidoId']==null){
    $pdf = new FPDF ('P','mm',array(210,297));
}
$pdf->AddPage();

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

//DATOS PROPIOS
$pdf->Image('corvision.jpg', 8, 8, 65, 17.5, 'jpg');
$pdf->SetFont('Arial','B',10);
$pdf->Text(5,30, utf8_decode("Razón Social: "));
$pdf->Text(5,34, utf8_decode("Domicilio Comercial: ")); 
$pdf->SetFont('Arial','',10);
$pdf->Text(30,30, utf8_decode("Argañaras Lucas Matias"));
$pdf->Text(40,34, utf8_decode(" Av Gaona 901 -"));
$pdf->Text(5,38, utf8_decode("Paso del Rey, Buenos Aires"));
$pdf->SetFont('Arial','B',10);
$pdf->Text(5,42, utf8_decode("Condición frente al IVA: Responsable Inscripto")); 

//FACTURA LETRA
$pdf->SetFont('Arial','B',24);
$pdf->Text(95.5,13, strtoupper($arrCabecera[0]['factura_letra'])) ;
$pdf->SetFont('Arial','B',8);
$pdf->Text(92.7,18, "COD." . str_pad($arrCabecera[0]['factura_tipo'], 3, "0", STR_PAD_LEFT)) ;
//BORDER INFERIOR
$pdf->Line(92, 20 ,105 , 20);
//BORDER IZQUIERDO
$pdf->Line(105, 5 ,105, 20);
//BORDER DERECHO
$pdf->Line(92, 5 ,92, 20);
//BORDER QUE LIMITA
$pdf->Line(98.5, 20 ,98.5, 45);

$pdf->SetFont('Arial','B',24);
//$pdf->Text(115,17, utf8_decode("FACTURA"));
if($arrCabecera[0]['factura_tipo']==1 || $arrCabecera[0]['factura_tipo']==6){
        $pdf->Text(115,17, utf8_decode("FACTURA"));
}else{
        $pdf->Text(115,17, utf8_decode("NOTA DE CRÉDITO"));
}

$pdf->SetFont('Arial','B',10);
$pdf->Text(115,23, utf8_decode("Punto de Venta: " . str_pad($arrCabecera[0]['factura_puesto_venta'], 4, "0", STR_PAD_LEFT)));
$pdf->Text(155,23, utf8_decode("Comp.Nro: " . substr($arrCabecera[0]['factura_nro'], 6)));


//FECHA
$pdf->Text(115,28, utf8_decode("Fecha de Emisión: ") . $arrCabecera[0]['factura_fecha']) ;
//DATOS FISCALES
$pdf->Text(115,33, utf8_decode("CUIT: "));
$pdf->Text(115,37, utf8_decode("Ingresos Brutos: ")) ;
$pdf->Text(115,42, utf8_decode("Fecha de Inicio de Actividades: ")) ;
$pdf->SetFont('Arial','',10);
$pdf->Text(125,33, utf8_decode("20384567798"));
$pdf->Text(145,37, utf8_decode("Convenio Multilateral"));
$pdf->Text(170,42, utf8_decode("01/08/2017"));

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
    //echo "entra";exit;
    $pdf->Text(5,65, utf8_decode("Remito: "));
}
$pdf->SetFont('Arial','',10);

$pdf->Text(62,50, utf8_decode($arrCabecera[0]['cliente_nombre'])) ;
$pdf->Text(20,55, str_replace("-","",$arrCabecera[0]['cliente_cuit'])) ;
$pdf->Text(85,55, utf8_decode("Cuenta Corriente")) ;
$pdf->Text(147,55, utf8_decode($arrCabecera[0]['condicion_iva'])) ;
$pdf->Text(43,60, utf8_decode($arrCabecera[0]['cliente_calle'] . " " . $arrCabecera[0]['cliente_altura'] . " - " . $arrCabecera[0]['cliente_loca_nombre'] . " , " . $arrCabecera[0]['cliente_prov_nombre'])) ;

//TRAEMOS LOS REMITOS Y PEDIDOS
$db->addSelect('remito_nro, pedido_id');
$db->addFrom('datos_remitos');
$db->addWhere('factura_id = \'' . $arrCabecera[0][factura_id]. '\'');
$db->addOrderby('remito_nro');
$db->generarSelect();
$arrremitosPedidos =  $db->ejecutar();

foreach($arrremitosPedidos AS $datos){
    $remitos.=$datos['remito_nro'] . " / ";
    $pedidos.=$datos['pedido_id'] . ",";
}
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
if($arrCabecera[0]['factura_letra']=='A'){
    $pdf->Text(5,75, "Item" );
    $pdf->Text(20,75, "Producto" );
    $pdf->Text(95,75, "Cantidad" );
    $pdf->Text(115,75, "Precio Unit" );
    $pdf->Text(135,75, "% Bonf" );
    $pdf->Text(153,75, "Subtotal" );
    $pdf->Text(170,75, "IVA." );
    $pdf->Text(180,75, "Subtotal c/IVA." );
}else{
    $pdf->Text(5,75, "Item" );
    $pdf->Text(20,75, "Producto" );
    $pdf->Text(115,75, "Cantidad" );
    $pdf->Text(135,75, "Precio Unit" );
    $pdf->Text(157,75, "% Bonf" );
    $pdf->Text(178,75, "Subtotal" );
}

$alto = 80;
$i=1;

foreach ($arrCabecera as $detalle){
    
/*    
        //DETALLES                                                                                    
        $db->addSelect('dl.lote');
        //$db->addSelect('to_char(dl.lote_vencimiento, \'DD/MM/YYYY\') AS lote_vencimiento');           

        $db->addFrom('pedidos_lotes pl');
        $db->addFrom('INNER JOIN datos_lotes dl USING(lote_id)');        
        $db->addWhere('pl.producto_id = \'' . $detalle['producto_id']  . '\'');
        $db->addWhere("pl.pedido_id IN ($pedidos)");
        $db->addOrderby('lote');
        $db->generarSelect(); 
*/
        $db->setQry("SELECT dl.lote || ' Venc: ' || to_char(lote_vencimiento,'DD-MM-YY') AS lote FROM datos_movimientos dm INNER JOIN detalle_movimientos dem USING (id_movimiento) INNER JOIN datos_lotes dl ON (dl.lote_id = dem.lote_id) WHERE dm.remito_nro = '" . $detalle['remito_nro'] ."' AND dem.producto_id = " . $detalle['producto_id'] ." AND dem.cantidad = " . $detalle['cantidad']*-1 .";");
        //echo $db->getQry();
        $arrDetalles =  $db->ejecutar();
        $lote=null;
        $antLote=null;
        foreach($arrDetalles AS $lotes){
            if($antLote!=$lotes['lote']){
                $lote.= $lotes['lote'] . ",";
            }
            $antLote=$lotes['lote'];
        }
        $lote=substr($lote, 0, -1);
        $pdf->SetFont('Arial','',8);
        if($arrCabecera[0]['factura_letra']=='A'){
            $pdf->Text(6,$alto, $i) ;
            $pdf->Text(15,$alto, utf8_decode($detalle['producto_nombre'])) ;
            $pdf->Text(100,$alto, $detalle['cantidad'] );
            $pdf->Text(118,$alto, $detalle['producto_pventa']);
            $pdf->Text(140,$alto, $detalle['desc_detalle']);
            $pdf->Text(153,$alto, $detalle['detalle_neto']);
            $pdf->Text(169,$alto, $detalle['producto_iva'] . "%");
            $pdf->Text(182,$alto, $detalle['importe_detalle']);
        }else{
            $pdf->Text(6,$alto, $i) ;
            $pdf->Text(15,$alto, utf8_decode($detalle['producto_nombre'])) ;
            $pdf->Text(122,$alto, $detalle['cantidad'] );
            if($detalle['producto_iva']==21){
                $pdf->Text(137,$alto, $detalle['producto_pventa'] *1.21);
            }else{
                $pdf->Text(137,$alto, $detalle['producto_pventa'] *1.105);
            }

            $pdf->Text(161,$alto, $detalle['desc_detalle']);
            $pdf->Text(180,$alto, $detalle['importe_detalle']);
        }
        $subtotal=$subtotal + $detalle['importe_detalle'];
        $alto = $alto + 3;
        $pdf->SetFont('Arial','',7);
        $pdf->Text(15,$alto, "Lote/s: " . $lote);
        $alto = $alto + 4;
        $i++;
        if($i==25 || $i==48 || $i==69){
            $pdf->SetFont('Arial','B',10);
            $pdf->Text(155,270, utf8_decode("Subtotal: ". $objFuncionesComunes->formatoMoneda($subtotal)));
            //BORDER SUPERIOR
            $pdf->Line(3, 255 ,205 , 255);
            //BORDER INFERIOR
            $pdf->Line(3, 290 ,205 , 290);

            $objQR = NEW codigoQR();
            $objQR->generarQR($arrCabecera);
            $pdf->Image('codigoQR/'. $arrCabecera[0][factura_cae] .'.png', 10, 257, 30, 30, 'png');
            //parte legible
            $pdf->SetFont('Arial','',8);    
            $pdf->Text(18,290, $codigoBarras);

            $pdf->SetFont('Arial','B',10);    
            if(count($arrCabecera)>48 && count($arrCabecera)<69 && $i==25){
                $pdf->Text(92,253, utf8_decode("Pag 1/3"));
            }
            if(count($arrCabecera)>69 && $i==25){
                $pdf->Text(92,253, utf8_decode("Pag 1/4"));
            }
            if(count($arrCabecera)<48){
                $pdf->Text(92,253, utf8_decode("Pag 1/2"));
            }           
            if($i==48 && count($arrCabecera)<69){
                $pdf->Text(92,253, utf8_decode("Pag 2/3"));
            }
            if($i==48 && count($arrCabecera)>69){
                $pdf->Text(92,253, utf8_decode("Pag 2/4"));
            }
            if($i==69){
                $pdf->Text(92,253, utf8_decode("Pag 3/4"));
            }
            //BORDER DIVISOR
            $pdf->Line(98, 273 ,205 , 273);
            $pdf->Text(150,280, utf8_decode("CAE Nro: " . $arrCabecera[0]['factura_cae']));
            $pdf->Text(128.5,285, utf8_decode("Fecha de Vto. de CAE: " . $arrCabecera[0]['vencimiento_cae_cast']));
            $pdf->Line(98.5, 255 ,98.5, 290);
            $pdf->AddPage();
            //DATOS PROPIOS
//DATOS PROPIOS
$pdf->Image('corvision.jpg', 5, 5, 65, 20, 'jpg');
$pdf->SetFont('Arial','B',10);
$pdf->Text(5,30, utf8_decode("Razón Social: "));
$pdf->Text(5,34, utf8_decode("Domicilio Comercial: "));
$pdf->SetFont('Arial','',10);
$pdf->Text(30,30, utf8_decode("Corvision de Pappolla Juan Jose"));
$pdf->Text(40,34, utf8_decode(" La Tablada 238 -"));
$pdf->Text(5,38, utf8_decode("Villa General Belgrano, Córdoba "));
$pdf->SetFont('Arial','B',10);
$pdf->Text(5,42, utf8_decode("Condición frente al IVA: Responsable Inscripto"));

//FACTURA LETRA
$pdf->SetFont('Arial','B',24);
$pdf->Text(95.5,13, strtoupper($arrCabecera[0]['factura_letra'])) ;
$pdf->SetFont('Arial','B',8);
$pdf->Text(92.7,18, "COD." . str_pad($arrCabecera[0]['factura_tipo'], 3, "0", STR_PAD_LEFT)) ;
//BORDER INFERIOR
$pdf->Line(92, 20 ,105 , 20);
//BORDER IZQUIERDO
$pdf->Line(105, 5 ,105, 20);
//BORDER DERECHO
$pdf->Line(92, 5 ,92, 20);
//BORDER QUE LIMITA
$pdf->Line(98.5, 20 ,98.5, 45);

$pdf->SetFont('Arial','B',24);
//$pdf->Text(115,17, utf8_decode("FACTURA"));
if($arrCabecera[0]['factura_tipo']==1 || $arrCabecera[0]['factura_tipo']==6){
    $pdf->Text(115,17, utf8_decode("FACTURA"));
}else{
    $pdf->Text(115,17, utf8_decode("NOTA DE CRÉDITO"));
}
$pdf->SetFont('Arial','B',10);
$pdf->Text(115,23, utf8_decode("Punto de Venta: " . str_pad($arrCabecera[0]['factura_puesto_venta'], 4, "0", STR_PAD_LEFT)));
$pdf->Text(155,23, utf8_decode("Comp.Nro: " . substr($arrCabecera[0]['factura_nro'], 6)));


//FECHA
$pdf->Text(115,28, utf8_decode("Fecha de Emisión: ") . $arrCabecera[0]['factura_fecha']) ;
//DATOS FISCALES
$pdf->Text(115,33, utf8_decode("CUIT: "));
$pdf->Text(115,37, utf8_decode("Ingresos Brutos: ")) ;
$pdf->Text(115,42, utf8_decode("Fecha de Inicio de Actividades: ")) ;
$pdf->SetFont('Arial','',10);
$pdf->Text(125,33, utf8_decode("20137401534"));
$pdf->Text(145,37, utf8_decode("9040063141"));
$pdf->Text(170,42, utf8_decode("01/01/2000"));

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
if($arrCabecera[0]['condicion_iva_id']==6){
    $pdf->SetFont('Arial','B',8);
    $pdf->Text(4,250, utf8_decode("El crédito fiscal discriminado en el presente comprobante, sólo podrá ser computado a efectos del Régimen de Sostenimiento e Inclusión Fiscal"));
    $pdf->Text(4,253, utf8_decode("para Pequeños Contribuyentes de la Ley Nº 27.618"));
}
$pdf->SetFont('Arial','B',10);
if($arrCabecera[0]['factura_letra']=='A'){

    $pdf->Text(140,260, utf8_decode("Importe Neto Gravado:  " . $objFuncionesComunes->formatoMoneda($arrCabecera[0]['total_neto'])));
    $qryIVA="SELECT sum(importe_iva) AS iva_detalle, sum(importe_detalle - importe_iva) AS base_imp, CASE WHEN producto_iva = 21 THEN '5' ELSE '4' END AS alicuota_id FROM detalle_facturas INNER JOIN datos_productos USING(producto_id) WHERE factura_id ={$arrCabecera[0][factura_id]} GROUP BY CASE WHEN producto_iva = 21 THEN '5' ELSE '4' END, producto_iva ORDER BY producto_iva;";
    $db->setQry($qryIVA);
    //echo $qryIVA;exit;
    $resultadoIVA=$db->ejecutar();
    $iva10=false;
    $iva21=false;
    foreach($resultadoIVA AS $iva){
        if($iva[alicuota_id]==4){
            $pdf->Text(160,265, utf8_decode("IVA 10.5%:  "));
            $pdf->Text(182,265, $objFuncionesComunes->formatoMoneda($iva['iva_detalle']));
            $iva10=true;
        }
        if($iva[alicuota_id]==5){
            $pdf->Text(163,270, utf8_decode("IVA 21%:  "));
            $pdf->Text(184,270, $objFuncionesComunes->formatoMoneda($iva['iva_detalle']));
            $iva21=true;
        }
        //$iva10=false;
    }
    if($iva10==false){
        $pdf->Text(160,265, utf8_decode("IVA 10.5%:  "));
        $pdf->Text(191,265, $objFuncionesComunes->formatoMoneda(0));
    }
    if($iva21==false){
        $pdf->Text(163,270, utf8_decode("IVA 21%:  "));
        $pdf->Text(191,270, $objFuncionesComunes->formatoMoneda(0));
    }
    $pdf->Text(155,275, utf8_decode("Importe Total:  " . $objFuncionesComunes->formatoMoneda($arrCabecera[0]['total_factura'])));

    //linea medio separa qr
    $pdf->Line(98.5, 255 ,98.5, 290);
    //separa cae e importes
    $pdf->Line(98, 278 ,205 , 278);

}else{

    $pdf->Text(164,260, utf8_decode("Subtotal: $ " . $arrCabecera[0]['total_factura']));
    $pdf->Text(140,265, utf8_decode("Importe Otros Tributos: $           0"));
    $pdf->Text(155,270, utf8_decode("Importe Total:  $ " . $arrCabecera[0]['total_factura']));
    //linea q separa tran de importes
    $pdf->Line(127, 255 ,127, 273);
    //transarencia fiscal
    $pdf->SetFont('Arial','B',8);
    $pdf->Text(44,260, utf8_decode("Regímen de Transparencia Fiscal al Consumidor (Ley 27.743)"));
    //separa que separa titulo
    $pdf->Line(44, 262 ,127 , 262);
    $pdf->Text(84,265, utf8_decode("IVA Contenido: $ " . $arrCabecera[0]['total_iva']));
    $pdf->Text(48,270, utf8_decode("Importe Impuestos Nacionales Indirectos: $       0"));
    //linea medio separa qr
    $pdf->Line(43, 255 ,43, 290);
    //separa cae e importes
    $pdf->Line(43, 273 ,205 , 273);
}
//BORDER SUPERIOR
$pdf->Line(3, 255 ,205 , 255);
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
//var_dump($arrCabecera);exit;
//echo $arrCabecera[0]['factura_puesto_venta'];exit;
if($arrCabecera[0]['factura_puesto_venta']=='5'){
    //echo "entra";exit;
    $objQR = NEW codigoQR();
    $objQR->generarQR($arrCabecera);
    $pdf->Image('codigoQR/'. $arrCabecera[0][factura_cae] .'.png', 10, 257, 30, 30, 'png');

    //parte legible
    /*$pdf->SetFont('Arial','',8);
    $pdf->Text(18,290, $codigoBarras);
    */
    $pdf->SetFont('Arial','B',10);

    if($i>24 && $i<48){
        $pdf->Text(92,253, utf8_decode("Pag 2/2"));
    }
    if($i>48 && $i<69){
        $pdf->Text(92,253, utf8_decode("Pag 3/3"));
    }
    if($i>=69){
            $pdf->Text(92,253, utf8_decode("Pag 4/4"));
    }

    $pdf->Text(140,283, utf8_decode("CAE Nro: " . $arrCabecera[0]['factura_cae']));
    $pdf->Text(118.5,288, utf8_decode("Fecha de Vto. de CAE: " . $arrCabecera[0]['vencimiento_cae_cast']));
}


$pdf->Output();

    

