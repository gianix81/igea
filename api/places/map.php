<?php
require __DIR__ . '/../bootstrap.php';

$date    = trim((string) request_input('date', date('Y-m-d')));
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
    $date = date('Y-m-d');
}
$placeId = (int) request_input('place_id', 0);

$sql = "
    SELECT
        p.id, p.area_id, p.code, p.row_label, p.number, p.type, p.base_price, p.status,
        a.name area_name,
        e.id entry_id, e.checkin_at,
        CONCAT(ec.first_name,' ',ec.last_name) entry_customer_name,
        ec.phone entry_customer_phone,
        ecard.card_code entry_card_code,
        r.id reservation_id, r.reservation_code, r.time_slot, r.status res_status,
        CONCAT(rc.first_name,' ',rc.last_name) res_customer_name,
        rc.phone res_customer_phone,
        rcard.card_code res_card_code,
        CASE
            WHEN ep_a.place_id IS NOT NULL THEN
                CASE entry_res.time_slot
                    WHEN 'mattina'    THEN 'occupato-mattina'
                    WHEN 'pomeriggio' THEN 'occupato-pomeriggio'
                    ELSE 'occupato'
                END
            WHEN rp_a.place_id IS NOT NULL THEN
                CASE
                    WHEN r.status = 'confermata' AND r.time_slot = 'mattina'    THEN 'prenotato-confermato-mattina'
                    WHEN r.status = 'confermata' AND r.time_slot = 'pomeriggio' THEN 'prenotato-confermato-pomeriggio'
                    WHEN r.status = 'confermata'                                THEN 'prenotato-confermato'
                    WHEN r.status = 'in attesa'  AND r.time_slot = 'mattina'    THEN 'prenotato-attesa-mattina'
                    WHEN r.status = 'in attesa'  AND r.time_slot = 'pomeriggio' THEN 'prenotato-attesa-pomeriggio'
                    ELSE 'prenotato-attesa'
                END
            WHEN p.status IN ('manutenzione','bloccato') THEN p.status
            ELSE 'disponibile'
        END day_status
    FROM pool_places p
    JOIN pool_areas a ON a.id = p.area_id
    LEFT JOIN (
        SELECT ep.place_id, MIN(ep.entry_id) entry_id
        FROM entry_places ep
        JOIN entries e ON e.id = ep.entry_id
        WHERE ep.status = 'assegnato' AND e.entry_date = ? AND e.status = 'dentro'
        GROUP BY ep.place_id
    ) ep_a ON ep_a.place_id = p.id
    LEFT JOIN entries e ON e.id = ep_a.entry_id
    LEFT JOIN reservations entry_res ON entry_res.id = e.reservation_id
    LEFT JOIN customers ec ON ec.id = e.customer_id
    LEFT JOIN cards ecard ON ecard.id = e.card_id
    LEFT JOIN (
        SELECT rp.place_id, MIN(rp.reservation_id) reservation_id
        FROM reservation_places rp
        JOIN reservations r ON r.id = rp.reservation_id
        WHERE rp.status = 'prenotato' AND r.usage_date = ? AND r.status IN ('confermata','in attesa')
        GROUP BY rp.place_id
    ) rp_a ON rp_a.place_id = p.id
    LEFT JOIN reservations r ON r.id = rp_a.reservation_id
    LEFT JOIN customers rc ON rc.id = r.customer_id
    LEFT JOIN (SELECT customer_id, MIN(card_code) card_code FROM cards WHERE status='attiva' GROUP BY customer_id) rcard ON rcard.customer_id = rc.id
";

if ($placeId > 0) {
    $stmt = db()->prepare($sql . " WHERE p.id = ? ORDER BY p.id");
    $stmt->execute([$date, $date, $placeId]);
    $place = $stmt->fetch() ?: null;
    json_response(['success' => true, 'place' => $place]);
} else {
    $stmt = db()->prepare($sql . " ORDER BY a.id, p.row_label, p.number");
    $stmt->execute([$date, $date]);
    json_response(['success' => true, 'date' => $date, 'places' => $stmt->fetchAll()]);
}
