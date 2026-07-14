-- Igea Club — Aree lettini richieste dal cliente (nota 13/07/2026, rif. 6)
-- "Zona Ombrelloni" -> "Ombrelloni"; nuove aree "Corridoio" e "Siepe",
-- create vuote in attesa dell'assegnazione fisica dei lettini (editor planimetria).

UPDATE pool_areas SET name = 'Ombrelloni' WHERE name = 'Zona Ombrelloni';

INSERT INTO pool_areas (name, description, active)
VALUES
  ('Corridoio', 'Area in attesa di assegnazione lettini dall''editor planimetria.', 1),
  ('Siepe',     'Area in attesa di assegnazione lettini dall''editor planimetria.', 1);
