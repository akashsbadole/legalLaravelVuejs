<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\SignatureRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SignatureController extends Controller
{
    public function index(): JsonResponse
    {
        $requests = SignatureRequest::with(['document', 'requestedBy'])->latest()->get();
        return response()->json($requests);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'document_id' => 'required|exists:documents,id',
            'signer_email' => 'required|email',
            'signer_name' => 'required|string',
            'esign_provider' => 'required|string|in:esign,dsc,demo',
        ]);

        $signatureRequest = SignatureRequest::create([
            'document_id' => $validated['document_id'],
            'requested_by' => auth()->id(),
            'signer_email' => $validated['signer_email'],
            'signer_name' => $validated['signer_name'],
            'esign_provider' => $validated['esign_provider'],
            'status' => 'sent',
        ]);

        // In a real integration, we would call the provider API here
        // For 'demo', we'll just simulate it being sent

        return response()->json([
            'message' => 'Signature request sent successfully',
            'data' => $signatureRequest
        ], 201);
    }

    public function show(SignatureRequest $signatureRequest): JsonResponse
    {
        return response()->json($signatureRequest->load(['document', 'requestedBy']));
    }

    public function sign(Request $request, SignatureRequest $signatureRequest): JsonResponse
    {
        if ($signatureRequest->isSigned()) {
            return response()->json(['message' => 'Document already signed'], 422);
        }

        $validated = $request->validate([
            'signature_data' => 'required|string',
        ]);

        $signatureRequest->update([
            'status' => 'signed',
            'signed_at' => now(),
            'signature_data' => ['signature' => $validated['signature_data']],
            'ip_address' => $request->ip(),
        ]);

        // Trigger Audit Log
        \App\Models\AuditLog::create([
            'user_id' => auth()->id() ?? $signatureRequest->requested_by,
            'action' => 'sign',
            'description' => "Document signed: {$signatureRequest->document->name}",
        ]);

        return response()->json([
            'message' => 'Document signed successfully',
            'data' => $signatureRequest
        ]);
    }
}
