<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { AlertTriangle, CheckCircle2 } from 'lucide-vue-next';

type Incident = {
    id: number;
    monitorName: string | null;
    targetUrl: string | null;
    status: string;
    failureCount: number;
    lastError: string | null;
    startedAt: string | null;
    durationSeconds: number | null;
};
defineProps<{ incidents: Incident[] }>();
const breadcrumbs = [{ title: 'Incidents', href: '/incidents' }];
const formatDate = (value: string | null) => (value ? new Date(value).toLocaleString() : '—');
const formatDuration = (seconds: number | null) => {
    if (seconds === null) return 'Ongoing';
    if (seconds < 60) return seconds + 's';
    return Math.floor(seconds / 60) + 'm ' + (seconds % 60) + 's';
};
</script>

<template>
    <Head title="Incidents" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-6 p-4 md:p-6">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight">Incidents</h1>
                <p class="text-muted-foreground">Review outages and recovery history for your services.</p>
            </div>
            <div v-if="incidents.length" class="overflow-hidden rounded-xl border bg-card shadow-sm">
                <div v-for="incident in incidents" :key="incident.id" class="border-b p-5 last:border-b-0">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div class="flex gap-3">
                            <AlertTriangle v-if="incident.status === 'open'" class="mt-0.5 size-5 text-amber-600" /><CheckCircle2
                                v-else
                                class="mt-0.5 size-5 text-emerald-600"
                            />
                            <div>
                                <p class="font-medium">{{ incident.monitorName }}</p>
                                <p class="text-sm text-muted-foreground">{{ incident.targetUrl }}</p>
                            </div>
                        </div>
                        <span
                            class="rounded-full px-2.5 py-1 text-xs font-medium"
                            :class="incident.status === 'open' ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800'"
                            >{{ incident.status === 'open' ? 'Open' : 'Resolved' }}</span
                        >
                    </div>
                    <div class="mt-4 grid gap-3 text-sm text-muted-foreground sm:grid-cols-3">
                        <p><span class="font-medium text-foreground">Started:</span> {{ formatDate(incident.startedAt) }}</p>
                        <p><span class="font-medium text-foreground">Duration:</span> {{ formatDuration(incident.durationSeconds) }}</p>
                        <p><span class="font-medium text-foreground">Failures:</span> {{ incident.failureCount }}</p>
                    </div>
                    <p v-if="incident.lastError" class="mt-3 rounded-md bg-muted px-3 py-2 text-sm text-muted-foreground">{{ incident.lastError }}</p>
                </div>
            </div>
            <div v-else class="flex min-h-72 flex-col items-center justify-center rounded-xl border border-dashed bg-card p-8 text-center">
                <CheckCircle2 class="mb-3 size-8 text-emerald-600" />
                <h2 class="font-semibold">No incidents to display</h2>
                <p class="mt-1 max-w-md text-sm text-muted-foreground">Your services have no recorded outages in the current history.</p>
            </div>
        </div>
    </AppLayout>
</template>
