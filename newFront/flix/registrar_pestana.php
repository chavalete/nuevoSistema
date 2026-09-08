<?php
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['sistema'])) {
    echo json_encode(['status' => 'error', 'es_valida' => false, 'msg' => 'Sin sesion activa']);
    exit();
}

if (isset($_POST['pestana_id'])) {
    $pestanaId = $_POST['pestana_id'];

    // Si no hay pestaña registrada en el servidor para esta sesión, la asignamos
    if (!isset($_SESSION['pestana_activa'])) {
        $_SESSION['pestana_activa'] = $pestanaId;
        echo json_encode(['status' => 'ok', 'es_valida' => true]);
        exit();
    }

    // Si la pestaña enviada coincide con la registrada en la sesión
    if ($_SESSION['pestana_activa'] === $pestanaId) {
        echo json_encode(['status' => 'ok', 'es_valida' => true]);
        exit();
    }

    // Si no coincide, hay otra pestaña intentando usar la sesión
    echo json_encode(['status' => 'bloqueado', 'es_valida' => false]);
    exit();
}

echo json_encode(['status' => 'error', 'es_valida' => false]);
?>
