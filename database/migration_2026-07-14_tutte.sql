-- ============================================================================
-- Igea Club Pool Manager — Migration combinate del 14 luglio 2026
-- Copre le 7 modifiche al database introdotte dalle richieste del cliente
-- (nota del 13 luglio 2026): rif. 6, 2, 15, 8, 7, 1, 18.
--
-- USO: eseguire UNA SOLA VOLTA su phpMyAdmin (o client equivalente) sul
-- database di produzione, PRIMA di caricare i file PHP aggiornati.
-- Le istruzioni sono nello stesso ordine in cui sono state applicate e
-- verificate in locale. Non è idempotente: se eseguita due volte, gli ALTER
-- TABLE su colonne già esistenti daranno errore "Duplicate column" — è il
-- segnale che è già stata applicata, non un problema.
--
-- Prima di eseguire: fare un backup/export del database.
-- ============================================================================


-- ── 1/7 · rif. 6 — Aree lettini: Solarium, Ombrelloni, Corridoio, Siepe ──────
-- "Zona Ombrelloni" -> "Ombrelloni"; nuove aree "Corridoio" e "Siepe",
-- create vuote in attesa dell'assegnazione fisica dei lettini (editor planimetria).

UPDATE pool_areas SET name = 'Ombrelloni' WHERE name = 'Zona Ombrelloni';

INSERT INTO pool_areas (name, description, active)
VALUES
  ('Corridoio', 'Area in attesa di assegnazione lettini dall''editor planimetria.', 1),
  ('Siepe',     'Area in attesa di assegnazione lettini dall''editor planimetria.', 1);


-- ── 2/7 · rif. 2 — Foto d'esempio prodotti bar/ristorante ───────────────────

ALTER TABLE products
  ADD COLUMN image_path VARCHAR(255) NULL AFTER notes;


-- ── 3/7 · rif. 15 — Doppio tasto cassa "registra OK" / "non OK" ─────────────
-- Predisposizione in vista del collegamento alla cassa fiscale (rif. 12): distingue un
-- pagamento confermato da un tentativo non riuscito, senza toccare saldo e movimenti
-- per i tentativi non confermati.

ALTER TABLE payments
  ADD COLUMN status ENUM('confermato','non_confermato') NOT NULL DEFAULT 'confermato' AFTER reason;


-- ── 4/7 · rif. 8 — Voce di incasso "piscina" nei report ─────────────────────
-- L'incasso dell'ingresso piscina era registrato come 'reception', confondendosi con
-- altri eventuali incassi di quel reparto. Verificato in locale che non esistevano
-- storicamente incassi con entry_fee > 0: da verificare anche in produzione prima
-- di eseguire, se ci sono incassi piscina storici da riclassificare a mano dopo.

ALTER TABLE card_movements
  MODIFY COLUMN department ENUM('bar','ristorante','reception','extra','cassa','piscina') NOT NULL;


-- ── 5/7 · rif. 7 — Listino tariffe lettini/ombrelloni + ridotto bimbi ───────
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


-- ── 6/7 · rif. 1 — Card familiare: nucleo condiviso sulla stessa card ───────
-- La card resta legata a un intestatario (cards.customer_id, invariato) per non toccare i
-- ~10 punti che oggi assumono un solo customer_id per card_id. I familiari si aggiungono
-- come membri collegati alla stessa card: condividono già di fatto saldo e movimenti,
-- perché quelli sono ancorati a card_id e non a customer_id.

CREATE TABLE IF NOT EXISTS card_members (
  id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  card_id     INT UNSIGNED NOT NULL,
  customer_id INT UNSIGNED NOT NULL,
  created_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY idx_card_members_unique (card_id, customer_id),
  KEY idx_card_members_customer (customer_id),
  CONSTRAINT fk_card_members_card     FOREIGN KEY (card_id)     REFERENCES cards (id),
  CONSTRAINT fk_card_members_customer FOREIGN KEY (customer_id) REFERENCES customers (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ── 7/7 · rif. 18 — Ruolo "gestore" ──────────────────────────────────────────
-- Accesso operativo pieno come l'amministratore, tranne le sezioni riservate
-- esclusivamente all'admin (gestione utenti, eliminazioni permanenti).

ALTER TABLE users
  MODIFY COLUMN role ENUM('admin','reception','bar','ristorazione','cassa','gestore') NOT NULL;


-- ============================================================================
-- Verifica finale — deve dare questi risultati se tutto è andato a buon fine:
--   pool_areas: 4 righe (Solarium, Ombrelloni, Corridoio, Siepe)
--   products.image_path, payments.status: colonne presenti
--   card_movements.department: include 'piscina' nell'ENUM
--   price_list: 4 righe
--   card_members: tabella vuota ma esistente
--   users.role: include 'gestore' nell'ENUM
-- ============================================================================
SELECT name FROM pool_areas ORDER BY id;
SHOW COLUMNS FROM products LIKE 'image_path';
SHOW COLUMNS FROM payments LIKE 'status';
SHOW COLUMNS FROM card_movements LIKE 'department';
SELECT code, price FROM price_list ORDER BY id;
SHOW COLUMNS FROM users LIKE 'role';
