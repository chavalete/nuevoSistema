<?php
// Diagnostico temporal, sin cache de sesion. Borrar cuando resolvamos
// el tema del aviso de precios.
session_start();
header('Content-Type: application/json');

require_once '/var/www/html/limonLocal/libreria/almacenamiento/miPDO.php';

$out = array(
    'usuarioId' => isset($_SESSION['usuarioId']) ? $_SESSION['usuarioId'] : null,
    'usuarioValidado' => isset($_SESSION['usuarioValidado']) ? $_SESSION['usuarioValidado'] : null,
);

try {
    $db = new miPDO('dmelmac', '/var/www/html/limonLocal/libreria/almacenamiento/almacenamiento.ini');

    $usuarioId = $out['usuarioId'];

    $stmtSusc = $db->prepare("SELECT 1 FROM aviso_precios_usuarios WHERE usuario_id = :usuarioId");
    $stmtSusc->execute(array(':usuarioId' => $usuarioId));
    $out['dado_de_alta'] = (bool) $stmtSusc->fetchColumn();

    $stmt = $db->prepare(
        "SELECT lp.producto_id, dp.producto_nombre, lp.producto_pventa, lp.fecha_actualizacion,
                pc.fecha_confirmado
         FROM lista_precios_10 lp
         JOIN datos_productos dp ON dp.producto_id = lp.producto_id
         LEFT JOIN precio_confirmaciones pc
                ON pc.producto_id = lp.producto_id AND pc.usuario_id = :usuarioId
         WHERE lp.fecha_actualizacion IS NOT NULL
         ORDER BY lp.fecha_actualizacion DESC
         LIMIT 5"
    );
    $stmt->execute(array(':usuarioId' => $usuarioId));
    $out['ultimos_5_con_fecha_actualizacion'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $out['error'] = null;
} catch (Exception $e) {
    $out['error'] = $e->getMessage();
}

echo json_encode($out, JSON_PRETTY_PRINT);
