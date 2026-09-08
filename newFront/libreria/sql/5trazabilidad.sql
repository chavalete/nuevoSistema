CREATE SEQUENCE seq_datos_lotes_id;

CREATE TABLE datos_lotes 
	(
	lote_id bigint NOT NULL PRIMARY KEY,
	lote varchar NOT NULL,
	letra_griega_id smallint,
	lote_vencimiento date NOT NULL,
	lote_activo boolean DEFAULT 't',
	observaciones varchar
	) WITH OIDS;

CREATE UNIQUE INDEX da_lotes_lote_idx ON datos_lotes (lote_id);
CREATE INDEX da_lotes_lote_desc_idx ON datos_lotes (lote);
CREATE INDEX da_lotes_letra_griega_idx ON datos_lotes(letra_griega_id);
CREATE INDEX da_lotes_lote_activo_true ON datos_lotes (lote_activo) WHERE (lote_activo=true);
CREATE INDEX da_lotes_lote_activo_false ON datos_lotes (lote_activo) WHERE (lote_activo=false);

--para el tipo_movimiento_id de tipos_movimientos_trazas
CREATE SEQUENCE seq_tipos_movimientos_trazas_tipo_mov_id;

CREATE TABLE tipos_movimientos_trazas (
    tipo_movimiento_id integer NOT NULL PRIMARY KEY,
    tipo_movimiento_nombre character varying(50),
    tipo_movimiento_alta boolean);

CREATE UNIQUE INDEX tipos_movimientos_trazas_tipo_mov_idx ON tipos_movimientos_trazas (tipo_movimiento_id);

--para el trazabilidad_id de datos_trazabilidad
CREATE SEQUENCE seq_datos_trazabilidad_traza_id;

CREATE TABLE datos_trazabilidad (
    trazabilidad_id integer PRIMARY KEY NOT NULL,
    trazabilidad_codigo character varying(10) NOT NULL,
    trazabilidad_fecha_alta timestamp DEFAULT now()::timestamp,
    sucursal_id integer REFERENCES datos_sucursales(sucursal_id),
    almacen_id integer REFERENCES datos_almacenes(almacen_id),
    estanteria_id integer REFERENCES datos_estanterias(estanteria_id),
    producto_id integer REFERENCES datos_productos(producto_id) ,
    lote_id integer REFERENCES datos_lotes(lote_id),
    en_stock boolean DEFAULT true
) WITH oids;

CREATE UNIQUE INDEX da_trazabilidad_trazabilidad_idx ON datos_trazabilidad (trazabilidad_id);
CREATE INDEX da_trazabilidad_sucursal_idx ON datos_trazabilidad (sucursal_id);
CREATE INDEX da_trazabilidad_almacen_idx ON datos_trazabilidad (almacen_id);
CREATE INDEX da_trazabilidad_estanteria_idx ON datos_trazabilidad (estanteria_id);
CREATE INDEX da_trazabilidad_producto_idx ON datos_trazabilidad (producto_id);
CREATE INDEX da_trazabilidad_lote_idx ON datos_trazabilidad (lote_id);
CREATE INDEX da_trazabilidad_en_stock_true ON datos_trazabilidad (en_stock) WHERE (en_stock=true);
CREATE INDEX da_trazabilidad_en_stock_false ON datos_trazabilidad (en_stock) WHERE (en_stock=false);


CREATE SEQUENCE seq_movimientos_trazabilidad_id_movimiento;

CREATE TABLE movimientos_trazabilidad (
    trazabilidad_id integer PRIMARY KEY NOT NULL REFERENCES datos_trazabilidad (trazabilidad_id),
    fecha_hora timestamp without time zone DEFAULT now() NOT NULL,
    id_movimiento integer NOT NULL,
    tipo_movimiento_id smallint NOT NULL REFERENCES tipos_movimientos_trazas (tipo_movimiento_id),
    usuario_movimiento_id smallint
)WITH OIDS;

CREATE INDEX mov_trazabilidad_trazabilidad_id ON movimientos_trazabilidad (trazabilidad_id);
CREATE INDEX mov_trazabilidad_id_movimiento_idx ON movimientos_trazabilidad (id_movimiento);
CREATE INDEX mov_trazabilidad_tipo_mov_idx ON movimientos_trazabilidad (tipo_movimiento_id);
CREATE INDEX mov_trazabilidad_usuario_idx ON movimientos_trazabilidad (usuario_movimiento_id);

CREATE SEQUENCE seq_datos_movimientos_id_mov;

CREATE TABLE datos_movimientos (
    id_movimiento integer PRIMARY KEY NOT NULL,
    movimiento_fecha date DEFAULT now()::date,
    tipo_movimiento_id integer NOT NULL REFERENCES tipos_movimientos_trazas(tipo_movimiento_id),
    cliente_id integer REFERENCES datos_clientes (cliente_id),
    paciente_id integer REFERENCES datos_pacientes (paciente_id),
    medico_id integer REFERENCES datos_medicos (medico_id),
    obra_social_id integer REFERENCES datos_obras_sociales (obra_social_id),
    prove_id integer NOT NULL REFERENCES datos_proveedores (prove_id),
    despacho_nro integer,
    pm varchar(20),
    factura_nro varchar(20),
    remito_nro varchar(20),    
    de_id_movimiento integer REFERENCES datos_movimientos(id_movimiento),
    movimiento_usuario_id integer REFERENCES datos_usuarios (usuario_id)
) WITH OIDS;

CREATE UNIQUE INDEX da_movimientos_id_movimiento_idxs ON datos_movimientos (id_movimiento);
CREATE INDEX dm_movimientos_provi_idx ON datos_movimientos(prove_id);
CREATE INDEX dm_movientos_tipo_mov_idx ON datos_movimientos(tipo_movimiento_id);
CREATE INDEX dm_movimientos_mov_idx ON datos_movimientos(de_id_movimiento);
CREATE INDEX dm_movimientos_usuario_idxs ON datos_movimientos(movimiento_usuario_id);
CREATE INDEX dm_movimientos_cliente_idx ON datos_movimientos(cliente_id);
CREATE INDEX dm_movimientos_paciente_idx ON datos_movimientos(paciente_id);
CREATE INDEX dm_movimientos_medico_idx ON datos_movimientos(medico_id);
CREATE INDEX dm_movimientos_obra_social_idx ON datos_movimientos(obra_social_id);


CREATE SEQUENCE seq_detalle_movimientos_detalle_id;

CREATE TABLE detalle_movimientos
    (
    id_movimiento integer NOT NULL,
    detalle_id integer NOT NULL,
    sucursal_id integer REFERENCES datos_sucursales (sucursal_id),
    almacen_id integer REFERENCES datos_almacenes (almacen_id),
    estanteria_id integer REFERENCES datos_estanterias (estanteria_id),
    producto_id integer REFERENCES datos_productos (producto_id),
    lote_id integer REFERENCES datos_lotes (lote_id),
    cantidad integer
    ) WITH OIDS;

CREATE INDEX dem_movimientos_movimiento_idx ON detalle_movimientos(id_movimiento);
CREATE UNIQUE INDEX dem_movimientos_detalle_idx ON detalle_movimientos(detalle_id);
CREATE INDEX dem_movimientos_sucursal_idx ON detalle_movimientos(sucursal_id);
CREATE INDEX dem_movimientos_almacen_idx ON detalle_movimientos(almacen_id);
CREATE INDEX dem_movimientos_estanteria_id ON detalle_movimientos(estanteria_id);
CREATE INDEX dem_movimientos_producto_idx ON detalle_movimientos(producto_id);
CREATE INDEX dem_movimientos_lote_idx ON detalle_movimientos(lote_id);

--para el codigo_id de trazabilidad_codigo
CREATE SEQUENCE seq_trazabilidad_codigos_codigo_id;

CREATE TABLE trazabilidad_codigos (
    codigo_id integer NOT NULL PRIMARY KEY,
    trazabilidad_codigo character varying(10) NOT NULL
);

CREATE UNIQUE INDEX traza_codigos_codigo_idx ON trazabilidad_codigos (codigo_id);
CREATE UNIQUE INDEX traza_codigos_id_codigo_idx ON trazabilidad_codigos (codigo_id,codigo_trazabilidad);
CREATE UNIQUE INDEX traza_codigos_codigo_traza_idx ON trazabilidad_codigos (trazabilidad_codigo);

