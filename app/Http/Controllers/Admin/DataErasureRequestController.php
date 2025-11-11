<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DataErasureRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Throwable;
use Inertia\Inertia;
use Inertia\Response;

class DataErasureRequestController extends Controller
{
    public function index(Request $request): Response
    {
        $requests = DataErasureRequest::query()
            ->with(['user:id,name,username,email'])
            ->latest()
            ->get()
            ->map(fn (DataErasureRequest $dataRequest) => [
                'id'           => $dataRequest->id,
                'request_type' => $dataRequest->request_type,
                'details'      => $dataRequest->details,
                'status'       => $dataRequest->status,
                'admin_notes'  => $dataRequest->admin_notes,
                'created_at'   => $dataRequest->created_at?->toIso8601String(),
                'resolved_at'  => $dataRequest->resolved_at?->toIso8601String(),
                'user'         => [
                    'id'       => $dataRequest->user->id,
                    'name'     => $dataRequest->user->name,
                    'username' => $dataRequest->user->username,
                    'email'    => $dataRequest->user->email,
                ],
            ])
            ->values()
            ->all();

        return Inertia::render('admin/DataRequests', [
            'requests' => $requests,
            'flash'    => [
                'success' => $request->session()->get('success'),
                'error'   => $request->session()->get('error'),
            ],
        ]);
    }

    public function update(Request $request, DataErasureRequest $dataErasureRequest): RedirectResponse
    {
        $validated = $request->validate([
            'status'      => ['required', Rule::in(['pending', 'in_progress', 'resolved'])],
            'admin_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $dataErasureRequest->fill([
            'status'      => $validated['status'],
            'admin_notes' => $validated['admin_notes'] ?? null,
        ]);

        if ($validated['status'] === 'resolved') {
            $dataErasureRequest->resolved_at = $dataErasureRequest->resolved_at ?? now();
        } else {
            $dataErasureRequest->resolved_at = null;
        }

        $dataErasureRequest->save();

        return redirect()
            ->back()
            ->with('success', 'La demande a été mise à jour.');
    }

    public function destroyUser(Request $request, DataErasureRequest $dataErasureRequest): RedirectResponse
    {
        $user = $dataErasureRequest->user;

        if (! $user) {
            return redirect()
                ->back()
                ->with('error', 'Le compte associé à cette demande est introuvable ou déjà supprimé.');
        }

        $username = $user->username;

        try {
            DB::transaction(function () use ($user) {
                $user->commentReactions()->delete();
                $user->tipReactions()->delete();
                $user->comments()->delete();
                $user->tips()->delete();
                $user->ratings()->delete();

                $user->delete();
            });
        } catch (Throwable $exception) {
            report($exception);

            return redirect()
                ->back()
                ->with('error', 'Une erreur est survenue lors de la suppression du compte.');
        }

        return redirect()
            ->route('admin.privacy.requests.index')
            ->with('success', sprintf('Le compte @%s et toutes ses interactions ont été supprimés.', $username));
    }
}
