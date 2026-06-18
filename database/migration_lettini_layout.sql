-- =============================================================
-- Igea Club — Migrazione layout lettini (v2)
-- ATTENZIONE: azzera entry_places, reservation_places, pool_places, pool_areas
-- Eseguire PRIMA dell'avvio in produzione o su DB di test.
-- =============================================================

USE igea_pool_manager;
SET FOREIGN_KEY_CHECKS = 0;

TRUNCATE TABLE entry_places;
TRUNCATE TABLE reservation_places;
TRUNCATE TABLE pool_places;
TRUNCATE TABLE pool_areas;

SET FOREIGN_KEY_CHECKS = 1;

-- AREE
INSERT INTO pool_areas (id, name, description, active) VALUES
(1, 'Solarium',        'File SA–SE vicino alla piscina', 1),
(2, 'Zona Ombrelloni', 'Blocco sinistro (colonne OA–OD) + blocco destro (file ORA–ORD)', 1);

-- ============================================================
-- SOLARIUM — 5 file orizzontali
-- SA = fila più arretrata (10 posti)
-- SB–SC = con ombrellone sx (9 posti ciascuna)
-- SD–SE = file centrali (9 posti ciascuna)
-- ============================================================

-- Fila SA (10 lettini)
INSERT INTO pool_places (area_id,code,row_label,number,type,base_price,status) VALUES
(1,'SA01','SA',1,'lettino',8.00,'disponibile'),(1,'SA02','SA',2,'lettino',8.00,'disponibile'),
(1,'SA03','SA',3,'lettino',8.00,'disponibile'),(1,'SA04','SA',4,'lettino',8.00,'disponibile'),
(1,'SA05','SA',5,'lettino',8.00,'disponibile'),(1,'SA06','SA',6,'lettino',8.00,'disponibile'),
(1,'SA07','SA',7,'lettino',8.00,'disponibile'),(1,'SA08','SA',8,'lettino',8.00,'disponibile'),
(1,'SA09','SA',9,'lettino',8.00,'disponibile'),(1,'SA10','SA',10,'lettino',8.00,'disponibile');

-- Fila SB (9 lettini — ombrellone sinistra)
INSERT INTO pool_places (area_id,code,row_label,number,type,base_price,status) VALUES
(1,'SB01','SB',1,'lettino',8.00,'disponibile'),(1,'SB02','SB',2,'lettino',8.00,'disponibile'),
(1,'SB03','SB',3,'lettino',8.00,'disponibile'),(1,'SB04','SB',4,'lettino',8.00,'disponibile'),
(1,'SB05','SB',5,'lettino',8.00,'disponibile'),(1,'SB06','SB',6,'lettino',8.00,'disponibile'),
(1,'SB07','SB',7,'lettino',8.00,'disponibile'),(1,'SB08','SB',8,'lettino',8.00,'disponibile'),
(1,'SB09','SB',9,'lettino',8.00,'disponibile');

-- Fila SC (9 lettini — ombrellone sinistra)
INSERT INTO pool_places (area_id,code,row_label,number,type,base_price,status) VALUES
(1,'SC01','SC',1,'lettino',8.00,'disponibile'),(1,'SC02','SC',2,'lettino',8.00,'disponibile'),
(1,'SC03','SC',3,'lettino',8.00,'disponibile'),(1,'SC04','SC',4,'lettino',8.00,'disponibile'),
(1,'SC05','SC',5,'lettino',8.00,'disponibile'),(1,'SC06','SC',6,'lettino',8.00,'disponibile'),
(1,'SC07','SC',7,'lettino',8.00,'disponibile'),(1,'SC08','SC',8,'lettino',8.00,'disponibile'),
(1,'SC09','SC',9,'lettino',8.00,'disponibile');

-- Fila SD (9 lettini)
INSERT INTO pool_places (area_id,code,row_label,number,type,base_price,status) VALUES
(1,'SD01','SD',1,'lettino',8.00,'disponibile'),(1,'SD02','SD',2,'lettino',8.00,'disponibile'),
(1,'SD03','SD',3,'lettino',8.00,'disponibile'),(1,'SD04','SD',4,'lettino',8.00,'disponibile'),
(1,'SD05','SD',5,'lettino',8.00,'disponibile'),(1,'SD06','SD',6,'lettino',8.00,'disponibile'),
(1,'SD07','SD',7,'lettino',8.00,'disponibile'),(1,'SD08','SD',8,'lettino',8.00,'disponibile'),
(1,'SD09','SD',9,'lettino',8.00,'disponibile');

-- Fila SE (9 lettini)
INSERT INTO pool_places (area_id,code,row_label,number,type,base_price,status) VALUES
(1,'SE01','SE',1,'lettino',8.00,'disponibile'),(1,'SE02','SE',2,'lettino',8.00,'disponibile'),
(1,'SE03','SE',3,'lettino',8.00,'disponibile'),(1,'SE04','SE',4,'lettino',8.00,'disponibile'),
(1,'SE05','SE',5,'lettino',8.00,'disponibile'),(1,'SE06','SE',6,'lettino',8.00,'disponibile'),
(1,'SE07','SE',7,'lettino',8.00,'disponibile'),(1,'SE08','SE',8,'lettino',8.00,'disponibile'),
(1,'SE09','SE',9,'lettino',8.00,'disponibile');

-- ============================================================
-- ZONA OMBRELLONI — Blocco SINISTRO
-- 4 colonne verticali (OA, OB, OC, OD) × 8 file
-- OA+OB = coppia sinistra | OC+OD = coppia destra (gap tra le coppie)
-- Più 2 lettini extra nella riga bassa (OE01, OE02)
-- ============================================================

-- Colonna OA
INSERT INTO pool_places (area_id,code,row_label,number,type,base_price,status) VALUES
(2,'OA01','OA',1,'lettino',7.00,'disponibile'),(2,'OA02','OA',2,'lettino',7.00,'disponibile'),
(2,'OA03','OA',3,'lettino',7.00,'disponibile'),(2,'OA04','OA',4,'lettino',7.00,'disponibile'),
(2,'OA05','OA',5,'lettino',7.00,'disponibile'),(2,'OA06','OA',6,'lettino',7.00,'disponibile'),
(2,'OA07','OA',7,'lettino',7.00,'disponibile'),(2,'OA08','OA',8,'lettino',7.00,'disponibile');

-- Colonna OB
INSERT INTO pool_places (area_id,code,row_label,number,type,base_price,status) VALUES
(2,'OB01','OB',1,'lettino',7.00,'disponibile'),(2,'OB02','OB',2,'lettino',7.00,'disponibile'),
(2,'OB03','OB',3,'lettino',7.00,'disponibile'),(2,'OB04','OB',4,'lettino',7.00,'disponibile'),
(2,'OB05','OB',5,'lettino',7.00,'disponibile'),(2,'OB06','OB',6,'lettino',7.00,'disponibile'),
(2,'OB07','OB',7,'lettino',7.00,'disponibile'),(2,'OB08','OB',8,'lettino',7.00,'disponibile');

-- Colonna OC
INSERT INTO pool_places (area_id,code,row_label,number,type,base_price,status) VALUES
(2,'OC01','OC',1,'lettino',7.00,'disponibile'),(2,'OC02','OC',2,'lettino',7.00,'disponibile'),
(2,'OC03','OC',3,'lettino',7.00,'disponibile'),(2,'OC04','OC',4,'lettino',7.00,'disponibile'),
(2,'OC05','OC',5,'lettino',7.00,'disponibile'),(2,'OC06','OC',6,'lettino',7.00,'disponibile'),
(2,'OC07','OC',7,'lettino',7.00,'disponibile'),(2,'OC08','OC',8,'lettino',7.00,'disponibile');

-- Colonna OD
INSERT INTO pool_places (area_id,code,row_label,number,type,base_price,status) VALUES
(2,'OD01','OD',1,'lettino',7.00,'disponibile'),(2,'OD02','OD',2,'lettino',7.00,'disponibile'),
(2,'OD03','OD',3,'lettino',7.00,'disponibile'),(2,'OD04','OD',4,'lettino',7.00,'disponibile'),
(2,'OD05','OD',5,'lettino',7.00,'disponibile'),(2,'OD06','OD',6,'lettino',7.00,'disponibile'),
(2,'OD07','OD',7,'lettino',7.00,'disponibile'),(2,'OD08','OD',8,'lettino',7.00,'disponibile');

-- Riga bassa parziale (2 lettini — sotto la coppia sinistra OA+OB)
INSERT INTO pool_places (area_id,code,row_label,number,type,base_price,status) VALUES
(2,'OE01','OE',1,'lettino',7.00,'disponibile'),(2,'OE02','OE',2,'lettino',7.00,'disponibile');

-- ============================================================
-- ZONA OMBRELLONI — Blocco DESTRO
-- Sfasato in basso di 3 righe rispetto al blocco sinistro
-- OP = 2 lettini isolati in alto a destra (riga 4 del blocco sx)
-- ORA–ORC = 6 lettini per fila con ombrellone a destra
-- ORD = 5 lettini riga finale (senza ombrellone)
-- ============================================================

-- 2 lettini isolati (top right)
INSERT INTO pool_places (area_id,code,row_label,number,type,base_price,status) VALUES
(2,'OP01','OP',1,'lettino',7.00,'disponibile'),(2,'OP02','OP',2,'lettino',7.00,'disponibile');

-- Fila ORA (6 lettini — ombrellone dx)
INSERT INTO pool_places (area_id,code,row_label,number,type,base_price,status) VALUES
(2,'ORA01','ORA',1,'lettino',7.00,'disponibile'),(2,'ORA02','ORA',2,'lettino',7.00,'disponibile'),
(2,'ORA03','ORA',3,'lettino',7.00,'disponibile'),(2,'ORA04','ORA',4,'lettino',7.00,'disponibile'),
(2,'ORA05','ORA',5,'lettino',7.00,'disponibile'),(2,'ORA06','ORA',6,'lettino',7.00,'disponibile');

-- Fila ORB (6 lettini — ombrellone dx)
INSERT INTO pool_places (area_id,code,row_label,number,type,base_price,status) VALUES
(2,'ORB01','ORB',1,'lettino',7.00,'disponibile'),(2,'ORB02','ORB',2,'lettino',7.00,'disponibile'),
(2,'ORB03','ORB',3,'lettino',7.00,'disponibile'),(2,'ORB04','ORB',4,'lettino',7.00,'disponibile'),
(2,'ORB05','ORB',5,'lettino',7.00,'disponibile'),(2,'ORB06','ORB',6,'lettino',7.00,'disponibile');

-- Fila ORC (6 lettini — ombrellone dx)
INSERT INTO pool_places (area_id,code,row_label,number,type,base_price,status) VALUES
(2,'ORC01','ORC',1,'lettino',7.00,'disponibile'),(2,'ORC02','ORC',2,'lettino',7.00,'disponibile'),
(2,'ORC03','ORC',3,'lettino',7.00,'disponibile'),(2,'ORC04','ORC',4,'lettino',7.00,'disponibile'),
(2,'ORC05','ORC',5,'lettino',7.00,'disponibile'),(2,'ORC06','ORC',6,'lettino',7.00,'disponibile');

-- Fila ORD (5 lettini — riga bassa, nessun ombrellone)
INSERT INTO pool_places (area_id,code,row_label,number,type,base_price,status) VALUES
(2,'ORD01','ORD',1,'lettino',7.00,'disponibile'),(2,'ORD02','ORD',2,'lettino',7.00,'disponibile'),
(2,'ORD03','ORD',3,'lettino',7.00,'disponibile'),(2,'ORD04','ORD',4,'lettino',7.00,'disponibile'),
(2,'ORD05','ORD',5,'lettino',7.00,'disponibile');

-- Totale: 46 Solarium + 34 OL + 2 OP + 23 OR = 105 lettini
