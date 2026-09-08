<?php
require_once('BCGFontFile.php');
require_once('BCGColor.php');
require_once('BCGDrawing.php');
require_once('BCGdatamatrix.barcode2d.php');

$gtin = '01' . $_GET['gtin'];
$serie = '21' . $_GET['serie'];
$vencimiento = '17' . $_GET['vencimiento'];
$nro_lote = '10' . $_GET['lote'];
$text = "$gtin$serie~F$vencimiento$nro_lote";

// Label, this part is optional
$label = new BCGLabel();
$label->setFont(new BCGFontFile('./font/Arial.ttf', 8));
$label->setPosition(BCGLabel::POSITION_BOTTOM);
$label->setAlignment(BCGLabel::ALIGN_CENTER);
$label->setText("");

$drawException = null;
try {
	// Datamatrix Part
	$code = new BCGdatamatrix();
	$code->setEncoding(BCGdatamatrix::DATAMATRIX_ENCODING_ASCII);
	$code->setTilde(true);
	$code->setFNC1(BCGdatamatrix::DATAMATRIX_FNC1_GS1);
	$code->setScale(3.5);
	$code->setSize(BCGdatamatrix::DATAMATRIX_SIZE_SQUARE);

	// Remove the following line if you don't want any label
	$code->addLabel($label);

	$code->parse($text);
} catch(Exception $exception) {
	$drawException = $exception;
}

// Drawing Part
$color_white = new BCGColor(255, 255, 255);
$drawing = new BCGDrawing('', $color_white);
if($drawException) {
	$drawing->drawException($drawException);
} else {
	$drawing->setBarcode($code);
	$drawing->draw();
}

header('Content-Type: image/jpg');

$drawing->finish(BCGDrawing::IMG_FORMAT_JPEG);
