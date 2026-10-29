                <?php require_once __DIR__ . '/../inc/cambioSucursal.php'; ?>
                <?php if (puedeCambiarSucursal()): ?>
                <form method="post" action="cambiarSucursal.php" class="flotar-derecha" style="margin-right:10px;">
                    <select name="sucursalId" onchange="this.form.submit()">
                        <?php foreach ($SUCURSALES as $id => $nombre): ?>
                        <option value="<?php echo $id; ?>" <?php if ($id == $_SESSION['sucursalId']) echo 'selected'; ?>><?php echo $nombre; ?></option>
                        <?php endforeach; ?>
                    </select>
                </form>
                <?php endif; ?>
