<?php

class PriceListService
{
    public function all(): array
    {
        return db()->query('SELECT * FROM price_list ORDER BY id')->fetchAll();
    }

    public function rate(string $code): ?float
    {
        $stmt = db()->prepare('SELECT price FROM price_list WHERE code = ? AND active = 1');
        $stmt->execute([$code]);
        $price = $stmt->fetchColumn();
        return $price !== false ? (float) $price : null;
    }

    /**
     * Prezzo lettino/ombrellone dal listino per la data indicata (feriale/festivo per i lettini).
     * Torna null se il tipo posto non ha una tariffa di listino (es. tavolo, cabana) o se la
     * tariffa non è attiva: in quel caso il chiamante usa il prezzo statico del posto.
     */
    public function seatPrice(string $seatType, string $date): ?float
    {
        $code = $this->seatRateCode($seatType, $date);
        return $code ? $this->rate($code) : null;
    }

    public function childEntryRate(): ?float
    {
        return $this->rate('ingresso_ridotto_bimbi');
    }

    private function seatRateCode(string $seatType, string $date): ?string
    {
        if (in_array($seatType, ['lettino', 'sdraio'], true)) {
            $ts        = strtotime($date) ?: time();
            $isWeekend = in_array((int) date('N', $ts), [6, 7], true);
            return $isWeekend ? 'lettino_festivo' : 'lettino_feriale';
        }
        if ($seatType === 'ombrellone') {
            return 'ombrellone';
        }
        return null;
    }
}
