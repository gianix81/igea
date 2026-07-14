-- Igea Club — Voce di incasso "piscina" nei report (nota 13/07/2026, rif. 8)
-- L'incasso dell'ingresso piscina era registrato come 'reception', confondendosi con
-- altri eventuali incassi di quel reparto. Verificato che non esistono storicamente
-- incassi con entry_fee > 0 (nessun backfill necessario): da qui in avanti l'ingresso
-- piscina viene registrato con il proprio reparto dedicato.

ALTER TABLE card_movements
  MODIFY COLUMN department ENUM('bar','ristorante','reception','extra','cassa','piscina') NOT NULL;
