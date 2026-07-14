-- ============================================================================
-- Igea Club Pool Manager — Migration del 15 luglio 2026
-- Permessi personalizzati per operatore: l'admin può assegnare a ogni utente
-- solo alcune sezioni dell'app (invece del solo ruolo, che resta il default).
--
-- USO: eseguire UNA SOLA VOLTA su phpMyAdmin sul database di produzione,
-- PRIMA di caricare i file PHP aggiornati.
-- ============================================================================

ALTER TABLE users
  ADD COLUMN permissions JSON NULL AFTER role;

-- Verifica finale:
SHOW COLUMNS FROM users LIKE 'permissions';
