<?php
/**
 * Impresion de etiquetas de precios en hoja A4.
 *
 * Parametros (GET):
 *   tamano : chica | mediana | grande   (default: chica)
 *   ids    : lista de producto_id separados por coma. Cada id puede llevar
 *            cantidad de copias con ":"  -> ids=12,15:3,20
 *
 * Ej: impresionEtiquetasPrecios.php?tamano=chica&ids=12,15:3,20
 *
 * Datos: datos_etiquetas (etiqueta_desc, uxb) + lista_precios_10
 *        (precio_promocion = caja efectivo, producto_pventa = unidad efectivo)
 */
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/almacenamiento/FrenteAlmacenamiento.php';
if (ob_get_level()) {
    ob_end_clean();
}
require_once $_SERVER['DOCUMENT_ROOT'] . '/newFront/libreria/fpdf/fpdf.php';

// ---------------------------------------------------------------------------
// CONFIGURACION
// ---------------------------------------------------------------------------

// Medidas de cada formato en mm (ancho x alto)
$FORMATOS = array(
    'chica'   => array('ancho' => 60,  'alto' => 30),
    'mediana' => array('ancho' => 130, 'alto' => 50),
    'grande'  => array('ancho' => 210, 'alto' => 297),
);

// Hoja A4 y margen que la impresora no llega a imprimir
define('HOJA_ANCHO', 210);
define('HOJA_ALTO', 297);
define('HOJA_MARGEN', 5);

// Recargo de cada lista sobre el precio efectivo (en %)
$LISTAS = array(
    array('nombre' => 'Lista 1', 'detalle' => 'Efectivo',      'recargo' => 0),
    array('nombre' => 'Lista 2', 'detalle' => 'Transf/Débito', 'recargo' => 3),
    array('nombre' => 'Lista 3', 'detalle' => 'Crédito',       'recargo' => 7),
);

// ---------------------------------------------------------------------------
// PRECIOS
// Toda la logica de precios esta aca. Devuelve los precios efectivo
// (sin recargo) de caja y unidad; los recargos de cada lista se aplican despues.
// ---------------------------------------------------------------------------
function calcularPreciosEfectivo($fila) {
    $caja   = is_numeric($fila['precio_promocion']) ? (float) $fila['precio_promocion'] : null;
    $unidad = is_numeric($fila['producto_pventa'])  ? (float) $fila['producto_pventa']  : null;

    return array('caja' => $caja, 'unidad' => $unidad);
}

function aplicarRecargo($precio, $recargo) {
    if ($precio === null) {
        return null;
    }
    return round($precio * (1 + $recargo / 100), 2);
}

function formatearPrecio($precio) {
    if ($precio === null) {
        return '-';
    }
    return '$ ' . number_format($precio, 2, ',', '.');
}

// ---------------------------------------------------------------------------
// PDF
// ---------------------------------------------------------------------------
class PdfEtiquetas extends FPDF
{
    // Rota lo que se dibuje a continuacion $angulo grados (antihorario)
    // alrededor de ($x, $y). Rotar(0) vuelve a la normalidad.
    private $_rotado = false;

    const PT_MM      = 0.3527778; // 1 pt en mm
    const INTERLINEA = 1.15;

    public function Rotar($angulo, $x = 0, $y = 0) {
        if ($this->_rotado) {
            $this->_out('Q');
            $this->_rotado = false;
        }
        if ($angulo != 0) {
            $a  = $angulo * M_PI / 180;
            $c  = cos($a);
            $s  = sin($a);
            $cx = $x * $this->k;
            $cy = ($this->h - $y) * $this->k;
            $this->_out(sprintf('q %.5F %.5F %.5F %.5F %.2F %.2F cm 1 0 0 1 %.2F %.2F cm',
                $c, $s, -$s, $c, $cx, $cy, -$cx, -$cy));
            $this->_rotado = true;
        }
    }

    /**
     * Tamaño base (pt) mas grande con el que $lineas entran en un recuadro de
     * $w x $h mm. $lineas = array(array(texto, estilo, peso), ...) donde el
     * peso es el tamaño relativo de cada linea.
     */
    public function TamanoQueEntra($w, $h, $lineas, $maxPt = 72) {
        $sumPeso = 0;
        foreach ($lineas as $l) {
            $sumPeso += $l[2];
        }
        $base  = min($maxPt, ($h * 0.82) / ($sumPeso * self::PT_MM * self::INTERLINEA));
        $util  = $w - 2 * min(1.5, $w * 0.06);
        foreach ($lineas as $l) {
            $this->SetFont('Arial', $l[1], $base * $l[2]);
            $ancho = $this->GetStringWidth($l[0]);
            if ($ancho > $util) {
                $base *= $util / $ancho;
            }
        }
        return $base;
    }

    /**
     * Escribe $lineas centradas en el recuadro con tamaño base $base (pt).
     */
    public function TextoCentrado($x, $y, $w, $h, $lineas, $base) {
        $altoTotal = 0;
        foreach ($lineas as $l) {
            $altoTotal += $base * $l[2] * self::PT_MM * self::INTERLINEA;
        }
        $yLinea = $y + ($h - $altoTotal) / 2;
        foreach ($lineas as $l) {
            $pt    = $base * $l[2];
            $altoL = $pt * self::PT_MM * self::INTERLINEA;
            $this->SetFont('Arial', $l[1], $pt);
            $ancho = $this->GetStringWidth($l[0]);
            // Linea base: centro de la linea + ~1/3 del tamaño de la letra
            $this->Text($x + ($w - $ancho) / 2, $yLinea + $altoL / 2 + $pt * self::PT_MM * 0.35, $l[0]);
            $yLinea += $altoL;
        }
    }

    /**
     * Nombre del producto en 1 o 2 lineas, la que permita letra mas grande.
     */
    private function _lineasNombre($nombre, $w, $h, $maxPt) {
        $una   = array(array($nombre, 'B', 1));
        $mejor = array($una, $this->TamanoQueEntra($w, $h, $una, $maxPt));

        // Corte en 2 lineas lo mas parejas posible
        $palabras = explode(' ', $nombre);
        $this->SetFont('Arial', 'B', 10);
        $dos      = null;
        $anchoMax = null;
        for ($i = 1; $i < count($palabras); $i++) {
            $l1 = implode(' ', array_slice($palabras, 0, $i));
            $l2 = implode(' ', array_slice($palabras, $i));
            $a  = max($this->GetStringWidth($l1), $this->GetStringWidth($l2));
            if ($anchoMax === null || $a < $anchoMax) {
                $anchoMax = $a;
                $dos = array(array($l1, 'B', 1), array($l2, 'B', 1));
            }
        }
        if ($dos !== null) {
            $pt = $this->TamanoQueEntra($w, $h, $dos, $maxPt);
            if ($pt > $mejor[1] * 1.05) {
                $mejor = array($dos, $pt);
            }
        }
        return $mejor;
    }

    /**
     * Dibuja una etiqueta con su esquina superior izquierda en ($x, $y).
     */
    public function Etiqueta($x, $y, $w, $h, $datos, $listas) {
        $this->SetLineWidth(0.2);
        $this->Rect($x, $y, $w, $h);

        // Proporciones del dibujo: titulo, encabezado de listas, caja, unidad
        $filas    = array(0.24, 0.22, 0.27, 0.27);
        $colIni   = 0.22;
        $colLista = (1 - $colIni) / count($listas);

        $altos = array();
        foreach ($filas as $f) {
            $altos[] = $h * $f;
        }
        $anchos = array($w * $colIni);
        foreach ($listas as $l) {
            $anchos[] = $w * $colLista;
        }

        // Lineas horizontales
        $yy = $y;
        for ($i = 0; $i < count($altos) - 1; $i++) {
            $yy += $altos[$i];
            $this->Line($x, $yy, $x + $w, $yy);
        }
        // Lineas verticales (desde debajo del titulo)
        $xx = $x;
        for ($i = 0; $i < count($anchos) - 1; $i++) {
            $xx += $anchos[$i];
            $this->Line($xx, $y + $altos[0], $xx, $y + $h);
        }

        // Recuadros interiores (con un pequeño margen)
        $pad    = min(0.6, $h * 0.01);
        $maxPt  = 72;
        $colX   = array();
        $xx     = $x;
        foreach ($anchos as $a) {
            $colX[] = $xx + $pad;
            $xx += $a;
        }
        $filaY = array();
        $yy    = $y;
        foreach ($altos as $a) {
            $filaY[] = $yy + $pad;
            $yy += $a;
        }
        $cw = function ($i) use ($anchos, $pad) { return $anchos[$i] - 2 * $pad; };
        $ch = function ($i) use ($altos, $pad) { return $altos[$i] - 2 * $pad; };

        // Titulo: nombre del producto
        list($lineas, $pt) = $this->_lineasNombre($datos['nombre'], $w - 2 * $pad, $ch(0), $maxPt);
        $this->TextoCentrado($x + $pad, $filaY[0], $w - 2 * $pad, $ch(0), $lineas, $pt);

        // Cada grupo de celdas usa el mismo tamaño de letra: el que entra en todas
        $celdas = array(
            'uxb'     => array(),
            'listas'  => array(),
            'rotulos' => array(),
            'precios' => array(),
        );
        $celdas['uxb'][] = array(0, 1, array(array('U x B', '', 0.7), array($datos['uxb'], 'B', 1)));
        foreach ($listas as $i => $l) {
            $celdas['listas'][] = array($i + 1, 1, array(array($l['nombre'], 'B', 1), array($l['detalle'], '', 0.75)));
        }
        $filasPrecio = array(
            2 => array('Caja', $datos['precios']['caja']),
            3 => array('Unidad', $datos['precios']['unidad']),
        );
        foreach ($filasPrecio as $f => $fp) {
            $celdas['rotulos'][] = array(0, $f, array(array('Precio x', '', 1), array($fp[0], '', 1)));
            foreach ($fp[1] as $i => $precio) {
                $celdas['precios'][] = array($i + 1, $f, array(array($precio, 'B', 1)));
            }
        }

        foreach ($celdas as $grupo) {
            $pt = $maxPt;
            foreach ($grupo as $c) {
                $pt = min($pt, $this->TamanoQueEntra($cw($c[0]), $ch($c[1]), $c[2], $maxPt));
            }
            foreach ($grupo as $c) {
                $this->TextoCentrado($colX[$c[0]], $filaY[$c[1]], $cw($c[0]), $ch($c[1]), $c[2], $pt);
            }
        }
    }
}

/**
 * Calcula como acomodar las etiquetas en la hoja para que entren la mayor
 * cantidad posible: prueba derecha y girada y se queda con la que mas entra.
 * Si la etiqueta no entra en el area imprimible, se achica hasta entrar.
 */
function calcularGrilla($ancho, $alto) {
    $utilW = HOJA_ANCHO - 2 * HOJA_MARGEN;
    $utilH = HOJA_ALTO - 2 * HOJA_MARGEN;

    $opciones = array();
    foreach (array(false, true) as $girada) {
        $w = $girada ? $alto : $ancho;   // lo que ocupa en la hoja
        $h = $girada ? $ancho : $alto;
        $cols = floor($utilW / $w);
        $rows = floor($utilH / $h);
        $opciones[] = array('girada' => $girada, 'cols' => $cols, 'filas' => $rows,
                            'ancho' => $ancho, 'alto' => $alto);
    }
    usort($opciones, function ($a, $b) {
        return ($b['cols'] * $b['filas']) - ($a['cols'] * $a['filas']);
    });
    $g = $opciones[0];

    // No entra ninguna (ej: grande = A4 completa): se escala al area imprimible
    if ($g['cols'] * $g['filas'] == 0) {
        $g = array('girada' => false, 'cols' => 1, 'filas' => 1,
                   'ancho' => min($ancho, $utilW), 'alto' => min($alto, $utilH));
    }

    // Grilla centrada en la hoja
    $ocupaW = $g['girada'] ? $g['alto'] : $g['ancho'];
    $ocupaH = $g['girada'] ? $g['ancho'] : $g['alto'];
    $g['ocupaW'] = $ocupaW;
    $g['ocupaH'] = $ocupaH;
    $g['x0'] = (HOJA_ANCHO - $g['cols'] * $ocupaW) / 2;
    $g['y0'] = (HOJA_ALTO - $g['filas'] * $ocupaH) / 2;
    $g['porHoja'] = $g['cols'] * $g['filas'];
    return $g;
}

function aLatin1($texto) {
    $r = @iconv('UTF-8', 'windows-1252//TRANSLIT', (string) $texto);
    return $r === false ? (string) $texto : $r;
}

// ---------------------------------------------------------------------------
// PARAMETROS
// ---------------------------------------------------------------------------
$tamano = isset($_GET['tamano']) ? strtolower($_GET['tamano']) : 'chica';
if (!isset($FORMATOS[$tamano])) {
    echo "<h2>Tamaño inválido: " . htmlspecialchars($tamano) . " (chica, mediana o grande)</h2>";
    exit;
}

// ids=12,15:3,20  ->  array(array(12,1), array(15,3), array(20,1))
$pedidos = array();
$idsUnicos = array();
foreach (explode(',', isset($_GET['ids']) ? $_GET['ids'] : '') as $item) {
    $partes = explode(':', trim($item));
    $id     = (int) $partes[0];
    $copias = isset($partes[1]) ? max(1, (int) $partes[1]) : 1;
    if ($id > 0) {
        $pedidos[] = array($id, $copias);
        $idsUnicos[$id] = $id;
    }
}
if (count($pedidos) == 0) {
    echo "<h2>No se indicaron productos (parámetro ids)</h2>";
    exit;
}

// ---------------------------------------------------------------------------
// DATOS
// ---------------------------------------------------------------------------
$db = new FrenteAlmacenamiento();
$db->addSelect('de.producto_id');
$db->addSelect('de.etiqueta_desc');
$db->addSelect('de.uxb');
$db->addSelect('lp.producto_pventa');
$db->addSelect('lp.precio_promocion');
$db->addFrom('datos_etiquetas de LEFT JOIN lista_precios_10 lp ON (lp.producto_id = de.producto_id)');
$db->addWhere('de.producto_id IN (' . implode(',', $idsUnicos) . ')');
$db->generarSelect();
$resultado = $db->ejecutar();

$productos = array();
if (is_array($resultado)) {
    foreach ($resultado as $fila) {
        $productos[$fila['producto_id']] = $fila;
    }
}
if (count($productos) == 0) {
    echo "<h2>Sin registros en datos_etiquetas para: " . implode(',', $idsUnicos) . "</h2>";
    exit;
}

// Armar la lista de etiquetas a imprimir, respetando el orden y las copias
$etiquetas = array();
foreach ($pedidos as $p) {
    if (!isset($productos[$p[0]])) {
        continue;
    }
    $fila   = $productos[$p[0]];
    $efvo   = calcularPreciosEfectivo($fila);
    $precios = array('caja' => array(), 'unidad' => array());
    foreach ($LISTAS as $l) {
        $precios['caja'][]   = formatearPrecio(aplicarRecargo($efvo['caja'], $l['recargo']));
        $precios['unidad'][] = formatearPrecio(aplicarRecargo($efvo['unidad'], $l['recargo']));
    }
    $uxb = (is_numeric($fila['uxb']) && $fila['uxb'] > 0) ? (string) (0 + $fila['uxb']) : '-';

    $datos = array(
        'nombre'  => aLatin1($fila['etiqueta_desc']),
        'uxb'     => $uxb,
        'precios' => $precios,
    );
    for ($c = 0; $c < $p[1]; $c++) {
        $etiquetas[] = $datos;
    }
}

$listasPdf = array();
foreach ($LISTAS as $l) {
    $listasPdf[] = array('nombre' => aLatin1($l['nombre']), 'detalle' => aLatin1($l['detalle']));
}

// ---------------------------------------------------------------------------
// IMPRESION
// ---------------------------------------------------------------------------
$g = calcularGrilla($FORMATOS[$tamano]['ancho'], $FORMATOS[$tamano]['alto']);

$pdf = new PdfEtiquetas('P', 'mm', 'A4');
$pdf->SetAutoPageBreak(false);
$pdf->SetMargins(0, 0, 0);
$pdf->SetTitle('Etiquetas ' . $tamano);

foreach ($etiquetas as $i => $datos) {
    $pos = $i % $g['porHoja'];
    if ($pos == 0) {
        $pdf->AddPage();
    }
    $col = $pos % $g['cols'];
    $fil = floor($pos / $g['cols']);
    $x = $g['x0'] + $col * $g['ocupaW'];
    $y = $g['y0'] + $fil * $g['ocupaH'];

    if ($g['girada']) {
        // Se dibuja como si estuviera derecha con esquina en (x, y + ocupaH)
        // y se rota 90 grados: el texto queda leyendose de abajo hacia arriba.
        $pdf->Rotar(90, $x, $y + $g['ocupaH']);
        $pdf->Etiqueta($x, $y + $g['ocupaH'], $g['ancho'], $g['alto'], $datos, $listasPdf);
        $pdf->Rotar(0);
    } else {
        $pdf->Etiqueta($x, $y, $g['ancho'], $g['alto'], $datos, $listasPdf);
    }
}

$pdf->Output('etiquetas_' . $tamano . '.pdf', 'I');
