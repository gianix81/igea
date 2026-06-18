<?php

class BalanceService
{
    public function calculate(int $cardId): float
    {
        $charges = db()->prepare("SELECT COALESCE(SUM(total_amount),0) FROM card_movements WHERE card_id = ? AND movement_type IN ('charge','adjustment') AND status <> 'cancelled'");
        $charges->execute([$cardId]);

        $credits = db()->prepare("SELECT COALESCE(SUM(total_amount),0) FROM card_movements WHERE card_id = ? AND movement_type IN ('payment','refund') AND status <> 'cancelled'");
        $credits->execute([$cardId]);

        $balance = (float) $charges->fetchColumn() - (float) $credits->fetchColumn();
        $this->syncCard($cardId, $balance);
        return round($balance, 2);
    }

    public function syncCard(int $cardId, float $balance): void
    {
        $paid = db()->prepare("SELECT COALESCE(SUM(total_amount),0) FROM card_movements WHERE card_id = ? AND movement_type = 'payment' AND status <> 'cancelled'");
        $paid->execute([$cardId]);
        $stmt = db()->prepare('UPDATE cards SET current_balance = ?, total_paid = ? WHERE id = ?');
        $stmt->execute([$balance, (float) $paid->fetchColumn(), $cardId]);
    }

    public function movements(int $cardId): array
    {
        $stmt = db()->prepare('SELECT m.*, p.name product_name, u.name operator_name FROM card_movements m LEFT JOIN products p ON p.id = m.product_id LEFT JOIN users u ON u.id = m.operator_id WHERE m.card_id = ? ORDER BY m.created_at DESC, m.id DESC');
        $stmt->execute([$cardId]);
        return $stmt->fetchAll();
    }
}
