<?php

class EntryService
{
    public function checkout(int $cardId, int $operatorId, bool $force = false): array
    {
        $pdo = db();
        $pdo->beginTransaction();
        try {
            // Cerca anche 'bloccato': un tentativo precedente con saldo aperto lo imposta a bloccato
            $stmt = $pdo->prepare("SELECT * FROM entries WHERE card_id = ? AND status IN ('dentro','bloccato') ORDER BY id DESC LIMIT 1 FOR UPDATE");
            $stmt->execute([$cardId]);
            $entry = $stmt->fetch();
            if (!$entry) {
                throw new RuntimeException('Nessun ingresso attivo trovato.');
            }

            $balance = (new BalanceService())->calculate($cardId);
            if ($balance > 0 && !$force) {
                $pdo->prepare("UPDATE entries SET status = 'bloccato' WHERE id = ?")->execute([(int) $entry['id']]);
                $pdo->commit();
                return [
                    'success' => false,
                    'message' => 'Check-out bloccato: saldo aperto € ' . number_format($balance, 2, ',', '.') . '. Regolarizzare in Cassa prima di procedere.',
                    'balance' => $balance,
                ];
            }

            // Checkout: chiude l'ingresso e libera i posti, la card rimane sempre 'attiva'
            $pdo->prepare("UPDATE entries SET status = 'uscito', checkout_at = NOW(), closed_by = ? WHERE id = ?")->execute([$operatorId, (int) $entry['id']]);
            $pdo->prepare("UPDATE entry_places SET status = 'liberato', released_at = NOW() WHERE entry_id = ? AND status = 'assegnato'")->execute([(int) $entry['id']]);
            $pdo->prepare("UPDATE pool_places p JOIN entry_places ep ON ep.place_id = p.id SET p.status = 'disponibile' WHERE ep.entry_id = ?")->execute([(int) $entry['id']]);
            // La card non si chiude mai automaticamente: rimane 'attiva' per gli ingressi futuri
            $pdo->prepare("UPDATE cards SET is_inside = 0, active_entry_id = NULL WHERE id = ?")->execute([$cardId]);
            audit_log('checkout', 'entry', (int) $entry['id'], ['balance' => $balance, 'forced' => $force]);
            $pdo->commit();
            return ['success' => true, 'message' => 'Uscita registrata.' . ($balance > 0 ? ' Saldo aperto: € ' . number_format($balance, 2, ',', '.') : ''), 'balance' => $balance];
        } catch (Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }
}
