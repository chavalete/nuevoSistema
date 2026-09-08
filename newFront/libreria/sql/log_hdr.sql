CREATE SEQUENCE seq_movimientos_pedidos_id;

CREATE TABLE movimientos_pedidos(
    hoja_detalle_id bigint,
    fecha_hora timestamp default now() not null,
    pedido_id bigint not null,
    estado_id smallint not null,
    movimiento_pedidos_id bigint DEFAULT nextval('seq_movimientos_pedidos_id'),
    movimiento_usuario_id integer not null

) WITH OIDS;

CREATE INDEX movped_hoja_detalle_idx ON movimientos_pedidos (hoja_detalle_id);
CREATE INDEX movped_pedido_idx ON movimientos_pedidos(pedido_id);
CREATE INDEX movped_estado_idx ON movimientos_pedidos(estado_id);
CREATE INDEX movped_mov_pedido_idx ON movimientos_pedidos(movimiento_pedidos_id);


CREATE OR REPLACE FUNCTION fn_log_historico_estados_pedidos(hoja_detalle_id bigint,pedido_id bigint,estado_id integer, movimiento_usuario_id integer) RETURNS INTEGER AS $$

BEGIN
    INSERT INTO movimientos_pedidos(hoja_detalle_id,pedido_id,estado_id,movimiento_usuario_id)
    VALUES ($1,$2,$3,$4);
RETURN 1;
END;
$$ LANGUAGE plpgsql;
