<?php

namespace App\Http\Controllers\Api\Documents;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    public function presign(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'document_type' => ['required', 'string', 'max:100'],
            'original_name' => ['required', 'string', 'max:255'],
        ]);

        return response()->json([
            'message' => 'Presign request prepared.',
            'data' => [
                'document_type' => $validated['document_type'],
                'original_name' => $validated['original_name'],
                'upload_url' => 'signed-url-placeholder',
                'expires_in_seconds' => 900,
            ],
        ]);
    }
}
