<?php
require_once 'class/ParseINI.php';
require_once 'class/Includes.php';

header('Content-Type: text/plain; charset=utf-8');

$rutaEsperada = __DIR__ . DIRECTORY_SEPARATOR . 'cfg' . DIRECTORY_SEPARATOR . 'config.ini';
echo "Ruta calculada: " . $rutaEsperada . "\n";
echo "Existe ese archivo?: " . (file_exists($rutaEsperada) ? 'SI' : 'NO') . "\n";
echo "Ruta real (realpath): " . realpath($rutaEsperada) . "\n\n";

$css = ParseINI::getConfig('common', 'css');
echo "Tipo de dato devuelto: " . gettype($css) . "\n";
echo "Cantidad de CSS encontrados: " . (is_array($css) ? count($css) : 'N/A - no es un array') . "\n\n";
echo "Lista completa:\n";
print_r($css);

echo "\n\nHTML que generaria Includes::get:\n";
echo Includes::get('common', 'css');
