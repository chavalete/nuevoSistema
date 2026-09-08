ALTER TABLE datos_movimientos ADD fecha_vencimiento_alquiler date;

CREATE TRIGGER trigger_calcular_vencimiento_alquiler BEFORE UPDATE ON datos_movimientos  
FOR EACH ROW EXECUTE PROCEDURE fn_calcular_vencimiento_alquiler();

CREATE OR REPLACE FUNCTION fn_calcular_vencimiento_alquiler () RETURNS trigger AS $$
BEGIN

    IF(NEW.movimiento_finalizado=true) THEN
    
	IF (OLD.remito_nro NOT NULL OR OLD.nro_nota_carga NOT NULL)) THEN

	    IF (OLD.tipo_movimiento_id IN (2,7) AND OLD.cant_dias_alquiler > 0) THEN 
		NEW.fecha_vencimiento_alquiler = NEW.movimiento_finalizado_fecha_hora::DATE + OLD.cant_dias_alquiler;
	    ELSE
		RAISE EXCEPTION 'Debe ingresar ';
	    END IF;
	ELSE
		RAISE EXCEPTION 'Nonexistent ID --> ';
    
	RETURN NEW;
END;
$$ LANGUAGE plpgsql; 


RAISE EXCEPTION 'Nonexistent ID --> ';