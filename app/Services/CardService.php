<?php

class CardService
{
    public function findByCode(string $code): ?array
    {
        $stmt = db()->prepare('SELECT c.*, CONCAT(cu.first_name, " ", cu.last_name) customer_name, cu.first_name, cu.last_name, cu.phone FROM cards c JOIN customers cu ON cu.id = c.customer_id WHERE c.card_code = ?');
        $stmt->execute([trim($code)]);
        $card = $stmt->fetch();
        if (!$card) {
            return null;
        }
        $card['balance'] = (new BalanceService())->calculate((int) $card['id']);
        return $card;
    }

    public function requireActiveInside(int $cardId): array
    {
        $stmt = db()->prepare('SELECT c.*, e.id entry_id FROM cards c LEFT JOIN entries e ON e.id = c.active_entry_id WHERE c.id = ?');
        $stmt->execute([$cardId]);
        $card = $stmt->fetch();
        if (!$card || $card['status'] !== 'attiva') {
            throw new RuntimeException('Card non attiva.');
        }
        if (!$card['is_inside'] || !$card['entry_id']) {
            throw new RuntimeException('Cliente non presente in struttura.');
        }
        return $card;
    }
}
