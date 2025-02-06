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

    public function getPaymentMethodText($methodCode) {
        $methods = [
            0 => 'VISA',
            1 => 'MASTERCARD',
            2 => 'AMEX',
            3 => 'BAN_CONTACT',
            4 => 'CHINA_UNION_PAY',
            5 => 'MAESTRO',
            6 => 'DINERS',
            7 => 'DISCOVER',
            8 => 'JCB',
            9 => 'ALIPAY',
            10 => 'WECHAT'
        ];
        return $methods[$methodCode] ?? 'UNKNOWN';
    }

    public function formatAmount($amount, $currency) {
        return number_format($amount/100, 2) . ' ' . $currency;
    }
};
