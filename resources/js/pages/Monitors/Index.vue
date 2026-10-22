<script setup lang="ts">
import { useLanguage } from '@/composables/useLanguage';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { Activity, Pencil, Plus, Trash2 } from 'lucide-vue-next';
import { onMounted, onUnmounted, ref } from 'vue';

type Monitor = {
    id: number;
    name: string;
    target_url: string;
    method: 'GET' | 'HEAD';
    interval_seconds: number;
    is_enabled: boolean;
    next_check_at: string | null;
    organization: { name: string };
};

defineProps<{ monitors: Monitor[] }>();
const { isPortuguese } = useLanguage();
const now = ref(Date.now());
const fallbackNextChecks = new Map<number, number>();
let countdownTimer: number | undefined;

onMounted(() => {
    countdownTimer = window.setInterval(() => {
        now.value = Date.now();
    }, 1000);
});

onUnmounted(() => {
    if (countdownTimer) window.clearInterval(countdownTimer);
});

const nextCheckLabel = (monitor: Monitor) => {
    if (!monitor.is_enabled) return isPortuguese.value ? 'Pausado' : 'Paused';

    const targetTime = monitor.next_check_at
        ? new Date(monitor.next_check_at).getTime()
        : (fallbackNextChecks.get(monitor.id) ??
          (() => {
              const fallback = now.value + monitor.interval_seconds * 1000;
              fallbackNextChecks.set(monitor.id, fallback);
              return fallback;
          })());
    const remainingSeconds = Math.max(0, Math.ceil((targetTime - now.value) / 1000));
    if (remainingSeconds === 0) return isPortuguese.value ? 'Agora · 0s' : 'Now · 0s';

    const minutes = Math.floor(remainingSeconds / 60);
    const seconds = remainingSeconds % 60;
    const remaining = minutes > 0 ? `${minutes}m ${seconds.toString().padStart(2, '0')}s` : `${seconds}s`;
    return isPortuguese.value ? `Em ${remaining}` : `In ${remaining}`;
};
const breadcrumbs = [{ title: 'Monitors', href: '/monitors' }];

function removeMonitor(id: number) {
    if (window.confirm(isPortuguese.value ? 'Excluir este monitor?' : 'Delete this monitor?'))
        router.delete(route('monitors.destroy', { monitor: id }));
}
</script>

<template>
    <Head :title="isPortuguese ? 'Monitores' : 'Monitors'" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-6 p-4 md:p-6">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-semibold tracking-tight">{{ isPortuguese ? 'Monitores' : 'Monitors' }}</h1>
                    <p class="text-muted-foreground">
                        {{
                            isPortuguese
                                ? 'Gerencie os sites e APIs verificados pelo PulseMonitor.'
                                : 'Manage the websites and APIs PulseMonitor checks.'
                        }}
                    </p>
                </div>
                <Link
                    :href="route('monitors.create')"
                    class="inline-flex items-center gap-2 rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground shadow hover:opacity-90"
                >
                    <Plus class="size-4" /> {{ isPortuguese ? 'Adicionar monitor' : 'Add monitor' }}
                </Link>
            </div>
            <div v-if="monitors.length" class="overflow-hidden rounded-xl border bg-card">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="border-b bg-muted/40 text-muted-foreground">
                            <tr>
                                <th class="px-4 py-3 font-medium">{{ isPortuguese ? 'Monitor' : 'Monitor' }}</th>
                                <th class="px-4 py-3 font-medium">{{ isPortuguese ? 'Método' : 'Method' }}</th>
                                <th class="px-4 py-3 font-medium">{{ isPortuguese ? 'Intervalo' : 'Interval' }}</th>
                                <th class="px-4 py-3 font-medium">{{ isPortuguese ? 'Próximo teste' : 'Next check' }}</th>
                                <th class="px-4 py-3 font-medium">{{ isPortuguese ? 'Status' : 'Status' }}</th>
                                <th class="px-4 py-3 text-right font-medium">{{ isPortuguese ? 'Ações' : 'Actions' }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            <tr v-for="monitor in monitors" :key="monitor.id">
                                <td class="px-4 py-4">
                                    <div class="font-medium">{{ monitor.name }}</div>
                                    <div class="max-w-xs truncate text-xs text-muted-foreground">{{ monitor.target_url }}</div>
                                </td>
                                <td class="px-4 py-4">{{ monitor.method }}</td>
                                <td class="px-4 py-4">{{ isPortuguese ? 'A cada' : 'Every' }} {{ monitor.interval_seconds }}s</td>
                                <td class="px-4 py-4">
                                    <span class="font-medium" :class="monitor.is_enabled ? 'text-primary' : 'text-muted-foreground'">{{
                                        nextCheckLabel(monitor)
                                    }}</span>
                                </td>
                                <td class="px-4 py-4">
                                    <span :class="monitor.is_enabled ? 'text-emerald-600' : 'text-muted-foreground'">{{
                                        monitor.is_enabled ? (isPortuguese ? 'Ativo' : 'Enabled') : isPortuguese ? 'Desativado' : 'Disabled'
                                    }}</span>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="flex justify-end gap-2">
                                        <Link
                                            :href="route('monitors.edit', { monitor: monitor.id })"
                                            class="rounded-md p-2 hover:bg-muted"
                                            :title="isPortuguese ? 'Editar' : 'Edit'"
                                            ><Pencil class="size-4" /></Link
                                        ><button
                                            class="rounded-md p-2 text-destructive hover:bg-muted"
                                            :title="isPortuguese ? 'Excluir' : 'Delete'"
                                            @click="removeMonitor(monitor.id)"
                                        >
                                            <Trash2 class="size-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div v-else class="flex min-h-72 flex-col items-center justify-center rounded-xl border border-dashed bg-card p-8 text-center">
                <Activity class="mb-3 size-8 text-muted-foreground" />
                <h2 class="font-semibold">{{ isPortuguese ? 'Nenhum monitor ainda' : 'No monitors yet' }}</h2>
                <p class="mt-1 max-w-md text-sm text-muted-foreground">
                    {{
                        isPortuguese
                            ? 'Crie seu primeiro monitor para começar a acompanhar a disponibilidade.'
                            : 'Create your first monitor to start tracking availability.'
                    }}
                </p>
            </div>
        </div>
    </AppLayout>
</template>
