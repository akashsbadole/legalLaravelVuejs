<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class CourtFeeCalculator
{
    private array $feeStructure = [
        'civil' => [
            'plaint' => ['min' => 200, 'max' => 50000, 'rate' => 0.05, 'max_fee' => 10000],
            'memo' => ['min' => 100, 'max' => 10000, 'rate' => 0.025, 'max_fee' => 5000],
            'execution' => ['min' => 100, 'max' => 20000, 'rate' => 0.05, 'max_fee' => 5000],
        ],
        'criminal' => [
            'bail' => ['fixed' => 5000, 'description' => 'Bail application'],
            'criminal_compound' => ['fixed' => 1000, 'description' => 'Compoundable offense'],
            'criminal_misc' => ['fixed' => 2000, 'description' => 'Miscellaneous application'],
        ],
        'family' => [
            'divorce' => ['fixed' => 1500, 'description' => 'Divorce petition'],
            'maintenance' => ['min' => 200, 'max' => 10000, 'rate' => 0.03, 'max_fee' => 3000],
            'custody' => ['fixed' => 1000, 'description' => 'Custody petition'],
        ],
        'property' => [
            'partition' => ['min' => 500, 'max' => 100000, 'rate' => 0.01, 'max_fee' => 50000],
            'declaration' => ['min' => 500, 'max' => 50000, 'rate' => 0.02, 'max_fee' => 25000],
        ],
    ];

    public function calculate(string $caseType, string $filingType, float $claimAmount = 0): array
    {
        $fee = 0;
        $breakdown = [];
        
        $caseType = strtolower($caseType);
        $filingType = strtolower($filingType);

        if (isset($this->feeStructure[$caseType][$filingType])) {
            $structure = $this->feeStructure[$caseType][$filingType];

            if (isset($structure['fixed'])) {
                $fee = $structure['fixed'];
                $breakdown = [
                    'type' => 'fixed',
                    'amount' => $fee,
                    'description' => $structure['description'] ?? 'Fixed fee',
                ];
            } else {
                $minFee = $structure['min'];
                $maxFee = $structure['max'];
                $rate = $structure['rate'];
                $maxFeeCap = $structure['max_fee'];

                $calculatedFee = $claimAmount * $rate;
                $fee = max($minFee, min($calculatedFee, $maxFeeCap));

                $breakdown = [
                    'type' => 'ad valorem',
                    'claim_amount' => $claimAmount,
                    'rate' => ($rate * 100) . '%',
                    'calculated' => $calculatedFee,
                    'min_fee' => $minFee,
                    'max_fee' => $maxFeeCap,
                    'amount' => $fee,
                ];
            }
        }

        return [
            'case_type' => $caseType,
            'filing_type' => $filingType,
            'court_fee' => $fee,
            'breakdown' => $breakdown,
            'currency' => 'INR',
        ];
    }

    public function calculateAll(string $caseType, float $claimAmount = 0): array
    {
        $results = [];
        $caseType = strtolower($caseType);

        if (isset($this->feeStructure[$caseType])) {
            foreach ($this->feeStructure[$caseType] as $filingType => $structure) {
                $results[$filingType] = $this->calculate($caseType, $filingType, $claimAmount);
            }
        }

        return $results;
    }

    public function getFeeStructure(): array
    {
        return $this->feeStructure;
    }

    public function getCourtTypes(): array
    {
        return array_keys($this->feeStructure);
    }

    public function getFilingTypes(string $courtType): array
    {
        $courtType = strtolower($courtType);
        return $this->feeStructure[$courtType] ?? [];
    }

    public function getIndianCourtFees(): array
    {
        return [
            'high_court' => [
                'civil_appeal' => ['min' => 2000, 'max' => 10000, 'rate' => 0.05],
                'writ_petition' => ['fixed' => 5000],
                'criminal_appeal' => ['fixed' => 2500],
            ],
            'district_court' => [
                'plaint_upto_1lakh' => ['min' => 200, 'max' => 1000, 'rate' => 0.05],
                'plaint_1to_5lakh' => ['min' => 1000, 'max' => 5000, 'rate' => 0.03],
                'plaint_above_5lakh' => ['min' => 5000, 'max' => 50000, 'rate' => 0.02],
            ],
            'civil_court' => [
                'small_claims' => ['fixed' => 100, 'description' => 'Up to Rs 10,000'],
                'money_recovery' => ['min' => 200, 'max' => 10000, 'rate' => 0.05],
            ],
            'family_court' => [
                'divorce_mutual' => ['fixed' => 1500],
                'divorce_contested' => ['fixed' => 2500],
                'maintenance' => ['min' => 200, 'max' => 5000, 'rate' => 0.03],
            ],
        ];
    }

    public function calculateProcessFee(int $pages, string $serviceType = 'summons'): array
    {
        $baseFee = match($serviceType) {
            'summons' => 100,
            'notice' => 150,
            'warrant' => 200,
            'bail' => 300,
            default => 100,
        };

        $pageFee = 0;
        if ($pages > 10) {
            $pageFee = ($pages - 10) * 10;
        }

        $total = $baseFee + $pageFee;

        return [
            'service_type' => $serviceType,
            'base_fee' => $baseFee,
            'pages' => $pages,
            'page_fee' => $pageFee,
            'total_fee' => $total,
        ];
    }

    public function getFeeChallanDetails(array $feeDetails, string $courtName, string $caseNumber): array
    {
        return [
            'court_name' => $courtName,
            'case_number' => $caseNumber,
            'fee_type' => 'Court Fee',
            'amount' => $feeDetails['court_fee'] ?? 0,
            'challan_number' => 'CF' . date('Ymd') . rand(1000, 9999),
            'challan_date' => now()->format('Y-m-d'),
            'valid_upto' => now()->addDays(30)->format('Y-m-d'),
            'bank_name' => 'State Bank of India',
            'account_number' => '1234567890',
            'ifsc_code' => 'SBIN0001234',
        ];
    }
}
