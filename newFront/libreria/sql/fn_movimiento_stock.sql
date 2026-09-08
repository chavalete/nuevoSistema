--TABLA PARA LA RELACION ENTRE EL PEDIDO Y LOS LOTES QUE UTILIZO.
CREATE TABLE pedidos_lotes (
    pedido_id integer not null,
    cantidad integer not null,
    producto_id integer not null,
    lote_id integer not null,
    estanteria_id integer not null) WITH OIDS;
CREATE INDEX idx_pedidos_lotes_pedido_id ON pedidos_lotes (pedido_id);
CREATE INDEX idx_pedidos_lotes_lote_id ON pedidos_lotes (lote_id);
CREATE INDEX idx_pedidos_lotes_producto_id ON pedidos_lotes (producto_id);
CREATE INDEX idx_pedidos_lotes_estanteria_id ON pedidos_lotes (estanteria_id);
ALTER TABLE pedidos_lotes ADD pedido_detalle_id bigint;

-- VISTA PARA CONTROLAR LAS CANTIDADES
CREATE VIEW vw_stock_completo_agrupado AS 
SELECT SUM(cantidad) as cantidad_total , producto_id, producto_nombre || ' ' || producto_presentacion AS producto_nombre 
FROM stock_completo INNER JOIN datos_productos USING (producto_id) 
GROUP BY producto_id, producto_nombre, producto_presentacion;


--MODIFICACIONES  PARA QUE TOME EL pedido_detalle_id en pedidos_lotes DESDE EL 18-11 
ALTER TABLE detalle_pedidos ALTER pedido_detalle_id DROP DEFAULT;
ALTER TABLE detalle_pedidos ALTER pedido_detalle_id SET NOT NULL;
CREATE UNIQUE INDEX idx_pedidos_lotes_pedido_detalle_id ON pedidos_lotes(pedido_detalle_id);

--PARA DROPEAR LA FN 
DROP FUNCTION fn_movimiento_stock(integer, integer,  integer, integer, integer,integer);

--FN PARA MOVIMIENTO DE SALIDA POR PEDIDO.
CREATE OR REPLACE FUNCTION fn_movimiento_stock(lote_automatico integer, id_pedido integer, id_detalle_pedido integer ,cantidad_pedida integer,unidades_pedidas integer ,id_producto integer, id_lote integer) RETURNS INTEGER AS $$
DECLARE                       
reg_lotes_disponibles RECORD;
reg_datos_vw_stock RECORD;
int_pendiente  integer := $5 ;
int_cantidad integer := 0;
txt_error text;
BEGIN
--segun tipo de movimiento
IF $1 = 1 THEN --solo para el movimiento de "Baja por pedido tomado" con lote_automatioco en 1.
    SELECT INTO  reg_datos_vw_stock cantidad_total, producto_nombre FROM vw_stock_completo_agrupado WHERE producto_id = $6;
    IF reg_datos_vw_stock.cantidad_total < (int_pendiente*-1) THEN
        txt_error := 'Las unidades solicitadas (' || $5 || ') es mayor a la cantidad en stock ('|| reg_datos_vw_stock.cantidad_total || ') disponible del producto ' || reg_datos_vw_stock.producto ;
        RAISE EXCEPTION '" % " ' , txt_error ;
    END IF;
    FOR reg_lotes_disponibles IN SELECT * FROM stock_completo INNER JOIN datos_estanterias USING (estanteria_id) INNER JOIN datos_lotes USING (lote_id) WHERE stock_completo.producto_id =$6 AND cantidad > 0 ORDER BY lote_vencimiento, lote_id ASC LOOP
        --int_pendiente:= reg_lotes_disponibles.cantidad + int_cantidad;
         --Actualizar stock_completo 
        IF (int_pendiente * -1) < reg_lotes_disponibles.cantidad THEN
            int_cantidad := int_pendiente;
            int_pendiente := 0;
        ELSE
            int_cantidad := reg_lotes_disponibles.cantidad * -1;
            int_pendiente := reg_lotes_disponibles.cantidad + int_pendiente;
        END IF;
        PERFORM fn_actualizar_stock_completo(int_cantidad::integer, reg_lotes_disponibles.sucursal_id::integer, reg_lotes_disponibles.almacen_id::integer, reg_lotes_disponibles.estanteria_id::integer, reg_lotes_disponibles.lote_id::integer, $6::integer);
        --Insert en la tabla que relaciona el pedido con el lote a utilizar y su ubicacion dentro de la sucursal
        INSERT INTO pedidos_lotes(pedido_id, cantidad, unidades,lote_id, estanteria_id, pedido_detalle_id, producto_id) VALUES ($2, $4, $5,reg_lotes_disponibles.lote_id, reg_lotes_disponibles.estanteria_id,$3,$6);
        int_cantidad:=int_pendiente;
        IF int_pendiente = 0 THEN 
            EXIT;--corta solo el for
        END IF;
    END LOOP;
ELSE --solo para el movimiento de "Baja por pedido tomado" con lote_automatioco en 0.
    SELECT INTO  reg_datos_vw_stock SUM(sc.cantidad) AS cantidad_total , dp.producto_nombre, sc.producto_id FROM stock_completo sc INNER JOIN datos_productos dp USING(producto_id) INNER JOIN datos_estanterias de USING (estanteria_id) WHERE sc.producto_id = $5 AND sc.lote_id = $6 GROUP BY dp.producto_nombre, sc.producto_id ; 
    IF reg_datos_vw_stock.cantidad_total < (int_pendiente*-1) THEN
        txt_error := 'Las unidades solicitadas (' || $5 || ') es mayor a la cantidad en stock ('|| reg_datos_vw_stock.cantidad_total || ') disponible del producto ' || reg_datos_vw_stock.producto ;
        RAISE EXCEPTION '" % " ' , txt_error ;
    END IF;    
    FOR reg_lotes_disponibles IN SELECT * FROM stock_completo INNER JOIN datos_productos USING (producto_id) INNER JOIN datos_estanterias USING (estanteria_id)  WHERE lote_id = $7  AND producto_id = $6 AND cantidad > 0 LOOP
        --Actualizar stock_completo 
        IF (int_pendiente * -1) < reg_lotes_disponibles.cantidad THEN
            int_cantidad := int_pendiente;
            int_pendiente := 0;
        ELSE
            int_cantidad := reg_lotes_disponibles.cantidad * -1;
            int_pendiente := reg_lotes_disponibles.cantidad + int_pendiente;
        END IF;
        PERFORM fn_actualizar_stock_completo($5::integer, reg_lotes_disponibles.sucursal_id::integer, reg_lotes_disponibles.almacen_id::integer, reg_lotes_disponibles.estanteria_id::integer, reg_lotes_disponibles.lote_id::integer, $6::integer);
        --Insert en la tabla que relaciona el pedido con el lote a utilizar y su ubicacion dentro de la sucursal
        INSERT INTO pedidos_lotes(pedido_id, cantidad, lote_id, estanteria_id, pedido_detalle_id,producto_id) VALUES ($2, $4, $5, reg_lotes_disponibles.lote_id, reg_lotes_disponibles.estanteria_id, $3,$6);
        int_cantidad := int_pendiente;
                IF int_pendiente = 0 THEN 
            EXIT;--corta solo el for
        END IF;
    END LOOP;
END IF;
RETURN 1;
END;
$$ LANGUAGE plpgsql;