<?php                                                                                                                                                                            
//ALMACENAMIENTO                                                                                                                                                                 
require_once $_SERVER['DOCUMENT_ROOT'] .'/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';                                                                        
require_once 'php-barcode.php';                                                                                                                                                      
ob_end_clean();                                                                                                                                                                  
require_once $_SERVER['DOCUMENT_ROOT'] .'/newFront/libreria/fpdf/fpdf.php';                                                                                                  
require_once $_SERVER['DOCUMENT_ROOT'] .'/newFront/libreria/includes/FuncionesComunes.php'; 
require_once('barcode.inc.php'); 

switch($_GET['crIdSucursal']){
	case 2:
		require_once 'generadorFacturaLimon1hoja.php';
		break;
	case 3:
		require_once 'generadorFacturaLimoneta.php';
		break;
}
