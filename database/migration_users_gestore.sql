-- Igea Club — Ruolo "gestore" (nota 13/07/2026, rif. 18)
-- Accesso operativo pieno come l'amministratore, tranne le sezioni riservate
-- esclusivamente all'admin (gestione utenti, eliminazioni permanenti).

ALTER TABLE users
  MODIFY COLUMN role ENUM('admin','reception','bar','ristorazione','cassa','gestore') NOT NULL;
