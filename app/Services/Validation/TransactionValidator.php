<?php

declare(strict_types=1);

namespace App\Services\Validation;

use DateTime;

class TransactionValidator
{
    public function validate(array $record): array
    {
        $errors = [];

        if (empty($record['transaction_id'])) {
            $errors[] = 'transaction_id is required.';
        }

        if (empty($record['account_number'])) {
            $errors[] = 'account_number is required.';
        } elseif (!$this->isValidIban((string) $record['account_number'])) {
            $errors[] = 'account_number must be a valid IBAN.';
        }

        if (empty($record['transaction_date'])) {
            $errors[] = 'transaction_date is required.';
        } elseif (!$this->isValidDate((string) $record['transaction_date'])) {
            $errors[] = 'transaction_date must be a valid date (YYYY-MM-DD).';
        }

        if (!isset($record['amount']) || $record['amount'] === '') {
            $errors[] = 'amount is required.';
        } elseif (!is_numeric($record['amount']) || (float) $record['amount'] <= 0) {
            $errors[] = 'amount must be a number greater than 0.';
        }

        if (empty($record['currency'])) {
            $errors[] = 'currency is required.';
        } elseif (!preg_match('/^[A-Z]{3}$/', (string) $record['currency'])) {
            $errors[] = 'currency must be exactly 3 uppercase letters.';
        }

        return $errors;
    }

    private function isValidIban(string $value): bool
    {
        $iban = strtoupper(preg_replace('/\s+/', '', $value));

        if (!preg_match('/^[A-Z]{2}[0-9]{2}[A-Z0-9]{1,30}$/', $iban)) {
            return false;
        }

        $rearranged = substr($iban, 4) . substr($iban, 0, 4);

        $numeric = '';
        foreach (str_split($rearranged) as $char) {
            $numeric .= ctype_alpha($char) ? (string) (ord($char) - 55) : $char;
        }

        return bcmod($numeric, '97') === '1';
    }

    private function isValidDate(string $value): bool
    {
        $date = DateTime::createFromFormat('Y-m-d', $value);

        return $date !== false && $date->format('Y-m-d') === $value;
    }
}
