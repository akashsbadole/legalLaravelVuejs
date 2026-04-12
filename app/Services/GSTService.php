<?php

namespace App\Services;

class GSTService
{
    const GST_RATE = 18; // 18% GST for legal services
    const SAC_CODE = '9982'; // Legal services SAC code

    public function calculateGST(float $amount): array
    {
        $cgst = $amount * (self::GST_RATE / 2) / 100;
        $sgst = $amount * (self::GST_RATE / 2) / 100;
        $igst = $cgst + $sgst;
        
        return [
            'subtotal' => $amount,
            'cgst_rate' => self::GST_RATE / 2,
            'sgst_rate' => self::GST_RATE / 2,
            'igst_rate' => self::GST_RATE,
            'cgst_amount' => round($cgst, 2),
            'sgst_amount' => round($sgst, 2),
            'igst_amount' => 0, // For intrastate
            'total_gst' => round($cgst + $sgst, 2),
            'grand_total' => round($amount + $cgst + $sgst, 2),
            'sac_code' => self::SAC_CODE,
        ];
    }

    public function calculateIGST(float $amount): array
    {
        $igst = $amount * self::GST_RATE / 100;
        
        return [
            'subtotal' => $amount,
            'igst_rate' => self::GST_RATE,
            'igst_amount' => round($igst, 2),
            'cgst_amount' => 0,
            'sgst_amount' => 0,
            'total_gst' => round($igst, 2),
            'grand_total' => round($amount + $igst, 2),
            'sac_code' => self::SAC_CODE,
        ];
    }

    public function formatInvoiceNumber(string $prefix = 'INV'): string
    {
        $year = date('Y');
        $month = date('m');
        return "{$prefix}-{$year}{$month}-" . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
    }

    public function generateGSTInvoiceData(array $lineItems, ?string $clientGstin = null): array
    {
        $subtotal = array_sum(array_column($lineItems, 'amount'));
        
        if ($clientGstin) {
            $taxData = $this->calculateIGST($subtotal);
        } else {
            $taxData = $this->calculateGST($subtotal);
        }

        return [
            'invoice_number' => $this->formatInvoiceNumber(),
            'line_items' => $lineItems,
            'subtotal' => $taxData['subtotal'],
            'cgst' => $taxData['cgst_amount'],
            'sgst' => $taxData['sgst_amount'],
            'igst' => $taxData['igst_amount'],
            'total_gst' => $taxData['total_gst'],
            'grand_total' => $taxData['grand_total'],
            'sac_code' => $taxData['sac_code'],
            'is_inter_state' => !empty($clientGstin),
        ];
    }
}