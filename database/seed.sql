USE igea_pool_manager;

INSERT INTO users (name, email, password_hash, role) VALUES
('Amministratore', 'admin@igeaclub.it', '$2y$10$F/ijMVAUjhRL9buTgRTag.1zZTu.Fx2t5vCRmATVCE/fZP/y9bBTy', 'admin'),
('Reception', 'reception@igeaclub.it', '$2y$10$F/ijMVAUjhRL9buTgRTag.1zZTu.Fx2t5vCRmATVCE/fZP/y9bBTy', 'reception'),
('Bar', 'bar@igeaclub.it', '$2y$10$F/ijMVAUjhRL9buTgRTag.1zZTu.Fx2t5vCRmATVCE/fZP/y9bBTy', 'bar'),
('Cassa', 'cassa@igeaclub.it', '$2y$10$F/ijMVAUjhRL9buTgRTag.1zZTu.Fx2t5vCRmATVCE/fZP/y9bBTy', 'cassa');

INSERT INTO pool_areas (name, description) VALUES
('Zona Piscina Centrale', 'Area principale bordo piscina'),
('Zona Ombrelloni', 'Area ombreggiata con ombrelloni'),
('Zona Relax', 'Area tranquilla'),
('Zona Famiglie', 'Area dedicata a famiglie e gruppi');

INSERT INTO pool_places (area_id, code, row_label, number, type, base_price)
SELECT 1, CONCAT('A', LPAD(n, 2, '0')), 'A', n, 'lettino', 8.00 FROM (
  SELECT 1 n UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION SELECT 10 UNION
  SELECT 11 UNION SELECT 12 UNION SELECT 13 UNION SELECT 14 UNION SELECT 15
) x;
INSERT INTO pool_places (area_id, code, row_label, number, type, base_price)
SELECT 2, CONCAT('B', LPAD(n, 2, '0')), 'B', n, 'lettino', 7.00 FROM (
  SELECT 1 n UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION SELECT 10 UNION
  SELECT 11 UNION SELECT 12 UNION SELECT 13 UNION SELECT 14 UNION SELECT 15
) x;
INSERT INTO pool_places (area_id, code, row_label, number, type, base_price)
SELECT 3, CONCAT('C', LPAD(n, 2, '0')), 'C', n, 'lettino', 6.00 FROM (
  SELECT 1 n UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION SELECT 10
) x;
INSERT INTO pool_places (area_id, code, row_label, number, type, base_price)
SELECT 4, CONCAT('D', LPAD(n, 2, '0')), 'D', n, 'lettino', 8.00 FROM (
  SELECT 1 n UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION SELECT 10
) x;

INSERT INTO product_categories (name, department) VALUES
('Caffetteria', 'bar'),
('Bibite', 'bar'),
('Snack', 'bar'),
('Ristorazione', 'ristorante'),
('Extra piscina', 'extra');

INSERT INTO products (category_id, name, price, vat_rate) VALUES
(1, 'Caffe', 1.20, 10.00),
(2, 'Acqua', 1.00, 10.00),
(2, 'Coca Cola', 2.50, 22.00),
(3, 'Cornetto', 1.50, 10.00),
(3, 'Panino', 4.50, 10.00),
(4, 'Insalata', 6.00, 10.00),
(4, 'Primo piatto', 8.00, 10.00),
(4, 'Secondo piatto', 10.00, 10.00),
(3, 'Gelato', 2.00, 10.00);

INSERT INTO customers (first_name, last_name, phone, email, privacy_consent) VALUES
('Mario', 'Rossi', '3331112222', 'mario.rossi@example.test', 1),
('Laura', 'Bianchi', '3332223333', 'laura.bianchi@example.test', 1);

INSERT INTO cards (card_code, customer_id, card_type, status) VALUES
('CARD001', 1, 'nominale', 'attiva'),
('CARD002', 2, 'nominale', 'attiva');
