--FACTURAS
CREATE SEQUENCE seq_datos_factura_id;
CREATE SEQUENCE seq_datos_factura_nro;


CREATE TABLE datos_facturas (
    factura_id bigint NOT NULL,
    factura_fecha date NOT NULL,
    cliente_id integer NOT NULL,
    factura_nro character varying(14) NOT NULL,
    factura_total_fact numeric(14,2) NOT NULL,
    factura_total_iva numeric(14,2) NOT NULL,
    factura_faltan_fact numeric(14,2) NOT NULL,
    id_movimiento bigint NOT NULL,
    factura_paga boolean DEFAULT false,
    observaciones character varying,
    factura_usuario_id integer NOT NULL,
    factura_cancelada boolean DEFAULT false,
    factura_cancelada_fecha_hora timestamp without time zone,
    factura_cancelada_usuario_id integer
)WITH OIDS;

CREATE UNIQUE INDEX idx_datos_facturas_id ON datos_facturas(factura_id);
CREATE INDEX idx_datos_facturas_factura_fecha ON datos_facturas (factura_fecha);
CREATE INDEX idx_datos_facturas_cliente_id ON datos_facturas (cliente_id);
CREATE INDEX idx_datos_facturas_nro ON datos_facturas (factura_nro);
CREATE INDEX idx_datos_facturas_factura_total ON datos_facturas(factura_total_fact);
CREATE INDEX idx_datos_facturas_factura_falta ON datos_facturas(factura_faltan_fact);
CREATE INDEX idx_datos_facturas_factura_iva ON datos_facturas(factura_total_iva);
CREATE INDEX idx_datos_facturas_movimiento_id ON datos_facturas(id_movimiento);

CREATE INDEX  ON datos_facturas (factura_paga) WHERE factura_paga=true;
CREATE INDEX  ON datos_facturas (factura_paga) WHERE factura_paga=false;
CREATE INDEX  ON datos_facturas (factura_cancelada) WHERE factura_cancelada=false;
CREATE INDEX  ON datos_facturas (factura_cancelada) WHERE factura_cancelada=true;


CREATE SEQUENCE seq_detalle_factura_id;
CREATE TABLE detalle_facturas (
    factura_id bigint NOT NULL,
    producto_id bigint NOT NULL,
    cantidad smallint NOT NULL,
    importe_unitario numeric(14,2) NOT NULL DEFAULT 0,
    importe_detalle numeric(14,2) NOT NULL DEFAULT 0,
    importe_iva numeric(14,2) NOT NULL DEFAULT 0,
    detalle_factura_id bigint DEFAULT nextval('seq_detalle_factura_id'::regclass) NOT NULL
) WITH OIDS;
CREATE INDEX idx_detalle_facturas_id ON detalle_facturas(factura_id);
CREATE INDEX idx_detalle_facturas_producto_id ON detalle_facturas(producto_id);
CREATE INDEX idx_detalle_facturas_cantidad ON detalle_facturas(cantidad);
CREATE INDEX idx_detalle_facturas_imp_uni ON detalle_facturas(importe_unitario);
CREATE INDEX idx_detalle_facturas_imp_detalle ON detalle_facturas(importe_detalle);
CREATE INDEX idx_detalle_facturas_importe_iva ON detalle_facturas(importe_iva);


CREATE VIEW vw_total_deuda_clientes AS SELECT sum(factura_faltan_fact) AS importe_deuda, cliente_id FROM datos_facturas WHERE factura_cancelada = false AND factura_paga=false AND factura_faltan_fact > 0 GROUP BY cliente_id;

CREATE VIEW vw_pendiente_por_factura AS SELECT factura_faltan_fact, factura_id,cliente_id FROM datos_facturas WHERE factura_cancelada = false AND factura_paga=false AND factura_faltan_fact > 0 ORDER BY factura_fecha ASC;





CREATE OR REPLACE FUNCTION fn_actualizar_total_movimiento() RETURNS trigger LANGUAGE plpgsql AS $$
DECLARE
rec_factura record;
rec_lista record;
rec_movimiento record;
qry text;
importeTotal numeric(14,2);
importeBonificado numeric(14,2);
BEGIN

IF (NEW.tipo_movimiento_id = 2 OR NEW.tipo_movimiento_id = 10 OR NEW.tipo_movimiento_id = 18) THEN

    importeTotal:=0;
    importeBonificado:=0;
    SELECT INTO rec_factura nextval('seq_datos_factura_nro') as nro;

    SELECT INTO rec_factura '0001-' || lpad(rec_factura.nro::varchar,8,'0') AS remito_nro, nextval('seq_datos_factura_id') as id;

    UPDATE datos_movimientos SET remito_nro= rec_factura.remito_nro   WHERE id_movimiento = NEW.id_movimiento;

    qry:='SELECT detalle_movimientos.producto_id, producto_pventa, abs(cantidad) AS cantidad, detalle_total_fact,detalle_movimientos.importe_bonificado, datos_movimientos.descuento FROM datos_movimientos INNER JOIN detalle_movimientos USING(id_movimiento) WHERE id_movimiento =' ||  NEW.id_movimiento || ';';
    
    
    FOR rec_movimiento IN EXECUTE qry  LOOP
    
        INSERT INTO detalle_facturas (factura_id,producto_id,cantidad,importe_detalle, importe_unitario) VALUES (rec_factura.id,rec_movimiento.producto_id, rec_movimiento.cantidad, rec_movimiento.detalle_total_fact, rec_movimiento.producto_pventa);
        importeTotal:= importeTotal + rec_movimiento.detalle_total_fact;
        importeBonificado:= importeBonificado + rec_movimiento.importe_bonificado;
    END LOOP;
    UPDATE datos_movimientos SET movimiento_total_fact = importeTotal WHERE id_movimiento = NEW.id_movimiento;
    IF rec_movimiento.descuento = 100 THEN
        importeTotal:=0;
        importeBonificado:=0;
    END IF;
    IF rec_movimiento.descuento > 0 THEN
        importeTotal:= importeTotal - (importeTotal * rec_movimiento.descuento /100);
    END IF;
    UPDATE datos_movimientos SET importe_total_bonificado = importeBonificado  WHERE id_movimiento = NEW.id_movimiento;
    
    INSERT INTO datos_facturas (factura_id, factura_fecha,factura_nro,cliente_id, id_movimiento, factura_total_fact,factura_total_iva, factura_faltan_fact, factura_usuario_id) VALUES (rec_factura.id,now(),rec_factura.remito_nro,NEW.cliente_id, NEW.id_movimiento,importeTotal,0,importeTotal,NEW.movimiento_usuario_id);
    
    INSERT INTO cta_cte_clientes (fecha, cliente_id,factura_id,importe_deuda, usuario_id) VALUES(now()::date,new.cliente_id,rec_factura.id,importeTotal,NEW.movimiento_usuario_id);
    
END IF;
IF NEW.tipo_movimiento_id = 6 THEN
    SELECT INTO rec_factura factura_id,factura_total_fact FROM datos_facturas WHERE id_movimiento = NEW.de_id_movimiento AND factura_paga=false;
    IF FOUND THEN
        SELECT INTO rec_lista cta_cte_id, importe_deuda FROM cta_cte_clientes WHERE cliente_id = NEW.cliente_id ORDER BY cta_cte_id DESC LIMIT 1;
            UPDATE cta_cte_clientes SET importe_deuda = importe_deuda - rec_factura.factura_total_fact WHERE cta_cte_id = rec_lista.cta_cte_id AND cliente_id = NEW.cliente_id;
            UPDATE cta_cte_clientes SET anulado=true WHERE factura_id = rec_factura.factura_id;
            UPDATE datos_facturas SET factura_cancelada = true, factura_cancelada_fecha_hora = now(), factura_cancelada_usuario_id = NEW.movimiento_usuario_id WHERE factura_id = rec_factura.factura_id;
    END IF;
END IF;
RETURN NEW;
END;
$$ ;



CREATE SEQUENCE seq_orden_cobro_nro;
CREATE SEQUENCE seq_datos_cobros_id;

CREATE TABLE datos_cobros (

    cobro_id bigint not null,
    fecha_cobro date DEFAULT now(), 
    cliente_id integer NOT NULL,
    orden_cobro_nro bigint not null,
    importe_total_cobro numeric (14,2),
    cobro_usuario_id smallint not null,
    cobro_cancelado boolean default false,
    cobro_cancelado_fecha_hora timestamp,
    cobro_cancelado_usuario_id smallint,
    observaciones varchar

) WITH OIDS;

CREATE UNIQUE INDEX idx_datos_cobros_cobro_id ON datos_cobros (cobro_id);
CREATE UNIQUE INDEX idx_datos_cobros_orden_nro ON datos_cobros (orden_cobro_nro);
CREATE INDEX idx_datos_cobros_cliente_id ON datos_cobros (cliente_id);
CREATE INDEX ON datos_cobros (cobro_cancelado) WHERE cobro_cancelado = false;
CREATE INDEX ON datos_cobros (cobro_cancelado) WHERE cobro_cancelado = true;


CREATE SEQUENCE seq_detalle_cobro_id;

CREATE TABLE detalle_cobros (

    cobro_id bigint not null,
    importe_pago numeric(14,2) not null,
    forma_pago_id smallint not null,
    comprobante_nro varchar(15),
    banco_id smallint,
    fecha_cheque date,
    detalle_cobro_id bigint default nextval('seq_detalle_cobro_id')

) WITH OIDS;

CREATE INDEX idx_detalle_cobros_forma ON detalle_cobros (forma_pago_id);
CREATE INDEX idx_detalle_cobros_id ON detalle_cobros (cobro_id);
CREATE INDEX idx_detalle_cobros_factura_id ON detalle_facturas (factura_id);
CREATE INDEX idx_detalle_cobros_banco_id ON detalle_cobros(banco_id);


CREATE TABLE datos_cobros_facturas(

    cobro_id integer not null,
    factura_id bigint not null,
    importe_pendiente numeric(14,2) NOT NULL

) WITH OIDS;

CREATE INDEX idx_datos_cobros_facturas_cobro_id ON datos_cobros_facturas(cobro_id);
CREATE INDEX idx_datos_cobros_facturas_factura_id ON datos_cobros_facturas(factura_id);


CREATE SEQUENCE seq_datos_bancos_id;

CREATE TABLE datos_bancos(
    banco_id smallint  DEFAULT nextval('seq_datos_bancos_id'),
    banco_nombre varchar(100) not null
) WITH OIDS;

CREATE UNIQUE INDEX idx_datos_bancos_id ON datos_bancos(banco_id);
CREATE SEQUENCE seq_datos_formas_pago_id;

CREATE TABLE datos_formas_pagos (
    forma_pago_id smallint DEFAULT nextval('seq_datos_formas_pago_id'),
    forma_desc varchar(30) NOT NULL
) WITH OIDS;

CREATE UNIQUE INDEX idx_datos_formas_pagos_id ON datos_formas_pagos (forma_pago_id);
INSERT INTO datos_formas_pagos VALUES (1,'Transferencia');
INSERT INTO datos_formas_pagos VALUES (2,'Efectivo');
INSERT INTO datos_formas_pagos VALUES (3,'Cheque');
INSERT INTO datos_formas_pagos VALUES (4,'Deposito');
INSERT INTO datos_formas_pagos VALUES (5,'Nota de Credito');


CREATE SEQUENCE seq_cta_cte_id;

CREATE TABLE cta_cte_clientes (

    cta_cte_id bigint not null DEFAULT nextval('seq_cta_cte_id'),
    fecha date not null,
    operacion_fecha_hora timestamp default now(),
    cliente_id integer not null,
    cobro_id bigint,
    factura_id bigint,
    importe_deuda numeric(14,2) DEFAULT 0,
    anulado boolean default false
) WITH OIDS;

CREATE UNIQUE INDEX idx_cta_cte_id ON cta_cte_clientes (cta_cte_id);
CREATE INDEX idx_cta_cte_cliente_id ON cta_cte_clientes (cliente_id);
CREATE INDEX idx_cta_cte_factura_id ON cta_cte_clientes (factura_id);
CREATE INDEX idx_cta_cte_cobro_id ON cta_cte_clientes (cobro_id);
CREATE INDEX ON cta_cte_clientes (anulado) WHERE anulado=false;
CREATE INDEX ON cta_cte_clientes (anulado) WHERE anulado=true;



CREATE OR REPLACE FUNCTION fn_actualizar_cta_cte() RETURNS trigger LANGUAGE plpgsql AS $$
DECLARE
rec_movimiento record;
importeTotal numeric(14,2);
BEGIN

SELECT INTO rec_movimiento importe_deuda FROM cta_cte_clientes WHERE cliente_id = NEW.cliente_id ORDER BY cta_cte_id DESC LIMIT 1;

IF FOUND THEN
    
    IF NEW.factura_id > 0 THEN
        NEW.importe_deuda:= NEW.importe_deuda + rec_movimiento.importe_deuda;
    ELSE
        NEW.importe_deuda:= rec_movimiento.importe_deuda - NEW.importe_deuda;
    END IF;
END IF;

RETURN NEW;
END;
$$ ;

CREATE TRIGGER tr_actualizar_cta_cte BEFORE INSERT ON cta_cte_clientes FOR EACH ROW EXECUTE PROCEDURE fn_actualizar_cta_cte();

CREATE SEQUENCE seq_datos_puesto_venta_id;

CREATE TABLE datos_puesto_ventas (

    puesto_id smallint not null,
    puesto_letra varchar(1) NOT NULL,
    puesto_nro smallint not null,
    puesto_activo boolean default true

) WITH OIDS;

CREATE UNIQUE INDEX idx_datos_puesto_ventas_id ON datos_puesto_ventas(puesto_id);
CREATE INDEX idx_datos_puesto_ventas_letra ON datos_puesto_ventas (puesto_letra);
CREATE INDEX idx_datos_puesto_ventas_nro ON datos_puesto_ventas (puesto_nro);
CREATE INDEX ON datos_puesto_ventas (puesto_activo) WHERE puesto_activo=true;
CREATE INDEX ON datos_puesto_ventas (puesto_activo) WHERE puesto_activo=false;

INSERT INTO datos_puesto_ventas  VALUES (nextval('seq_datos_puesto_venta_id'),'A',2);
INSERT INTO datos_puesto_ventas VALUES(nextval('seq_datos_puesto_venta_id'),'B',2);


--NUMERACION ORDENES
CREATE OR REPLACE FUNCTION fn_set_orden_nro() RETURNS trigger LANGUAGE plpgsql AS $$
DECLARE
BEGIN
    NEW.orden_cobro_nro = nextval('seq_orden_cobro_nro');
RETURN NEW;
END;
$$ ;
CREATE TRIGGER tr_set_orden_nro BEFORE INSERT ON datos_cobros FOR EACH ROW EXECUTE PROCEDURE fn_set_orden_nro();
