CREATE OR REPLACE FUNCTION fn_calcular_vencimiento_alquiler () RETURNS trigger AS $$

BEGIN                                                                         

IF (NEW.movimiento_finalizado=true AND OLD.tipo_movimiento_id IN (2,7,9) AND OLD.cant_dias_alquiler > 0) THEN
    NEW.fecha_vencimiento_alquiler = NEW.fecha_inicio_alquiler + OLD.cant_dias_alquiler;
END IF;                                                                   
RETURN NEW;                                                               
END;
$$ LANGUAGE plpgsql; 



CREATE TRIGGER trigger_set_anulado BEFORE INSERT ON datos_movimientos FOR EACH ROW EXECUTE PROCEDURE fn_set_anulado();


CREATE OR REPLACE FUNCTION fn_set_anulado() RETURNS trigger AS $$

BEGIN                                                                         

IF (NEW.tipo_movimiento_id = 6 AND NEW.de_id_movimiento > 0) THEN
    UPDATE datos_movimientos SET anulado = true, anulado_fecha_hora = now(), anulado_user_id = NEW.movimiento_usuario_id WHERE id_movimiento = NEW.de_id_movimiento;
END IF;                                                                   
RETURN NEW;                                                               
END;
$$ LANGUAGE plpgsql; 