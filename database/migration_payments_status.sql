-- Igea Club — Doppio tasto cassa "registra OK" / "non OK" (nota 13/07/2026, rif. 15)
-- Predisposizione in vista del collegamento alla cassa fiscale (rif. 12): distingue un
-- pagamento confermato da un tentativo non riuscito, senza toccare saldo e movimenti
-- per i tentativi non confermati.

ALTER TABLE payments
  ADD COLUMN status ENUM('confermato','non_confermato') NOT NULL DEFAULT 'confermato' AFTER reason;
