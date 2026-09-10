<?php
// Endpoint aislado: confirma que el usuario vio los precios nuevos.
// No pasa por process.php ni por frenteDeFrentes — solo un INSERT en
// precio_confirmaciones para los productos que se le mostraron.
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../../libreria/almacenamiento/miPDO.php';

$respuesta = array('soyError' => true, 'mensaje' => '');

if (empty($_SESSION['usuarioValidado']) || empty($_SESSION['usuarioId'])) {
    $respuesta['mensaje'] = 'Sesión inválida.';
    echo json_encode($respuesta);
    exit;
}

$usuarioId = $_SESSION['usuarioId'];
$productoIds = isset($_POST['productoIds']) ? explode(',', $_POST['productoIds']) : array();
$productoIds = array_filter(array_map('intval', $productoIds));

if (empty($productoIds)) {
    $respuesta['mensaje'] = 'No hay productos para confirmar.';
    echo json_encode($respuesta);
    exit;
}

try {
    $db = new miPDO('dmelmac', __DIR__ . '/../../libreria/almacenamiento/almacenamiento.ini');
    $stmt = $db->prepare(
        "INSERT INTO precio_confirmaciones (usuario_id, producto_id, fecha_confirmado)
         VALUES (:usuarioId, :productoId, now())
         ON CONFLICT (usuario_id, producto_id)
         DO UPDATE SET fecha_confirmado = now()"
    );
    foreach ($productoIds as $productoId) {
        $stmt->execute(array(':usuarioId' => $usuarioId, ':productoId' => $productoId));
    }
    $respuesta['soyError'] = false;
} catch (Exception $e) {
    $respuesta['mensaje'] = 'No se pudo guardar la confirmación.';
}

echo json_encode($respuesta);
