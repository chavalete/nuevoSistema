<?php
// Aviso de precios actualizados — pantalla aislada, no pasa por
// inc/process.php ni por frenteDeFrentes. Solo lee lista_precios_10 y
// la compara contra las confirmaciones del usuario logueado.
//
// Dos reglas:
// - Solo se consulta/muestra a usuarios dados de alta en
//   aviso_precios_usuarios (no es para todo el mundo).
// - La consulta a la base se cachea en sesion por 1 hora, para no
//   pegarle a la base en cada carga de pantalla. Al confirmar
//   (avisoPrecios_confirmar.php) se fuerza un chequeo fresco.
require_once '/var/www/html/limonLocal/libreria/almacenamiento/miPDO.php';

define('AVISO_PRECIOS_TTL_SEGUNDOS', 3600);

$avisoPreciosPendientes = array();
try {
    $usuarioId = isset($_SESSION['usuarioId']) ? $_SESSION['usuarioId'] : null;
    $cacheVencido = !isset($_SESSION['avisoPreciosUltimoCheck'])
        || (time() - $_SESSION['avisoPreciosUltimoCheck']) >= AVISO_PRECIOS_TTL_SEGUNDOS;

    if ($usuarioId && $cacheVencido) {
        $db = new miPDO('dmelmac', '/var/www/html/limonLocal/libreria/almacenamiento/almacenamiento.ini');

        $stmtSusc = $db->prepare("SELECT 1 FROM aviso_precios_usuarios WHERE usuario_id = :usuarioId");
        $stmtSusc->execute(array(':usuarioId' => $usuarioId));

        if ($stmtSusc->fetchColumn()) {
            // Si sumas la columna de "Precio x Caja" a la consulta, llamala
            // producto_pxcaja (o ajusta el nombre abajo, en el foreach) para
            // que la tercera columna de la tabla la muestre.
            $stmt = $db->prepare(
                "SELECT lp.producto_id, dp.producto_nombre, dp.producto_presentacion, lp.producto_pventa
                 FROM lista_precios_10 lp
                 JOIN datos_productos dp ON dp.producto_id = lp.producto_id
                 LEFT JOIN precio_confirmaciones pc
                        ON pc.producto_id = lp.producto_id AND pc.usuario_id = :usuarioId
                 WHERE lp.fecha_actualizacion IS NOT NULL
                   AND (pc.fecha_confirmado IS NULL OR pc.fecha_confirmado < lp.fecha_actualizacion)
                 ORDER BY lp.fecha_actualizacion DESC"
            );
            $stmt->execute(array(':usuarioId' => $usuarioId));
            $avisoPreciosPendientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        $_SESSION['avisoPreciosPendientes'] = $avisoPreciosPendientes;
        $_SESSION['avisoPreciosUltimoCheck'] = time();
    } elseif (isset($_SESSION['avisoPreciosPendientes'])) {
        $avisoPreciosPendientes = $_SESSION['avisoPreciosPendientes'];
    }
} catch (Exception $e) {
    // Si falla la consulta (por ejemplo, todavia no corriste el ALTER/CREATE
    // en la base), no mostramos el aviso en vez de romper la pantalla.
    $avisoPreciosPendientes = array();
}
?>
<?php if (!empty($avisoPreciosPendientes)): ?>
<style>
    .aviso-precios-banner{
        display:flex; align-items:center; gap:12px; flex-wrap:wrap;
        width:1200px; margin:0 auto; box-sizing:border-box;
        background:#e7f1fb; border-bottom:3px solid #0d5aa7; padding:10px 20px;
        font-family:Tahoma, Verdana, Arial, sans-serif;
    }
    .aviso-precios-banner .ico{
        flex:none; width:26px; height:26px; border-radius:50%; background:#0d5aa7; color:#fff;
        display:flex; align-items:center; justify-content:center; font-weight:bold; font-size:13px;
    }
    .aviso-precios-banner .msg{ flex:1; min-width:220px; font-size:13px; color:#00335e; }
    .aviso-precios-banner .msg b{ font-size:14px; }
    .aviso-precios-banner button{
        flex:none; background:#0d5aa7; color:#fff; border:none; border-radius:4px; font-weight:bold;
        padding:8px 16px; font-size:12px; cursor:pointer; font-family:inherit;
    }
    .aviso-precios-banner button:hover{ background:#0a4886; }
    .aviso-precios-banner.hidden{ display:none; }

    .aviso-precios-scrim{ position:fixed; inset:0; background:rgba(0,0,0,.55); opacity:0; pointer-events:none; transition:opacity .15s; z-index:9500; }
    .aviso-precios-scrim.open{ opacity:1; pointer-events:auto; }
    .aviso-precios-box{
        position:fixed; top:50%; left:50%; transform:translate(-50%,-46%); width:min(460px,92vw);
        background:#fff; border-radius:6px; box-shadow:0 0 20px rgba(0,0,0,.5); z-index:9600;
        opacity:0; pointer-events:none; transition:opacity .15s, transform .15s;
        font-family:Tahoma, Verdana, Arial, sans-serif;
    }
    .aviso-precios-box.open{ opacity:1; pointer-events:auto; transform:translate(-50%,-50%); }
    .aviso-precios-head{ background:#4199C2; color:#fff; padding:12px 18px; border-radius:6px 6px 0 0; font-weight:bold; font-size:14px; position:relative; }
    .aviso-precios-head span{ display:block; font-weight:normal; font-size:11px; opacity:.85; margin-top:2px; }
    .aviso-precios-close{ position:absolute; top:8px; right:10px; background:none; border:none; color:#fff; font-size:16px; cursor:pointer; }
    .aviso-precios-body{ padding:6px 18px; max-height:340px; overflow-y:auto; }
    .aviso-precios-cols{
        display:grid; grid-template-columns:1fr auto auto; gap:10px; align-items:end;
        padding:0 0 6px; border-bottom:1px solid #9db3c2;
    }
    .aviso-precios-cols span{ font-size:10px; font-weight:bold; color:#5a7386; text-transform:uppercase; letter-spacing:.3px; }
    .aviso-precios-cols span.col-precio, .aviso-precios-cols span.col-caja{ text-align:right; white-space:nowrap; }
    .aviso-precios-row{ display:grid; grid-template-columns:1fr auto auto; align-items:center; gap:10px; padding:10px 0; border-bottom:1px solid #c9d6de; font-size:12px; color:#00335e; }
    .aviso-precios-row .nombre{ font-size:13px; }
    .aviso-precios-row .presentacion{ display:block; font-size:11px; color:#6b7f8c; font-weight:normal; }
    .aviso-precios-row .precio, .aviso-precios-row .precio-caja{ font-weight:bold; font-size:13px; white-space:nowrap; text-align:right; }
    .aviso-precios-row .precio-caja{ color:#5a7386; font-size:12px; }
    .aviso-precios-foot{ padding:14px 18px 18px; }
    .aviso-precios-confirm{ width:100%; background:#2a8f4f; color:#fff; border:none; border-radius:4px; padding:10px; font-weight:bold; font-size:13px; cursor:pointer; font-family:inherit; }
    .aviso-precios-confirm:hover{ background:#237a42; }
    .aviso-precios-confirm:disabled{ opacity:.6; cursor:default; }
    .aviso-precios-toast{
        position:fixed; bottom:20px; left:50%; transform:translateX(-50%) translateY(15px); background:#2a8f4f; color:#fff;
        padding:10px 16px; border-radius:5px; font-size:13px; font-weight:bold; opacity:0; pointer-events:none;
        transition:opacity .2s, transform .2s; z-index:9700; font-family:Tahoma, Verdana, Arial, sans-serif;
    }
    .aviso-precios-toast.show{ opacity:1; transform:translateX(-50%) translateY(0); }
</style>

<div class="aviso-precios-banner" id="avisoPreciosBanner">
    <div class="ico">$</div>
    <div class="msg">
        <b>Se actualizaron precios</b> —
        <?=count($avisoPreciosPendientes)?> producto<?=count($avisoPreciosPendientes) == 1 ? '' : 's'?>
        con precio nuevo, todavía no los confirmaste
    </div>
    <button type="button" onclick="avisoPreciosAbrir()">Ver y confirmar</button>
</div>

<div class="aviso-precios-scrim" id="avisoPreciosScrim" onclick="avisoPreciosCerrar()"></div>
<div class="aviso-precios-box" id="avisoPreciosBox" role="dialog" aria-label="Precios actualizados">
    <div class="aviso-precios-head">
        Precios actualizados
        <span><?=count($avisoPreciosPendientes)?> producto<?=count($avisoPreciosPendientes) == 1 ? '' : 's'?></span>
        <button type="button" class="aviso-precios-close" onclick="avisoPreciosCerrar()" aria-label="Cerrar">✕</button>
    </div>
    <div class="aviso-precios-body">
        <div class="aviso-precios-cols">
            <span class="col-producto">Producto</span>
            <span class="col-precio">Precio</span>
            <span class="col-caja">Precio x Caja</span>
        </div>
        <?php foreach ($avisoPreciosPendientes as $p): ?>
        <div class="aviso-precios-row" data-producto-id="<?=(int)$p['producto_id']?>">
            <span class="nombre">
                <?=htmlspecialchars($p['producto_nombre'])?>
                <span class="presentacion"><?=htmlspecialchars($p['producto_presentacion'])?></span>
            </span>
            <span class="precio">$ <?=number_format((float)$p['producto_pventa'], 2, ',', '.')?></span>
            <span class="precio-caja"><?=isset($p['producto_pxcaja']) ? '$ ' . number_format((float)$p['producto_pxcaja'], 2, ',', '.') : '—'?></span>
        </div>
        <?php endforeach; ?>
    </div>
    <div class="aviso-precios-foot">
        <button type="button" class="aviso-precios-confirm" id="avisoPreciosConfirmBtn" onclick="avisoPreciosConfirmar()">Confirmar que vi los precios nuevos</button>
    </div>
</div>

<div class="aviso-precios-toast" id="avisoPreciosToast">✓ Confirmado — gracias</div>
<?php endif; ?>
