-- Aggiunge pos_row / pos_col a pool_places per l'editor schematico
ALTER TABLE pool_places
  ADD COLUMN pos_row TINYINT UNSIGNED NULL DEFAULT NULL AFTER notes,
  ADD COLUMN pos_col TINYINT UNSIGNED NULL DEFAULT NULL AFTER pos_row,
  ADD INDEX idx_pool_places_pos (pos_row, pos_col);

-- ── Solarium ──────────────────────────────────────
UPDATE pool_places SET pos_row = 0, pos_col = number - 1 WHERE code LIKE 'SA%';
UPDATE pool_places SET pos_row = 1, pos_col = number - 1 WHERE code LIKE 'SB%';
UPDATE pool_places SET pos_row = 2, pos_col = number - 1 WHERE code LIKE 'SC%';
UPDATE pool_places SET pos_row = 3, pos_col = number - 1 WHERE code LIKE 'SD%';
UPDATE pool_places SET pos_row = 4, pos_col = number - 1 WHERE code LIKE 'SE%';

-- ── Ombrelloni sinistra (verticali, righe 6-10) ───
UPDATE pool_places SET pos_row = 6,  pos_col = number - 1 WHERE code LIKE 'OA%';
UPDATE pool_places SET pos_row = 7,  pos_col = number - 1 WHERE code LIKE 'OB%';
UPDATE pool_places SET pos_row = 8,  pos_col = number - 1 WHERE code LIKE 'OC%';
UPDATE pool_places SET pos_row = 9,  pos_col = number - 1 WHERE code LIKE 'OD%';
UPDATE pool_places SET pos_row = 10, pos_col = number - 1 WHERE code LIKE 'OE%';

-- ── Ombrelloni destra (colonne 10-15, stesse righe) ─
UPDATE pool_places SET pos_row = 10, pos_col = number + 12 WHERE code LIKE 'OP%';
UPDATE pool_places SET pos_row = 6,  pos_col = number + 9  WHERE code LIKE 'ORA%';
UPDATE pool_places SET pos_row = 7,  pos_col = number + 9  WHERE code LIKE 'ORB%';
UPDATE pool_places SET pos_row = 8,  pos_col = number + 9  WHERE code LIKE 'ORC%';
UPDATE pool_places SET pos_row = 9,  pos_col = number + 9  WHERE code LIKE 'ORD%';
