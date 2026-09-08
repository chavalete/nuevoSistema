CREATE OR REPLACE FUNCTION fn_actualizar_movimiento_anulado() RETURNS TRIGGER LANGUAGE plpgsql AS $$

BEGIN

IF(NEW.de_id_movimiento > 0 AND NEW.tipo_movimiento_id  = 6) THEN
    UPDATE
	datos_movimientos SET anulado = TRUE,  anulado_fecha_hora = NOW(), anulado_user_id = NEW.movimiento_usuario_id
    WHERE
        id_movimiento = NEW.de_id_movimiento;
    
END IF;
RETURN NEW;
END;
$$;

CREATE TRIGGER tr_actualizar_movimiento_anulado BEFORE INSERT ON datos_movimientos FOR EACH ROW EXECUTE PROCEDURE fn_actualizar_movimiento_anulado();

