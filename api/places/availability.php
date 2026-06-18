<?php
require __DIR__ . '/../bootstrap.php';

$date = request_input('date', date('Y-m-d'));
$stmt = db()->prepare("
    SELECT p.*, a.name area_name,
      CASE WHEN EXISTS (
        SELECT 1 FROM entry_places ep
        JOIN entries e ON e.id = ep.entry_id
        WHERE ep.place_id = p.id AND ep.status = 'assegnato' AND e.entry_date = ? AND e.status = 'dentro'
      ) THEN 'occupato' ELSE p.status END AS day_status
    FROM pool_places p
    JOIN pool_areas a ON a.id = p.area_id
    ORDER BY a.name, p.code
");
$stmt->execute([$date]);
json_response(['success' => true, 'date' => $date, 'places' => $stmt->fetchAll()]);
