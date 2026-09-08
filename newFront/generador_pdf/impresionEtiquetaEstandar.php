<?php
//ALMACENAMIENTO
require_once $_SERVER['DOCUMENT_ROOT'] .'/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
ob_end_clean();
require_once $_SERVER['DOCUMENT_ROOT'] .'/newFront/libreria/fpdf/fpdf.php';
$db = NEW frenteAlmacenamiento();



$db->addSelect("dl.lote AS lote");
$db->addSelect("to_char(dl.lote_vencimiento, 'YYMMDD') AS venc_cast");
$db->addSelect("CASE WHEN char_length(dp.producto_gtin) = 13 THEN '0' || dp.producto_gtin ELSE dp.producto_gtin END AS gtin");
$db->addSelect("dp.producto_nombre");
$db->addSelect("dp.producto_presentacion");
$db->addSelect("dt.trazabilidad_codigo AS traza");
$db->addFrom("datos_trazabilidad dt 
              INNER JOIN movimientos_trazabilidad mt USING (trazabilidad_id)
              INNER JOIN datos_movimientos dm USING (id_movimiento)
              INNER JOIN datos_lotes dl USING (lote_id)
              INNER JOIN datos_productos dp ON (dp.producto_id=dt.producto_id)              
     ");
if($_GET['crIdMovimiento']!=null){
    $db->addWhere("dm.id_movimiento = "  . $_GET['crIdMovimiento'] );
}else{
    $db->addWhere("dt.trazabilidad_id = "  . $_GET['crIdTrazabilidad'] );

}
$db->addWhere('dm.tipo_movimiento_id IN (1,14,16)');
$db->addOrderBy('trazabilidad_id ASC');
$db->generarSelect();   
//echo $db->getQry();exit;
$codigos = $db->ejecutar();
//exit;

if(count($codigos) ==0){
    echo "<h2>Sin registros para {$_GET['crIdMovimiento']} {$_GET['crIdTrazabilidad']} ";
    exit;
}



$pdf = new FPDF ('P','mm',array(65,45));

foreach($codigos AS $codigo){
    
    $pdf->AddPage();
    
    if($codigo['traza']){
        $pdf->Image('http://localhost/newFront/libreria/barcode/datamatrixEtiqueta.php?gtin='.$codigo['gtin'].'&serie='.$codigo['traza'].'&vencimiento='.$codigo['venc_cast'].'&lote='.$codigo['lote'],4,6,0,0,'JPEG');
        $text = "(01){$codigo['gtin']}(21){$codigo['traza']}(17){$codigo['venc_cast']}(10){$codigo['lote']}";
    }
    
    $pdf->SetFont('Arial','B',10);
    $pdf->Text(22,6 ,'BIOFARMA SA');
    
    $vecY= 13;
    
    $pdf->SetFont('Arial','',8);
    $pdf->Text(30,$vecY,'(01)' . $codigo['gtin'] );
    $vecY+=4; 
    
    $pdf->Text(30,$vecY,'(21)' . $codigo['traza'] );
    $vecY+=4;  
    
    $pdf->Text(30,$vecY,'(17)' . $codigo['venc_cast']);
    $vecY+=4;  
    
    $pdf->Text(30,$vecY,'(10)' . $codigo['lote']);
    $vecY+=4;  
    
    $pdf->SetFont('Arial','',6);
    $pdf->Text(4,33, $text);
    
    $pdf->SetFont('Arial','B',8);
    $pdf->Text(2,40, utf8_decode($codigo['producto_nombre']) . " " . utf8_decode($codigo['producto_presentacion']));
       
        
}
$pdf->Output(); 
