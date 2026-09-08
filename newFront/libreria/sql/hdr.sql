CREATE TABLE datos_hojas_de_ruta(

    hoja_id bigint not null,
    hoja_fecha_hora timestamp DEFAULT now(),
    hoja_distri_id integer not null,
    hoja_anulada boolean DEFAULT 'f',
    hoja_anulada_fecha_hora timestamp,
    hoja_anulada_usuario_id smallint,
    hoja_usuario_id smallint NOT NULL,
    hoja_obs text

)WITH OIDS;

CREATE UNIQUE INDEX dhr_hoja_idx ON datos_hojas_de_ruta(hoja_id);
CREATE INDEX dahr_hoja_distri_idx ON datos_hojas_de_ruta(hoja_distri_id);
CREATE INDEX dahr_hojas_anulada_false ON datos_hojas_de_ruta (hoja_anulada) WHERE (hoja_anulada=false);
CREATE INDEX dahr_hojas_anulada_true ON datos_hojas_de_ruta (hoja_anulada) WHERE hoja_anulada=true;
CREATE INDEX dahr_hojas_usuario_id ON datos_hojas_de_ruta (hoja_usuario_id);

CREATE SEQUENCE seq_detalle_hoja_detalle_id;

CREATE TABLE detalle_hojas_de_ruta(
    hoja_id bigint not null,
    pedido_id bigint,
    id_movimiento bigint,
    hoja_estado_id smallint not null,
    hoja_estado_fecha timestamp,
    hoja_estado_usuario_id smallint,
    hoja_old_estado_id smallint,
    hoja_old_estado_fecha date,
    hoja_old_estado_usuario_id smallint,
    hoja_detalle_id bigint not null

)WITH OIDS;

CREATE INDEX dehr_hoja_idx ON detalle_hojas_de_ruta (hoja_id);
CREATE INDEX dehr_pedido_idx ON detalle_hojas_de_ruta(pedido_id);
CREATE INDEX dehr_movimiento_idx ON detalle_hojas_de_ruta(id_movimiento);
CREATE INDEX dehr_estado_idx ON detalle_hojas_de_ruta(hoja_estado_id);
CREATE INDEX dehr_old_estado_idx ON detalle_hojas_de_ruta(hoja_old_estado_id);
CREATE INDEX dehr_usuario_estado_idx ON detalle_hojas_de_ruta(hoja_estado_usuario_id);
CREATE INDEX dehr_usuario_old_estado_idx ON detalle_hojas_de_ruta(hoja_old_estado_usuario_id);
CREATE UNIQUE INDEX dehr_detalle_idx ON detalle_hojas_de_ruta(hoja_detalle_id);


CREATE SEQUENCE seq_datos_estado_id;

CREATE TABLE datos_estados(
    estado_id smallint not null,
    estado_desc varchar(100) NOT NULL,
    obs text,
    estado_usuario_id smallint NOT NULL,
    estado_fecha_hora timestamp DEFAULT now(),
    estado_activo boolean DEFAULT 't',
    estado_inactivo_fecha_hora date,
    estado_inactivo_usuario_id smallint

)WITH OIDS;

CREATE UNIQUE INDEX da_estado_idx ON datos_estados(estado_id);


En prepacacion
En hoja de ruta
En Espera
Confirmado
Suspendido
Rechazado
En Desposito
Anulado
1era Visita
2da  Visita



CREATE SEQUENCE seq_datos_pedidos_pedidos_id;
CREATE SEQUENCE seq_datos_pedidos_pedido_nro;

CREATE TABLE datos_pedidos (

	pedido_id bigint NOT NULL,
	pedido_fecha_hora timestamp DEFAULT now(),
	cliente_id integer NOT NULL,
	pedido_nro bigint NOT NULL,
	pedido_nro_ext bigint,
	estado_id smallint NOT NULL,
	pedido_calle varchar(200),
	pedido_altura varchar(10),
	pedido_piso smallint,
	pedido_depto varchar(10),
	pedido_tel varchar(20),
	codigo_postal varchar(10),
	localidad_id bigint,
	paciente_id integer,
	pedido_obs text,
	pedido_usuario_id integer NOT NULL,
	pedido_preparado boolean DEFAULT 'f',
	pedido_preparado_fecha timestamp,
	pedido_preparado_usuario_id integer,
	pedido_cancelado boolean DEFAULT 'f',
	pedido_cancelado_usuario_id integer,
	pedido_cancelado_fecha_hora timestamp  

) WITH OIDS;


CREATE UNIQUE INDEX dap_pedido_idx ON datos_pedidos (pedido_id);
CREATE INDEX dap_cliente_idx ON datos_pedidos(cliente_id);
CREATE INDEX dap_paciente_idx ON datos_pedidos(paciente_id);
CREATE UNIQUE INDEX dap_pedido_nro_idx ON datos_pedidos(pedido_nro);
CREATE INDEX dap_pedido_nro_ext_idx ON datos_pedidos(pedido_nro_ext);
CREATE INDEX dap_estado_idx ON datos_pedidos (estado_id);
CREATE INDEX dap_loca_idx ON datos_pedidos (pedido_loca_id);
CREATE INDEX dap_cancelado_false ON datos_pedidos (pedido_cancelado )WHERE pedido_cancelado=false;
CREATE INDEX dap_cancelado_true ON datos_pedidos (pedido_cancelado) WHERE pedido_cancelado=true;
CREATE INDEX dap_usuario_idx ON datos_pedidos(pedido_usuario_id);
CREATE INDEX dap_preparado_false ON datos_pedidos (pedido_preparado)WHERE pedido_preparado=false;
CREATE INDEX dap_preparado_true ON datos_pedidos (pedido_preparado) WHERE pedido_preparado=true;
CREATE INDEX dap_preparado_usuario_idx ON datos_pedidos(pedido_preparado_usuario_id);


CREATE SEQUENCE seq_detalle_pedidos_detalle_id;

CREATE TABLE detalle_pedidos (

    pedido_id bigint NOT NULL,
    producto_id bigint NOT NULL,
    cantidad integer NOT NULL,
    pedido_detalle_id bigint DEFAULT nextval('seq_detalle_pedidos_detalle_id')   

) WITH OIDS;

CREATE INDEX dep_pedido_idx ON detalle_pedidos (pedido_id);
CREATE INDEX dep_producto_idx ON detalle_pedidos (producto_id);
CREATE INDEX  dep_pedido_cant_idx ON detalle_pedidos (cantidad);
CREATE UNIQUE INDEX  dep_pedido_detalle_idx ON detalle_pedidos(pedido_detalle_id);

CREATE SEQUENCE seq_datos_provincias_id;

CREATE TABLE datos_provincias (

    provincia_id integer NOT NULL,
    provincia_nombre varchar(100) NOT NULL,
    provincia_obs text,
    provincia_activa boolean DEFAULT 't',
    provincia_fecha_hora timestamp DEFAULT now(),
    provincia_usuario_id smallint NOT NULL,
    provincia_inactiva_fecha_hora timestamp,
    provincia_inactiva_usuario_id smallint

) WITH OIDS;

CREATE UNIQUE INDEX daprov_prov_idx ON datos_provincias (provincia_id);


CREATE SEQUENCE seq_datos_loca_idx;

CREATE TABLE datos_localidades (

    localidad_id integer NOT NULL,
    localidad_nombre varchar(100) NOT NULL,
    localidad_prov_id smallint NOT NULL,
    localidad_obs text,
    localidad_activa boolean DEFAULT 't',
    localidad_fecha_hora timestamp DEFAULT now(),
    localidad_usuario_id smallint NOT NULL,
    localidad_inactiva_fecha_hora timestamp,
    localidad_inactiva_usuario_id smallint

) WITH OIDS;

CREATE UNIQUE INDEX daloca_loca_idx ON datos_localidades(localidad_id);
CREATE  INDEX daloca_prov_idx ON datos_localidades(localidad_prov_id);


ALTER TABLE datos_productos ADD COLUMN producto_gtin varchar(14);