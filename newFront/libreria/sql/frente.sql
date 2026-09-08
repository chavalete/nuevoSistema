CREATE SCHEMA frente;

CREATE TABLE frente.datos_modelo
    (
    modelo_id integer,
    modelo_nombre varchar(30),
    fecha_alta timestamp DEFAULT now()::timestamp
    ) WITH OIDS;



CREATE INDEX "frente.da_modelo_idx" ON frente.datos_modelo (modelo_id);

CREATE SEQUENCE frente.seq_detalle_modelo_id;

CREATE TABLE frente.detalle_modelo
    (
    modelo_id integer,
    display varchar,
    name varchar,
    sortable boolean default 't',
    align varchar,
    editable boolean DEFAULT 'f',
    orden_nro integer,
    detalle_modelo_id bigint default nextval('frente.seq_detalle_modelo_id')
    ) WITH OIDS;

CREATE INDEX "frente.de_modelo_idx" ON frente.detalle_modelo (modelo_id);


INSERT INTO frente.datos_modelo VALUES (1,'transacciones');


INSERT INTO frente.detalle_modelo VALUES (1,'Nro','Nro','t','center','f',1);
INSERT INTO frente.detalle_modelo VALUES (1,'Fecha Hora','fecha_hora','t','center','f',2);
INSERT INTO frente.detalle_modelo VALUES (1,'Origen','Origen','t','center','f',3);
INSERT INTO frente.detalle_modelo VALUES (1,'Destino','Destino','t','center','f',4);
INSERT INTO frente.detalle_modelo VALUES (1,'Tipo Movimiento','TipoMovimiento','t','center','f',5);
INSERT INTO frente.detalle_modelo VALUES (1,'Remito/Factura','Remito/Facturas','t','center','f',6);
INSERT INTO frente.detalle_modelo VALUES (1,'Herramientas','herramientas','f','center','f',7);

INSERT INTO frente.datos_modelo VALUES (2,'abmClientes');
INSERT INTO frente.detalle_modelo VALUES (2,'Nombre','cliente_nombre','t','center','t',1);
INSERT INTO frente.detalle_modelo VALUES (2,'Categoria Nombre','categoria_nombre','t','center','t',2);
INSERT INTO frente.detalle_modelo VALUES (3,'Observaciones','observaciones','t','center','t',3);
INSERT INTO frente.detalle_modelo VALUES (2,'Activo','cliente_activo','t','center','t',4);
INSERT INTO frente.detalle_modelo VALUES (2,'Herramientas','herramientas','f','center','f',5);

INSERT INTO frente.datos_modelo VALUES (3,'abmMedicos');
INSERT INTO frente.detalle_modelo VALUES (3,'Nombre','medico_nombre','t','center','t',1);
INSERT INTO frente.detalle_modelo VALUES (3,'Nro Matricula','matricula_nro','t','center','t',2);
INSERT INTO frente.detalle_modelo VALUES (3,'Observaciones','observaciones','t','center','t',3);
INSERT INTO frente.detalle_modelo VALUES (3,'Activo','medico_activo','t','center','t',4);
INSERT INTO frente.detalle_modelo VALUES (3,'Herramientas','herramientas','f','center','f',5);


INSERT INTO frente.datos_modelo VALUES (4,'abmProvee');
INSERT INTO frente.detalle_modelo VALUES (4,'Nombre','prove_nombre','t','center','t',1);
INSERT INTO frente.detalle_modelo VALUES (4,'Categoria Nombre','categoria_nombre','t','center','t',2);
INSERT INTO frente.detalle_modelo VALUES (4,'Observaciones','observaciones','t','center','t',3);
INSERT INTO frente.detalle_modelo VALUES (4,'Activo','prove_activo','t','center','t',4);
INSERT INTO frente.detalle_modelo VALUES (4,'Herramientas','herramientas','f','center','f',5);


INSERT INTO frente.datos_modelo VALUES (5,'abmMarcas');
INSERT INTO frente.detalle_modelo VALUES (5,'Nombre','marca_nombre','t','center','t',1);
INSERT INTO frente.detalle_modelo VALUES (5,'Observaciones','observaciones','t','center','t',2);
INSERT INTO frente.detalle_modelo VALUES (5,'Activa','marca_activa','t','center','t',3);
INSERT INTO frente.detalle_modelo VALUES (5,'Herramientas','herramientas','f','center','f',4);


INSERT INTO frente.datos_modelo VALUES (6,'abmCategorias');
INSERT INTO frente.detalle_modelo VALUES (6,'Nombre','categoria_nombre','t','center','t',1);
INSERT INTO frente.detalle_modelo VALUES (6,'Observaciones','observaciones','t','center','t',2);
INSERT INTO frente.detalle_modelo VALUES (6,'Activa','categoria_activa','t','center','t',3);
INSERT INTO frente.detalle_modelo VALUES (6,'Herramientas','herramientas','f','center','f',4);

INSERT INTO frente.datos_modelo VALUES (7,'entradas');

INSERT INTO frente.detalle_modelo VALUES (7,'Nro','id_movimiento','t','center','f',1);
INSERT INTO frente.detalle_modelo VALUES (7,'Fecha Hora','fecha_hora','t','center','f',2);
INSERT INTO frente.detalle_modelo VALUES (7,'Proveedor','prove_id','t','center','f',3);
INSERT INTO frente.detalle_modelo VALUES (7,'Comprobante','Comprobante','t','center','f',3);
INSERT INTO frente.detalle_modelo VALUES (7,'Tipo Movimiento','tipo_movimiento_id','t','center','f',4);
INSERT INTO frente.detalle_modelo VALUES (7,'Herramientas','herramientas','f','center','f',5);

INSERT INTO frente.datos_modelo VALUES (8,'abmProductos');

INSERT INTO frente.detalle_modelo VALUES (8,'Nombre','producto_nombre','t','center','f',1);
INSERT INTO frente.detalle_modelo VALUES (8,'Presentacion','producto_presentacion','t','center','f',2);
INSERT INTO frente.detalle_modelo VALUES (8,'Proveedor','prove_id','t','center','f',3);
INSERT INTO frente.detalle_modelo VALUES (8,'Marca','marca_id','t','center','f',4);
INSERT INTO frente.detalle_modelo VALUES (8,'CodigoReferencia','codigo_referencia','t','center','f',5);
INSERT INTO frente.detalle_modelo VALUES (8,'Herramientas','herramientas','f','center','f',6);

INSERT INTO frente.datos_modelo VALUES (9,'abmAlmacenes');
INSERT INTO frente.detalle_modelo VALUES (9,'Nombre','almacen_nombre','t','center','t',1);
INSERT INTO frente.detalle_modelo VALUES (9,'Sucursal Nombre','sucursal_nombre','t','center','t',2);
INSERT INTO frente.detalle_modelo VALUES (9,'Observaciones','observaciones','t','center','t',3);
INSERT INTO frente.detalle_modelo VALUES (9,'Activo','prove_activo','t','center','t',4);
INSERT INTO frente.detalle_modelo VALUES (9,'Herramientas','herramientas','f','center','f',5);


INSERT INTO frente.datos_modelo VALUES (10,'salidas');

INSERT INTO frente.detalle_modelo VALUES (10,'Nro','id_movimiento','t','center','f',1);
INSERT INTO frente.detalle_modelo VALUES (10,'Fecha Hora','fecha_hora','t','center','f',2);
INSERT INTO frente.detalle_modelo VALUES (10,'Cliente','cliente_id','t','center','f',3);
INSERT INTO frente.detalle_modelo VALUES (10,'Medico','medico_id','t','center','f',4);
INSERT INTO frente.detalle_modelo VALUES (10,'Paciente','paciente_id','t','center','f',5);
INSERT INTO frente.detalle_modelo VALUES (10,'Comprobante','Comprobante','t','center','f',6);
INSERT INTO frente.detalle_modelo VALUES (10,'Tipo Movimiento','tipo_movimiento_id','t','center','f',7);
INSERT INTO frente.detalle_modelo VALUES (10,'Herramientas','herramientas','f','center','f',8);

INSERT INTO frente.datos_modelo VALUES (11,'abmPacientes');
INSERT INTO frente.detalle_modelo VALUES (11,'Nombre','paciente_nombre','t','center','t',1);
INSERT INTO frente.detalle_modelo VALUES (11,'Observaciones','observaciones','t','center','t',2);
INSERT INTO frente.detalle_modelo VALUES (11,'Activo','paciente_activo','t','center','t',3);
INSERT INTO frente.detalle_modelo VALUES (11,'Herramientas','herramientas','f','center','f',4);


INSERT INTO frente.datos_modelo VALUES (12,'abmEstanterias');
INSERT INTO frente.detalle_modelo VALUES (12,'Nombre','estanteria_nombre','t','center','t',1);
INSERT INTO frente.detalle_modelo VALUES (12,'Almacen','almacen_nombre','t','center','t',2);
INSERT INTO frente.detalle_modelo VALUES (12,'Observaciones','observaciones','t','center','t',3);
INSERT INTO frente.detalle_modelo VALUES (12,'Activo','estanteria_activa','t','center','t',4);
INSERT INTO frente.detalle_modelo VALUES (12,'Herramientas','herramientas','f','center','f',5);


INSERT INTO frente.datos_modelo VALUES (13,'abmObrasSociales');
INSERT INTO frente.detalle_modelo VALUES (13,'Nombre','obra_social_nombre','t','center','t',1);
INSERT INTO frente.detalle_modelo VALUES (13,'Observaciones','observaciones','t','center','t',2);
INSERT INTO frente.detalle_modelo VALUES (13,'Activa','obra_social_activa','t','center','t',3);
INSERT INTO frente.detalle_modelo VALUES (13,'Herramientas','herramientas','f','center','f',4);

INSERT INTO frente.datos_modelo VALUES (14,'abmSucursales');
INSERT INTO frente.detalle_modelo VALUES (14,'Nombre','sucursal_nombre','t','center','t',1);
INSERT INTO frente.detalle_modelo VALUES (14,'Observaciones','observaciones','t','center','t',2);
INSERT INTO frente.detalle_modelo VALUES (14,'Activa','sucursal_activa','t','center','t',3);
INSERT INTO frente.detalle_modelo VALUES (14,'Herramientas','herramientas','f','center','f',4);



              $objMovimiento->getMovimietoFechaHora(),//fecha_hora
                                $objMovimiento->getObjTipoMovimiento()->getTipoMovimientoNombre(),//nombre del movimiento 
                                $objMovimiento->getObjDatoMovimiento()->getRemitoNro() . " / " . $objMovimiento->getObjDatoMovimiento()->getFacturaNro(),//remito/factura
                                $objMovimiento->getObjDetalleMovimiento()->getObjSucursal()->getSucursalNombre(),
                                $objMovimiento->getObjDetalleMovimiento()->getObjAlmacen->getAlmacenNombre(),
                                $objMovimiento->getObjDetalleMovimiento()->getObjEstanteria->getEstanteriaNombre(),
                                $objMovimiento->getObjDetalleMovimiento()->getObjLote->getLote(),
                                $objMovimiento->getObjDetalleMovimiento()->getObjLote->getLoteVencimiento(),
                                $objMovimiento->getObjDetalleMovimiento()->getCantidad(),
                                $this->getTotalAcumulado()

INSERT INTO frente.datos_modelo VALUES (15,'auditorStock');

INSERT INTO frente.detalle_modelo VALUES (15,'Nro','id_movimiento','t','center','f',1);
INSERT INTO frente.detalle_modelo VALUES (15,'Tipo Movimiento','tipo_movimiento_id','t','center','f',2);
INSERT INTO frente.detalle_modelo VALUES (15,'Fecha Hora','movimiento_fecha_hora','t','center','f',3);
INSERT INTO frente.detalle_modelo VALUES (15,'Comprobante','Comprobante','t','center','f',4);
INSERT INTO frente.detalle_modelo VALUES (15,'Sucursal','sucursal_id','t','center','f',5);
INSERT INTO frente.detalle_modelo VALUES (15,'Almacen','almacen_id','t','center','f',6);
INSERT INTO frente.detalle_modelo VALUES (15,'Estanteria','estanteria_id','t','center','f',7);
INSERT INTO frente.detalle_modelo VALUES (15,'Lote','lote_id','t','center','f',8);
INSERT INTO frente.detalle_modelo VALUES (15,'Vencimiento','lote_vencimiento','t','center','f',9);
INSERT INTO frente.detalle_modelo VALUES (15,'Cantidad','cantidad','t','center','f',9);
INSERT INTO frente.detalle_modelo VALUES (15,'Herramientas','herramientas','f','center','f',10);



INSERT INTO frente.datos_modelo VALUES (18,'entradasCodigos');

INSERT INTO frente.detalle_modelo VALUES (18,'Nro','id_movimiento','t','center','f',1);
INSERT INTO frente.detalle_modelo VALUES (18,'Fecha Hora','fecha_hora','t','center','f',2);
INSERT INTO frente.detalle_modelo VALUES (18,'Proveedor','prove_id','t','center','f',3);
INSERT INTO frente.detalle_modelo VALUES (18,'Comprobante','Comprobante','t','center','f',3);
INSERT INTO frente.detalle_modelo VALUES (18,'Tipo Movimiento','tipo_movimiento_id','t','center','f',4);
INSERT INTO frente.detalle_modelo VALUES (18,'Herramientas','herramientas','f','center','f',5);




INSERT INTO frente.datos_modelo VALUES (19,'controlStock');

INSERT INTO frente.detalle_modelo VALUES (19,'Estanteria','estanteria_id','f','center','f',1);
INSERT INTO frente.detalle_modelo VALUES (19,'Producto Nombre','producto_id_id','f','center','f',2);
INSERT INTO frente.detalle_modelo VALUES (19,'Codigos Leidos','fecha_hora','f','center','f',3);
INSERT INTO frente.detalle_modelo VALUES (19,'Total','prove_id','t','center','f',4);
INSERT INTO frente.detalle_modelo VALUES (19,'Codigos Faltantes','prove_id','f','center','f',5);
INSERT INTO frente.detalle_modelo VALUES (19,'Cantidad','prove_id','f','center','f',6);
INSERT INTO frente.detalle_modelo VALUES (19,'Diferencia','prove_id','f','center','f',7);
