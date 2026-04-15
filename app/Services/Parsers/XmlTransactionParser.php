<?php

declare(strict_types=1);

namespace App\Services\Parsers;

use RuntimeException;
use SimpleXMLElement;

class XmlTransactionParser implements TransactionParserInterface
{
    public function parse(string $filePath): array
    {
        $content = file_get_contents($filePath);

        if ($content === false) {
            return [];
        }

        libxml_use_internal_errors(true);

        $xml = simplexml_load_string($content);

        if ($xml === false) {
            $errors = libxml_get_errors();
            libxml_clear_errors();
            $message = !empty($errors) ? $errors[0]->message : 'Unknown XML error';
            throw new RuntimeException('Invalid XML file: ' . trim($message));
        }

        $records = [];

        foreach ($xml->transaction as $transaction) {
            $records[] = [
                'transaction_id'   => (string) $transaction->transaction_id,
                'account_number'   => (string) $transaction->account_number,
                'transaction_date' => (string) $transaction->transaction_date,
                'amount'           => (string) $transaction->amount,
                'currency'         => (string) $transaction->currency,
            ];
        }

        return $records;
    }
}
