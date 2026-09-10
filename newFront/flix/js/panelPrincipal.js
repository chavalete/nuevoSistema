// Panel Principal (Tablero) — interacción de la pantalla. Datos de ejemplo
// por ahora (nada de esto llama a inc/process.php todavía). Reusa el toggle
// claro/oscuro que ya existe en el sistema (js/themeToggle.js) en vez de uno
// propio: un MutationObserver detecta el cambio de data-theme en <html> y
// vuelve a pintar los gráficos con los colores correctos.
(function(){
    if(!document.getElementById('panelPrincipal')) return;

    const fmtARS = n => n.toLocaleString('es-AR', {style:'currency', currency:'ARS', maximumFractionDigits:0});
    const fmtARSCompact = n => {
        const abs = Math.abs(n);
        if(abs>=1000000) return '$'+(n/1000000).toLocaleString('es-AR',{maximumFractionDigits:1})+'M';
        if(abs>=1000) return '$'+(n/1000).toLocaleString('es-AR',{maximumFractionDigits:0})+'K';
        return fmtARS(n);
    };
    const months = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
    const ventasSerie   = [31.2,33.8,36.1,38.9,41.4,44.0,42.1,45.6,48.9,52.3,55.8,58.4].map(v=>v*100000);
    const gananciaSerie = [8.9,9.7,10.5,11.3,12.1,12.9,12.0,13.4,14.8,16.1,17.6,18.9].map(v=>v*100000);

    const clientes = [
        {n:'Farmacia San Martín', total:2140000, vencida:1180000, estado:'bad'},
        {n:'Distribuidora Sur Dental', total:1860000, vencida:640000, estado:'warn'},
        {n:'Óptica y Ortodoncia Belgrano', total:1520000, vencida:0, estado:'ok'},
        {n:'Farmacia del Puerto', total:980000, vencida:210000, estado:'warn'},
        {n:'Clínica Odontológica Rosario', total:740000, vencida:0, estado:'ok'}
    ];
    const proveedores = [
        {n:'Laboratorios Andina S.A.', total:3120000, vencida:890000, estado:'bad'},
        {n:'Insumos Dentales del Plata', total:2340000, vencida:0, estado:'ok'},
        {n:'Quimix Argentina', total:1780000, vencida:520000, estado:'warn'},
        {n:'Ortopedia Industrial SRL', total:1120000, vencida:0, estado:'ok'},
        {n:'Droguería Central', total:860000, vencida:140000, estado:'warn'}
    ];

    document.getElementById('ppKpiVentas').textContent = fmtARS(ventasSerie[ventasSerie.length-1]);
    document.getElementById('ppKpiGanancia').textContent = fmtARS(gananciaSerie[gananciaSerie.length-1]);
    document.getElementById('ppKpiCobrar').textContent = fmtARS(2360000);
    document.getElementById('ppKpiCobrarVencida').textContent = fmtARSCompact(826000);
    document.getElementById('ppKpiCobrarProxima').textContent = fmtARSCompact(1062000);
    document.getElementById('ppKpiCobrarVigente').textContent = fmtARSCompact(472000);
    document.getElementById('ppKpiPagar').textContent = fmtARS(4180000);

    function sparkline(svgId, values, colorVar){
        const svg = document.getElementById(svgId);
        if(!svg) return;
        const w=220, h=40, pad=3;
        const min=Math.min(...values), max=Math.max(...values);
        const pts = values.map((v,i)=>{
            const x = pad + (i/(values.length-1))*(w-2*pad);
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

    function fillTable(id, rows, tipo){
        document.getElementById(id).innerHTML = rows.map(r=>{
            const label = r.estado==='bad' ? 'Vencido' : r.estado==='warn' ? 'Próx. vencer' : 'Al día';
            return `<tr class="row-click" onclick="ppOpenDrawer('${tipo}', null, '${r.n.replace(/'/g,"\\'")}')">`+
                `<td class="name">${r.n}</td><td class="num">${fmtARS(r.total)}</td>`+
                `<td class="num">${r.vencida? fmtARS(r.vencida) : '—'}</td>`+
                `<td><span class="pill ${r.estado}"><i></i>${label}</span></td></tr>`;
        }).join('');
    }
    fillTable('ppTblClientes', clientes, 'cobrar');
    fillTable('ppTblProveedores', proveedores, 'pagar');

    // ---- Drill-down: comprobantes de ejemplo detrás de cada factura ----
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

    function genFacturas(personas, tipo){
        let out = [];
        personas.forEach((p, pi)=>{
            const nFacturas = p.vencida>0 ? 2 : 1;
            for(let i=0;i<nFacturas;i++){
                const esVencida = p.vencida>0 && i===0;
                const dias = esVencida ? -(4+pi*3+i) : (6+pi*2+i);
                const monto = esVencida ? Math.min(p.vencida, p.total) : Math.max(p.total-p.vencida, 60000)/nFacturas;
                out.push({
                    id: tipo+'-'+pi+'-'+i,
                    persona: p.n,
                    numero: (tipo==='cobrar'?'FC-A-000': 'FP-000') + (1200+pi*7+i),
                    fecha: '2025-08-'+String(10+pi+i).padStart(2,'0'),
                    vencimiento: '2025-09-'+String(5+pi+i).padStart(2,'0'),
                    monto: Math.round(monto),
                    dias,
                    estado: esVencida ? 'bad' : (dias<=10 ? 'warn' : 'ok'),
                    seed: pi*3+i+7
                });
            }
        });
        return out;
    }
    const facturasCobrar = genFacturas(clientes, 'cobrar');
    const facturasPagar  = genFacturas(proveedores, 'pagar');

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

    function fmtDias(d){
        if(d<0) return `<span style="color:var(--critical)">${Math.abs(d)} días vencida</span>`;
        return `<span style="color:var(--text-muted)">vence en ${d} días</span>`;
    }

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
        document.getElementById('ppDrawerSubtitle').textContent = `Por ${etiquetas[dim].toLowerCase()} · ${rows.length} ${plural[dim]}`;
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

    window.ppOpenDrawer = function(tipo, estadoFiltro, personaFiltro){
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
            document.getElementById('ppDrawerSubtitle').textContent = `${nombres[estadoFiltro]} · ${rows.length} producto${rows.length===1?'':'s'}`;
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
        const fuente = tipo==='cobrar' ? facturasCobrar : facturasPagar;
        const rol = tipo==='cobrar' ? 'Cliente' : 'Proveedor';
        let rows = fuente;
        let subt;
        if(personaFiltro){
            rows = fuente.filter(f=>f.persona===personaFiltro);
            subt = `${rol}: ${personaFiltro}`;
        }else if(estadoFiltro){
            rows = fuente.filter(f=>f.estado===estadoFiltro);
            const nombres = { bad:'Vencidas', warn:'Próximas a vencer', ok:'Vigentes' };
            subt = `Comprobantes — ${nombres[estadoFiltro]}`;
        }else{
            subt = 'Todos los comprobantes';
        }
        document.getElementById('ppDrawerTitle').textContent = tipo==='cobrar' ? 'Cuentas por cobrar' : 'Cuentas por pagar';
        document.getElementById('ppDrawerSubtitle').textContent = subt + ` · ${rows.length} comprobante${rows.length===1?'':'s'}`;
        document.getElementById('ppDrawerBody').innerHTML = rows.map(f=>
            `<div class="pp-drawer-row" onclick="ppOpenInvoice('${f.id}')">`+
                `<div><div class="who">${f.persona}</div><div class="meta">${f.numero} · ${f.fecha}</div></div>`+
                `<div class="amt">${fmtARS(f.monto)}<span class="days">${fmtDias(f.dias)}</span></div>`+
            `</div>`
        ).join('') || `<div style="padding:20px 8px; color:var(--text-muted); font-size:.85rem;">Sin comprobantes en esta categoría.</div>`;
        document.getElementById('ppScrim').classList.add('open');
        document.getElementById('ppDrawer').classList.add('open');
    };
    window.ppCloseDrawer = function(){
        document.getElementById('ppScrim').classList.remove('open');
        document.getElementById('ppDrawer').classList.remove('open');
    };

    window.ppOpenInvoice = function(id){
        const f = facturasCobrar.concat(facturasPagar).find(x=>x.id===id);
        if(!f) return;
        const esCobrar = id.startsWith('cobrar');
        const items = genItems(f.seed);
        document.getElementById('ppModalTitle').textContent = f.numero;
        document.getElementById('ppModalSubtitle').textContent = (esCobrar?'Cliente: ':'Proveedor: ') + f.persona;
        document.getElementById('ppModalKv').innerHTML = [
            ['Fecha emisión', f.fecha], ['Vencimiento', f.vencimiento],
            ['Importe total', fmtARS(f.monto)], ['Estado', f.estado==='bad'?'Vencida':(f.estado==='warn'?'Próxima a vencer':'Vigente')]
        ].map(([k,v])=>`<div><span class="k">${k}</span><span class="v">${v}</span></div>`).join('');
        document.getElementById('ppModalItems').innerHTML = items.map(it=>
            `<tr><td>${it.prod}</td><td class="num">${it.cant}</td><td class="num">${it.precio.toFixed(2)}</td><td class="num">${it.total.toFixed(2)}</td></tr>`
        ).join('');
        document.getElementById('ppModalItemsWrap').hidden = false;
        document.getElementById('ppModalScrim').classList.add('open');
    };

    window.ppOpenProducto = function(id){
        const p = productos.find(x=>x.id===id);
        if(!p) return;
        document.getElementById('ppModalTitle').textContent = p.nombre;
        document.getElementById('ppModalSubtitle').textContent = 'Código: ' + p.codigo;
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
            data:{ labels:['Óptimo','Bajo','Agotado'], datasets:[{ data:[64,27,9], backgroundColor:[c.success,c.warning,c.critical], borderColor:c.surface, borderWidth:3 }] },
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
                    { label:'Por cobrar', data:[472,1062,826], backgroundColor:c.accent, borderRadius:6, borderSkipped:false },
                    { label:'Por pagar', data:[1045,2383,752], backgroundColor:c.series2, borderRadius:6, borderSkipped:false }
                ]},
            options:{ responsive:true, maintainAspectRatio:false, animation: reduceMotion?false:undefined,
                onHover:(evt,els)=>{ evt.native.target.style.cursor = els.length ? 'pointer' : 'default'; },
                onClick:(evt,els)=>{
                    if(!els.length) return;
                    const el = els[0];
                    ppOpenDrawer(el.datasetIndex===0 ? 'cobrar' : 'pagar', ['ok','warn','bad'][el.index]);
                },
                plugins:{ legend:{ position:'bottom', labels:{ color:c.text, font:baseFont, boxWidth:10, usePointStyle:true } },
                    tooltip:{ ...tooltip, callbacks:{ label: ctx => ctx.dataset.label+': $'+ctx.parsed.y+'k' } } },
                scales:{ x:{ grid:{ display:false }, ticks:{ color:c.text, font:baseFont } },
                    y:{ grid:{ color:c.grid }, ticks:{ color:c.text, font:baseFont, callback:v=>'$'+v+'k' } } }
            }
        });

        sparkline('ppSparkVentas', ventasSerie, '--accent');
        sparkline('ppSparkGanancia', gananciaSerie, '--success');
    }

    window.ppSetPreset = function(preset, btn){
        document.querySelectorAll('#panelPrincipal .range-chip').forEach(b=>b.classList.remove('active'));
        btn.classList.add('active');
        const labels = { '7d':'Últimos 7 días', 'mes':'Este mes · 1 – 30 sep 2025', 'anio':'Este año · ene – sep 2025' };
        document.getElementById('ppRangeLabel').textContent = labels[preset];
    };

    buildCharts();

    // El toggle claro/oscuro real (#themeToggle, js/themeToggle.js) no dispara
    // ningún evento propio: observamos el atributo data-theme del <html> para
    // repintar los gráficos con los colores del tema que quedó activo.
    new MutationObserver(buildCharts).observe(document.documentElement, { attributes:true, attributeFilter:['data-theme'] });
})();
