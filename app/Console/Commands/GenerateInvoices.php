<?php

namespace App\Console\Commands;

use App\Models\LegalCase;
use App\Models\Invoice;
use App\Models\TimeEntry;
use App\Models\Expense;
use App\Services\GSTService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

#[Signature('app:generate-invoices')]
#[Description('Generate invoices for unbilled time entries and expenses')]
class GenerateInvoices extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $cases = LegalCase::where('status_id', '!=', 4) // Assuming 4 is closed
                ->whereHas('timeEntries', function($q) {
                    $q->where('billed', false);
                })
                ->orWhereHas('expenses', function($q) {
                    $q->where('billed', false);
                })
                ->get();

            $gstService = new GSTService();
            $generatedCount = 0;
            
            foreach ($cases as $case) {
                $unbilledTime = $case->timeEntries()->where('billed', false)->sum('hours') * $case->hourly_rate;
                $unbilledExpenses = $case->expenses()->where('billed', false)->sum('amount');
                $subtotal = $unbilledTime + $unbilledExpenses;

                if ($subtotal > 0) {
                    // Get client GSTIN for determining if it's inter-state or intra-state
                    $client = $case->client;
                    $clientGstin = $client->gstin ?? null;
                    
                    // Calculate GST based on whether it's inter-state or intra-state
                    $gstData = $clientGstin ? $gstService->calculateIGST($subtotal) : $gstService->calculateGST($subtotal);
                    
                    $invoice = Invoice::create([
                        'invoice_number' => Invoice::generateInvoiceNumber(),
                        'case_id' => $case->id,
                        'client_id' => $case->client_id,
                        'gstin' => $clientGstin,
                        'sac_code' => $gstData['sac_code'],
                        'subtotal' => $subtotal,
                        'tax_amount' => $gstData['total_gst'],
                        'cgst_amount' => $gstData['cgst_amount'],
                        'sgst_amount' => $gstData['sgst_amount'],
                        'igst_amount' => $gstData['igst_amount'],
                        'total_amount' => $gstData['grand_total'],
                        'status' => 'sent',
                        'issue_date' => now(),
                        'due_date' => now()->addDays(30),
                    ]);

                    // Mark as billed
                    $case->timeEntries()->where('billed', false)->update(['billed' => true, 'invoice_id' => $invoice->id]);
                    $case->expenses()->where('billed', false)->update(['billed' => true, 'invoice_id' => $invoice->id]);

                    $this->info("Generated invoice {$invoice->invoice_number} for case {$case->case_number}");
                    $generatedCount++;
                }
            }

            $this->info("Invoice generation completed. Generated {$generatedCount} invoices.");
            Log::info("Invoice generation completed. Generated {$generatedCount} invoices.");
        } catch (\Exception $e) {
            $this->error('Invoice generation failed: ' . $e->getMessage());
            Log::error('Invoice generation failed: ' . $e->getMessage());
            return 1;
        }
    }
}
