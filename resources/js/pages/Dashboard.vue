<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { Activity, AlertTriangle, CheckCircle2, Clock3, HelpCircle } from 'lucide-vue-next';

type Stats = {
    activeMonitors: number;
    totalMonitors: number;
    healthyMonitors: number;
    openIncidents: number;
    averageResponseTime: number | null;
    uptimePercentage: number | null;
};
type Check = { id: number; name: string | null; url: string | null; status: string; responseTime: number | null; checkedAt: string | null };

const props = defineProps<{ stats: Stats; period: { label: string }; recentChecks: Check[] }>();
const breadcrumbs: BreadcrumbItem[] = [{ title: 'Dashboard', href: '/dashboard' }];
const formatResponseTime = (value: number | null) => (value === null ? '—' : Math.round(value) + ' ms');
const formatDate = (value: string | null) => (value ? new Date(value).toLocaleString() : 'No checks yet');
const statusLabel = (status: string) => (status === 'up' ? 'Operational' : status === 'down' ? 'Down' : 'Unknown');
const statusClass = (status: string) => (status === 'up' ? 'text-emerald-600' : status === 'down' ? 'text-destructive' : 'text-muted-foreground');
const statCards = [
    { label: 'Active monitors', value: () => props.stats.activeMonitors, detail: 'Enabled monitoring targets', icon: Activity, tone: 'text-sky-600' },
    {
        label: 'Healthy monitors',
        value: () => props.stats.healthyMonitors,
        detail: () => 'of ' + props.stats.totalMonitors + ' registered monitors',
        icon: CheckCircle2,
        tone: 'text-emerald-600',
    },
    { label: 'Open incidents', value: () => props.stats.openIncidents, detail: 'Needs your attention', icon: AlertTriangle, tone: 'text-amber-600' },
    {
        label: 'Avg. response time',
        value: () => formatResponseTime(props.stats.averageResponseTime),
        detail: () => props.period.label,
        icon: Clock3,
        tone: 'text-violet-600',
    },
];
</script>

<template>
    <Head title="Dashboard" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-6 p-4 md:p-6">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight">Good morning</h1>
                <p class="text-muted-foreground">Here’s the current health of your services.</p>
            </div>
            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <div v-for="stat in statCards" :key="stat.label" class="rounded-xl border bg-card p-5 shadow-sm">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-medium text-muted-foreground">{{ stat.label }}</p>
                        <component :is="stat.icon" class="size-5" :class="stat.tone" />
                    </div>
                    <p class="mt-3 text-2xl font-semibold">{{ stat.value() }}</p>
                    <p class="mt-1 text-xs text-muted-foreground">{{ typeof stat.detail === 'function' ? stat.detail() : stat.detail }}</p>
                </div>
            </div>
            <div class="grid gap-6 lg:grid-cols-[1fr_20rem]">
                <div class="rounded-xl border bg-card shadow-sm">
                    <div class="flex items-center justify-between border-b p-5">
                        <div>
                            <h2 class="font-semibold">Recent checks</h2>
                            <p class="text-sm text-muted-foreground">Latest results from your monitoring targets.</p>
                        </div>
                        <a href="/monitors" class="text-sm font-medium text-primary hover:underline">View monitors</a>
                    </div>
                    <div v-if="recentChecks.length" class="divide-y">
                        <div v-for="check in recentChecks" :key="check.id" class="grid gap-2 p-5 sm:grid-cols-[1fr_auto_auto] sm:items-center">
                            <div>
                                <p class="font-medium">{{ check.name }}</p>
                                <p class="truncate text-sm text-muted-foreground">{{ check.url }}</p>
                            </div>
                            <span class="text-sm" :class="statusClass(check.status)">{{ statusLabel(check.status) }}</span>
                            <div class="text-left text-sm text-muted-foreground sm:text-right">
                                <p>{{ formatResponseTime(check.responseTime) }}</p>
                                <p>{{ formatDate(check.checkedAt) }}</p>
                            </div>
                        </div>
                    </div>
                    <div v-else class="flex min-h-48 flex-col items-center justify-center p-8 text-center">
                        <HelpCircle class="mb-3 size-8 text-muted-foreground" />
                        <p class="font-medium">No monitoring data yet</p>
                        <p class="mt-1 text-sm text-muted-foreground">Run a check to start building your service history.</p>
                    </div>
                </div>
                <div class="rounded-xl border bg-card p-5 shadow-sm">
                    <h2 class="font-semibold">Availability</h2>
                    <p class="mt-1 text-sm text-muted-foreground">{{ period.label }}</p>
                    <p v-if="stats.uptimePercentage !== null" class="mt-6 text-4xl font-semibold">{{ stats.uptimePercentage }}%</p>
                    <p v-else class="mt-6 text-lg font-semibold">Unknown</p>
                    <p class="mt-2 text-sm text-muted-foreground">
                        {{
                            stats.uptimePercentage === null
                                ? 'There is not enough monitoring data for this period.'
                                : 'Confirmed uptime based on completed checks.'
                        }}
                    </p>
                    <a href="/incidents" class="mt-6 inline-block text-sm font-medium text-primary hover:underline">View incident history</a>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
