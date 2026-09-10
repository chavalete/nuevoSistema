<div id='header'>
    <div class="arriba">
        <div id="logo" class='flotar-izquierda'>
    <?php
        $esLimoneta = (isset($_SESSION['sistemaNombre']) && strtolower(trim($_SESSION['sistemaNombre'])) === 'limoneta');
        $logo   = $esLimoneta ? 'img/limoneta2.png' : 'img/limon.jpeg';
        $width  = $esLimoneta ? '140' : '100'; // Ajustá el tamaño a gusto
        $height = $esLimoneta ? '40'  : '35';
    ?>
    <img src="<?php echo $logo; ?>" alt="Dmelmac" title="Dmelmac" width="<?php echo $width; ?>" height="<?php echo $height; ?>" />

    <?php if (!$esLimoneta): ?>
        <span><?php echo $_SESSION['sistemaNombre']; ?></span>
    <?php endif; ?>
    </div>
        <div id="barra-busqueda" class='flotar-derecha'>
            <label for="valor">Valor</label>
            <input type="text" id="valor" name="valor" />
            <label for="tipo">Tipo</label>
            <select id="tipo" name="tipo">
                <option value="consultaStock">Consulta Stock</option>
                <option value="salidas">Salidas</option>
            </select>
            <input type="button" name="buscar" id="buscar" value="buscar" class="buscar-btn" />
        </div>
    </div>
    <div class='abajo'>
        <div id="contenedor-botonera">
            <?php include('header-menu.php'); ?>

            <div class="logout-contenedor">
                <a href="login.php?crDestruir=1" title="Logout" alt="Logout" class="logout flotar-derecha">
                    <img src="img/logout-btn.png" width="28" height="27" alt="Logout" title="Logout" border="0" />
                </a>
                <p class="logout-usuario flotar-derecha">
                    <?php echo $_SESSION['usuarioNombre']; ?>
                </p>
            </div>
        </div>
    </div>
</div>

<?php include('avisoPrecios.php'); ?>
