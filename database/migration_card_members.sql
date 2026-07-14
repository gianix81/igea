-- Igea Club — Card familiare: nucleo condiviso sulla stessa card (nota 13/07/2026, rif. 1)
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
