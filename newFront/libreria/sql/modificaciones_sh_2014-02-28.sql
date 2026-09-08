INSERT INTO tipos_movimientos_trazas VALUES (9,'Inicio alquiler desde Custodia', false);
INSERT INTO tipos_movimientos_trazas VALUES (10,' Robo - Rotura ', false);



SELECT 
    producto_nombre || ' '|| producto_presentacion AS producto,codigo_referencia,
    count(*) AS cantidad,
    lote, 
    estanteria_nombre, 
    despacho_nro 
FROM 
    datos_trazabilidad 
    INNER JOIN movimientos_trazabilidad USING(trazabilidad_id) 
    INNER JOIN datos_movimientos USING(id_movimiento)
    INNER JOIN datos_estanterias USING(estanteria_id) 
    INNER JOIN datos_productos USING(producto_id) 
    INNER JOIN datos_lotes USING(lote_id) 
WHERE 
    datos_movimientos.prove_id = '1' 
    AND en_stock 
GROUP BY 
    producto_nombre, producto_presentacion, lote, estanteria_nombre, codigo_referencia, despacho_nro, lote_id
ORDER BY 
    producto_nombre;



SELECT 
    producto_nombre || ' '||  producto_presentacion AS producto,
    codigo_referencia,
    cantidad,
    lote, 
    estanteria_nombre, 
    to_char(lote_vencimiento,'DD-MM-YYYY') AS lote_vencimiento
FROM datos_trazabilidad
INNER JOIN datos_estanterias USING(estanteria_id)
INNER JOIN datos_productos USING(producto_id)
INNER JOIN datos_lotes USING(lote_id)
INNER JOIN stock_completo ON datos_trazabilidad.producto_id = stock_completo.producto_id AND datos_trazabilidad.lote_id = stock_completo.lote_id 
AND datos_trazabilidad.estanteria_id = stock_completo.estanteria_id
WHERE prove_id = 1 AND en_stock AND cantidad > 0 
GROUP BY producto_nombre,producto_presentacion,lote,estanteria_nombre,codigo_referencia,lote_vencimiento, cantidad, datos_lotes.lote_id 
ORDER BY producto_nombre;
