<?php

class CardService
{
    public function findByCode(string $code): ?array
    {
        $stmt = db()->prepare('SELECT c.*, CONCAT(cu.last_name, " ", cu.first_name) customer_name, cu.first_name, cu.last_name, cu.phone, cu.photo_path FROM cards c JOIN customers cu ON cu.id = c.customer_id WHERE c.card_code = ?');
        $stmt->execute([trim($code)]);
        $card = $stmt->fetch();
        if (!$card) {
            return null;
        }
        $card['balance'] = (new BalanceService())->calculate((int) $card['id']);
        $card['members'] = $this->members((int) $card['id']);
        return $card;
    }

    /** Membri del nucleo familiare collegati alla card, oltre all'intestatario. */
    public function members(int $cardId): array
    {
        $stmt = db()->prepare('
            SELECT cu.id, cu.first_name, cu.last_name, cu.phone, cu.photo_path
            FROM card_members cm
            JOIN customers cu ON cu.id = cm.customer_id
            WHERE cm.card_id = ?
            ORDER BY cu.last_name, cu.first_name
        ');
        $stmt->execute([$cardId]);
        return $stmt->fetchAll();
    }

    /**
     * Card "principale" per un cliente: la propria (se è intestatario) oppure,
     * in mancanza, quella del nucleo familiare a cui appartiene come membro.
     */
    public function findCardForCustomer(int $customerId): ?array
    {
        $stmt = db()->prepare("SELECT * FROM cards WHERE customer_id = ? AND status = 'attiva' ORDER BY created_at DESC LIMIT 1");
        $stmt->execute([$customerId]);
        $card = $stmt->fetch();
        if ($card) {
            return $card;
        }
        $stmt = db()->prepare("
            SELECT c.* FROM card_members cm
            JOIN cards c ON c.id = cm.card_id
            WHERE cm.customer_id = ? AND c.status = 'attiva'
            ORDER BY c.created_at DESC LIMIT 1
        ");
        $stmt->execute([$customerId]);
        return $stmt->fetch() ?: null;
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
