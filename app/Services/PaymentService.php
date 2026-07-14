<?php

class PaymentService
{
    public function pay(int $cardId, float $amount, string $method, string $reason, int $operatorId, ?string $notes = null, bool $confirmed = true): void
    {
        if ($amount <= 0) {
            throw new InvalidArgumentException('Importo pagamento non valido.');
        }

        $pdo = db();
        $pdo->beginTransaction();
        try {
            $card = $pdo->prepare('SELECT * FROM cards WHERE id = ? FOR UPDATE');
            $card->execute([$cardId]);
            $card = $card->fetch();
            if (!$card) {
                throw new RuntimeException('Card non trovata.');
            }

            $status  = $confirmed ? 'confermato' : 'non_confermato';
            $entryId = $card['active_entry_id'] ?: null;
            $stmt = $pdo->prepare('INSERT INTO payments (card_id, customer_id, entry_id, amount, payment_method, reason, status, operator_id, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
            $stmt->execute([$cardId, (int) $card['customer_id'], $entryId, $amount, $method, $reason, $status, $operatorId, $notes]);

            // Un tentativo "non OK" resta solo come traccia in payments: non tocca
            // movimenti né saldo, perché il pagamento non è stato realmente incassato.
            if ($confirmed) {
                $movement = $pdo->prepare("INSERT INTO card_movements (card_id, customer_id, entry_id, movement_type, department, description, quantity, unit_price, total_amount, status, operator_id, notes) VALUES (?, ?, ?, 'payment', 'cassa', ?, 1, ?, ?, 'paid', ?, ?)");
                $movement->execute([$cardId, (int) $card['customer_id'], $entryId, 'Pagamento ' . $reason, $amount, $amount, $operatorId, $notes]);
                (new BalanceService())->calculate($cardId);
            }

            audit_log($confirmed ? 'payment' : 'payment_failed', 'card', $cardId, ['amount' => $amount, 'method' => $method, 'reason' => $reason, 'status' => $status]);
            $pdo->commit();
        } catch (Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }
}
