CREATE SEQUENCE seq_datos_legra_griega_id;

CREATE TABLE datos_letras_griegas 
	(
	letra_griega_id smallint PRIMARY KEY NOT NULL,
	letra_nombre varchar,
	letra_directorio varchar
	) WITH OIDS;

CREATE UNIQUE INDEX da_letra_g_idx ON datos_letras_griegas (letra_griega_id);

CREATE SEQUENCE seq_datos_tablas_id;

CREATE TABLE datos_tablas
    (
    tabla_id integer NOT NULL PRIMARY KEY DEFAULT nextval('seq_datos_tablas_id'),
    tabla_nombre varchar(100)
    ) WITH OIDS;

CREATE UNIQUE INDEX da_tablas_tabla_idx ON datos_tablas (tabla_id);

CREATE SEQUENCE seq_datos_autogestion_id;

CREATE TABLE datos_autogestion 
    (
    autogestion_id bigint NOT NULL PRIMARY KEY,
    tabla_id bigint NOT NULL REFERENCES datos_tablas (tabla_id),
    usuario_id smallint NOT NULL REFERENCES datos_usuarios (usuario_id),
    fecha_hora timestamp  DEFAULT now()::timestamp
    ) WITH OIDS;

CREATE UNIQUE INDEX da_auto_autogestion_idx ON datos_autogestion (autogestion_id);
CREATE INDEX da_auto_tabla_idx ON datos_tablas (tabla_id);
CREATE INDEX da_auto_usuario_idx ON datos_autogestion (usuario_id);

CREATE TABLE detalle_autogestion 
    (
    autogestion_id bigint,
    campo varchar(50) NOT NULL,
    campo_valor_anterior character varying(200) NOT NULL,
    campo_valor_nuevo character varying(200) NOT NULL
    ) WITH OIDS;

CREATE INDEX de_auto_autogestion_idx ON detalle_autogestion (autogestion_id);