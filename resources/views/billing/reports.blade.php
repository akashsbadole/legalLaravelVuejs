@extends('layouts.app')

@section('title', 'Financial Reports')

@section('content')
<h2 class="mb-4">Financial Reports</h2>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h5>Total Revenue</h5>
                <h3>${{ number_format($totalRevenue, 2) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <h5>Outstanding</h5>
                <h3>${{ number_format($outstandingAmount, 2) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <h5>Overdue</h5>
                <h3>${{ number_format($overdueAmount, 2) }}</h3>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header"><h5>Cases with Billing</h5></div>
    <div class="card-body">
        <table class="table">
            <thead>
                <tr>
                    <th>Case</th>
                    <th>Attorney</th>
                    <th>Total Hours</th>
                    <th>Total Expenses</th>
                    <th>Total Billed</th>
                </tr>
            </thead>
            <tbody>
                @forelse($casesWithBilling as $case)
                    <tr>
                        <td>{{ $case->case_number }}</td>
                        <td>{{ $case->assignedAttorney->name ?? 'N/A' }}</td>
                        <td>{{ number_format($case->total_hours ?? 0, 2) }}</td>
                        <td>${{ number_format($case->total_expenses ?? 0, 2) }}</td>
                        <td>${{ number_format($case->invoices->sum('total_amount'), 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center">No data.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection