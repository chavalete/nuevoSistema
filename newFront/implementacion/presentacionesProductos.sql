/*
CREATE TABLE presentaciones_productos (
pres_prod_id integer NOT NULL,
presentacion_id integer NOT NULL, 
producto_id integer NOT NULL,
gtin varchar(14),
) WITH oids;

CREATE UNIQUE INDEX idxu_presentacion_productos_prese_produ_unid ON presentacion_productos (presentacion_id, producto_id, unidades);
*/

CREATE TABLE datos_presentaciones(
presentacion_id integer NOT NULL,
descripcion varchar(50) NOT NULL
)  WITH oids;
CREATE UNIQUE INDEX idxu_datos_presentaciones_pres_id ON datos_presentaciones(presentacion_id);


ALTER TABLE datos_productos ADD forma varchar;
ALTER TABLE datos_productos ADD relacion_monodroga_id integer;

ALTER TABLE datos_trazabilidad ADD relacion_id integer NOT NULL;

ALTER TABLE detalle_movimientos ADD unidades integer NOT NULL;

--MOVIMIENTOS PARA CONSOLIDADO Y DESCONSOLIDADO DE PALLET
INSERT INTO tipos_movimientos_trazas VALUES (11, 'Baja por Desconsolidado', FALSE);
INSERT INTO tipos_movimientos_trazas VALUES (12, 'Alta por por Desconsolidado', TRUE);

INSERT INTO tipos_movimientos_trazas VALUES (13, 'Baja por Consolidado', FALSE);
INSERT INTO tipos_movimientos_trazas VALUES (14, 'Alta por Consolidado', TRUE);




--NUEVO BIOFARMA

--CODIGO
--Detalle_MovimientoRecepcionExtendido.php
--DatoTrazabilidad.php
--DatoTrazabilidadExtendido.php
--PresentacionesProductos.php
--PresentacionesProductosExtendido.php


--Producto.php
--ProductoExtendido.php
--FrenteProductos.php

--FrenteMovimientos.php
--FuncionesComunes.php

--impresionEtiquetaEstadar.php
--datamatrixEtiqueta.php

--FrenteLotes.php
--LoteExtendido.php



--movimientos_entradas_altaStock.php -->cambien a donde va el autocompletado

--DATA BASE

--NO, SALE DE LA RELACION DE PRES_PROD
--ALTER TABLE presentaciones_productos ADD envases integer default 1; --ENVASES A INFORMAR A SENASA.
--ALTER TABLE presentaciones_productos ALTER unidades set default 1; --PESO  DE CADA PRESENTACION. 

--NO , SALE DE LA RELACION DE PRES_PROD
--ALTER TABLE datos_trazabilidad ADD unidades INTEGER default 1; -- PESO DE CADA PRESENTACION.
--ALTER TABLE datos_trazabilidad ADD envases INTEGER default 1; -- ENVASES A INFORMAR A SENASA.

--NO, LO DEJAMOS PARA CUANDO SOPORTEMOS LOTE O SERIADO
--ALTER TABLE datos_productos ADD producto_seriado boolean DEFAULT FALSE;
--Productos se agregó el campo producto_seriado -- si -> Genera la trazabilidad por serie / no -> No genera serie , la trazabilidad se realiza por lote
--Alta de stock por codigo AGREGAR CAMPO estanteria_origen
-- ALTER TABLE detalle_movimientos ADD relacion_id_pp INTEGER; no va!!


ALTER TABLE datos_productos ADD unidad_medida_id INTEGER;
UPDATE datos_productos SET unidad_medida_id = 1 ;
ALTER TABLE datos_productos ALTER unidad_medida_id SET NOT NULL;

CREATE TABLE tipos_unidad_medida(
id_unidad integer,
unidad_nombre varchar(100),
unidad_abreviatura varchar(10)
)WITH oids;
CREATE UNIQUE INDEX idx_tipos_unidad_medida_id_unidad ON tipos_unidad_medida(id_unidad);

INSERT INTO tipos_unidad_medida VALUES (1,'Kilogramos','Kg');
INSERT INTO tipos_unidad_medida VALUES (2,'Litros','Lts');

ALTER TABLE datos_trazabilidad DROP relacion_id;

ALTER TABLE datos_trazabilidad ADD es_agrupador boolean DEFAULT false;

alter table datos_movimientos ADD a_id_movimiento integer;
 
ALTER TABLE detalle_pedidos ADD unidades integer NOT NULL;

ALTER TABLE pedidos_lotes ADD unidades INTEGER NOT NULL;


--Frente
--datos_productos se agragó el campo unidad_medida_id -- Segun table tipos_unidad_medida
--datos_productos se agragó el campo unidades --  unidades en kilos/litros/etc del producto
--ABM de unidad de medida -- La tabla es tipos_unidad_medida
--agregar los movimientos de consolidado y desconsolidado al combo del alta por codigo
      --<option value="13">Consolidado</option>
      --<option value="11">Desconsolidado</option>
--agregar unidades en el detalle del pedido -- lupa
--agregar unidades en la preparacion del pedido -- SALIDAS


--Error Frente 
--ABM de productos , al editar el producto, el campo "Producto Anexo" simpre aparece simpre en "No".
--En el resumen del cierre de pedidos el titulo del modal "Alta de Stock" y el boton dice "hacer alta"
--En el resumen de alta de HDR, no le llega el campo obs.
--En el resumen de alta de HDR, el titulo del modal dice "salida de stock.
--En el resumen del alta por codigo no pasa la obs.




