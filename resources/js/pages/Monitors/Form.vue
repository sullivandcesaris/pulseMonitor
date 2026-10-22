<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

type Monitor = {
    id?: number;
    name: string;
    target_url: string;
    method: 'GET' | 'HEAD';
    interval_seconds: number;
    expected_status: number;
    timeout_seconds: number;
    is_enabled: boolean;
};
const props = withDefaults(defineProps<{ monitor?: Monitor }>(), { monitor: undefined });
const editing = Boolean(props.monitor?.id);
const form = useForm({
    name: props.monitor?.name ?? '',
    target_url: props.monitor?.target_url ?? '',
    method: props.monitor?.method ?? 'GET',
    interval_seconds: props.monitor?.interval_seconds ?? 60,
    expected_status: props.monitor?.expected_status ?? 200,
    timeout_seconds: props.monitor?.timeout_seconds ?? 10,
    is_enabled: props.monitor?.is_enabled ?? true,
});
const breadcrumbs = [
    { title: 'Monitors', href: '/monitors' },
    { title: editing ? 'Edit monitor' : 'Create monitor', href: '#' },
];
function submit() {
    if (editing && props.monitor?.id) {
        form.put(route('monitors.update', { monitor: props.monitor.id }));
    } else {
        form.post(route('monitors.store'));
    }
}
</script>

<template>
    <Head :title="editing ? 'Edit monitor' : 'Create monitor'" /><AppLayout :breadcrumbs="breadcrumbs"
        ><div class="max-w-3xl flex-1 p-4 md:p-6">
            <div class="mb-6">
                <h1 class="text-2xl font-semibold tracking-tight">{{ editing ? 'Edit monitor' : 'Create monitor' }}</h1>
                <p class="text-muted-foreground">Configure an HTTP availability check.</p>
            </div>
            <form class="space-y-6 rounded-xl border bg-card p-6" @submit.prevent="submit">
                <div class="grid gap-2">
                    <label for="name" class="text-sm font-medium">Name</label
                    ><input id="name" v-model="form.name" class="rounded-md border bg-background px-3 py-2" placeholder="Production API" />
                    <p v-if="form.errors.name" class="text-sm text-destructive">{{ form.errors.name }}</p>
                </div>
                <div class="grid gap-2">
                    <label for="target_url" class="text-sm font-medium">Target URL</label
                    ><input
                        id="target_url"
                        v-model="form.target_url"
                        type="url"
                        class="rounded-md border bg-background px-3 py-2"
                        placeholder="https://example.com/health"
                    />
                    <p v-if="form.errors.target_url" class="text-sm text-destructive">{{ form.errors.target_url }}</p>
                </div>
                <div class="grid gap-4 md:grid-cols-2">
                    <div class="grid gap-2">
                        <label for="method" class="text-sm font-medium">HTTP method</label
                        ><select id="method" v-model="form.method" class="rounded-md border bg-background px-3 py-2">
                            <option value="GET">GET</option>
                            <option value="HEAD">HEAD</option>
                        </select>
                    </div>
                    <div class="grid gap-2">
                        <label for="expected_status" class="text-sm font-medium">Expected status</label
                        ><input
                            id="expected_status"
                            v-model.number="form.expected_status"
                            type="number"
                            min="100"
                            max="599"
                            class="rounded-md border bg-background px-3 py-2"
                        />
                    </div>
                    <div class="grid gap-2">
                        <label for="interval_seconds" class="text-sm font-medium">Interval (seconds)</label
                        ><input
                            id="interval_seconds"
                            v-model.number="form.interval_seconds"
                            type="number"
                            min="10"
                            class="rounded-md border bg-background px-3 py-2"
                        />
                    </div>
                    <div class="grid gap-2">
                        <label for="timeout_seconds" class="text-sm font-medium">Timeout (seconds)</label
                        ><input
                            id="timeout_seconds"
                            v-model.number="form.timeout_seconds"
                            type="number"
                            min="1"
                            class="rounded-md border bg-background px-3 py-2"
                        />
                    </div>
                </div>
                <label class="flex items-center gap-2 text-sm"
                    ><input v-model="form.is_enabled" type="checkbox" class="rounded border" /> Enable this monitor</label
                >
                <div class="flex justify-end gap-3">
                    <a href="/monitors" class="rounded-md border px-4 py-2 text-sm font-medium hover:bg-muted">Cancel</a
                    ><button
                        type="submit"
                        :disabled="form.processing"
                        class="rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground hover:opacity-90 disabled:opacity-50"
                    >
                        {{ form.processing ? 'Saving…' : editing ? 'Save changes' : 'Create monitor' }}
                    </button>
                </div>
            </form>
        </div></AppLayout
    >
</template>
