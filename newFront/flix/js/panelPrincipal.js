// Panel Principal (Tablero) — interacción de la pantalla.
//
// Fase 1 "con vida": los KPIs de arriba, el gráfico de ventas/ganancia,
// el de stock, el de deuda por antigüedad, y las 2 tablas de mayor deuda
// salen de accion=obtenerDatosPanelPrincipal (inc/process.php). El
// drill-down (clickear un KPI, una barra del gráfico, o el chart de
// stock) TODAVÍA es de ejemplo — no hay endpoint de comprobante/producto
// individual todavía, eso queda para una fase siguiente. Por eso las 2
// tablas de deuda no tienen click: mostrar el nombre/monto real pero
// abrir un detalle inventado sería peor que no abrir nada.
//
// Reusa el toggle claro/oscuro que ya existe en el sistema
// (js/themeToggle.js) en vez de uno propio: un MutationObserver detecta
// el cambio de data-theme en <html> y vuelve a pintar los gráficos con
// los colores correctos.
(function(){
    if(!document.getElementById('panelPrincipal')) return;

    const fmtARS = n => n.toLocaleString('es-AR', {style:'currency', currency:'ARS', maximumFractionDigits:0});
    const fmtARSCompact = n => {
        const abs = Math.abs(n);
        if(abs>=1000000) return '$'+(n/1000000).toLocaleString('es-AR',{maximumFractionDigits:1})+'M';
        if(abs>=1000) return '$'+(n/1000).toLocaleString('es-AR',{maximumFractionDigits:0})+'K';
        return fmtARS(n);
    };

    // ---- Estado real (llega de inc/process.php) ----
    let months = [];
    let ventasSerie = [];
    let gananciaSerie = [];
    let estadoStock = { ok:0, warn:0, bad:0 };
    let deudaPorAntiguedad = { cobrar:{vigente:0,proximaAVencer:0,vencida:0}, pagar:{vigente:0,proximaAVencer:0,vencida:0} };

    function renderKpis(kpis){
        document.getElementById('ppKpiVentas').textContent = fmtARS(kpis.ventas || 0);
        document.getElementById('ppKpiGanancia').textContent = fmtARS(kpis.gananciaNeta || 0);
        document.getElementById('ppKpiCobrar').textContent = fmtARS(kpis.cuentasPorCobrar.total || 0);
        document.getElementById('ppKpiCobrarVencida').textContent = fmtARSCompact(kpis.cuentasPorCobrar.vencida || 0);
        document.getElementById('ppKpiCobrarProxima').textContent = fmtARSCompact(kpis.cuentasPorCobrar.proximaAVencer || 0);
        document.getElementById('ppKpiCobrarVigente').textContent = fmtARSCompact(kpis.cuentasPorCobrar.vigente || 0);
        document.getElementById('ppKpiPagar').textContent = fmtARS(kpis.cuentasPorPagar.total || 0);

        const fmtPct = n => n.toLocaleString('es-AR', {minimumFractionDigits:1, maximumFractionDigits:1})+'%';
        document.getElementById('ppKpiMargenBruto').textContent = fmtPct(kpis.margenBruto ?? 0);
        document.getElementById('ppKpiMargenNeto').textContent = fmtPct(kpis.margenNeto ?? 0);

        deudaPorAntiguedad = {
            cobrar: kpis.cuentasPorCobrar,
            pagar: kpis.cuentasPorPagar
        };
    }

    function sparkline(svgId, values, colorVar){
        const svg = document.getElementById(svgId);
        if(!svg || !values.length) return;
        const w=220, h=40, pad=3;
        const min=Math.min(...values), max=Math.max(...values);
        const pts = values.map((v,i)=>{
            const x = pad + (i/((values.length-1)||1))*(w-2*pad);
            const y = h-pad - ((v-min)/((max-min)||1))*(h-2*pad);
            return [x,y];
        });
        const line = pts.map((p,i)=> (i===0?'M':'L')+p[0].toFixed(1)+','+p[1].toFixed(1)).join(' ');
        const area = line + ` L${pts[pts.length-1][0].toFixed(1)},${h} L${pts[0][0].toFixed(1)},${h} Z`;
        const last = pts[pts.length-1];
        const color = getComputedStyle(document.documentElement).getPropertyValue(colorVar).trim();
        svg.innerHTML =
            `<path d="${area}" fill="${color}" opacity="0.12"></path>`+
            `<path d="${line}" fill="none" stroke="${color}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>`+
            `<circle cx="${last[0]}" cy="${last[1]}" r="3" fill="${color}"></circle>`;
    }

    // Sin onclick: todavia no hay endpoint de detalle de comprobante, asi
    // que no simulamos un drill-down sobre datos que si son reales.
    function fillTable(id, rows){
        document.getElementById(id).innerHTML = rows.map(r=>{
            const label = r.estado==='bad' ? 'Vencido' : r.estado==='warn' ? 'Próx. vencer' : 'Al día';
            return `<tr>`+
                `<td class="name">${r.nombre}</td><td class="num">${fmtARS(r.total)}</td>`+
                `<td class="num">${r.vencida? fmtARS(r.vencida) : '—'}</td>`+
                `<td><span class="pill ${r.estado}"><i></i>${label}</span></td></tr>`;
        }).join('') || `<tr><td colspan="4" style="padding:20px 8px; color:var(--text-muted); font-size:.85rem;">Sin datos para este período.</td></tr>`;
    }

    // ---- Carga real de datos ----
    function cargarPanel(fechaDesde, fechaHasta){
        const body = document.getElementById('panelPrincipal');
        body.classList.add('pp-loading');
        $.post('inc/process.php', {
            accion: 'obtenerDatosPanelPrincipal',
            parametros: { fechaDesde, fechaHasta }
        }, function(data){
            body.classList.remove('pp-loading');
            if(!data || data.soyError){
                body.classList.add('pp-error');
                return;
            }
            body.classList.remove('pp-error');
            months = data.serieVentasGanancia.meses;
            ventasSerie = data.serieVentasGanancia.ventas;
            gananciaSerie = data.serieVentasGanancia.ganancia;
            estadoStock = data.estadoStock;
            renderKpis(data.kpis);
            fillTable('ppTblClientes', data.clientesConMayorDeuda);
            fillTable('ppTblProveedores', data.proveedoresConMayorDeuda);
            buildCharts();
        }, 'json').fail(function(){
            body.classList.remove('pp-loading');
            body.classList.add('pp-error');
        });
    }

    // ---- Drill-down de ejemplo (stock/rentabilidad) — fase siguiente ----
    const productosPharma = ['Jeringa descartable 5ml x100', 'Guante nitrilo talle M x50', 'Bracket metálico Roth .022', 'Alcohol en gel 5L', 'Alambre NiTi termoactivado', 'Barbijo quirúrgico x50'];
    function genItems(seed){
        const n = 2 + (seed % 3);
        let items = [];
        for(let i=0;i<n;i++){
            const prod = productosPharma[(seed+i*3) % productosPharma.length];
            const cant = 2 + ((seed+i) % 6);
            const precio = +(800 + ((seed*7+i*53) % 4200)).toFixed(2);
            items.push({ prod, cant, precio, total: +(precio*cant).toFixed(2) });
        }
        return items;
    }

    const productos = [
        {id:'p1', nombre:'Jeringa descartable 5ml x100', codigo:'JD-5100', categoria:'Descartables', stockActual:480, stockMinimo:150, estado:'ok', ubicacion:'Depósito A · Estante 3', ultimoIngreso:'2025-08-22'},
        {id:'p2', nombre:'Guante nitrilo talle M x50', codigo:'GN-M050', categoria:'Descartables', stockActual:60, stockMinimo:120, estado:'warn', ubicacion:'Depósito A · Estante 5', ultimoIngreso:'2025-08-10'},
        {id:'p3', nombre:'Bracket metálico Roth .022', codigo:'BR-R022', categoria:'Ortodoncia', stockActual:0, stockMinimo:40, estado:'bad', ubicacion:'Depósito B · Estante 1', ultimoIngreso:'2025-07-30'},
        {id:'p4', nombre:'Alambre NiTi termoactivado', codigo:'AN-TA14', categoria:'Ortodoncia', stockActual:210, stockMinimo:80, estado:'ok', ubicacion:'Depósito B · Estante 2', ultimoIngreso:'2025-09-01'},
        {id:'p5', nombre:'Alcohol en gel 5L', codigo:'AG-5000', categoria:'Higiene', stockActual:32, stockMinimo:60, estado:'warn', ubicacion:'Depósito A · Estante 1', ultimoIngreso:'2025-08-15'},
        {id:'p6', nombre:'Barbijo quirúrgico x50', codigo:'BQ-050X', categoria:'Descartables', stockActual:340, stockMinimo:100, estado:'ok', ubicacion:'Depósito A · Estante 4', ultimoIngreso:'2025-09-05'},
        {id:'p7', nombre:'Resina compuesta A2', codigo:'RC-A200', categoria:'Odontología', stockActual:0, stockMinimo:25, estado:'bad', ubicacion:'Depósito B · Estante 3', ultimoIngreso:'2025-07-18'},
        {id:'p8', nombre:'Banda ortodóncica molar', codigo:'BM-0044', categoria:'Ortodoncia', stockActual:55, stockMinimo:90, estado:'warn', ubicacion:'Depósito B · Estante 2', ultimoIngreso:'2025-08-28'},
        {id:'p9', nombre:'Aguja dental corta 27G', codigo:'AD-27C', categoria:'Odontología', stockActual:620, stockMinimo:200, estado:'ok', ubicacion:'Depósito A · Estante 2', ultimoIngreso:'2025-09-08'},
        {id:'p10', nombre:'Cemento de vidrio ionómero', codigo:'CV-1020', categoria:'Odontología', stockActual:0, stockMinimo:15, estado:'bad', ubicacion:'Depósito B · Estante 4', ultimoIngreso:'2025-06-30'}
    ];

    const rentabilidadPorFamilia = [
        {nombre:'Ortodoncia', ventas:1420000, ganancia:611000, margen:43.0},
        {nombre:'Descartables', ventas:1840000, ganancia:736000, margen:40.0},
        {nombre:'Odontología', ventas:980000, ganancia:362000, margen:37.0},
        {nombre:'Higiene', ventas:520000, ganancia:187000, margen:36.0}
    ];
    const rentabilidadPorSubfamilia = [
        {nombre:'Brackets y bandas', ventas:780000, ganancia:351000, margen:45.0},
        {nombre:'Guantes y descartables', ventas:980000, ganancia:372000, margen:38.0},
        {nombre:'Alambres', ventas:420000, ganancia:176000, margen:42.0},
        {nombre:'Resinas y cementos', ventas:560000, ganancia:207000, margen:37.0},
        {nombre:'Agujas y jeringas', ventas:420000, ganancia:155000, margen:36.9},
        {nombre:'Alcohol e higiene', ventas:520000, ganancia:187000, margen:36.0},
        {nombre:'Elásticos y accesorios', ventas:220000, ganancia:84000, margen:38.2}
    ];
    const rentabilidadPorProducto = [
        {nombre:'Bracket metálico Roth .022', ventas:480000, ganancia:230000, margen:47.9},
        {nombre:'Alambre NiTi termoactivado', ventas:300000, ganancia:129000, margen:43.0},
        {nombre:'Banda ortodóncica molar', ventas:300000, ganancia:121000, margen:40.3},
        {nombre:'Resina compuesta A2', ventas:340000, ganancia:129000, margen:37.9},
        {nombre:'Barbijo quirúrgico x50', ventas:290000, ganancia:104000, margen:35.9},
        {nombre:'Jeringa descartable 5ml x100', ventas:420000, ganancia:151000, margen:36.0},
        {nombre:'Guante nitrilo talle M x50', ventas:380000, ganancia:133000, margen:35.0},
        {nombre:'Cemento de vidrio ionómero', ventas:220000, ganancia:78000, margen:35.5},
        {nombre:'Alcohol en gel 5L', ventas:220000, ganancia:77000, margen:35.0},
        {nombre:'Aguja dental corta 27G', ventas:200000, ganancia:69000, margen:34.5}
    ];

    window.ppRenderRentabilidad = function(dim){
        const fuentes = { familia:rentabilidadPorFamilia, subfamilia:rentabilidadPorSubfamilia, producto:rentabilidadPorProducto };
        const etiquetas = { familia:'Familia', subfamilia:'Subfamilia', producto:'Producto' };
        const plural = { familia:'familias', subfamilia:'subfamilias', producto:'productos' };
        const rows = fuentes[dim].slice().sort((a,b)=>b.ganancia-a.ganancia);
        document.getElementById('ppDrawerTitle').textContent = 'Rentabilidad';
        document.getElementById('ppDrawerSubtitle').textContent = `Por ${etiquetas[dim].toLowerCase()} · ${rows.length} ${plural[dim]} (ejemplo)`;
        const switchHtml = `<div class="pp-dim-switch">` + Object.keys(etiquetas).map(k=>
            `<button type="button" class="${k===dim?'active':''}" onclick="ppRenderRentabilidad('${k}')">${etiquetas[k]}</button>`
        ).join('') + `</div>`;
        const rowsHtml = rows.map(r=>
            `<div class="pp-drawer-row static">`+
                `<div><div class="who">${r.nombre}</div><div class="meta">Ventas: ${fmtARSCompact(r.ventas)}</div></div>`+
                `<div class="amt">${r.margen.toFixed(1)}%<span class="days" style="color:var(--text-muted)">Ganancia ${fmtARSCompact(r.ganancia)}</span></div>`+
            `</div>`
        ).join('');
        document.getElementById('ppDrawerBody').innerHTML = switchHtml + rowsHtml;
    };

    window.ppOpenDrawer = function(tipo, estadoFiltro){
        if(tipo==='rentabilidad'){
            ppRenderRentabilidad('familia');
            document.getElementById('ppScrim').classList.add('open');
            document.getElementById('ppDrawer').classList.add('open');
            return;
        }
        if(tipo==='stock'){
            const nombres = { ok:'Stock óptimo', warn:'Stock bajo', bad:'Agotados' };
            const rows = productos.filter(p=>p.estado===estadoFiltro);
            document.getElementById('ppDrawerTitle').textContent = 'Estado de stock';
            document.getElementById('ppDrawerSubtitle').textContent = `${nombres[estadoFiltro]} · ${rows.length} producto${rows.length===1?'':'s'} (ejemplo)`;
            document.getElementById('ppDrawerBody').innerHTML = rows.map(p=>
                `<div class="pp-drawer-row" onclick="ppOpenProducto('${p.id}')">`+
                    `<div><div class="who">${p.nombre}</div><div class="meta">${p.codigo} · ${p.categoria}</div></div>`+
                    `<div class="amt">${p.stockActual} u.<span class="days" style="color:var(--text-muted)">mín. ${p.stockMinimo} u.</span></div>`+
                `</div>`
            ).join('') || `<div style="padding:20px 8px; color:var(--text-muted); font-size:.85rem;">Sin productos en esta categoría.</div>`;
            document.getElementById('ppScrim').classList.add('open');
            document.getElementById('ppDrawer').classList.add('open');
            return;
        }
        // cobrar/pagar por antiguedad: montos reales (deudaPorAntiguedad),
        // pero todavia sin el listado de comprobantes detras de cada barra.
        document.getElementById('ppDrawerTitle').textContent = tipo==='cobrar' ? 'Cuentas por cobrar' : 'Cuentas por pagar';
        const nombresEstado = { bad:'Vencidas', warn:'Próximas a vencer', ok:'Vigentes' };
        document.getElementById('ppDrawerSubtitle').textContent = `${nombresEstado[estadoFiltro] || 'Todas'} — detalle por comprobante próximamente`;
        document.getElementById('ppDrawerBody').innerHTML = `<div style="padding:20px 8px; color:var(--text-muted); font-size:.85rem;">El detalle por comprobante todavía no está conectado.</div>`;
        document.getElementById('ppScrim').classList.add('open');
        document.getElementById('ppDrawer').classList.add('open');
    };
    window.ppCloseDrawer = function(){
        document.getElementById('ppScrim').classList.remove('open');
        document.getElementById('ppDrawer').classList.remove('open');
    };

    window.ppOpenProducto = function(id){
        const p = productos.find(x=>x.id===id);
        if(!p) return;
        document.getElementById('ppModalTitle').textContent = p.nombre;
        document.getElementById('ppModalSubtitle').textContent = 'Código: ' + p.codigo + ' (ejemplo)';
        document.getElementById('ppModalKv').innerHTML = [
            ['Categoría', p.categoria], ['Ubicación', p.ubicacion],
            ['Stock actual', p.stockActual+' unidades'], ['Stock mínimo', p.stockMinimo+' unidades'],
            ['Último ingreso', p.ultimoIngreso], ['Estado', p.estado==='bad'?'Agotado':(p.estado==='warn'?'Stock bajo':'Óptimo')]
        ].map(([k,v])=>`<div><span class="k">${k}</span><span class="v">${v}</span></div>`).join('');
        document.getElementById('ppModalItemsWrap').hidden = true;
        document.getElementById('ppModalScrim').classList.add('open');
    };

    window.ppCloseInvoice = function(){ document.getElementById('ppModalScrim').classList.remove('open'); };

    document.addEventListener('keydown', e=>{
        if(e.key==='Escape'){ ppCloseInvoice(); ppCloseDrawer(); }
    });

    const reduceMotion = matchMedia('(prefers-reduced-motion: reduce)').matches;
    let chartRevenue, chartStock, chartDebt;

    function chartColors(){
        const cs = getComputedStyle(document.documentElement);
        return {
            accent: cs.getPropertyValue('--accent').trim(),
            success: cs.getPropertyValue('--success').trim(),
            warning: cs.getPropertyValue('--warning').trim(),
            critical: cs.getPropertyValue('--critical').trim(),
            series2: cs.getPropertyValue('--series-2').trim(),
            text: cs.getPropertyValue('--text-muted').trim(),
            grid: cs.getPropertyValue('--grid-line').trim(),
            surface: cs.getPropertyValue('--surface').trim(),
            border: cs.getPropertyValue('--border').trim()
        };
    }

    function buildCharts(){
        if(typeof Chart === 'undefined') return;
        const c = chartColors();
        const baseFont = { family:"'Public Sans', sans-serif", size:11, weight:'600' };
        const tooltip = { backgroundColor:c.surface, titleColor:c.text, bodyColor:c.text, borderColor:c.border, borderWidth:1, padding:10, displayColors:true };

        if(chartRevenue) chartRevenue.destroy();
        chartRevenue = new Chart(document.getElementById('ppChartRevenue'), {
            type:'line',
            data:{ labels:months, datasets:[
                { label:'Ventas', data:ventasSerie, borderColor:c.accent, backgroundColor:c.accent+'22', fill:true, tension:.35, pointRadius:0, pointHoverRadius:4, borderWidth:2.5 },
                { label:'Ganancia neta', data:gananciaSerie, borderColor:c.success, backgroundColor:c.success+'22', fill:true, tension:.35, pointRadius:0, pointHoverRadius:4, borderWidth:2.5 }
            ]},
            options:{ responsive:true, maintainAspectRatio:false, animation: reduceMotion?false:undefined,
                plugins:{ legend:{ position:'bottom', labels:{ color:c.text, font:baseFont, boxWidth:10, usePointStyle:true } },
                    tooltip:{ ...tooltip, callbacks:{ label: ctx => ctx.dataset.label+': '+ctx.parsed.y.toLocaleString('es-AR',{style:'currency',currency:'ARS',maximumFractionDigits:0}) } } },
                scales:{ x:{ grid:{ color:c.grid }, ticks:{ color:c.text, font:baseFont } },
                    y:{ grid:{ color:c.grid }, ticks:{ color:c.text, font:baseFont, callback:v=>'$'+(v/1000).toFixed(0)+'k' } } }
            }
        });

        if(chartStock) chartStock.destroy();
        chartStock = new Chart(document.getElementById('ppChartStock'), {
            type:'doughnut',
            data:{ labels:['Óptimo','Bajo','Agotado'], datasets:[{ data:[estadoStock.ok||0, estadoStock.warn||0, estadoStock.bad||0], backgroundColor:[c.success,c.warning,c.critical], borderColor:c.surface, borderWidth:3 }] },
            options:{ responsive:true, maintainAspectRatio:false, animation: reduceMotion?false:undefined, cutout:'68%',
                onHover:(evt,els)=>{ evt.native.target.style.cursor = els.length ? 'pointer' : 'default'; },
                onClick:(evt,els)=>{ if(!els.length) return; ppOpenDrawer('stock', ['ok','warn','bad'][els[0].index]); },
                plugins:{ legend:{ position:'bottom', labels:{ color:c.text, font:baseFont, boxWidth:10, usePointStyle:true } },
                    tooltip:{ ...tooltip, callbacks:{ label: ctx => ctx.label+': '+ctx.parsed+'%' } } } }
        });

        if(chartDebt) chartDebt.destroy();
        chartDebt = new Chart(document.getElementById('ppChartDebt'), {
            type:'bar',
            data:{ labels:['Vigente','Próxima a vencer','Vencida'],
                datasets:[
                    { label:'Por cobrar', data:[deudaPorAntiguedad.cobrar.vigente||0, deudaPorAntiguedad.cobrar.proximaAVencer||0, deudaPorAntiguedad.cobrar.vencida||0], backgroundColor:c.accent, borderRadius:6, borderSkipped:false },
                    { label:'Por pagar', data:[deudaPorAntiguedad.pagar.vigente||0, deudaPorAntiguedad.pagar.proximaAVencer||0, deudaPorAntiguedad.pagar.vencida||0], backgroundColor:c.series2, borderRadius:6, borderSkipped:false }
                ]},
            options:{ responsive:true, maintainAspectRatio:false, animation: reduceMotion?false:undefined,
                onHover:(evt,els)=>{ evt.native.target.style.cursor = els.length ? 'pointer' : 'default'; },
                onClick:(evt,els)=>{
                    if(!els.length) return;
                    const el = els[0];
                    ppOpenDrawer(el.datasetIndex===0 ? 'cobrar' : 'pagar', ['ok','warn','bad'][el.index]);
                },
                plugins:{ legend:{ position:'bottom', labels:{ color:c.text, font:baseFont, boxWidth:10, usePointStyle:true } },
                    tooltip:{ ...tooltip, callbacks:{ label: ctx => ctx.dataset.label+': '+ctx.parsed.y.toLocaleString('es-AR',{style:'currency',currency:'ARS',maximumFractionDigits:0}) } } },
                scales:{ x:{ grid:{ display:false }, ticks:{ color:c.text, font:baseFont } },
                    y:{ grid:{ color:c.grid }, ticks:{ color:c.text, font:baseFont, callback:v=>'$'+(v/1000).toFixed(0)+'k' } } }
            }
        });

        sparkline('ppSparkVentas', ventasSerie, '--accent');
        sparkline('ppSparkGanancia', gananciaSerie, '--success');
    }

    function rangoParaPreset(preset){
        const hoy = new Date();
        const pad = n => String(n).padStart(2,'0');
        const iso = d => `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}`;
        if(preset === '7d'){
            const desde = new Date(hoy); desde.setDate(desde.getDate()-6);
            return [iso(desde), iso(hoy)];
        }
        if(preset === 'anio'){
            return [`${hoy.getFullYear()}-01-01`, iso(hoy)];
        }
        // 'mes' (default)
        const desde = new Date(hoy.getFullYear(), hoy.getMonth(), 1);
        return [iso(desde), iso(hoy)];
    }

    window.ppSetPreset = function(preset, btn){
        document.querySelectorAll('#panelPrincipal .range-chip').forEach(b=>b.classList.remove('active'));
        btn.classList.add('active');
        const [desde, hasta] = rangoParaPreset(preset);
        document.getElementById('ppDateFrom').value = desde;
        document.getElementById('ppDateTo').value = hasta;
        const labels = { '7d':'Últimos 7 días', 'mes':'Este mes', 'anio':'Este año' };
        document.getElementById('ppRangeLabel').textContent = `${labels[preset]} · ${desde} – ${hasta}`;
        cargarPanel(desde, hasta);
    };

    // El toggle claro/oscuro real (#themeToggle, js/themeToggle.js) no dispara
    // ningún evento propio: observamos el atributo data-theme del <html> para
    // repintar los gráficos con los colores del tema que quedó activo.
    new MutationObserver(buildCharts).observe(document.documentElement, { attributes:true, attributeFilter:['data-theme'] });

    // Carga inicial: el preset "mes" ya viene marcado activo en el HTML.
    const [desdeInicial, hastaInicial] = rangoParaPreset('mes');
    document.getElementById('ppDateFrom').value = desdeInicial;
    document.getElementById('ppDateTo').value = hastaInicial;
    cargarPanel(desdeInicial, hastaInicial);
})();
