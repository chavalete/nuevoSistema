CREATE SEQUENCE seq_datos_marcas_id;

CREATE TABLE datos_marcas 
	(
	marca_id smallint NOT NULL PRIMARY KEY,
	marca_nombre varchar(100) NOT NULL,
	marca_activa boolean DEFAULT 't',
	marca_fecha_alta timestamp DEFAULT now()::timestamp,
	marca_usuario_alta smallint REFERENCES datos_usuarios (usuario_id),
	observaciones varchar
	) WITH OIDS;

CREATE UNIQUE INDEX da_marca_marca_idx ON datos_marcas (marca_id);
CREATE INDEX da_marca_marca_activa_true ON datos_marcas (marca_activa) WHERE  (marca_activa=true);
CREATE INDEX da_marca_marca_activa_false ON datos_marcas (marca_activa) WHERE (marca_activa=false);

CREATE SEQUENCE seq_datos_clientes_id;

CREATE TABLE datos_clientes 
	(
	cliente_id bigint  NOT NULL PRIMARY KEY,
	cliente_nombre varchar(100),
	cliente_cuit varchar(13),
	categoria_id smallint,
	observaciones text,
	cliente_activo boolean DEFAULT 't',
	cliente_fecha_alta date,
	cliente_usuario_alta  smallint REFERENCES datos_usuarios (usuario_id)
	) WITH OIDS;

CREATE UNIQUE INDEX da_cli_cliente_idx ON datos_clientes (cliente_id);
CREATE INDEX da_cli_cliente_activo_true ON datos_clientes (cliente_activo )WHERE (cliente_activo=true);
CREATE INDEX da_cli_ciente_activo_false ON datos_clientes (cliente_activo   )WHERE (cliente_activo=true);

CREATE SEQUENCE seq_datos_medicos_id;

CREATE TABLE datos_medicos
	(
	medico_id integer PRIMARY KEY NOT NULL,
	medico_nombre varchar(100),
	matricula_nro bigint,
	medico_activo boolean DEFAULT 't',
	observaciones varchar,
	medico_fecha_alta date DEFAULT now()::date,
	medico_usuario_alta smallint REFERENCES datos_usuarios (usuario_id)
	) WITH OIDS;

CREATE UNIQUE INDEX da_medi_medico_idx ON datos_medicos (medico_id);
CREATE INDEX da_medi_matricula_idx ON datos_medicos (matricula_nro);
CREATE INDEX da_medi_medico_activo_true ON datos_medicos (medico_activo) WHERE (medico_activo=true);
CREATE INDEX da_medi_medico_activo_false ON datos_medicos (medico_activo) WHERE (medico_activo=false);

CREATE SEQUENCE seq_datos_pacientes_id;

CREATE TABLE datos_pacientes
	(
	paciente_id bigint not null PRIMARY KEY,
	paciente_nombre VARCHAR(100) NOT NULL,
	observaciones varchar,
	paciente_fecha_alta date DEFAULT now()::date,
	paciente_usuario_alta smallint REFERENCES datos_usuarios (usuario_id),
	paciente_activo boolean DEFAULT 't'
	) WITH OIDS;

CREATE UNIQUE INDEX da_paci_paciente_idx ON datos_pacientes (paciente_id);
CREATE INDEX da_paci_paciente_activo_true ON datos_pacientes (paciente_activo) WHERE (paciente_activo=true);
CREATE INDEX da_paci_paciente_activo_false ON datos_pacientes (paciente_activo) WHERE (paciente_activo=false);


CREATE SEQUENCE seq_datos_categorias_id;

CREATE TABLE datos_categorias
	(
	categoria_id integer NOT NULL PRIMARY KEY,
	categoria_nombre varchar(100),
	categoria_activa boolean DEFAULT 't',
	observaciones varchar,
	categoria_fecha_alta date DEFAULT now()::date,
	categoria_usuario_alta smallint REFERENCES datos_categorias (categoria_id)
	) WITH OIDS;

CREATE UNIQUE INDEX da_cate_categoria_idx ON datos_categorias (categoria_id);
CREATE INDEX da_cate_categoria_activa_true ON datos_categorias (categoria_activa) WHERE (categoria_activa=true);
CREATE INDEX da_cate_categoria_activa_false ON datos_categorias (categoria_activa) WHERE (categoria_activa=false);

CREATE SEQUENCE seq_datos_obras_sociales_id;

CREATE TABLE datos_obras_sociales
	(
	obra_social_id integer NOT NULL PRIMARY KEY,
	obra_social_nombre varchar(100) NOT NULL,
	obra_social_activa boolean DEFAULT 't',
	observaciones varchar,
	obra_social_fecha_alta date DEFAULT now()::date,
	obra_social_usuario_alta smallint REFERENCES datos_usuarios (usuario_id)
	) WITH OIDS;

CREATE UNIQUE INDEX da_obra_social_idx ON datos_obras_sociales (obra_social_id);
CREATE INDEX da_obra_social_activa_true ON datos_obras_sociales (obra_social_activa) WHERE (obra_social_activa=true);
CREATE INDEX da_obra_social_activa_false ON datos_obras_sociales (obra_social_activa) WHERE (obra_social_activa=false);

CREATE SEQUENCE seq_datos_proveedor_id;

CREATE TABLE datos_proveedores 
	(
	prove_id integer NOT NULL PRIMARY KEY,
	prove_nombre varchar(100) NOT NULL,
	prove_cuit varchar(13),
	categoria_id smallint,
	prove_activo boolean DEFAULT 't',
	prove_fecha_alta timestamp DEFAULT now()::timestamp,
	prove_usuario_alta smallint REFERENCES datos_usuarios (usuario_id),
	observaciones varchar
	) WITH OIDS;

CREATE UNIQUE INDEX da_provee_prove_idx ON datos_proveedores (prove_id);
CREATE INDEX da_provee_prove_activo_true ON datos_proveedores (prove_activo) WHERE (prove_activo=true);
CREATE INDEX da_provee_prove_activo_false ON datos_proveedores (prove_activo) WHERE (prove_activo=false);



CREATE SEQUENCE seq_datos_condicion_venta_id;

CREATE TABLE datos_condicion_venta
	(
	condicion_venta_id integer NOT NULL PRIMARY KEY,
	condicion_venta_nombre varchar(100),
	condicion_venta_activa boolean DEFAULT 't',
	observaciones varchar,
	condicion_venta_fecha_alta date DEFAULT now()::date,
	condicion_venta_usuario_alta smallint
	) WITH OIDS;

CREATE UNIQUE INDEX da_cond_vent_condicion_venta_idx ON datos_condicion_venta (condicion_venta_id);
CREATE INDEX da_cond_vent_condicion_venta_activa_true ON datos_condicion_venta (condicion_venta_activa) WHERE (condicion_venta_activa=true);
CREATE INDEX da_cond_vent_condicion_venta_activa_false ON datos_condicion_venta (condicion_venta_activa) WHERE (condicion_venta_activa=false);
