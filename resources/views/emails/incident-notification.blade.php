<h1>{{ $event === 'opened' ? 'Incident opened' : 'Service recovered' }}</h1>
<p>Monitor: {{ $incident->monitor->name }}</p>
<p>Target: {{ $incident->monitor->target_url }}</p>
<p>Started: {{ $incident->started_at->toDateTimeString() }}</p>
@if ($incident->resolved_at)
    <p>Resolved: {{ $incident->resolved_at->toDateTimeString() }}</p>
@endif
@if ($incident->last_error)
    <p>Details: {{ $incident->last_error }}</p>
@endif
