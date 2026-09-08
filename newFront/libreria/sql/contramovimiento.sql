INSERT INTO tipos_movimientos_trazas VALUES (6,'Anulacion de Movimiento',false );
ALTER TABLE datos_movimientos ADD anulado boolean default false;
ALTER TABLE datos_movimientos ADD anulado_fecha_hora timestamp;
ALTER TABLE datos_movimientos ADD anulado_user_id integer;

