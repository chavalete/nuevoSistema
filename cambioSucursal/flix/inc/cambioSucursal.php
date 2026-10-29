<?php
/**
 * Cambio de sucursal sin volver a loguearse.
 *
 * Para habilitar a un usuario, agregar su usuario_id a $USUARIOS_CAMBIO_SUCURSAL.
 * Para ver los ids:  SELECT usuario_id, usuario_nombre FROM datos_usuarios;
 */
$USUARIOS_CAMBIO_SUCURSAL = array(
    // 1,
    // 5,
);

// Mismas sucursales que el combo del login
$SUCURSALES = array(
    2 => 'MORENO',
    3 => 'ROSAS',
    4 => 'LIMONETA',
);

function puedeCambiarSucursal(){
    global $USUARIOS_CAMBIO_SUCURSAL;
    return !empty($_SESSION['usuarioId'])
        && in_array((int)$_SESSION['usuarioId'], $USUARIOS_CAMBIO_SUCURSAL, true);
}
