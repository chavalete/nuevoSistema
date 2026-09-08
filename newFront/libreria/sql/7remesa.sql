CREATE SEQUENCE seq_datos_remesa_remesa_id ;

CREATE TABLE datos_remesas (
remesa_id integer NOT NULL PRIMARY KEY DEFAULT nextval('seq_datos_tablas_id'),
remesa_nombre varchar(50) NOT NULL,
remesa_nro varchar(10),
remesa_fecha_hora_importacion timestamp);

CREATE UNIQUE INDEX idx_datos_remesas_remesa_idx ON datos_remesas(remesa_id);

CREATE TABLE detalle_remesas (
remesa_id integer,
producto varchar(30),
numero_serial varchar(30),
lote varchar(30),
vencimieto date
) WITH OIDS;

CREATE UNIQUE INDEX idx_detalle_remesas_remesa_idx ON detalle_remesas(remesa_id);