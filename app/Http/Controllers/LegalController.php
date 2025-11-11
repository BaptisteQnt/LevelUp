<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class LegalController extends Controller
{
    public function mentions(Request $request): Response
    {
        $user = $request->user();

        $requests = [];

        if ($user) {
            $requests = $user->dataErasureRequests()
                ->latest()
                ->get()
                ->map(fn ($dataRequest) => [
                    'id'           => $dataRequest->id,
                    'request_type' => $dataRequest->request_type,
                    'details'      => $dataRequest->details,
                    'status'       => $dataRequest->status,
                    'admin_notes'  => $dataRequest->admin_notes,
                    'created_at'   => $dataRequest->created_at?->toIso8601String(),
                    'resolved_at'  => $dataRequest->resolved_at?->toIso8601String(),
                ])
                ->values()
                ->all();
        }

        return Inertia::render('LegalMentions', [
            'requests' => $requests,
            'flash'    => [
                'success' => $request->session()->get('success'),
                'error'   => $request->session()->get('error'),
            ],
        ]);
    }
}
