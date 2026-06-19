CREATE DATABASE IF NOT EXISTS igea_pool_manager CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE igea_pool_manager;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS audit_logs, daily_closures, payments, card_movements, products, product_categories, entry_places, entries, reservation_places, reservations, pool_places, pool_areas, cards, customers, users;
SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(190) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('admin','reception','bar','ristorazione','cassa') NOT NULL,
  active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE customers (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  first_name VARCHAR(100) NOT NULL,
  last_name VARCHAR(100) NOT NULL,
  phone VARCHAR(40),
  email VARCHAR(190),
  fiscal_code VARCHAR(32),
  birth_date DATE,
  address VARCHAR(255),
  privacy_consent TINYINT(1) NOT NULL DEFAULT 0,
  status ENUM('attivo','sospeso','blacklist') NOT NULL DEFAULT 'attivo',
  notes TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_customers_last_name (last_name),
  INDEX idx_customers_phone (phone),
  INDEX idx_customers_email (email)
) ENGINE=InnoDB;

CREATE TABLE cards (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  card_code VARCHAR(64) NOT NULL UNIQUE,
  customer_id INT UNSIGNED NOT NULL,
  card_type ENUM('nominale','abbonamento','ospite','staff') NOT NULL DEFAULT 'nominale',
  status ENUM('attiva','chiusa','bloccata','smarrita') NOT NULL DEFAULT 'attiva',
  activated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  expires_at DATETIME NULL,
  current_balance DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  total_paid DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  is_inside TINYINT(1) NOT NULL DEFAULT 0,
  active_entry_id INT UNSIGNED NULL,
  notes TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_cards_customer FOREIGN KEY (customer_id) REFERENCES customers(id),
  INDEX idx_cards_code (card_code),
  INDEX idx_cards_status (status)
) ENGINE=InnoDB;

CREATE TABLE pool_areas (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  description TEXT,
  active TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB;

CREATE TABLE pool_places (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  area_id INT UNSIGNED NOT NULL,
  code VARCHAR(32) NOT NULL UNIQUE,
  row_label VARCHAR(20),
  number INT UNSIGNED NOT NULL,
  type ENUM('lettino','sdraio','ombrellone','tavolo','cabana') NOT NULL DEFAULT 'lettino',
  base_price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  status ENUM('disponibile','prenotato','occupato','manutenzione','bloccato','liberato') NOT NULL DEFAULT 'disponibile',
  notes TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_places_area FOREIGN KEY (area_id) REFERENCES pool_areas(id),
  INDEX idx_pool_places_status (status)
) ENGINE=InnoDB;

CREATE TABLE reservations (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  reservation_code VARCHAR(64) NOT NULL UNIQUE,
  customer_id INT UNSIGNED NOT NULL,
  reservation_date DATE NOT NULL,
  usage_date DATE NOT NULL,
  time_slot ENUM('intera giornata','mattina','pomeriggio') NOT NULL DEFAULT 'intera giornata',
  people_count INT UNSIGNED NOT NULL DEFAULT 1,
  status ENUM('confermata','in attesa','cancellata','no-show','completata') NOT NULL DEFAULT 'in attesa',
  deposit_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  total_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  paid_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  payment_method ENUM('contanti','carta','bonifico','satispay','altro') NULL,
  notes TEXT,
  created_by INT UNSIGNED,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_res_customer FOREIGN KEY (customer_id) REFERENCES customers(id),
  CONSTRAINT fk_res_user FOREIGN KEY (created_by) REFERENCES users(id),
  INDEX idx_res_usage_date (usage_date),
  INDEX idx_res_status (status)
) ENGINE=InnoDB;

CREATE TABLE reservation_places (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  reservation_id INT UNSIGNED NOT NULL,
  place_id INT UNSIGNED NOT NULL,
  price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  status ENUM('prenotato','occupato','liberato','cancellato') NOT NULL DEFAULT 'prenotato',
  CONSTRAINT fk_res_places_res FOREIGN KEY (reservation_id) REFERENCES reservations(id),
  CONSTRAINT fk_res_places_place FOREIGN KEY (place_id) REFERENCES pool_places(id),
  UNIQUE KEY uq_res_place (reservation_id, place_id)
) ENGINE=InnoDB;

CREATE TABLE entries (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  customer_id INT UNSIGNED NOT NULL,
  card_id INT UNSIGNED NOT NULL,
  reservation_id INT UNSIGNED NULL,
  entry_date DATE NOT NULL,
  checkin_at DATETIME NOT NULL,
  checkout_at DATETIME NULL,
  status ENUM('dentro','uscito','bloccato','annullato') NOT NULL DEFAULT 'dentro',
  people_count INT UNSIGNED NOT NULL DEFAULT 1,
  entry_fee DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  paid_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  payment_method ENUM('contanti','carta','bonifico','satispay','altro') NULL,
  notes TEXT,
  created_by INT UNSIGNED,
  closed_by INT UNSIGNED NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_entries_customer FOREIGN KEY (customer_id) REFERENCES customers(id),
  CONSTRAINT fk_entries_card FOREIGN KEY (card_id) REFERENCES cards(id),
  CONSTRAINT fk_entries_res FOREIGN KEY (reservation_id) REFERENCES reservations(id),
  CONSTRAINT fk_entries_created_by FOREIGN KEY (created_by) REFERENCES users(id),
  CONSTRAINT fk_entries_closed_by FOREIGN KEY (closed_by) REFERENCES users(id),
  INDEX idx_entries_date (entry_date),
  INDEX idx_entries_status (status)
) ENGINE=InnoDB;

ALTER TABLE cards ADD CONSTRAINT fk_cards_active_entry FOREIGN KEY (active_entry_id) REFERENCES entries(id);

CREATE TABLE entry_places (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  entry_id INT UNSIGNED NOT NULL,
  place_id INT UNSIGNED NOT NULL,
  assigned_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  released_at DATETIME NULL,
  status ENUM('assegnato','liberato','annullato') NOT NULL DEFAULT 'assegnato',
  CONSTRAINT fk_entry_places_entry FOREIGN KEY (entry_id) REFERENCES entries(id),
  CONSTRAINT fk_entry_places_place FOREIGN KEY (place_id) REFERENCES pool_places(id),
  INDEX idx_entry_places_status (status)
) ENGINE=InnoDB;

CREATE TABLE product_categories (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  department ENUM('bar','ristorante','reception','extra') NOT NULL,
  active TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB;

CREATE TABLE products (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  category_id INT UNSIGNED NOT NULL,
  name VARCHAR(160) NOT NULL,
  price DECIMAL(10,2) NOT NULL,
  vat_rate DECIMAL(5,2) NULL,
  stock_enabled TINYINT(1) NOT NULL DEFAULT 0,
  stock_qty DECIMAL(10,2) NULL,
  active TINYINT(1) NOT NULL DEFAULT 1,
  notes TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_products_category FOREIGN KEY (category_id) REFERENCES product_categories(id)
) ENGINE=InnoDB;

CREATE TABLE card_movements (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  card_id INT UNSIGNED NOT NULL,
  customer_id INT UNSIGNED NOT NULL,
  entry_id INT UNSIGNED NULL,
  product_id INT UNSIGNED NULL,
  movement_type ENUM('charge','payment','refund','adjustment') NOT NULL,
  department ENUM('bar','ristorante','reception','extra','cassa') NOT NULL,
  description VARCHAR(255) NOT NULL,
  quantity DECIMAL(10,2) NOT NULL DEFAULT 1.00,
  unit_price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  total_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  status ENUM('open','paid','cancelled') NOT NULL DEFAULT 'open',
  operator_id INT UNSIGNED,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  cancelled_at DATETIME NULL,
  cancelled_by INT UNSIGNED NULL,
  notes TEXT,
  CONSTRAINT fk_mov_card FOREIGN KEY (card_id) REFERENCES cards(id),
  CONSTRAINT fk_mov_customer FOREIGN KEY (customer_id) REFERENCES customers(id),
  CONSTRAINT fk_mov_entry FOREIGN KEY (entry_id) REFERENCES entries(id),
  CONSTRAINT fk_mov_product FOREIGN KEY (product_id) REFERENCES products(id),
  CONSTRAINT fk_mov_operator FOREIGN KEY (operator_id) REFERENCES users(id),
  CONSTRAINT fk_mov_cancelled_by FOREIGN KEY (cancelled_by) REFERENCES users(id),
  INDEX idx_mov_card (card_id),
  INDEX idx_mov_status (status),
  INDEX idx_mov_created (created_at)
) ENGINE=InnoDB;

CREATE TABLE payments (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  card_id INT UNSIGNED NOT NULL,
  customer_id INT UNSIGNED NOT NULL,
  entry_id INT UNSIGNED NULL,
  reservation_id INT UNSIGNED NULL,
  amount DECIMAL(10,2) NOT NULL,
  payment_method ENUM('contanti','carta','bonifico','satispay','altro') NOT NULL,
  reason ENUM('ingresso','consumazioni','saldo finale','acconto prenotazione','extra') NOT NULL,
  operator_id INT UNSIGNED,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  notes TEXT,
  CONSTRAINT fk_pay_card FOREIGN KEY (card_id) REFERENCES cards(id),
  CONSTRAINT fk_pay_customer FOREIGN KEY (customer_id) REFERENCES customers(id),
  CONSTRAINT fk_pay_entry FOREIGN KEY (entry_id) REFERENCES entries(id),
  CONSTRAINT fk_pay_res FOREIGN KEY (reservation_id) REFERENCES reservations(id),
  CONSTRAINT fk_pay_operator FOREIGN KEY (operator_id) REFERENCES users(id),
  INDEX idx_pay_card (card_id),
  INDEX idx_pay_created (created_at)
) ENGINE=InnoDB;

CREATE TABLE daily_closures (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  closure_date DATE NOT NULL UNIQUE,
  total_entries INT UNSIGNED NOT NULL DEFAULT 0,
  total_charges DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  total_payments DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  open_balances DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  closed_by INT UNSIGNED NOT NULL,
  closed_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  notes TEXT,
  snapshot_json JSON,
  CONSTRAINT fk_closures_user FOREIGN KEY (closed_by) REFERENCES users(id)
) ENGINE=InnoDB;

CREATE TABLE audit_logs (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NULL,
  action VARCHAR(120) NOT NULL,
  entity_type VARCHAR(80) NOT NULL,
  entity_id INT UNSIGNED NULL,
  old_value JSON NULL,
  new_value JSON NULL,
  ip_address VARCHAR(45),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_audit_user FOREIGN KEY (user_id) REFERENCES users(id),
  INDEX idx_audit_entity (entity_type, entity_id),
  INDEX idx_audit_created (created_at)
) ENGINE=InnoDB;
