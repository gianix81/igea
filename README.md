# Igea Club Pool Manager

Gestionale PHP/MySQL per piscina estiva, card nominali, lettini, consumazioni, pagamenti e blocco uscita con saldo aperto.

## Requisiti

- PHP 8.1+
- MySQL/MariaDB
- Estensione PHP PDO MySQL
- XAMPP, Laragon o server web equivalente

## Installazione

1. Crea il database e le tabelle:

```sql
SOURCE database/schema.sql;
SOURCE database/seed.sql;
```

In alternativa importa prima `database/schema.sql` e poi `database/seed.sql` da phpMyAdmin.

2. Configura la connessione in `config/database.php` oppure tramite variabili ambiente:

```text
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=igea_pool_manager
DB_USERNAME=root
DB_PASSWORD=
```

3. Avvia in locale:

```bash
php -S localhost:8000 -t public public/router.php
```

4. Apri `http://localhost:8000`.

## Credenziali demo

Password per tutti gli utenti demo: `IgeaDemo2026!`

- `admin@igeaclub.it`
- `reception@igeaclub.it`
- `bar@igeaclub.it`
- `cassa@igeaclub.it`

## Flusso MVP da testare

1. Accedi come admin o reception.
2. Vai in `Clienti` e crea un cliente, oppure usa i clienti demo.
3. Vai in `Card` e crea/cerca una card, per esempio `CARD001`.
4. Vai in `Reception`, inserisci il codice card e registra un ingresso.
5. Accedi a `Bar`, cerca la card e registra una consumazione.
6. Vai in `Cassa`, cerca la card e verifica il saldo.
7. Tenta l'uscita: se il saldo è aperto viene bloccata.
8. Registra un pagamento completo.
9. Registra l'uscita: il sistema chiude l'ingresso, libera i posti assegnati e chiude la card.

## Endpoint API disponibili

- `GET /api/card/search.php?code=CARD001`
- `GET /api/card/balance.php?card_id=1`
- `POST /api/card/add-consumption.php`
- `POST /api/card/pay.php`
- `POST /api/entry/checkout.php`
- `GET /api/places/availability.php?date=YYYY-MM-DD`
- `GET /api/products/list.php`

Gli endpoint richiedono sessione autenticata. Le chiamate POST devono inviare il token CSRF, come campo `_csrf` o header `X-CSRF-Token`.

## Regole implementate

- Login con sessioni PHP, ruoli e password hash.
- Prepared statements PDO.
- Token CSRF sui form principali.
- Card nominale collegata a cliente.
- Consumazioni consentite solo con card attiva e cliente dentro.
- Saldo ricalcolato dai movimenti prima dell'uscita.
- Uscita bloccata se `saldo_residuo > 0`.
- Pagamenti e consumazioni non vengono cancellati fisicamente.
- Audit log per operazioni sensibili.
- Posti piscina assegnati e liberati al checkout.

## Note

Il progetto è un MVP funzionante e modulare. Le estensioni naturali sono QR/barcode fisici, stampa riepilogo, POS, abbonamenti stagionali, prenotazione pubblica e chiusura giornata avanzata con snapshot verificato.
# igea
