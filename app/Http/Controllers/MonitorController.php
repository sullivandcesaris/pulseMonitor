<?php

namespace App\Http\Controllers;

use App\Http\Requests\MonitorRequest;
use App\Models\Monitor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MonitorController extends Controller
{
    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Monitor::class);
        $organizationIds = $request->user()->organizations()->pluck('organizations.id');

        return Inertia::render('Monitors/Index', [
            'monitors' => Monitor::query()
                ->whereIn('organization_id', $organizationIds)
                ->with('organization:id,name')
                ->latest()
                ->get()
                ->map(fn (Monitor $monitor) => [
                    'id' => $monitor->id,
                    'name' => $monitor->name,
                    'target_url' => $monitor->target_url,
                    'method' => $monitor->method,
                    'interval_seconds' => $monitor->interval_seconds,
                    'is_enabled' => $monitor->is_enabled,
                    'next_check_at' => $monitor->next_check_at?->toIso8601String(),
                    'organization' => ['name' => $monitor->organization?->name],
                ]),
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', Monitor::class);

        return Inertia::render('Monitors/Create');
    }

    public function store(MonitorRequest $request): RedirectResponse
    {
        $organization = $request->user()->organizations()->firstOrFail();
        $attributes = $request->validated();
        $attributes['next_check_at'] = now()->addSeconds((int) $attributes['interval_seconds']);
        $organization->monitors()->create($attributes);

        return to_route('monitors.index')->with('success', 'Monitor created successfully.');
    }

    public function edit(Monitor $monitor): Response
    {
        $this->authorize('update', $monitor);

        return Inertia::render('Monitors/Edit', ['monitor' => $monitor]);
    }

    public function update(MonitorRequest $request, Monitor $monitor): RedirectResponse
    {
        $attributes = $request->validated();
        $attributes['next_check_at'] = now()->addSeconds((int) $attributes['interval_seconds']);
        $monitor->update($attributes);

        return to_route('monitors.index')->with('success', 'Monitor updated successfully.');
    }

    public function destroy(Monitor $monitor): RedirectResponse
    {
        $this->authorize('delete', $monitor);
        $monitor->delete();

        return to_route('monitors.index')->with('success', 'Monitor deleted successfully.');
    }
}
