<?php include('inc/master_header.php');

if($_SESSION['sistema'] !="limonMoreno"){

    session_destroy();
    header('Location: login.php');
}

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
        <!-- Incluyo los css y js de la seccion (los mismos que index.php) -->
        <?=Includes::get('common', 'css')?>
        <?=Includes::get('common', 'js')?>
        <link rel="stylesheet" href="css/panelPrincipal.css" />
        <!-- Chart.js: sirve los gráficos del panel, no se usa en ningún otro lado del sistema -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.js"></script>
        <script type="text/javascript">
            var accion = '<?=ParseINI::getConfig('defaults_js', 'accion')?>';
            var parametros = {
                ordenarOrden : '',
                ordenarPor   : '',
                pagina       : '<?=ParseINI::getConfig('defaults_js', 'pagina')?>',
                porPagina    : '<?=ParseINI::getConfig('defaults_js', 'porPagina')?>',
                tipo         : 'principal'
            };
            var accion_aux = accion;
            window.urlServices = 'http://<?=$_SERVER['HTTP_HOST']?>/limonMoreno/';

            window.allSeccionData = <?=ParseINI::getAllJson();?>;
        </script>
    </head>

    <body>

        <div id="main">
            <?php include('tpl/header.php'); ?>

            <div id="flix_content">
                <div id="flix_titulo">Principal</div>

                <div id="panelPrincipal">

                    <span class="example-badge">Datos de ejemplo — todavía sin conectar a la base real</span>

                    <div class="range">
                        <button type="button" class="range-chip" data-preset="7d" onclick="ppSetPreset('7d', this)">7 días</button>
                        <button type="button" class="range-chip active" data-preset="mes" onclick="ppSetPreset('mes', this)">Este mes</button>
                        <button type="button" class="range-chip" data-preset="anio" onclick="ppSetPreset('anio', this)">Este año</button>
                        <input type="date" id="ppDateFrom" value="2025-01-01" />
                        <span style="color:var(--text-muted); font-size:.78rem;">→</span>
                        <input type="date" id="ppDateTo" value="2025-09-10" />
                    </div>

                    <div class="section">
                        <div class="section-head">
                            <span class="section-title">Ventas y Rentabilidad</span>
                            <span class="section-note" id="ppRangeLabel">Este mes · 1 – 30 sep 2025</span>
                        </div>
                        <div class="kpi-grid">
                            <div class="kpi">
                                <div class="kpi-top"><span class="kpi-label">Ventas del período</span></div>
                                <div class="kpi-value" id="ppKpiVentas">$ 0</div>
                                <div class="kpi-delta up">↑ 12,4% vs período anterior</div>
                                <svg class="kpi-spark" id="ppSparkVentas" viewBox="0 0 220 40" preserveAspectRatio="none"></svg>
                            </div>
                            <div class="kpi">
                                <div class="kpi-top kpi-top-click" onclick="ppOpenDrawer('rentabilidad')" title="Ver rentabilidad por familia, subfamilia o producto">
                                    <span class="kpi-label">Rentabilidad</span>
                                </div>
                                <div class="kpi-value" id="ppKpiGanancia">$ 0</div>
                                <div class="kpi-delta up">↑ 18,2% vs período anterior</div>
                                <svg class="kpi-spark" id="ppSparkGanancia" viewBox="0 0 220 40" preserveAspectRatio="none"></svg>
                                <div class="kpi-submetrics">
                                    <div class="kpi-submetric">
                                        <span class="kpi-sub-label">Margen bruto</span>
                                        <span class="kpi-sub-value">42,3%</span>
                                        <span class="kpi-sub-delta up">↑ 1,5 pts</span>
                                    </div>
                                    <div class="kpi-submetric">
                                        <span class="kpi-sub-label">Margen neto</span>
                                        <span class="kpi-sub-value">28,5%</span>
                                        <span class="kpi-sub-delta down">↓ 2,1 pts</span>
                                    </div>
                                </div>
                            </div>
                            <div class="kpi">
                                <div class="kpi-top kpi-top-click" onclick="ppOpenDrawer('cobrar')" title="Ver todos los comprobantes">
                                    <span class="kpi-label">Cuentas por cobrar</span>
                                </div>
                                <div class="kpi-value" id="ppKpiCobrar">$ 0</div>
                                <div class="kpi-delta up">↓ 3,2% vs período anterior</div>
                                <div class="kpi-submetrics">
                                    <div class="kpi-submetric clickable" onclick="ppOpenDrawer('cobrar','bad')" title="Ver facturas vencidas">
                                        <span class="kpi-sub-label">Vencida</span>
                                        <span class="kpi-sub-value" id="ppKpiCobrarVencida">$ 0</span>
                                        <span class="kpi-sub-delta down">↑ 8,1%</span>
                                    </div>
                                    <div class="kpi-submetric clickable" onclick="ppOpenDrawer('cobrar','warn')" title="Ver próximas a vencer">
                                        <span class="kpi-sub-label">Próx. a vencer</span>
                                        <span class="kpi-sub-value" id="ppKpiCobrarProxima">$ 0</span>
                                        <span class="kpi-sub-delta up">↑ 5,3%</span>
                                    </div>
                                    <div class="kpi-submetric clickable" onclick="ppOpenDrawer('cobrar','ok')" title="Ver vigentes">
                                        <span class="kpi-sub-label">Vigente</span>
                                        <span class="kpi-sub-value" id="ppKpiCobrarVigente">$ 0</span>
                                        <span class="kpi-sub-delta up">↓ 1,8%</span>
                                    </div>
                                </div>
                            </div>
                            <div class="kpi">
                                <div class="kpi-top"><span class="kpi-label">Cuentas por pagar</span></div>
                                <div class="kpi-value" id="ppKpiPagar">$ 0</div>
                                <div class="kpi-bars">
                                    <span style="width:18%; background:var(--critical)" onclick="ppOpenDrawer('pagar','bad')" title="Ver facturas vencidas"></span>
                                    <span style="width:57%; background:var(--warning)" onclick="ppOpenDrawer('pagar','warn')" title="Ver próximas a vencer"></span>
                                    <span style="width:25%; background:var(--success)" onclick="ppOpenDrawer('pagar','ok')" title="Ver vigentes"></span>
                                </div>
                                <div class="kpi-legend">
                                    <span><i style="background:var(--critical)"></i>Vencida</span>
                                    <span><i style="background:var(--warning)"></i>Próx. a vencer</span>
                                    <span><i style="background:var(--success)"></i>Vigente</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="section charts-row">
                        <div class="panel">
                            <h3><span class="dot"></span>Ventas vs. ganancia neta — últimos 12 meses</h3>
                            <div class="chart-box"><canvas id="ppChartRevenue"></canvas></div>
                        </div>
                        <div class="panel">
                            <h3><span class="dot"></span>Estado de stock <span style="font-weight:500; color:var(--text-muted); font-size:.72rem; margin-left:4px;">· click para ver detalle</span></h3>
                            <div class="chart-box small"><canvas id="ppChartStock"></canvas></div>
                        </div>
                    </div>

                    <div class="section">
                        <div class="panel">
                            <h3><span class="dot"></span>Por cobrar vs. por pagar, por antigüedad <span style="font-weight:500; color:var(--text-muted); font-size:.72rem; margin-left:4px;">· click en una barra para ver detalle</span></h3>
                            <div class="chart-box small"><canvas id="ppChartDebt"></canvas></div>
                        </div>
                    </div>

                    <div class="section tables-row">
                        <div class="panel">
                            <h3>Clientes con mayor deuda</h3>
                            <div class="table-wrap">
                                <table>
                                    <thead><tr><th>Cliente</th><th class="num">Total</th><th class="num">Vencida</th><th>Estado</th></tr></thead>
                                    <tbody id="ppTblClientes"></tbody>
                                </table>
                            </div>
                        </div>
                        <div class="panel">
                            <h3>Proveedores con mayor deuda</h3>
                            <div class="table-wrap">
                                <table>
                                    <thead><tr><th>Proveedor</th><th class="num">Total</th><th class="num">Vencida</th><th>Estado</th></tr></thead>
                                    <tbody id="ppTblProveedores"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="foot">Vista previa — el selector de fecha todavía no recalcula nada, y los datos de arriba son de ejemplo.</div>

                </div>

                <div id="flix_grid"></div>
            </div>

            <?php include('tpl/footer.php'); ?>
        </div>

        <!-- Drill-down nivel 1: panel lateral con el listado filtrado -->
        <div class="pp-scrim" id="ppScrim" onclick="ppCloseDrawer()"></div>
        <div class="pp-drawer" id="ppDrawer" role="dialog" aria-label="Detalle filtrado">
            <div class="pp-drawer-head">
                <div>
                    <h4 id="ppDrawerTitle">—</h4>
                    <p id="ppDrawerSubtitle">—</p>
                </div>
                <button type="button" class="pp-drawer-close" onclick="ppCloseDrawer()" aria-label="Cerrar">✕</button>
            </div>
            <div class="pp-drawer-body" id="ppDrawerBody"></div>
        </div>

        <!-- Drill-down nivel 2: comprobante/producto individual -->
        <div class="pp-modal-scrim" id="ppModalScrim" onclick="if(event.target===this) ppCloseInvoice()">
            <div class="pp-modal-box">
                <div class="pp-modal-head">
                    <div>
                        <h4 id="ppModalTitle">—</h4>
                        <p id="ppModalSubtitle">—</p>
                    </div>
                    <button type="button" class="pp-modal-close" onclick="ppCloseInvoice()" aria-label="Cerrar">✕</button>
                </div>
                <div class="pp-modal-kv" id="ppModalKv"></div>
                <div class="pp-modal-items" id="ppModalItemsWrap">
                    <table>
                        <thead><tr><th>Producto</th><th class="num">Cant.</th><th class="num">Precio Unit.</th><th class="num">Precio Total</th></tr></thead>
                        <tbody id="ppModalItems"></tbody>
                    </table>
                </div>
            </div>
        </div>

        <script src="js/panelPrincipal.js"></script>
    </body>
</html>
