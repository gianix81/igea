-- Igea Club — Listino tariffe lettini/ombrelloni + ridotto bimbi (nota 13/07/2026, rif. 7)
-- Tariffe modificabili in qualsiasi momento dall'operatore tramite /tariffe.
-- "ingresso_ridotto_bimbi" è una tariffa d'ingresso ridotta (bimbi fino a 5 anni),
-- alternativa alla tariffa intera digitata oggi in Reception — non un prezzo lettino.

CREATE TABLE IF NOT EXISTS price_list (
  id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  code       VARCHAR(40)  NOT NULL,
  label      VARCHAR(120) NOT NULL,
  price      DECIMAL(10,2) NOT NULL,
  active     TINYINT(1)   NOT NULL DEFAULT 1,
  updated_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY idx_price_list_code (code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO price_list (code, label, price, active) VALUES
  ('lettino_feriale',        'Lettino (lun-ven)',                  12.00, 1),
  ('lettino_festivo',        'Lettino (sab-dom)',                  15.00, 1),
  ('ombrellone',              'Ombrellone',                         5.00, 1),
  ('ingresso_ridotto_bimbi', 'Ingresso ridotto (bimbi fino a 5 anni)', 8.00, 1)
ON DUPLICATE KEY UPDATE code = code;
