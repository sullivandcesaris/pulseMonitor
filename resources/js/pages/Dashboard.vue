<script setup lang="ts">
import { useLanguage } from '@/composables/useLanguage';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { LineChart } from 'echarts/charts';
import { GridComponent, TooltipComponent } from 'echarts/components';
import { use } from 'echarts/core';
import { CanvasRenderer } from 'echarts/renderers';
import { Activity, AlertTriangle, CheckCircle2, Clock3, HelpCircle } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import VChart from 'vue-echarts';

type Stats = {
    activeMonitors: number;
    totalMonitors: number;
    healthyMonitors: number;
    openIncidents: number;
    averageResponseTime: number | null;
    uptimePercentage: number | null;
};
type Check = { id: number; name: string | null; url: string | null; status: string; responseTime: number | null; checkedAt: string | null };
type HistoryPoint = { checkedAt: string | null; responseTime: number };
type MonitorOption = { id: number; name: string };
use([CanvasRenderer, LineChart, GridComponent, TooltipComponent]);
const { isPortuguese } = useLanguage();
const copy = computed(() =>
    isPortuguese.value
        ? {
              greeting: 'Bom dia',
              summary: 'Veja a saúde atual dos seus serviços.',
              recent: 'Checks recentes',
              recentText: 'Últimos resultados dos seus alvos de monitoramento.',
              viewMonitors: 'Ver monitores',
              availability: 'Disponibilidade',
              response: 'Tempo de resposta',
              checks: 'checks registrados',
              noData: 'Nenhum dado de monitoramento ainda',
              noDataText: 'Execute um check para começar a criar seu histórico.',
              incidents: 'Ver histórico de incidentes',
              points: 'pontos de dados',
              active: 'Monitores ativos',
              healthy: 'Monitores saudáveis',
              registered: 'de monitores cadastrados',
              open: 'Incidentes abertos',
              average: 'Tempo médio de resposta',
              enabled: 'Alvos de monitoramento ativos',
              attention: 'Precisa da sua atenção',
              unknown: 'Desconhecido',
              noPeriod: 'Não há dados suficientes para este período.',
              uptime: 'Disponibilidade confirmada com base nos checks concluídos.',
              noResponse: 'Não há dados de tempo de resposta para este período.',
          }
        : {
              greeting: 'Good morning',
              summary: 'Here’s the current health of your services.',
              recent: 'Recent checks',
              recentText: 'Latest results from your monitoring targets.',
              viewMonitors: 'View monitors',
              availability: 'Availability',
              response: 'Response time',
              checks: 'checks recorded',
              noData: 'No monitoring data yet',
              noDataText: 'Run a check to start building your service history.',
              incidents: 'View incident history',
              points: 'data points',
              active: 'Active monitors',
              healthy: 'Healthy monitors',
              registered: 'of registered monitors',
              open: 'Open incidents',
              average: 'Avg. response time',
              enabled: 'Enabled monitoring targets',
              attention: 'Needs your attention',
              unknown: 'Unknown',
              noPeriod: 'There is not enough monitoring data for this period.',
              uptime: 'Confirmed uptime based on completed checks.',
              noResponse: 'No response-time data for this period.',
          },
);

const props = defineProps<{
    stats: Stats;
    period: { key: string; label: string };
    recentChecks: Check[];
    responseTimeHistory: HistoryPoint[];
    monitors: MonitorOption[];
    coverage: { checks: number; expectedChecks: number; hasData: boolean };
}>();

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Dashboard', href: '/dashboard' }];
const selectedPeriod = ref(props.period.key);
const selectedMonitor = ref('');
const isLoading = ref(false);
const loadError = ref('');
const formatResponseTime = (value: number | null) => (value === null ? '—' : Math.round(value) + ' ms');
const formatDate = (value: string | null) => (value ? new Date(value).toLocaleString() : 'No checks yet');
const statusLabel = (status: string) =>
    status === 'up'
        ? isPortuguese.value
            ? 'Operacional'
            : 'Operational'
        : status === 'down'
          ? isPortuguese.value
              ? 'Indisponível'
              : 'Down'
          : copy.value.unknown;
const statusClass = (status: string) => (status === 'up' ? 'text-emerald-600' : status === 'down' ? 'text-destructive' : 'text-muted-foreground');
const chartOption = computed(() => ({
    tooltip: { trigger: 'axis' },
    grid: { left: 40, right: 16, top: 16, bottom: 24 },
    xAxis: {
        type: 'category',
        data: props.responseTimeHistory.map((point) => (point.checkedAt ? new Date(point.checkedAt).toLocaleTimeString() : '')),
    },
    yAxis: { type: 'value', name: 'ms' },
    series: [{ type: 'line', smooth: true, data: props.responseTimeHistory.map((point) => point.responseTime), areaStyle: {} }],
}));
const applyFilters = () => {
    isLoading.value = true;
    loadError.value = '';
    router.get(
        '/dashboard',
        { period: selectedPeriod.value, monitor_id: selectedMonitor.value || undefined },
        {
            preserveState: true,
            preserveScroll: true,
            onError: () => {
                loadError.value = 'Unable to refresh dashboard data.';
            },
            onFinish: () => {
                isLoading.value = false;
            },
        },
    );
};
const statCards = computed(() => [
    { label: copy.value.active, value: () => props.stats.activeMonitors, detail: copy.value.enabled, icon: Activity, tone: 'text-primary' },
    {
        label: copy.value.healthy,
        value: () => props.stats.healthyMonitors,
        detail: () => props.stats.totalMonitors + ' ' + copy.value.registered,
        icon: CheckCircle2,
        tone: 'text-emerald-600',
    },
    { label: copy.value.open, value: () => props.stats.openIncidents, detail: copy.value.attention, icon: AlertTriangle, tone: 'text-amber-600' },
    {
        label: copy.value.average,
        value: () => formatResponseTime(props.stats.averageResponseTime),
        detail: () => props.period.label,
        icon: Clock3,
        tone: 'text-violet-600',
    },
]);
</script>

<template>
    <Head title="Dashboard" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-6 p-4 md:p-6">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-semibold tracking-tight">{{ copy.greeting }}</h1>
                    <p class="text-muted-foreground">{{ copy.summary }}</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <select
                        v-model="selectedPeriod"
                        class="rounded-md border bg-background px-3 py-2 text-sm"
                        aria-label="Availability period"
                        @change="applyFilters"
                    >
                        <option value="6h">{{ isPortuguese ? 'Últimas 6 horas' : 'Last 6 hours' }}</option>
                        <option value="24h">{{ isPortuguese ? 'Últimas 24 horas' : 'Last 24 hours' }}</option>
                        <option value="7d">{{ isPortuguese ? 'Últimos 7 dias' : 'Last 7 days' }}</option>
                    </select>
                    <select
                        v-model="selectedMonitor"
                        class="rounded-md border bg-background px-3 py-2 text-sm"
                        aria-label="Monitor filter"
                        @change="applyFilters"
                    >
                        <option value="">{{ isPortuguese ? 'Todos os monitores' : 'All monitors' }}</option>
                        <option v-for="monitor in monitors" :key="monitor.id" :value="String(monitor.id)">{{ monitor.name }}</option>
                    </select>
                </div>
            </div>
            <p v-if="loadError" class="rounded-md border border-destructive/30 bg-destructive/5 px-4 py-3 text-sm text-destructive">
                {{ loadError }}
            </p>
            <p v-if="isLoading" class="text-sm text-muted-foreground">Refreshing monitoring data…</p>
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
                            <h2 class="font-semibold">{{ copy.recent }}</h2>
                            <p class="text-sm text-muted-foreground">{{ copy.recentText }}</p>
                        </div>
                        <a href="/monitors" class="text-sm font-medium text-primary hover:underline">{{ copy.viewMonitors }}</a>
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
                        <p class="font-medium">{{ copy.noData }}</p>
                        <p class="mt-1 text-sm text-muted-foreground">{{ copy.noDataText }}</p>
                    </div>
                </div>
                <div class="rounded-xl border bg-card p-5 shadow-sm">
                    <h2 class="font-semibold">{{ copy.availability }}</h2>
                    <p class="mt-1 text-sm text-muted-foreground">{{ period.label }}</p>
                    <p v-if="stats.uptimePercentage !== null" class="mt-6 text-4xl font-semibold">{{ stats.uptimePercentage }}%</p>
                    <p v-else class="mt-6 text-lg font-semibold">{{ copy.unknown }}</p>
                    <p class="mt-2 text-sm text-muted-foreground">
                        {{ stats.uptimePercentage === null ? copy.noPeriod : copy.uptime }}
                    </p>
                    <p class="mt-3 text-xs text-muted-foreground">
                        {{ coverage.checks }} {{ copy.checks
                        }}<span v-if="coverage.expectedChecks > coverage.checks"
                            >; {{ coverage.expectedChecks - coverage.checks }} expected intervals have no data.</span
                        >
                    </p>
                    <a href="/incidents" class="mt-6 inline-block text-sm font-medium text-primary hover:underline">{{ copy.incidents }}</a>
                </div>
            </div>
            <div class="rounded-xl border bg-card p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="font-semibold">{{ copy.response }}</h2>
                        <p class="text-sm text-muted-foreground">{{ period.label }}</p>
                    </div>
                    <span class="text-sm text-muted-foreground">{{ responseTimeHistory.length }} {{ copy.points }}</span>
                </div>
                <div v-if="responseTimeHistory.length" class="mt-5 h-48">
                    <VChart class="h-full w-full" :option="chartOption" autoresize aria-label="Response time history chart" />
                </div>
                <div v-else class="flex h-48 items-center justify-center text-sm text-muted-foreground">{{ copy.noResponse }}</div>
            </div>
        </div>
    </AppLayout>
</template>
