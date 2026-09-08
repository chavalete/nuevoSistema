    CREATE OR REPLACE FUNCTION fn_ins_autogestion() RETURNS TRIGGER LANGUAGE plpgsql AS $$

    BEGIN

    IF(NEW.error_id > 0 AND NEW.autogestion = FALSE AND NEW.no_requiere_autogestion = FALSE) THEN

	INSERT INTO datos_autogestion SELECT * FROM datos_transacciones_anmat WHERE transaccion_id = NEW.transaccion_id;
	INSERT INTO detalle_autogestion SELECT * FROM detalle_transacciones_anmat WHERE transaccion_id = NEW.transaccion_id;
    END IF;
    RETURN NEW;
    END;
    $$;

CREATE TRIGGER tr_ins_autogestion AFTER UPDATE ON datos_transacciones_anmat FOR EACH ROW EXECUTE PROCEDURE fn_ins_autogestion();

CREATE OR REPLACE FUNCTION fn_actualizar_reprocesado() RETURNS TRIGGER LANGUAGE plpgsql AS $$

BEGIN

IF(NEW.reprocesado_anterior_id > 0) THEN

    UPDATE
	datos_transacciones_anmat SET reprocesado_posterior_id = NEW.transaccion_id,  autogestion ='t'
    WHERE
        transaccion_id = NEW.reprocesado_anterior_id;
    UPDATE datos_autogestion SET autogestion=true WHERE transaccion_id = NEW.reprocesado_anterior_id;
END IF;
RETURN NEW;
END;   
$$;


