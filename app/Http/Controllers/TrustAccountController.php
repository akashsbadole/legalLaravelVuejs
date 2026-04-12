<?php

namespace App\Http\Controllers;

use App\Models\TrustAccount;
use App\Models\User;
use App\Models\LegalCase;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class TrustAccountController extends Controller
{
    public function index(Request $request): View
    {
        $query = TrustAccount::with(['client', 'case', 'createdBy']);

        if ($request->filled('client')) {
            $query->where('client_id', $request->client);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $accounts = $query->orderBy('transaction_date', 'desc')->paginate(15);
        $clients = User::where('role', 'client')->get();

        return view('trust-accounts.index', compact('accounts', 'clients'));
    }

    public function create(): View
    {
        $clients = User::where('role', 'client')->get();
        $cases = LegalCase::all();
        $transactionTypes = TrustAccount::getTransactionTypes();

        return view('trust-accounts.create', compact('clients', 'cases', 'transactionTypes'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:users,id',
            'case_id' => 'nullable|exists:cases,id',
            'transaction_type' => 'required|in:deposit,withdrawal,adjustment,transfer_to_operating',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string',
            'transaction_date' => 'required|date',
        ]);

        $clientId = $validated['client_id'];
        $lastBalance = TrustAccount::where('client_id', $clientId)
            ->orderBy('id', 'desc')
            ->value('balance') ?? 0;

        $amount = $validated['amount'];
        $isDebit = in_array($validated['transaction_type'], ['withdrawal', 'transfer_to_operating']);
        $newBalance = $isDebit ? $lastBalance - $amount : $lastBalance + $amount;

        if ($newBalance < 0) {
            return back()->with('error', 'Insufficient trust account balance.');
        }

        $validated['balance'] = $newBalance;
        $validated['created_by'] = auth()->id();
        $validated['reference_number'] = 'TA-' . date('Ymd') . '-' . strtoupper(uniqid());
        $validated['status'] = 'approved';

        TrustAccount::create($validated);

        return redirect()->route('trust-accounts.index')->with('success', 'Trust account transaction created.');
    }

    public function show(TrustAccount $trustAccount): View
    {
        $trustAccount->load(['client', 'case', 'createdBy']);

        $clientTransactions = TrustAccount::where('client_id', $trustAccount->client_id)
            ->orderBy('transaction_date', 'asc')
            ->get();

        return view('trust-accounts.show', compact('trustAccount', 'clientTransactions'));
    }

    public function clientBalance(User $client): View
    {
        $balance = TrustAccount::where('client_id', $client->id)
            ->orderBy('id', 'desc')
            ->value('balance') ?? 0;

        $transactions = TrustAccount::where('client_id', $client->id)
            ->with(['case', 'createdBy'])
            ->orderBy('transaction_date', 'desc')
            ->paginate(15);

        return view('trust-accounts.client-balance', compact('client', 'balance', 'transactions'));
    }
}
