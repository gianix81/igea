-- Igea Club — Registrazione completa cliente con documento, foto e firma

ALTER TABLE customers
  ADD COLUMN doc_type          ENUM('carta_identita','passaporto','patente','permesso_soggiorno') NULL AFTER notes,
  ADD COLUMN doc_number        VARCHAR(30)  NULL AFTER doc_type,
  ADD COLUMN doc_expiry        DATE         NULL AFTER doc_number,
  ADD COLUMN doc_issuer        VARCHAR(100) NULL AFTER doc_expiry,
  ADD COLUMN photo_path        VARCHAR(255) NULL AFTER doc_issuer,
  ADD COLUMN signature_path    VARCHAR(255) NULL AFTER photo_path,
  ADD COLUMN privacy_signed_at DATETIME     NULL AFTER signature_path;

-- UID del chip NFC hardware (assegnato al momento della consegna fisica)
ALTER TABLE cards
  ADD COLUMN nfc_uid VARCHAR(64) NULL AFTER card_code,
  ADD UNIQUE INDEX idx_cards_nfc (nfc_uid);
