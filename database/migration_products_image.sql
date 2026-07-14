-- Igea Club — Foto d'esempio prodotti bar/ristorante (nota 13/07/2026, rif. 2)

ALTER TABLE products
  ADD COLUMN image_path VARCHAR(255) NULL AFTER notes;
