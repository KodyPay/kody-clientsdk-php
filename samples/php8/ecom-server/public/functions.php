<?php
return new class {
    public function getStatusText($statusCode) {
        $statuses = [
            0 => 'PENDING',
            1 => 'SUCCESS',
            2 => 'FAILED',
            3 => 'CANCELLED',
            4 => 'EXPIRED'
        ];
        return $statuses[$statusCode] ?? 'UNKNOWN';
    }

    public function getRefundStatusText($statusCode) {
        $statuses = [
            0 => 'UNKNOWN',
            1 => 'REQUESTED',
            2 => 'SUCCESS',
            3 => 'FAILED',
            4 => 'PARTIAL_SUCCESS'
        ];
        return $statuses[$statusCode] ?? 'UNKNOWN';
    }

    public function formatAmount($amount, $currency) {
        return number_format($amount/100, 2) . ' ' . $currency;
    }
};
