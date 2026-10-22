<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class IncidentController extends Controller
{
    public function index(Request $request): Response
    {
        $organizationIds = $request->user()->organizations()->pluck('organizations.id');

        return Inertia::render('Incidents/Index', [
            'incidents' => Incident::query()
                ->whereHas('monitor', fn ($query) => $query->whereIn('organization_id', $organizationIds))
                ->with('monitor:id,name,target_url')
                ->latest('started_at')
                ->limit(50)
                ->get()
                ->map(fn (Incident $incident) => [
                    'id' => $incident->id,
                    'monitorName' => $incident->monitor?->name,
                    'targetUrl' => $incident->monitor?->target_url,
                    'status' => $incident->status,
                    'failureCount' => $incident->failure_count,
                    'lastError' => $incident->last_error,
                    'startedAt' => $incident->started_at?->toIso8601String(),
                    'resolvedAt' => $incident->resolved_at?->toIso8601String(),
                    'durationSeconds' => $incident->duration_seconds,
                ]),
        ]);
    }
}
