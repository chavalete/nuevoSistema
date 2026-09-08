CREATE SEQUENCE seq_datos_productos_id;

CREATE TABLE datos_productos 
	(
	producto_id integer PRIMARY KEY NOT NULL,
	producto_nombre varchar(100) NOT NULL,
	producto_presentacion varchar(100),
	marca_id smallint,
	prove_id integer,
	codigo_referencia varchar,
	producto_activo boolean DEFAULT 't',
	producto_fecha_alta timestamp DEFAULT now()::timestamp,
	producto_usuario_alta integer REFERENCES datos_usuarios (usuario_id)
	) WITH OIDS;

CREATE UNIQUE INDEX da_produ_producto_idx ON datos_productos (producto_id);
CREATE INDEX da_produ_marca_idx ON datos_productos (producto_id);
CREATE INDEX da_produ_provee_idx ON datos_productos (marca_id);
CREATE INDEX da_produ_producto_activo_true ON datos_productos (producto_activo )WHERE (producto_activo=true);
CREATE INDEX da_produ_producto_activo_false ON datos_productos (producto_activo )WHERE (producto_activo=false);


CREATE SEQUENCE seq_datos_sucursales_id;

CREATE TABLE datos_sucursales
	(
	sucursal_id  SMALLINT NOT NULL PRIMARY KEY,
	sucursal_nombre VARCHAR(100),
	sucursal_activa boolean DEFAULT 't',
	observaciones varchar,
	sucursal_fecha_alta date DEFAULT now()::date,
	sucursal_usuario_alta smallint REFERENCES datos_usuarios (usuario_id)
	) WITH OIDS;

CREATE UNIQUE INDEX da_su_sucursal_idx ON datos_sucursales (sucursal_id);
CREATE INDEX da_su_sucursal_activa_true ON datos_sucursales (sucursal_id ) WHERE (sucursal_activa=true);
CREATE INDEX da_su_sucursal_activa_false ON datos_sucursales (sucursal_id) WHERE (sucursal_activa=false);

CREATE SEQUENCE seq_datos_almacenes_id;

CREATE TABLE datos_almacenes
	(
	almacen_id smallint NOT NULL PRIMARY KEY,
	almacen_nombre varchar(100),
	sucursal_id smallint REFERENCES datos_sucursales(sucursal_id),
	almacen_activo boolean DEFAULT 't',
	observaciones varchar,
	almacen_fecha_alta date DEFAULT now()::date,
	almacen_usuaio_alta smallint REFERENCES datos_usuarios (usuario_id)
	) WITH OIDS;

CREATE UNIQUE INDEX da_alma_almacen_idx ON datos_almacenes (almacen_id);
CREATE INDEX da_alma_almacen_activo_true ON datos_almacenes (almacen_activo) WHERE (almacen_activo=true);
CREATE INDEX da_alma_almacen_activo_false ON datos_almacenes  (almacen_activo) WHERE (almacen_activo=false);

CREATE SEQUENCE seq_datos_estanterias_id;

CREATE TABLE datos_estanterias
	(
	estanteria_id smallint NOT NULL PRIMARY KEY,
	estanteria_nombre varchar(100),
	estanteria_activa boolean DEFAULT 't',
	almacen_id smallint REFERENCES datos_almacenes (almacen_id),
	observaciones varchar,
	estanteria_fecha_alta date DEFAULT now()::date,
	estanteria_usuario_alta smallint REFERENCES datos_usuarios (usuario_id)
	) WITH OIDS;

CREATE UNIQUE INDEX da_esta_estanteria_idx ON datos_estanterias (estanteria_id);
CREATE INDEX da_esta_estanteria_activa_true ON datos_estanterias (estanteria_activa) WHERE (estanteria_activa=true);
CREATE INDEX da_esta_estanteria_activa_false ON datos_estanterias (estanteria_activa) WHERE (estanteria_activa=false);


CREATE TABLE tmp_control_stock
    (
	control_id bigint,
	producto varchar,
	estanteria varchar,
	codigos_leidos varchar,
	total integer,
	codigos_faltantes varchar,
	cantidad integer,
	diferencia integer
    ) WITH OIDS;

CREATE SEQUENCE tmp_control_stock_control_id_seq;